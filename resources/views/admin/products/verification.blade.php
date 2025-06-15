@extends('admin')

@section('page_title', 'Product Verification')

@section('content')
<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Product Name</th>
                <th scope="col">Seller</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productsToVerify as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td><a href="{{ route('products.show', $product) }}" target="_blank" class="text-decoration-none">{{ $product->name }}</a></td>
                    <td>{{ $product->user->name ?? 'N/A' }}</td>
                    <td><span class="badge bg-{{ $product->status == 'pending' ? 'warning' : ($product->status == 'approved' ? 'success' : 'danger') }}">{{ ucfirst($product->status) }}</span></td>
                    <td>
                        <form action="{{ route('admin.products.approve', $product) }}" method="POST" class="d-inline-block me-1">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                        </form>
                        <form action="{{ route('admin.products.reject', $product) }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">Reject</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No products pending verification.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection