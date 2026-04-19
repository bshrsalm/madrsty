<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;

class ChatController extends Controller
{
    // إرسال رسالة
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message
        ],201);
    }

    // جلب محادثة بين مستخدمين
    public function getConversation($userId)
    {
        $authId = auth()->id();

        $messages = Message::where(function($q) use ($authId,$userId){
            $q->where('sender_id',$authId)
              ->where('receiver_id',$userId);
        })
        ->orWhere(function($q) use ($authId,$userId){
            $q->where('sender_id',$userId)
              ->where('receiver_id',$authId);
        })
        ->orderBy('created_at','asc')
        ->get();

        return response()->json($messages);
    }

    // الأدمن يشوف كل المحادثات
    public function allChats()
    {
        $messages = Message::with(['sender','receiver'])
            ->latest()
            ->get();

        return response()->json($messages);
    }
}
