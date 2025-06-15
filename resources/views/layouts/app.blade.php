<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    {{-- Font Awesome (jika diperlukan untuk ikon) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    {{-- Optional: Your custom CSS --}}
    {{--
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
    @stack('styles') {{-- Untuk CSS spesifik halaman --}}
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">JUBEL</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/profile">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/chats">Chats</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/transactions">Transactions</a>
                    </li>
                    {{-- Pindah link keranjang di sini agar masuk dalam navbar --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.show') }}">
                            <i class="fas fa-shopping-cart"></i> Keranjang
                            {{-- Opsional: Menampilkan jumlah item di keranjang --}}
                            @php
                                use Illuminate\Support\Facades\Session; // Import Session di sini jika belum di-import di layout
                                $cartCount = count(Session::get('cart', []));
                            @endphp
                            @if($cartCount > 0)
                                <span class="badge bg-danger ms-1">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    {{-- Form Logout --}}
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline"> {{-- Gunakan d-inline untuk menjaga styling dropdown-item --}}
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    {{-- Main content section --}}
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-light text-center py-3 mt-4">
        <p class="mb-0">&copy; {{ date('Y') }} My Application. All rights reserved.</p>
    </footer>

    {{-- Toast Container - Pastikan di dalam body, sebelum script --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>

        <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>

        <div id="infoToast" class="toast align-items-center text-bg-info border-0" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body text-white">
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    {{-- Bootstrap JavaScript Bundle - Pastikan di dalam body, sebelum script kustom --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    {{-- Custom JavaScript untuk Toast - Pastikan di dalam body, setelah Bootstrap JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function showToast(type, message) {
                let toastElement;
                if (type === 'success') {
                    toastElement = document.getElementById('successToast');
                } else if (type === 'error') {
                    toastElement = document.getElementById('errorToast');
                } else if (type === 'info') {
                    toastElement = document.getElementById('infoToast');
                }

                if (toastElement && message) {
                    toastElement.querySelector('.toast-body').textContent = message;
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();
                }
            }

            const successMessage = "{{ session('success') }}";
            if (successMessage.length > 0) {
                showToast('success', successMessage);
            }

            const errorMessage = "{{ session('error') }}";
            if (errorMessage.length > 0) {
                showToast('error', errorMessage);
            }

            const infoMessage = "{{ session('info') }}";
            if (infoMessage.length > 0) {
                showToast('info', infoMessage);
            }
        });
    </script>

    @stack('scripts') {{-- Untuk JavaScript spesifik halaman --}}
</body>

</html>
