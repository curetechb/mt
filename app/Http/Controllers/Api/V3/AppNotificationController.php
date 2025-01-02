<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Resources\V3\NotificationResource;
use App\Models\NotificationList;
use Illuminate\Http\Request;

class AppNotificationController extends Controller
{
    public function notifications(){
        $notifications = NotificationList::orderBy('id', 'desc')->paginate(request('paginate', 15));
        return NotificationResource::collection($notifications);
    }
}
