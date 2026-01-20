<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\DMReview;
use App\Traits\UploadSizeHelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DeliveryManReviewController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private DeliveryMan $deliveryMan,
        private DMReview    $dmReview,
    )
    {
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getReviews($id): JsonResponse
    {
        $reviews = $this->dmReview->with(['customer', 'delivery_man'])->where(['delivery_man_id' => $id])->get();

        $storage = [];
        foreach ($reviews as $item) {
            $item['attachment'] = json_decode($item['attachment']);
            $storage[] = $item;
        }

        return response()->json($storage, 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getRating($id): JsonResponse
    {
        try {
            $totalReviews = $this->dmReview->where(['delivery_man_id' => $id])->get();
            $rating = 0;
            foreach ($totalReviews as $key => $review) {
                $rating += $review->rating;
            }

            if ($rating == 0) {
                $overallRating = 0;
            } else {
                $overallRating = number_format($rating / $totalReviews->count(), 2);
            }

            return response()->json(floatval($overallRating), 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function submitReview(Request $request): JsonResponse
    {
        $this->initUploadLimits();
        $this->validateUploadedFile($request, ['attachment']);

        $validator = Validator::make($request->all(), [
            'delivery_man_id' => 'required',
            'order_id' => 'required',
            'comment' => 'required',
            'rating' => 'required|numeric|max:5',
            'attachment'   => 'sometimes|array|min:1',
            'attachment.*' => 'image|max:' . $this->maxImageSizeKB . '|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ]);

        $dm = $this->deliveryMan->find($request->delivery_man_id);
        if (!isset($dm)) {
            $validator->errors()->add('delivery_man_id', 'There is no such delivery man!');
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $multiReview = $this->dmReview->where(['order_id' => $request->order_id, 'delivery_man_id' => $request->delivery_man_id])->first();
        $review = $multiReview ?? $this->dmReview;

        $imageArray = [];
        if (!empty($request->file('attachment'))) {
            foreach ($request->file('attachment') as $image) {
                if ($image != null) {
                    if (!Storage::disk('public')->exists('review')) {
                        Storage::disk('public')->makeDirectory('review');
                    }
                    $imageArray[] = Storage::disk('public')->put('review', $image);
                }
            }
        }

        $review->user_id = $request->user()->id;
        $review->delivery_man_id = $request->delivery_man_id;
        $review->order_id = $request->order_id;
        $review->comment = $request->comment;
        $review->rating = $request->rating;
        $review->attachment = json_encode($imageArray);
        $review->save();

        return response()->json(['message' => translate('successfully review submitted')], 200);
    }
}
