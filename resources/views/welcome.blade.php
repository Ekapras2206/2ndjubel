<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to My Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: sans-serif;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('https://images.unsplash.com/photo-1515378791036-0648a3ef77b2') no-repeat center center;
            background-size: cover;
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }
    </style>

</head>

<body class="antialiased">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">JUBEL</a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    @if (Route::has('login'))
                        @auth
                            <a class="nav-link" href="{{ url('/home') }}">Home</a>
                        @else
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                            @if (Route::has('register'))
                                <a class="nav-link" href="{{ route('register') }}">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container">
            <h1 class="display-1 fw-bold mb-4">Welcome to JUBEL</h1>
            <p class="lead mb-5">Platform jual beli barang bekas.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg me-3">Explore Products</a>
            @guest
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">Get Started</a>
            @endguest
        </div>
    </div>

    <div class="container my-5">
        <div class="row text-center">
            <div class="col-md-4">
                <img src="https://cdn-icons-png.flaticon.com/512/1828/1828673.png" width="64" alt="Easy" class="mb-3">
                <h3>Easy to Use</h3>
                <p>Intuitive interface for seamless navigation.</p>
            </div>
            <div class="col-md-4">
                <img src="https://cdn-icons-png.flaticon.com/512/2910/2910768.png" width="64" alt="Secure" class="mb-3">
                <h3>Secure Transactions</h3>
                <p>Your data and transactions are safe with us.</p>
            </div>
            <div class="col-md-4">
                <img src="https://cdn-icons-png.flaticon.com/512/3062/3062634.png" width="64" alt="Community"
                    class="mb-3">
                <h3>Great Community</h3>
                <p>Join a thriving community of buyers and sellers.</p>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4">
        <p class="mb-0">&copy; {{ date('Y') }} My Application. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>