@extends('layouts.app')

@section('title', 'Chat Detail') {{-- Menambahkan judul halaman --}}

@section('content')
<div class="container py-4"> {{-- Menambahkan container untuk layout yang lebih baik --}}
    <h1 class="mb-3">Chat with
        {{-- Ini akan menampilkan nama pengguna lain dalam chat --}}
        @if(Auth::check()) {{-- Pastikan pengguna login sebelum mencoba Auth::id() --}}
            @if(Auth::id() === $chat->buyer_id)
                {{ $chat->seller->name ?? 'Penjual Tidak Ditemukan' }}
            @else
                {{ $chat->buyer->name ?? 'Pembeli Tidak Ditemukan' }}
            @endif
        @else
            Pengguna Tidak Dikenal
        @endif
    </h1>

    <div class="card mb-4">
        <div class="card-body chat-messages" style="height: 400px; overflow-y: scroll; display: flex; flex-direction: column-reverse;">
            @forelse($chat->messages->sortByDesc('created_at') as $message) {{-- Sort messages to show latest at bottom --}}
                <div class="d-flex {{ $message->isSentByCurrentUser() ? 'justify-content-end' : 'justify-content-start' }} mb-2"> {{-- Menggunakan helper isSentByCurrentUser() --}}
                    <div class="card {{ $message->isSentByCurrentUser() ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 75%;"> {{-- Menggunakan helper isSentByCurrentUser() --}}
                        <div class="card-body p-2">
                            <small class="fw-bold">{{ $message->sender->name ?? 'Pengirim Tidak Ditemukan' }}:</small> {{-- Menggunakan relasi sender() --}}
                            <p class="mb-0">{{ $message->message }}</p> {{-- <-- PERBAIKAN DI SINI: $message->message --}}
                            <small class="{{ $message->isSentByCurrentUser() ? 'text-white-50' : 'text-muted' }} float-end">{{ $message->created_at->format('H:i') }}</small> {{-- Menyesuaikan warna teks timestamp --}}
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center">
                    Belum ada pesan. Mulai percakapan!
                </div>
            @endforelse
        </div>
    </div>

    <form action="{{ route('messages.store', $chat) }}" method="POST" class="mt-3">
        @csrf
        <div class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Ketik pesan Anda..." required>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </div>
    </form>

    <div class="mt-3">
        <a href="{{ route('chats.index') }}" class="btn btn-secondary">Kembali ke Sesi Chat</a>
    </div>
</div>
@endsection
