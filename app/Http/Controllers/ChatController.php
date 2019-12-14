<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * チャット画面表示
     * 
     * @return View
     */
    public function index(): View
    {
        return view('chat');
    }
}
