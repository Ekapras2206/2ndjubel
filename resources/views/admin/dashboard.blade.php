@extends('admin')

@section('page_title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <p class="card-text h1">{{ $totalUsers ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Pending Product Verifications</h5>
                <p class="card-text h1">{{ $pendingProducts ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Total Revenue</h5>
                <p class="card-text h1">${{ number_format($totalRevenue ?? 0, 2) }}</p>
            </div>
        </div>
    </div>
</div>

{{-- You can add charts, recent activities, etc. here --}}
<div class="card mt-4">
    <div class="card-header">Recent Activities</div>
    <div class="card-body">
        <p>No recent activities to display.</p>
    </div>
</div>
@endsection