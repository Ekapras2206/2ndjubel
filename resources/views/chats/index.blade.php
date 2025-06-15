{{-- resources/views/chats/index.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container py-4"> {{-- Tambahkan container jika belum ada di layout --}}
    <h1 class="mb-4">Chat Sessions</h1>

    <div class="list-group">
        @forelse($chats as $chat)
            <a href="{{ route('chats.show', $chat) }}" class="list-group-item list-group-item-action d-flex flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">Session ID: {{ $chat->id }}</h5>
                    <small class="text-muted">{{ $chat->updated_at->diffForHumans() }}</small> {{-- Menampilkan waktu update chat --}}
                </div>
                {{-- PERBAIKAN DI SINI: Akses buyer dan seller secara langsung --}}
                <small class="text-muted">
                    Participants:
                    @php
                        $names = [];
                        if ($chat->buyer) {
                            $names[] = $chat->buyer->name;
                        }
                        if ($chat->seller) {
                            $names[] = $chat->seller->name;
                        }
                        // Hapus duplikat jika buyer dan seller adalah orang yang sama (kasus jarang untuk chat)
                        $names = array_unique($names);
                    @endphp
                    {{ implode(', ', $names) }}
                </small>
                {{-- Anda bisa menambahkan info produk di sini jika diperlukan --}}
                @if($chat->product)
                    <small class="text-muted">Product: {{ $chat->product->title }}</small>
                @endif
            </a>
        @empty
            <div class="alert alert-info text-center">
                Anda belum memiliki sesi chat.
            </div>
        @endforelse
    </div>
</div>
@endsection