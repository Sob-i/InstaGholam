<?php

namespace App\Http\Controllers\front\messages;

use App\Http\Controllers\Controller;
use App\Http\Requests\chat\createChatRequest;
use App\Models\chat\chatModel;
use App\Models\User;
use App\Services\messagesServices\messageServices;
use Illuminate\Http\Request;

class messageController extends Controller
{
    public function __construct(protected messageServices $messageServices)
    {

    }
    public function messagePageShow($id)
    {
        $chat = $this->messageServices->ShowOrCreateChat($id);
        return view('index.messages.messagePage',compact('chat',));
    }
}
