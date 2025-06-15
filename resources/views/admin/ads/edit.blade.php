@extends('admin')

@section('page_title', 'Edit Ad')

@section('content')
<div class="card p-4">
    <form action="{{ route('admin.ads.update', $ad) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">Ad Title:</label>
            <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $ad->title) }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ad Image:</label>
            <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror">
            @if($ad->image)
                <small class="form-text text-muted d-block mt-2">Current Image: <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="img-thumbnail" style="max-width: 100px;"></small>
            @endif
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="link" class="form-label">Ad Link:</label>
            <input type="url" id="link" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $ad->link) }}" required>
            @error('link')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status:</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                <option value="active" {{ (old('status', $ad->status) == 'active') ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ (old('status', $ad->status) == 'inactive') ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Ad</button>
    </form>
</div>
@endsection