<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Absensi QR')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @yield('css')
</head>
<body>
    <div class="main-wrapper">
        <header class="app-header">
            <div class="header-content">
                <div class="logo">SISTEM ABSENSI QR</div>
                <nav class="main-nav">
                    @auth
                        @if(Auth::user()->role === 'dosen')
                            <a href="{{ route('dosen.dashboard') }}">Dashboard Dosen</a>
                        @elseif(Auth::user()->role === 'mahasiswa')
                            <a href="{{ route('mahasiswa.dashboard') }}">Dashboard Mahasiswa</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="logout-btn">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="app-content">
            @yield('content')
        </main>

        <footer class="app-footer">
            <p>&copy; {{ date('Y') }} Sistem Absensi QR. All rights reserved.</p>
        </footer>
    </div>

    {{-- <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('js')

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}'
            });
        </script>
    @endif
</body>
</html>