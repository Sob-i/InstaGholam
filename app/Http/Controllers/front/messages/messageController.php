<?php

namespace App\Http\Controllers\front\messages;

use App\Http\Controllers\Controller;
use App\Http\Requests\chat\createChatRequest;
use App\Http\Requests\messages\sendMessageRequest;
use App\Models\chat\chatModel;
use App\Models\chat\messageModel;
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
        return view('index.messages.messagePage',compact('chat'));
    }
    public function sendMessage($userId,sendMessageRequest $request)
    {
        $request->validated();

        $data = [
            'chat_id' => $request->get('chat_id'),
            'sender_id' => $userId,
            'receiver_id' => $request->get('receiver_id'),
            'message' => $request->get('message'),
            'attachments' => $request->get('attachments') ?? null,
            'type' => $request->get('type'),
        ];

        $message = $this->messageServices->SendMessage($data);

        if($message){
            return response()->json([
                'success' => true,
                'message' => $message->message,
                'notif' => 'message send successfully',
            ]);
        }
        return response()->json([
            'success' => false,
            'notif' => 'Sorry, something went wrong'
        ]);
    }
    public function searchMessage($chatId , Request $request)
    {
        $word = $request->get('word');

        $searchedWord = $this->messageServices->SearchForMessage($chatId , $word);

        if($searchedWord->isNotEmpty()){
            return response()->json([
                'success' => true,
                'wordsFound' => $searchedWord,
            ]);
        }
        return response()->json([
            'success' => false,
            'wordsFound' => 'no word found'
        ]);
    }
}
