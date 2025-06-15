<?php

namespace App\Repositories;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ChatRepository
{
    /**
     * Find or create a chat session between a buyer and a seller for a specific product.
     *
     * @param \App\Models\User $buyer
     * @param \App\Models\Product $product
     * @return \App\Models\Chat
     */
    public function findOrCreateChat(User $buyer, Product $product): Chat
    {
        return Chat::firstOrCreate(
            [
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ],
            [
                'seller_id' => $product->user_id,
            ]
        );
    }

    /**
     * Get all chats for a specific user (as buyer or seller).
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserChats(User $user): Collection
    {
        return Chat::where('buyer_id', $user->id)
                   ->orWhere('seller_id', $user->id)
                   ->with(['product', 'seller', 'buyer']) // Eager load relationships
                   ->latest() // Order by latest chat activity
                   ->get();
    }

    /**
     * Get a chat session by ID.
     *
     * @param int $chatId
     * @return \App\Models\Chat|null
     */
    public function findChatById(int $chatId): ?Chat
    {
        return Chat::with(['product', 'seller', 'buyer', 'messages'])->find($chatId);
    }

    /**
     * Add a message to a chat session.
     *
     * @param \App\Models\Chat $chat
     * @param \App\Models\User $sender
     * @param string $messageContent
     * @return \App\Models\ChatMessage
     */
    public function addMessage(Chat $chat, User $sender, string $messageContent): ChatMessage
    {
        return $chat->messages()->create([
            'sender_id' => $sender->id,
            'message' => $messageContent,
        ]);
    }

    /**
     * Mark messages in a chat as read for a specific user.
     *
     * @param \App\Models\Chat $chat
     * @param \App\Models\User $user
     * @return int The number of messages marked as read.
     */
    public function markMessagesAsRead(Chat $chat, User $user): int
    {
        // Mark messages as read that were sent by the other user and are unread
        return $chat->messages()
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
    }
}