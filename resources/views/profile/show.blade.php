@extends('layouts.app')

@section('content')
<h1 class="mb-4">User Profile</h1>

<div class="card p-4">
    @if(Auth::check())
        <div class="mb-3">
            <strong>Name:</strong> {{ Auth::user()->name }}
        </div>
        <div class="mb-3">
            <strong>Email:</strong> {{ Auth::user()->email }}
        </div>
        {{-- Add other profile details --}}
        <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
    @else
        <div class="alert alert-warning" role="alert">
            Please log in to view your profile.
        </div>
    @endif
</div>
@endsection