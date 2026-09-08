<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\OrdenServicio;
use App\Models\Producto;
use App\Models\Compra;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Procesa el login del usuario
     */
    public function login(Request $request)
    {
        // Validar los datos del formulario
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Intentar autenticar al usuario
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerar la sesión para prevenir session fixation
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        // Si la autenticación falla, lanzar error de validación
        throw ValidationException::withMessages([
            'username' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Muestra el dashboard principal con estadísticas
     */
    public function dashboard(Request $request)
    {
        // -------------------------------------------------------------
        // Órdenes por entregar
        // -------------------------------------------------------------
        $ordenesQuery = OrdenServicio::with(['cliente', 'vehiculo'])
            ->where('estado', 'FINALIZADO')
            ->orderBy('fecha_entrada', 'desc');
        $ordenesPendientesTotal = $ordenesQuery->count();
        $ordenesPendientes = $ordenesQuery->paginate(10, ['*'], 'ordenes_page')
            ->withQueryString()
            ->fragment('ordenes-pendientes');

        // -------------------------------------------------------------
        // Stock bajo o agotado (solo clasificación A y B)
        // -------------------------------------------------------------
        $marcasExcluidas = ['MISCELANEA', 'VAFRI', 'VAFRY'];
        $stockQuery = Producto::whereColumn('stock', '<=', 'stock_minimo')
            ->whereIn('clasificacion', ['A', 'B'])
            ->whereNotIn('marca', $marcasExcluidas)
            ->where(function ($q) {
                $q->whereNull('marca')->orWhereRaw('CHAR_LENGTH(marca) > 1');
            })
            ->orderByRaw("FIELD(clasificacion, 'A', 'B')")
            ->orderBy('id', 'desc');
        $stockBajoTotal = $stockQuery->count();
        $stockBajo = $stockQuery->paginate(10, ['*'], 'stock_page')
            ->withQueryString()
            ->fragment('stock-bajo');

        // -------------------------------------------------------------
        // Cuentas por pagar por vencer en 7 días (agrupadas por proveedor)
        // -------------------------------------------------------------
        $limiteVencer = Carbon::now()->addDays(7)->format('Y-m-d');
        $comprasPorVencer = Compra::with('proveedor')
            ->where('saldo_pendiente', '>', 0)
            ->where('estado_pago', '!=', 'PAGADA')
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', $limiteVencer)
            ->get();

        $cuentasPorPagarTotal = $comprasPorVencer->count();
        $cuentasPorPagarAgrupadas = $comprasPorVencer->groupBy('proveedor_id')
            ->map(function ($facturas) {
                return (object) [
                    'proveedor' => optional($facturas->first()->proveedor)->nombre ?? 'Desconocido',
                    'fecha_vencimiento' => $facturas->min('fecha_vencimiento'),
                    'total_facturas' => $facturas->count(),
                    'total_pagar' => $facturas->sum('saldo_pendiente'),
                ];
            })
            ->sortByDesc('fecha_vencimiento')
            ->values();
        $cuentasPorPagar = $this->paginarColeccion($cuentasPorPagarAgrupadas, $request, 'pagar_page')
            ->fragment('cuentas-por-pagar');

        // -------------------------------------------------------------
        // Cuentas por cobrar vencidas a 15 días (agrupadas por cliente)
        // -------------------------------------------------------------
        $hoy = Carbon::now()->format('Y-m-d');
        $ventasVencidas = Venta::with('cliente')
            ->where('saldo_pendiente', '>', 0)
            ->where('estado', '!=', 'CANCELADA')
            ->whereRaw('DATE_ADD(fecha, INTERVAL 15 DAY) < ?', [$hoy])
            ->get()
            ->map(function ($v) {
                return (object) [
                    'cliente' => optional($v->cliente)->nombre ?? 'Desconocido',
                    'cliente_id' => $v->cliente_id,
                    'fecha_vencimiento' => $v->fecha->copy()->addDays(15)->format('Y-m-d'),
                    'saldo_pendiente' => $v->saldo_pendiente,
                ];
            });
        $ordenesVencidas = OrdenServicio::with('cliente')
            ->where('estado', 'PENDIENTE DE PAGO')
            ->where('saldo_pendiente', '>', 0)
            ->whereRaw('DATE_ADD(fecha_entrada, INTERVAL 15 DAY) < ?', [$hoy])
            ->get()
            ->map(function ($o) {
                return (object) [
                    'cliente' => optional($o->cliente)->nombre ?? 'Desconocido',
                    'cliente_id' => $o->cliente_id,
                    'fecha_vencimiento' => Carbon::parse($o->fecha_entrada)->addDays(15)->format('Y-m-d'),
                    'saldo_pendiente' => $o->saldo_pendiente,
                ];
            });

        $documentosVencidos = $ventasVencidas->concat($ordenesVencidas);
        $cuentasPorCobrarTotal = $documentosVencidos->count();
        $cuentasPorCobrarAgrupadas = $documentosVencidos->groupBy('cliente_id')
            ->map(function ($documentos) {
                return (object) [
                    'cliente' => $documentos->first()->cliente,
                    'fecha_vencimiento' => $documentos->min('fecha_vencimiento'),
                    'total_documentos' => $documentos->count(),
                    'total_saldo' => $documentos->sum('saldo_pendiente'),
                ];
            })
            ->sortByDesc('fecha_vencimiento')
            ->values();
        $cuentasPorCobrar = $this->paginarColeccion($cuentasPorCobrarAgrupadas, $request, 'cobrar_page')
            ->fragment('cuentas-por-cobrar');

        return view('dashboard', compact(
            'ordenesPendientes', 'ordenesPendientesTotal',
            'stockBajo', 'stockBajoTotal',
            'cuentasPorPagar', 'cuentasPorPagarTotal',
            'cuentasPorCobrar', 'cuentasPorCobrarTotal'
        ));
    }

    /**
     * Pagina manualmente una colección ya armada en PHP (agregados que no
     * vienen de una sola consulta), preservando el query string de las
     * demás tablas paginadas en la misma vista.
     */
    private function paginarColeccion($coleccion, Request $request, string $pageName, int $perPage = 10)
    {
        $page = (int) $request->input($pageName, 1);

        return new LengthAwarePaginator(
            $coleccion->forPage($page, $perPage)->values(),
            $coleccion->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => $pageName,
                'query' => $request->except($pageName),
            ]
        );
    }
}
