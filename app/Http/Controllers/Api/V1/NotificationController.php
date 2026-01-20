<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\GuestUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private Notification $notification
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $userId = $user ? $user->id : config('guest_id');
        $userCreatedAt = now();

        if ($user) {
            $userCreatedAt = User::find($userId)?->created_at ?? now();
        } else {
            $userCreatedAt = GuestUser::find($userId)?->created_at ?? now();
        }

        $notifications = $this->notification
            ->active()
            ->where('created_at', '>', $userCreatedAt)
            ->get();

        return response()->json($notifications, 200);
    }
}
