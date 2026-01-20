<?php

namespace App\CentralLogics;

use App\Models\Category;
use App\Models\Product;

class CategoryLogic
{
    /**
     * @return mixed
     */
    public static function parents(): mixed
    {
        return Category::where('position', 0)->get();
    }

    /**
     * @param $parent_id
     * @return mixed
     */
    public static function child($parent_id): mixed
    {
        return Category::where(['parent_id' => $parent_id])->get();
    }

    /**
     * @param $category_id
     * @return mixed
     */
    public static function products($category_id): mixed
    {
        $products = Product::active()->get();
        $product_ids = [];
        foreach ($products as $product) {
            foreach (json_decode($product['category_ids'], true) as $category) {
                if ($category['id'] == $category_id) {
                    $product_ids[] = $product['id'];
                }
            }
        }
        return Product::active()->withCount(['wishlist'])->with('rating')->whereIn('id', $product_ids)->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function all_products($id): mixed
    {
        $cate_ids=[];
        $cate_ids[] = (int)$id;
        foreach (CategoryLogic::child($id) as $ch1){
            $cate_ids[] = $ch1['id'];
            foreach (CategoryLogic::child($ch1['id']) as $ch2){
                $cate_ids[] = $ch2['id'];
            }
        }

        $products = Product::active()->get();
        $product_ids = [];
        foreach ($products as $product) {
            foreach (json_decode($product['category_ids'], true) as $category) {
                if (in_array($category['id'],$cate_ids)) {
                    $product_ids[] = $product['id'];
                }
            }
        }

        return Product::active()->withCount(['wishlist'])->with('rating')->whereIn('id', $product_ids)->get();
    }
}
