<?php

namespace App\Services\messagesServices;


use App\Http\Requests\chat\createChatRequest;
use App\Models\chat\chatMembersModel;
use App\Models\chat\chatModel;
use App\Models\chat\messageModel;
use Illuminate\Support\Facades\Auth;

class messageServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }

    public function ShowOrCreateChat($id)
    {
        $currentUid = Auth::id();

        $chat = $this->ChatExists($currentUid, $id);

        if (!$chat) {
            $chat = chatModel::create([
                'type' => 'private'
            ]);
            $chat->members()->attach([
                $currentUid,
                $id
            ]);
        }

        return $chat;
    }
    private function ChatExists($senderUid , $receiverUid)
    {
        return chatModel::where('type', 'private')
            ->whereHas('members', function ($q) use ($senderUid) {$q->where('user_id', $senderUid);})
            ->whereHas('members', function ($q) use ($receiverUid) {$q->where('user_id', $receiverUid);})->with(['members','messages'])->first();
    }
    public function SendMessage(array $data)
    {
        return  messageModel::create([
            'chat_id' => $data['chat_id'],
            'sender_id' => $data['sender_id'],
            'receiver_id' => $data['receiver_id'],
            'message' => $data['message'],
            'attachments' => $data['attachments'],
            'type' => $data['type'],
        ]);

    }
}
