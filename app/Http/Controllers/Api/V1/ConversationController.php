<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Admin;
use App\Models\BusinessSetting;
use App\Models\Conversation;
use App\Models\DcConversation;
use App\Models\DeliveryMan;
use App\Models\Message;
use App\Models\Order;
use App\Traits\UploadSizeHelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private Admin           $admin,
        private BusinessSetting $businessSetting,
        private Conversation    $conversation,
        private DcConversation  $dcConversation,
        private DeliveryMan     $deliveryMan,
        private Message         $message,
        private Order           $order
    )
    {
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getAdminMessage(Request $request): array
    {
        $limit = $request->has('limit') ? $request->limit : 10;
        $offset = $request->has('offset') ? $request->offset : 1;
        $messages = $this->conversation->where(['user_id' => $request->user()->id])->latest()->paginate($limit, ['*'], 'page', $offset);
        $messages = ConversationResource::collection($messages);
        return [
            'total_size' => $messages->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'messages' => $messages->items()
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeAdminMessage(Request $request): JsonResponse
    {
        $this->initUploadLimits();
        $this->validateUploadedFile($request, ['image']);

        $validator = Validator::make($request->all(), [
            'message'  => 'required_without_all:image|nullable|string',
            'image'   => 'sometimes|array',
            'image.*' => 'image|max:' . $this->maxImageSizeKB . '|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        try {
            $imageNameId = [];
            if (!empty($request->file('image'))) {
                foreach ($request->image as $img) {
                    $image = Helpers::upload('conversation/', APPLICATION_IMAGE_FORMAT, $img);
                    $imageUrl = asset('storage/app/public/conversation') . '/' . $image;
                    $imageNameId[] = $imageUrl;
                }
                $images = $imageNameId;
            } else {
                $images = null;
            }
            $conv = $this->conversation;
            $conv->user_id = $request->user()->id;
            $conv->message = $request->message;
            $conv->image = null;
            $conv->attachment = isset($images) ? json_encode($images) : null;
            $conv->save();

            $admin = $this->admin->first();
            $data = [
                'title' => $request->user()->f_name . ' ' . $request->user()->l_name . \App\CentralLogics\translate(' send a message'),
                'description' => $request->user()->id,
                'order_id' => '',
                'image' => asset('storage/app/public/restaurant') . '/' . $this->businessSetting->where(['key' => 'logo'])->first()->value,
                'type' => 'message',
            ];
            try {
                Helpers::send_push_notif_to_device($admin->fcm_token, $data);
            } catch (\Exception $exception) {
            }

            return response()->json(['message' => translate('Successfully sent')], 200);

        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }

    }

    /**
     * @param Request $request
     * @return array|JsonResponse|int[]
     */
    public function getMessageByOrder(Request $request): array|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $limit = $request->has('limit') ? $request->limit : 10;
        $offset = $request->has('offset') ? $request->offset : 1;

        $conversations = $this->dcConversation->where('order_id', $request->order_id)->first();
        if (!isset($conversations)) {
            return ['total_size' => 0, 'limit' => (int)$limit, 'offset' => (int)$offset, 'messages' => []];
        }
        $conversations = $conversations->setRelation('messages', $conversations->messages()->latest()->paginate($limit, ['*'], 'page', $offset));
        $message = MessageResource::collection($conversations->messages);

        return [
            'total_size' => $message->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'messages' => $message->items()
        ];
    }

    /**
     * @param Request $request
     * @param $senderType
     * @return JsonResponse
     */
    public function storeMessageByOrder(Request $request, $senderType): JsonResponse
    {
        $this->initUploadLimits();
        $this->validateUploadedFile($request, ['image']);


        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'message'  => 'required_without_all:image|nullable|string',
            'image'   => 'sometimes|array',
            'image.*' => 'image|max:' . $this->maxImageSizeKB . '|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $senderId = null;
        $order = $this->order->with('delivery_man')->with('customer')->find($request->order_id);

        if ($senderType == 'deliveryman') {
            $validator = Validator::make($request->all(), [
                'token' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $dm = $this->deliveryMan->where('auth_token', $request->token)->first();

            if (isset($dm) && $dm->id != $order->delivery_man->id) {
                return response()->json(['errors' => 'Unauthorized'], 401);
            }

            $senderId = $order->delivery_man->id;

        } elseif ($senderType == 'customer') {
            $senderId = $order->customer->id;
        }

        if ($request->message == null && $request->image == null) {
            return response()->json(['message' => translate('Message can not be empty')], 400);
        }

        $imageNameId = [];
        if (!empty($request->file('image'))) {
            foreach ($request->image as $img) {
                $image = Helpers::upload('conversation/', APPLICATION_IMAGE_FORMAT, $img);
                $imageUrl = asset('storage/app/public/conversation') . '/' . $image;
                $imageNameId[] = $imageUrl;
            }
            $images = $imageNameId;
        } else {
            $images = null;
        }

        if ($request->order_id != null) {
            DB::transaction(function () use ($request, $senderType, $images, $senderId) {
                $dcConversation = $this->dcConversation->where('order_id', $request->order_id)->first();
                if (!isset($dcConversation)) {
                    $dcConversation = $this->dcConversation;
                    $dcConversation->order_id = $request->order_id;
                    $dcConversation->save();
                }

                $message = $this->message;
                $message->conversation_id = $dcConversation->id;
                $message->customer_id = ($senderType == 'customer') ? $senderId : null;
                $message->deliveryman_id = ($senderType == 'deliveryman') ? $senderId : null;
                $message->message = $request->message ?? null;
                $message->attachment = json_encode($images);
                $message->save();
            });
        }

        $data = [
            'title' => 'New message arrived',
            'description' => $request->reply,
            'order_id' => $request->order_id ?? null,
            'image' => '',
            'type' => 'message',
        ];

        if ($senderType == 'customer') {
            $receiverFcmToken = $order->delivery_man->fcm_token ?? null;

            if (isset($order) && $order['is_guest'] == 0){
                $customerName = $order->customer ? $order->customer->f_name .' '.  $order->customer->l_name : '';
                $customerImage = $order->customer ? $order->customer->image : '';
            }
            $data['user_name'] = $customerName ?? '';
            $data['user_image'] = $customerImage ?? '';

        } elseif ($senderType == 'deliveryman') {
            $receiverFcmToken = $order->customer->cm_firebase_token ?? null;

            if (isset($order)){
                $deliverymanName = $order->delivery_man ? $order->delivery_man->f_name .' '.  $order->delivery_man->l_name : '';
                $deliverymanImage = $order->delivery_man ? $order->delivery_man->image : '';
            }

            $data['user_name'] = $deliverymanName ?? '';
            $data['user_image'] = $deliverymanImage ?? '';
        }

        try {
            Helpers::send_push_notif_to_device($receiverFcmToken, $data);

        } catch (\Exception $exception) {
            return response()->json(['message' => translate('Push notification send failed')], 200);
        }

        return response()->json(['message' => translate('Message successfully sent')], 200);

    }

    /**
     * @param Request $request
     * @return array|JsonResponse|int[]
     */
    public function getOrderMessageForDm(Request $request): array|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $deliveryMan = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
        if (!isset($deliveryMan)) {
            return response()->json(['errors' => 'Unauthenticated.'], 401);
        }

        $limit = $request->has('limit') ? $request->limit : 10;
        $offset = $request->has('offset') ? $request->offset : 1;

        $conversations = $this->dcConversation->where('order_id', $request->order_id)->first();
        if (!isset($conversations)) {
            return ['total_size' => 0, 'limit' => (int)$limit, 'offset' => (int)$offset, 'messages' => []];
        }
        $conversations = $conversations->setRelation('messages', $conversations->messages()->latest()->paginate($limit, ['*'], 'page', $offset));
        $message = MessageResource::collection($conversations->messages);

        return [
            'total_size' => $message->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'messages' => $message->items()
        ];
    }

}
