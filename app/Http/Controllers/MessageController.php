<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $conversations = Conversation::with(['buyer', 'seller', 'lastMessage', 'product'])
            ->where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->orderByDesc('last_message_at')
            ->get();

        return view('messages.index', compact('conversations', 'user'));
    }

    public function show(Conversation $conversation)
    {
        $user = auth()->user();
        if ($conversation->buyer_id !== $user->id && $conversation->seller_id !== $user->id) abort(403);

        $conversation->load(['buyer', 'seller', 'messages.sender', 'product']);
        $conversation->messages()->where('sender_id', '!=', $user->id)->update(['is_read' => true]);

        $conversations = Conversation::with(['buyer', 'seller', 'lastMessage'])
            ->where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->orderByDesc('last_message_at')
            ->get();

        return view('messages.show', compact('conversation', 'conversations', 'user'));
    }

    public function startConversation(Request $request, Product $product)
    {
        $buyer = auth()->user();
        if ($buyer->id === $product->user_id) return back()->with('error', 'You cannot message yourself.');

        $conversation = Conversation::firstOrCreate([
            'buyer_id'  => $buyer->id,
            'seller_id' => $product->user_id,
            'product_id' => $product->id,
        ]);

        return redirect()->route('messages.show', $conversation);
    }

    public function send(Request $request, Conversation $conversation)
    {
        $user = auth()->user();
        if ($conversation->buyer_id !== $user->id && $conversation->seller_id !== $user->id) abort(403);

        $request->validate(['body' => 'required|string|max:2000']);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'body'            => $request->body,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back();
    }
}
