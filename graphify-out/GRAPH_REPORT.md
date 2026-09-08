# Graph Report - gestion-automotriz  (2026-09-07)

## Corpus Check
- 198 files · ~112,833 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 715 nodes · 1185 edges · 147 communities (15 shown, 20 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 7 edges (avg confidence: 0.82)
- Token cost: 60,000 input · 6,600 output

## Community Hubs (Navigation)
- Payment & Auth Controllers
- Service Orders & Reports
- Purchases & Suppliers
- Composer Dependencies
- Client Management
- Service Order Detail Ops
- Migration & Deployment Notes
- Frontend Build Dependencies
- User & Client Seeding
- Auditable Boot & Env Config
- Core DB Migrations (Users/Orders)
- Cache & Audit Column Migrations
- Jobs & Sales Migrations
- App Service Provider
- Feature Test Bootstrap
- Layout Partials (Blade)
- App Bootstrap & Entrypoint
- Logging Configuration
- Inventory Classification Command
- Unit Test Bootstrap
- Console
- Intrucciones
- View: Crear.Blade
- View: Editar.Blade
- View: Pdf.Blade
- View: Pdf Media Carta.Blade
- View: Pdf.Blade
- View: Pdf Media Carta.Blade
- Intrucciones
- Intrucciones
- Intrucciones
- Intrucciones
- Intrucciones
- Intrucciones
- Robots

## God Nodes (most connected - your core abstractions)
1. `Producto` - 47 edges
2. `OrdenServicio` - 46 edges
3. `Cliente` - 34 edges
4. `Proveedor` - 33 edges
5. `Venta` - 33 edges
6. `Auditable` - 33 edges
7. `OrdenServicioController` - 21 edges
8. `Compra` - 21 edges
9. `ProductoController` - 19 edges
10. `Servicio` - 19 edges

## Surprising Connections (you probably didn't know these)
- `Route/Controller Method Naming Consistency` --references--> `OrdenServicioController`  [EXTRACTED]
  tasks/lessons.md → app/Http/Controllers/OrdenServicioController.php
- `Laravel Cache Clear Commands` --conceptually_related_to--> `Laravel Framework`  [INFERRED]
  instrucciones_migracion.txt → README.md
- `Manual Storage Symlink Method` --conceptually_related_to--> `Laravel Framework`  [INFERRED]
  instrucciones_migracion.txt → README.md
- `Eloquent Dynamic Attribute Persistence Bug` --conceptually_related_to--> `Eloquent ORM`  [INFERRED]
  tasks/lessons.md → README.md
- `Self-Improvement Feedback Loop` --references--> `Lessons Learned Log`  [EXTRACTED]
  intrucciones.md → tasks/lessons.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Laravel Core Features** — readme_routing, readme_di_container, readme_session_storage, readme_cache_storage, readme_eloquent_orm, readme_schema_migrations, readme_queues, readme_broadcasting [EXTRACTED 1.00]
- **SSH Cross-Domain Migration Workflow Steps** — instrucciones_migracion_rsync, instrucciones_migracion_env_config, instrucciones_migracion_cache_clear, instrucciones_migracion_storage_link, instrucciones_migracion_permissions [EXTRACTED 1.00]
- **Agent Workflow Orchestration Principles** — intrucciones_planning_mode, intrucciones_subagent_strategy, intrucciones_self_improvement_loop, intrucciones_verification, intrucciones_elegance_requirement, intrucciones_autonomous_error_correction [EXTRACTED 1.00]

## Communities (147 total, 20 thin omitted)

### Community 0 - "Payment & Auth Controllers"
Cohesion: 0.05
Nodes (13): AuthController, PagoVentaController, ProductoController, ServicioController, VentaController, Producto, Servicio, Venta (+5 more)

### Community 1 - "Service Orders & Reports"
Cohesion: 0.07
Nodes (14): ReporteController, VehiculoController, DetalleCompra, OrdenServicioDetalle, OrdenServicioImagen, OrdenServicioPago, PagoCompra, StockAlerta (+6 more)

### Community 2 - "Purchases & Suppliers"
Cohesion: 0.05
Nodes (11): CompraController, Controller, CuentasPorPagarController, DashboardBetaController, ProveedorController, Compra, NotaCreditoProveedor, Proveedor (+3 more)

### Community 3 - "Composer Dependencies"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 4 - "Client Management"
Cohesion: 0.08
Nodes (9): ClienteController, CreditoController, Collection, Cliente, SeguimientoCredito, ValidadorRfc, Closure, Illuminate\Contracts\Validation\ValidationRule (+1 more)

### Community 5 - "Service Order Detail Ops"
Cohesion: 0.12
Nodes (4): OrdenServicioController, OrdenServicio, NotificationService, Illuminate\Support\Collection

### Community 6 - "Migration & Deployment Notes"
Cohesion: 0.07
Nodes (32): VentaController::facturar, VentaController::registrarFactura (route target, method missing), Laravel Cache Clear Commands, .env Configuration Adjustment, Storage/Bootstrap Cache Permissions, rsync File Copy Step, Shared Database Warning, SSH Cross-Domain Project Copy Process (+24 more)

### Community 7 - "Frontend Build Dependencies"
Cohesion: 0.09
Nodes (22): dependencies, alpinejs, devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite (+14 more)

### Community 8 - "User & Client Seeding"
Cohesion: 0.17
Nodes (8): User, ClienteSeeder, DatabaseSeeder, UsuariosSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 9 - "Auditable Boot & Env Config"
Cohesion: 0.14
Nodes (5): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, Illuminate\Support\Str, static

### Community 13 - "App Service Provider"
Cohesion: 0.40
Nodes (3): AppServiceProvider, Illuminate\Support\Facades\URL, Illuminate\Support\ServiceProvider

### Community 14 - "Feature Test Bootstrap"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Testing\TestCase, ExampleTest, TestCase

### Community 15 - "Layout Partials (Blade)"
Cohesion: 0.33
Nodes (5): partials.eom-alert, partials.finalizado-alert, partials.navbar, partials.prev-month-alert, partials.sidebar

### Community 16 - "App Bootstrap & Entrypoint"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 17 - "Logging Configuration"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

## Knowledge Gaps
- **80 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+75 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 376 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **20 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `OrdenServicioController` connect `Service Order Detail Ops` to `Service Orders & Reports`, `Purchases & Suppliers`, `Migration & Deployment Notes`?**
  _High betweenness centrality (0.066) - this node is a cross-community bridge._
- **Why does `Route/Controller Method Naming Consistency` connect `Migration & Deployment Notes` to `Service Order Detail Ops`?**
  _High betweenness centrality (0.059) - this node is a cross-community bridge._
- **Why does `OrdenServicio` connect `Service Order Detail Ops` to `Service Orders & Reports`, `Purchases & Suppliers`, `Client Management`, `Auditable Boot & Env Config`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _80 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Payment & Auth Controllers` be split into smaller, more focused modules?**
  _Cohesion score 0.05063291139240506 - nodes in this community are weakly interconnected._
- **Should `Service Orders & Reports` be split into smaller, more focused modules?**
  _Cohesion score 0.06663141195134849 - nodes in this community are weakly interconnected._
- **Should `Purchases & Suppliers` be split into smaller, more focused modules?**
  _Cohesion score 0.05480225988700565 - nodes in this community are weakly interconnected._