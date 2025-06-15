@extends('admin')

@section('page_title', 'User Management')

@section('content')
<a href="{{ route('admin.users.create') }}" class="btn btn-success mb-3">Add New User</a> {{-- Assuming you might have a create user page --}}

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge {{ $user->isAdmin() ? 'bg-primary' : 'bg-secondary' }}">{{ $user->isAdmin() ? 'Admin' : 'User' }}</span></td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-info btn-sm me-1">Edit</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection