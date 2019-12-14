<?php

namespace App\Http\Controllers\Api;

use App\Chat;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    /**
     * チャット一覧取得
     * 
     * @return StreamedResponse
     */
    public function event(): StreamedResponse
    {
        // 最近のチャット5件と、一番最近のcreated_atを取得
        $chats = Chat::orderBy('created_at', 'desc')->limit(5)->get()->sortBy('created_at')->values();
        $tmpLatestCreatedAt = optional($chats->last())->created_at ?? Carbon::minValue();
        $response = new StreamedResponse(function() use ($chats, $tmpLatestCreatedAt) {
            printf("data: %s\n\n", json_encode(['posts' => $chats]));
            ob_flush();
            flush();
            while(true) {
                $latestCreatedAt = is_null(Redis::get('latest_created_at')) ? Carbon::minValue() : Carbon::parse(Redis::get('latest_created_at'));
                if ($latestCreatedAt->gt($tmpLatestCreatedAt)) {
                    // チャットに更新があった場合はテーブルから取得
                    $latestChats = Chat::where('created_at', '>', $tmpLatestCreatedAt)->orderBy('created_at', 'asc')->get();
                    $tmpLatestCreatedAt = $latestCreatedAt;
                }
                echo 'data: ' . json_encode(['posts' => $latestChats ?? []]) . "\n\n";
                ob_flush();
                flush();

                $latestChats = null;

                sleep(1);
            }
        });
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Cach-Control', 'no-cache');
        return $response;
    }
}
