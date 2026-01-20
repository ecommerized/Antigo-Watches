<?php

namespace App\CentralLogics;


use App\Models\FlashSale;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProductLogic
{
    public static function get_product($id)
    {
        return Product::active()->withCount(['wishlist'])->with(['rating', 'reviews'])->where('id', $id)->first();
    }

    public static function get_latest_products($sort_by, $limit = 10, $offset = 1)
    {
        $limit = is_null($limit) ? 10 : $limit;
        $offset = is_null($offset) ? 1 : $offset;

        $paginator = Product::active()
            ->withCount(['wishlist'])
            ->with(['rating'])
            ->when($sort_by == 'price_high_to_low', function ($query) {
                return $query->orderBy('price', 'desc');
            })
            ->when($sort_by == 'price_low_to_high', function ($query) {
                return $query->orderBy('price', 'asc');
            })
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_related_products($product_id)
    {
        $product = Product::find($product_id);
        return Product::active()->withCount(['wishlist'])->with(['rating'])->where('category_ids', $product->category_ids)
            ->where('id', '!=', $product->id)
            ->limit(10)
            ->get();
    }

    public static function search_products($name, $price_low, $price_high, $rating, $category_ids, $sort_by, $limit = 10, $offset = 1)
    {
        $key = $name;

        $searched_products = Product::active();

        // Clone query for price range
        $priceRangeQuery = clone $searched_products;
        $lowest_price = $priceRangeQuery->min('price');
        $highest_price = $priceRangeQuery->max('price');
        $searched_products = $searched_products->withCount('wishlist')
            ->with('rating')
            ->when($key, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    $q->orWhere('name', 'like', "%{$key}%");
                });
            })
            ->when($price_low !== null && $price_high !== null, function ($query) use ($price_low, $price_high) {
                return $query->whereBetween('price', [$price_low, $price_high]);
            })
            ->when($category_ids, function ($query) use ($category_ids) {
                $categories = is_array($category_ids) ? $category_ids : json_decode($category_ids, true);
                $query->where(function ($q) use ($categories) {
                    foreach ($categories as $categoryId) {
                        $q->orWhereJsonContains('category_ids', ['id' => (string)$categoryId]);
                    }
                });
            })
            ->when($rating !== null, function ($query) use ($rating) {
                $query->whereHas('reviews', function ($q) use ($rating) {
                    $q->select('product_id')
                        ->groupBy('product_id')
                        ->havingRaw('AVG(rating) >= ?', [$rating]);
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
                        $query->latest(); // fallback sort
                        break;
                }
            });



        $paginator = $searched_products->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'lowest_price' => (int)($lowest_price ?? 0),
            'highest_price' => (int)($highest_price ?? 0),
            'price_high' => $price_high,
            'price_low' => $price_low,
            'rating' => $rating,
            'category_ids' => $category_ids,
            'sort_by' => $sort_by,
            'products' => $paginator->items(),
        ];
    }

    public static function filterFlashSale($flashSaleProductIds, $price_low, $price_high, $rating, $category_ids, $sort_by, $limit = 10, $offset = 1)
    {
        $query = Product::active()
            ->whereIn('id', $flashSaleProductIds)
            ->withCount('wishlist')
            ->with('rating');

        // Get min & max price BEFORE pagination (clone query)
        $priceRangeQuery = (clone $query);
        $lowest_price = $priceRangeQuery->min('price');
        $highest_price = $priceRangeQuery->max('price');

        // Filter by rating
        if (!empty($rating)) {
            $query->whereHas('reviews', function ($q) use ($rating) {
                $q->select('product_id')
                    ->groupBy('product_id')
                    ->havingRaw('AVG(rating) >= ?', [$rating]);
            });
        }

        // Filter by category (supports multiple)
        if (!empty($category_ids)) {
            $categoryIds = is_array($category_ids) ? $category_ids : json_decode($category_ids, true);
            $query->where(function ($q) use ($categoryIds) {
                foreach ($categoryIds as $categoryId) {
                    $q->orWhereJsonContains('category_ids', ['id' => (string)$categoryId]);
                }
            });
        }

        // Filter by price range
        if ($price_low !== null && $price_high !== null) {
            $query->whereBetween('price', [$price_low, $price_high]);
        }

        // Sorting
        switch ($sort_by) {
            case 'new_arrival':
                $query->where('created_at', '>=', now()->subMonths(3))->orderBy('created_at', 'desc');
                break;
            case 'offer_product':
                $query->where('discount', '>', 0)->orderBy('discount', 'desc');
                break;
            case 'price_high_to_low':
                $query->orderBy('price', 'desc');
                break;
            case 'price_low_to_high':
                $query->orderBy('price', 'asc');
                break;
            case 'a_to_z':
                $query->orderBy('name', 'asc');
                break;
            case 'z_to_a':
                $query->orderBy('name', 'desc');
                break;
        }



        // Apply pagination
        $paginator = $query->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'lowest_price' => (int)($lowest_price ?? 0),
            'highest_price' => (int)($highest_price ?? 0),
            'price_high' => $price_high,
            'price_low' => $price_low,
            'rating' => $rating,
            'sort_by' => $sort_by,
            'category_ids' => $category_ids,
            'flash_sale' => FlashSale::active()->first(),
            'products' => $paginator->items(),
        ];
    }

    public static function get_product_review($id)
    {
        $reviews = Review::where('product_id', $id)->get();
        return $reviews;
    }

    public static function get_rating($reviews)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;
        foreach ($reviews as $key => $review) {
            if ($review->rating == 5) {
                $rating5 += 1;
            }
            if ($review->rating == 4) {
                $rating4 += 1;
            }
            if ($review->rating == 3) {
                $rating3 += 1;
            }
            if ($review->rating == 2) {
                $rating2 += 1;
            }
            if ($review->rating == 1) {
                $rating1 += 1;
            }
        }
        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }

    public static function get_favorite_products($limit, $offset, $user_id)
    {
        $limit = is_null($limit) ? 10 : $limit;
        $offset = is_null($offset) ? 1 : $offset;

        $ids = User::with('wishlist_products')->find($user_id)->wishlist_products->pluck('product_id')->toArray();
        $wishlist_products = Product::whereIn('id', $ids)->paginate($limit, ['*'], 'page', $offset);

        $formatted_products = Helpers::product_data_formatting($wishlist_products, true);

        return [
            'total_size' => $wishlist_products->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $formatted_products
        ];
    }

    public static function get_new_arrival_products($limit = 10, $offset = 1)
    {
        $threeMonthsAgo = now()->subMonths(3);

        $paginator = Product::active()
            ->withCount(['wishlist'])
            ->with(['rating'])
            ->where('created_at', '>=', $threeMonthsAgo)
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_product_rating_reviews($product)
    {
        $totalReview = $product->reviews()->count();

        // Average rating
        $averageRating = $product->reviews()
            ->avg('rating');
        $averageRating = round($averageRating, 2);
        $ratingGroupCount = $product->reviews()->select('rating', DB::raw('count(rating) as total'))
            ->groupBy('rating')
            ->orderBy('rating', 'asc')
            ->pluck('total', 'rating');
        // Count of each rating type
        $ratings = [1, 2, 3, 4, 5];  // List of possible ratings (1 to 5)
        $ratingData = [];

        foreach ($ratings as $rating) {
            // If the rating exists in the results, use its count, otherwise set to 0
            $ratingData[$rating] = $ratingGroupCount->get($rating, 0);
        }

        return [
            'total_review' => $totalReview,
            'average_rating' => $averageRating,
            'rating_group_count' => $ratingData,
        ];

    }
}
