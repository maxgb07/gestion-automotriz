<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Sistema de Gestión')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .swal2-popup {
            font-family: ui-sans-serif, system-ui, sans-serif !important;
            border-radius: 1.5rem !important;
        }

        /* Forzar visualización en mayúsculas para inputs (excepto email) y textareas */
        input:not([type="email"]), textarea {
            text-transform: uppercase;
        }

        /* Mantener placeholders en estilo normal si se prefiere, o también en mayúsculas */
        input::placeholder, textarea::placeholder {
            text-transform: none;
        }
    </style>

    <script>
        document.addEventListener('input', function (event) {
            const typesToSkip = ['email', 'number', 'password', 'date', 'datetime-local', 'time', 'file', 'range', 'color'];
            if ((event.target.tagName === 'INPUT' && !typesToSkip.includes(event.target.type)) || event.target.tagName === 'TEXTAREA') {
                const start = event.target.selectionStart;
                const end = event.target.selectionEnd;
                event.target.value = event.target.value.toUpperCase();
                if (start !== null && end !== null) {
                    event.target.setSelectionRange(start, end);
                }
            }
        }, true);
    </script>

    <style>
        /* Custom Breakpoint Logic for 1400px (Covers iPad Pro Landscape) */
        @media (min-width: 1400px) {
            #sidebar {
                display: block !important;
                left: 1rem; /* equivalent to left-4 */
                top: 6rem;  /* equivalent to top-24 */
                bottom: 1rem; /* equivalent to bottom-4 */
            }
            #main-content {
                margin-left: 18rem; /* equivalent to ml-72 */
            }
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased text-white">
    @auth
        <!-- Global Background wrapper -->
        <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
            <!-- Header -->
            @include('partials.navbar')

            <!-- Layout Shell -->
            <div class="w-full flex pt-16">

                <!-- Sidebar (Only if not dashboard) -->
                @if(!Route::is('dashboard'))
                    @include('partials.sidebar')
                    <main id="main-content" class="flex-grow transition-all duration-300 w-full">
                        <div class="p-4 sm:p-6 lg:p-8">
                            @yield('content')
                        </div>
                    </main>
                @else
                    <main class="w-full flex-grow min-h-[calc(100vh-64px)] flex flex-col justify-center items-center">
                        @yield('content')
                    </main>
                @endif
            </div>
        </div>
    @else
        @yield('content')
    @endauth

    @if (session('success'))
        <script>
            Swal.fire({
                title: '¡Éxito!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#3b82f6',
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Error',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#ef4444',
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                title: 'Atención',
                html: "{!! implode('<br>', $errors->all()) !!}",
                icon: 'warning',
                confirmButtonColor: '#f59e0b',
            });
        </script>
    @endif
    @stack('scripts')
</body>
</html>
