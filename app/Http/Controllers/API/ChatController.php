<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class ChatController extends Controller
{

    // public function fetchMessages()
    // {
    //     $userId = Auth::id(); // ID de l'utilisateur connecté

    //     // Récupérer uniquement les messages envoyés par l'utilisateur connecté
    //     $messages = Message::where('sender_id', $userId)
    //         ->with('sender:id,name') // Charger uniquement le nom de l'expéditeur
    //         ->latest() // Trier par ordre décroissant
    //         ->get();

    //     return response()->json($messages);
    // }

    public function getUsers()
    {
        return response()->json(
            User::where('id', '!=', Auth::id())
                ->select(['id', 'name'])
                ->get()
        );
    }

    public function fetchMessages()
    {
        return Message::where(function($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('receiver_id', Auth::id());
            })
            ->with(['sender:id,name', 'receiver:id,name'])
            ->latest()
            ->get();
    }

    // public function fetchMessages($receiverId)
    // {
    //     $messages = Message::where('receiver_id', $receiverId)
    //         ->with('sender')
    //         ->get();

    //     return $messages;
    // }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'receiver_id' => 'required|exists:users,id'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json([
            'message' => $message->load('sender:id,name')
        ]);
    }

    public function fetchAllMessages()
{
    if (Auth::user()->role !== 'admin') {
        return response()->json(['error' => 'Accès interdit'], 403);
    }

    $messages = Message::with('sender', 'receiver')
        ->orderBy('created_at', 'asc')
        ->get();

    return response()->json($messages);
}

    
}
