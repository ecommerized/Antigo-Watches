<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    public function __construct(
        private Wishlist $wishlist
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addToWishlist(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array',
        ],
            [
                'product_ids.required' => 'product_ids ' . translate('is required'),
                'product_ids.array' => 'product_ids ' . translate('must be an array')
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $favoriteIds = [];
        foreach ($request->product_ids as $id) {
            $values = [
                'user_id' => $request->user()->id,
                'product_id' => $id,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $favoriteIds[] = $values;
            $this->wishlist->updateOrInsert(
                ['user_id' => $values['user_id'], 'product_id' => $values['product_id']],
                $values
            );
        }
        return response()->json(['message' => translate('Item added to wishlist!')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function removeFromWishlist(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array',
        ],
            [
                'product_ids.required' => 'product_ids ' . translate('is required'),
                'product_ids.array' => 'product_ids ' . translate('must be an array')
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $collection = $this->wishlist->whereIn('product_id', $request->product_ids)->get(['id']);
        $this->wishlist->destroy($collection->toArray());

        return response()->json(['message' => translate('Item removed from wishlist list! ')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function wishlist(Request $request): JsonResponse
    {
        $products = ProductLogic::get_favorite_products($request['limit'], $request['offset'], $request->user()->id);
        return response()->json($products, 200);
    }
}
