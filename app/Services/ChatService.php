<?php

namespace App\Services;

use App\Repositories\ChatRepository;
use App\Repositories\ProductRepository;
use App\Models\User;
use App\Models\Product;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    protected $chatRepository;
    protected $productRepository;

    public function __construct(ChatRepository $chatRepository, ProductRepository $productRepository)
    {
        $this->chatRepository = $chatRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Start or get an existing chat session for a product.
     *
     * @param \App\Models\User $buyer
     * @param int $productId
     * @return \App\Models\Chat|null
     */
    public function getOrCreateChat(User $buyer, int $productId): ?Chat
    {
        $product = $this->productRepository->findById($productId);

        if (!$product || $product->user_id === $buyer->id) {
            // Cannot chat about non-existent product or chat with self
            return null;
        }

        return $this->chatRepository->findOrCreateChat($buyer, $product);
    }

    /**
     * Get all chat sessions for a user.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserChats(User $user): Collection
    {
        return $this->chatRepository->getUserChats($user);
    }

    /**
     * Send a message in a chat session.
     *
     * @param int $chatId
     * @param \App\Models\User $sender
     * @param string $messageContent
     * @return \App\Models\ChatMessage|null
     */
    public function sendMessage(int $chatId, User $sender, string $messageContent): ?ChatMessage
    {
        $chat = $this->chatRepository->findChatById($chatId);

        // Ensure sender is part of the chat
        if (!$chat || ($chat->buyer_id !== $sender->id && $chat->seller_id !== $sender->id)) {
            return null;
        }

        $message = $this->chatRepository->addMessage($chat, $sender, $messageContent);

        // Anda bisa menambahkan logika notifikasi real-time di sini (misal: Laravel Echo)
        return $message;
    }

    /**
     * Get a specific chat session and mark messages as read.
     *
     * @param int $chatId
     * @param \App\Models\User $user
     * @return \App\Models\Chat|null
     */
    public function getChatAndMarkRead(int $chatId, User $user): ?Chat
    {
        $chat = $this->chatRepository->findChatById($chatId);

        // Ensure user is part of the chat
        if (!$chat || ($chat->buyer_id !== $user->id && $chat->seller_id !== $user->id)) {
            return null;
        }

        $this->chatRepository->markMessagesAsRead($chat, $user);

        return $chat;
    }
}