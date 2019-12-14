<?php

namespace App\Http\Controllers\Api;

use App\Chat;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class ChatController extends Controller
{
    /**
     * チャット保存
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $chat = Chat::make([
            'user' => $request->get('user'), 
            'post' => $request->get('post'),
        ]);
        $chat->save();
        Redis::set('latest_created_at', $chat->created_at->toDateTimeString());
        return response()->json(['result' => 'ok']);
    }
}
