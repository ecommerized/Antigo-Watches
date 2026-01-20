<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Translation;
use function App\Library\errorProcessor;
use function App\Library\responseFormatter;

class ProductController extends Controller
{
    public function __construct(
        private Product $product,
        private Review  $review,
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getLatestProduct(Request $request): JsonResponse
    {
        $products = ProductLogic::get_latest_products($request['sort_by'], $request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getSearchedProduct(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $products = ProductLogic::search_products(
            name: $request['name'],
            price_low: $request['price_low'],
            price_high: $request['price_high'],
            rating: $request['rating'],
            category_ids: $request['category_ids'],
            sort_by: $request['sort_by'],
            limit: $request['limit'],
            offset: $request['offset']
        );

        if (empty($products['products']) || count($products['products']) == 0) {
            $key = $request['name'];
            $sort_by = $request['sort_by'];
            // 🔹 Build base query
            $searchedProducts = Product::active();

            // 🔹 Min/max in one clone
            $priceRangeQuery = clone $searchedProducts;
            $lowestPrice = $priceRangeQuery->min('price');
            $highestPrice = $priceRangeQuery->max('price');

            $searchedProducts = $searchedProducts->with(['rating'])
                ->withCount('wishlist')
                ->when(!empty($key), function ($query) use ($key) {
                    // Search in translations
                    $ids = Translation::where('key', 'name')
                        ->where('translationable_type', Product::class)
                        ->where(function ($q) use ($key) {
                            $q->orWhere('value', 'like', "%{$key}%");
                        })
                        ->pluck('translationable_id');
                    $query->whereIn('id', $ids);
                })
                ->when($request->filled('price_low') && $request->filled('price_high'), function ($query) use ($request) {
                    return $query->whereBetween('price', [$request['price_low'], $request['price_high']]);
                })
                ->when($request->filled('category_ids'), function ($query) use ($request) {
                    $categories = is_array($request['category_ids']) ? $request['category_ids'] : json_decode($request['category_ids'], true);
                    $query->where(function ($q) use ($categories) {
                        foreach ($categories as $categoryId) {
                            $q->orWhereJsonContains('category_ids', ['id' => (string)$categoryId]);
                        }
                    });
                })
                ->when($request->filled('rating'), function ($query) use ($request) {
                    $query->whereHas('reviews', function ($q) use ($request) {
                        $q->select('product_id')
                            ->groupBy('product_id')
                            ->havingRaw('AVG(rating) >= ?', [$request['rating']]);
                    });
                })
                ->when($sort_by, function ($query) use ($sort_by) {
                    switch ($sort_by) {
                        case 'new_arrival':
                            $query->where('created_at', '>=', now()->subMonths(3))
                                ->orderBy('created_at', 'desc');
                            break;

                        case 'offer_product':
                            $query->where('discount', '>', 0)
                                ->orderBy('discount', 'desc');
                            break;

                        case 'low_high':
                            $query->orderBy('price', 'asc');
                            break;

                        case 'high_low':
                            $query->orderBy('price', 'desc');
                            break;

                        case 'top_rated':
                            $query->withAvg('reviews', 'rating')
                                ->orderByDesc('reviews_avg_rating');
                            break;

                        default:
                            $query->latest();
                            break;
                    }
                });


            $paginator = $searchedProducts->paginate(
                $request['limit'] ?? 10,
                ['*'],
                'page',
                $request['offset'] ?? 1
            );

            $products = [
                'total_size'   => $paginator->total(),
                'limit'        => (int)($request['limit'] ?? 10),
                'offset'       => (int)($request['offset'] ?? 1),
                'lowest_price' => (int)($lowestPrice ?? 0),
                'highest_price'=> (int)($highestPrice ?? 0),
                'price_high'   => $request['price_high'],
                'price_low'    => $request['price_low'] ,
                'rating'       => $request['rating'] ,
                'category_ids' => $request['category_ids'] ,
                'sort_by' => $sort_by,
                'products'     => $paginator->items(),
            ];
        }

        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }


    public function getProduct($id): JsonResponse
    {
        $product = ProductLogic::get_product($id);
        if (!isset($product)) {
            return response()->json([
                'errors' => ['code' => 'product-001', 'message' => translate('Product not found')]
            ], 200);
        }
        $product = Helpers::product_data_formatting($product, false);

        $categoryIds = collect($product->category_ids)->pluck('id')->toArray();

        $relatedProducts = Product::active()
            ->with('rating')
            ->withCount(['wishlist'])
            ->where(function ($query) use ($categoryIds) {
                foreach ($categoryIds as $categoryId) {
                    $query->orWhere('category_ids', 'LIKE', '%"id":"' . $categoryId . '"%');
                }
            })
            ->where('id', '!=', $product->id) // Exclude main product
            ->get();

        $finalProducts = collect([2, 1])->flatMap(function ($position) use ($relatedProducts) {
            return $relatedProducts->filter(function ($product) use ($position) {
                $relatedCategories = json_decode($product->category_ids, true);
                foreach ($relatedCategories as $category) {
                    if ($category['position'] == $position) {
                        return true;
                    }
                }
                return false;
            });
        })->unique('id') // Avoid duplicate products
        ->take(10); // Get first 10 products

        $relatedProducts = Helpers::product_data_formatting($finalProducts, true);


        #review & rating
        $overallRating = ProductLogic::get_product_rating_reviews($product);

        $data = [
            'product' => $product,
            'related_products' => $relatedProducts,
            'overall_rating' => $overallRating,
        ];
        return response()->json($data, 200);

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getRelatedProduct($id): JsonResponse
    {
        if ($this->product->find($id)) {
            $products = ProductLogic::get_related_products($id);
            $products = Helpers::product_data_formatting($products, true);
            return response()->json($products, 200);
        }
        return response()->json([
            'errors' => ['code' => 'product-001', 'message' => translate('Product not found')]
        ], 404);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getProductReviews($id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required'
        ]);
        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (!$this->product->find($id)) {
            return response()->json([
                'errors' => ['code' => 'product-001', 'message' => translate('Product not found')]
            ], 404);
        }
        $reviews = $this->review->with(['customer'])->where(['product_id' => $id])->paginate(perPage: $request['limit'], page: $request['offset'] ?? 1);
        $reviews = ReviewResource::collection($reviews);
        return response()->json(responseFormatter(constant: DEFAULT_200, content: $reviews, limit: $request['limit'], offset: $request['offset']), 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getProductRating($id): JsonResponse
    {
        try {
            $product = $this->product->find($id);
            $overallRating = ProductLogic::get_overall_rating($product->reviews);
            return response()->json(floatval($overallRating[0]), 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function submitProductReview(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'order_id' => 'required',
            'comment' => 'required',
            'rating' => 'required|numeric|max:5',
        ]);

        $product = $this->product->find($request->product_id);
        if (!isset($product)) {
            $validator->errors()->add('product_id', 'There is no such product');
        }

        $multiReview = $this->review->where(['product_id' => $request->product_id, 'user_id' => $request->user()->id])->first();
        if (isset($multiReview)) {
            $review = $multiReview;
        } else {
            $review = $this->review;
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $imageArray = [];
        if (!empty($request->file('attachment'))) {
            foreach ($request->attachment as $img) {
                $image_data = Helpers::upload('review/', 'png', $img);
                $imageArray[] = $image_data;
            }
        }

        $review->user_id = $request->user()->id;
        $review->product_id = $request->product_id;
        $review->order_id = $request->order_id;
        $review->comment = $request->comment;
        $review->rating = $request->rating;
        $review->attachment = json_encode($imageArray);
        $review->save();

        return response()->json(['message' => translate('successfully review submitted')], 200);
    }

    /**
     * @return JsonResponse
     */
    public function getDiscountedProduct(): JsonResponse
    {
        try {
            $products = Helpers::product_data_formatting($this->product->orderBy('id', 'desc')->active()->withCount(['wishlist'])->with(['rating'])->where('discount', '>', 0)->get(), true);
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getNewArrivalProducts(Request $request): JsonResponse
    {
        $products = ProductLogic::get_new_arrival_products($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }
}
