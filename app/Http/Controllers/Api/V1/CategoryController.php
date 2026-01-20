<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CategoryLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function __construct(
        private Category $category
    )
    {
    }

    /**
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        $categories = Cache::rememberForever(CACHE_CATEGORY_TABLE, function (){
            return $this->category->where(['position' => 0, 'status' => 1])->get();
        });

        foreach ($categories as $category) {
            $category['products_count'] = Product::whereJsonContains('category_ids', ['id' => (string)$category['id']])->count();
        }

        return response()->json($categories, 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getChildes($id): JsonResponse
    {
        $categories = $this->category->where(['parent_id' => $id, 'status' => 1])->get();
        return response()->json($categories, 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getProducts($id): JsonResponse
    {
        return response()->json(Helpers::product_data_formatting(CategoryLogic::products($id), true), 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getAllProducts($id): JsonResponse
    {
        try {
            return response()->json(Helpers::product_data_formatting(CategoryLogic::all_products($id), true), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getFeaturedCategories(): JsonResponse
    {
        $featuredCategoryList = Category::active()->where(['is_featured' => 1])->get();

        $featuredData = [];

        foreach ($featuredCategoryList as $category) {
            $products = Product::active()->whereJsonContains('category_ids', ['id' => (string)$category->id])->take(15)->get();
            if ($products->count() > 0) {

                $featuredData[] = [
                    'category' => $category,
                    'products' => Helpers::product_data_formatting($products, true)
                ];
            }
        }

        return response()->json(['featured_data' => $featuredData], 200);
    }
    public function getPopularCategories(): JsonResponse
    {
        $categories = Cache::rememberForever(CACHE_POPULAR_CATEGORY_TABLE, function (){
            return $this->category->where(['position' => 0, 'status' => 1,'is_featured' => 1])->get();
        });
        return response()->json($categories, 200);
    }
}
