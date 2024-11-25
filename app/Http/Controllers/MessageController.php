<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        try {
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'content' => 'required|string',
            ]);

            $currentUserId = Auth::id();
            $receiverId = $request->receiver_id;

            // Normalize user IDs
            $user1Id = min($currentUserId, $receiverId);
            $user2Id = max($currentUserId, $receiverId);

            // Find or create a conversation
            $conversation = Conversation::firstOrCreate(
                [
                    'user1_id' => $user1Id,
                    'user2_id' => $user2Id,
                ]
            );

            // Create the message
            $message = Message::create([
                'sender_id' => $currentUserId,
                'receiver_id' => $receiverId,
                'content' => $request->content,
                'conversation_id' => $conversation->id,
            ]);

            return response()->json(['message' => $message], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getMessages($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)->get();
        return response()->json(['messages' => $messages]);
    }

    public function getConversations()
    {
        try {
            $conversations = Conversation::where('user1_id', Auth::id())
                ->orWhere('user2_id', Auth::id())
                ->with(['messages' => function ($query) {
                    $query->latest()->take(1); // Get the latest message for each conversation
                }])
                ->get();

            return response()->json(['conversations' => $conversations]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $currentUserId = Auth::id();
        $userIdToCheck = $request->user_id;

        // Normalize user IDs
        $user1Id = min($currentUserId, $userIdToCheck);
        $user2Id = max($currentUserId, $userIdToCheck);

        $conversation = Conversation::where('user1_id', $user1Id)
            ->where('user2_id', $user2Id)
            ->first();

        if ($conversation) {
            return response()->json(['id' => $conversation->id], Response::HTTP_OK);
        }

        return response()->json(['message' => 'No conversation found'], Response::HTTP_NOT_FOUND);
    }

    public function createConversation(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $currentUserId = Auth::id();
        $receiverId = $request->receiver_id;

        // Normalize user IDs
        $user1Id = min($currentUserId, $receiverId);
        $user2Id = max($currentUserId, $receiverId);

        $conversation = Conversation::create([
            'user1_id' => $user1Id,
            'user2_id' => $user2Id,
        ]);

        return response()->json(['id' => $conversation->id], Response::HTTP_CREATED);
    }
}
