@extends('admin')

@section('page_title', 'Ad Management')

@section('content')
<a href="{{ route('admin.ads.create') }}" class="btn btn-success mb-3">Create New Ad</a>

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th scope="col">Image</th>
                <th scope="col">Link</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ads as $ad)
                <tr>
                    <td>{{ $ad->id }}</td>
                    <td>{{ $ad->title }}</td>
                    <td>
                        @if($ad->image)
                            <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="img-thumbnail" style="max-width: 80px;">
                        @else
                            No Image
                        @endif
                    </td>
                    <td><a href="{{ $ad->link }}" target="_blank">{{ Str::limit($ad->link, 30) }}</a></td>
                    <td><span class="badge bg-{{ $ad->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($ad->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.ads.edit', $ad) }}" class="btn btn-info btn-sm me-1">Edit</a>
                        <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this ad?');">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No ads found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection