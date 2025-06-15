@extends('layouts.app') {{-- Default Laravel auth layout is often 'layouts.app' --}}

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="mb-0">{{ __('You are logged in!') }}</p>
                    <hr>
                    <a href="{{ url('/products') }}" class="btn btn-primary">Go to Products</a>
                    <a href="{{ url('/profile') }}" class="btn btn-secondary">View Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection