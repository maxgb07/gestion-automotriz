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

        /* Cursor pointer global para elementos interactivos */
        button, a, input[type="submit"], input[type="button"], select, [role="button"] {
            cursor: pointer !important;
        }

        /* Mantener placeholders en estilo normal si se prefiere, o también en mayúsculas */
        input::placeholder, textarea::placeholder {
            text-transform: none;
        }
    </style>

    <script>
        // Autocapitalize inputs
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

        // Prevención global de dobles envíos y feedback visual
        document.addEventListener('submit', function(e) {
            const form = e.target;
            
            // Si el formulario ya se está enviando, bloqueamos el evento
            if (form.hasAttribute('data-submitting')) {
                e.preventDefault();
                return;
            }
            
            // Marcar como en proceso
            form.setAttribute('data-submitting', 'true');
            
            // Encontrar el botón de submit (incluso si está fuera del form pero vinculado por el atributo form)
            const submitButtons = document.querySelectorAll(`button[type="submit"][form="${form.id}"], input[type="submit"][form="${form.id}"]`);
            const internalSubmitButtons = form.querySelectorAll('button[type="submit"]:not([form]), input[type="submit"]:not([form])');
            
            const allButtons = [...submitButtons, ...internalSubmitButtons];
            
            allButtons.forEach(btn => {
                // Guardar el texto/estado original por si se necesita restaurar (ej. error de validación AJAX)
                btn.setAttribute('data-original-html', btn.innerHTML);
                
                // Fijar el ancho para que el botón no brinque visualmente
                btn.style.width = btn.offsetWidth + 'px';
                
                // Cambiar el contenido visual
                if (btn.tagName === 'BUTTON') {
                    btn.innerHTML = `<svg class="w-5 h-5 inline-block mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Procesando...`;
                } else {
                    btn.value = 'Procesando...';
                }
                
                // Deshabilitar después del thread actual para no interferir con el evento Submit nativo
                setTimeout(() => {
                    btn.disabled = true;
                    btn.classList.add('opacity-70', 'cursor-not-allowed');
                }, 0);
            });
        });
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
    {{-- @include('partials.eom-alert') --}}
    {{-- @include('partials.prev-month-alert') --}}
    {{-- @include('partials.finalizado-alert') --}}
    @stack('scripts')
</body>
</html>
