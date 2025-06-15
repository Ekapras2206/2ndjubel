<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    {{-- Optional: Your custom Admin CSS --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/admin.css') }}"> --}}
    <style>
        body { display: flex; min-height: 100vh; }
        #sidebar { width: 250px; flex-shrink: 0; background-color: #343a40; color: white; padding-top: 1rem; }
        #sidebar .nav-link { color: rgba(255, 255, 255, 0.75); }
        #sidebar .nav-link.active, #sidebar .nav-link:hover { color: white; background-color: rgba(255, 255, 255, 0.1); }
        #main-content { flex-grow: 1; padding: 1.5rem; }
        .sidebar-heading { padding: 0.875rem 1.25rem; font-size: 1.2rem; }
    </style>
</head>

<body>
    <div id="sidebar" class="d-flex flex-column p-3">
        <h4 class="sidebar-heading">Admin Panel</h4>
        <nav class="nav flex-column">
            <a class="nav-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}" href="/admin">Dashboard</a>
            <a class="nav-link {{ Request::routeIs('admin.users.*') ? 'active' : '' }}" href="/admin/users">Users Management</a>
            <a class="nav-link {{ Request::routeIs('admin.products.verification') ? 'active' : '' }}" href="/admin/products/verification">Product Verification</a>
            <a class="nav-link {{ Request::routeIs('admin.transactions.report') ? 'active' : '' }}" href="/admin/transactions/report">Transaction Reports</a>
            <a class="nav-link {{ Request::routeIs('admin.ads.*') ? 'active' : '' }}" href="/admin/ads">Ad Management</a>
            <a class="nav-link {{ Request::routeIs('admin.categories.*') ? 'active' : '' }}" href="/admin/categories">Category Management</a>
            <hr class="text-white-50">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm w-100">Logout</button>
            </form>
        </nav>
    </div>

    <main id="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">@yield('page_title', 'Admin Dashboard')</h1>
        </div>

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    {{-- Optional: Your custom Admin JS --}}
    {{-- <script src="{{ asset('js/admin.js') }}"></script> --}}
</body>
</html>