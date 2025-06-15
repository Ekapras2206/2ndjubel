<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $currentUser = Auth::user();
        // Query ini sudah benar, memuat relasi latestMessage
        $chats = Chat::where('buyer_id', $currentUser->id)
                     ->orWhere('seller_id', $currentUser->id)
                     ->with(['buyer', 'seller', 'product', 'latestMessage']) // Memuat pesan terakhir
                     ->latest('updated_at') // Urutkan daftar chat berdasarkan pesan/update terbaru
                     ->get();

        return view('chats.index', compact('chats'));
    }

    public function show(Chat $chat)
    {
        if (Auth::id() !== $chat->buyer_id && Auth::id() !== $chat->seller_id) {
            abort(403, 'AKSES DITOLAK.');
        }

        // PERBAIKAN: Menggunakan relasi 'sender' bukan 'user'
        $messages = $chat->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        return view('chats.show', compact('chat', 'messages'));
    }

    public function storeMessage(Request $request, Chat $chat)
    {
        if (Auth::id() !== $chat->buyer_id && Auth::id() !== $chat->seller_id) {
            return redirect()->back()->with('error', 'Anda tidak berhak mengirim pesan di chat ini.');
        }

        // PERBAIKAN: Validasi input 'message' bukan 'body'
        $request->validate(['message' => 'required|string|max:2000']);

        // PERBAIKAN: Membuat pesan dengan field 'sender_id' dan 'message'
        $chat->messages()->create([
            'sender_id' => Auth::id(),
            'message'   => $request->message,
        ]);

        $chat->touch();

        return redirect()->route('chats.show', $chat->id);
    }

    // ... (metode startChatWithSeller tetap sama seperti sebelumnya) ...
    public function startChatWithSeller(Request $request, User $seller, Product $product)
    {
        $buyer = Auth::user();
        if ($buyer->id === $seller->id) {
            return redirect()->route('products.show', $product->id)->with('error', 'Anda tidak dapat memulai chat dengan diri sendiri.');
        }
        $chat = Chat::firstOrCreate(
            ['buyer_id' => $buyer->id, 'seller_id' => $seller->id, 'product_id' => $product->id]
        );
        if ($chat->wasRecentlyCreated) {
            $chat->messages()->create([
                'sender_id' => $buyer->id,
                'message' => "Halo, saya tertarik dengan produk Anda: \"{$product->title}\"",
            ]);
        }
        return redirect()->route('chats.show', $chat->id);
    }
}
