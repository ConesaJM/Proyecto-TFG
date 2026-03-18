<?php
// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/style.php'; // (3º: Carga los estilos CSS)
require_once __DIR__ . '/../app/utils.php'; // (4º: Carga nuestras funciones)

// 2. PROTECCIÓN DE LA PÁGINA 
// Esta función de auth.php comprobará si hay una sesión iniciada.
// Si no la hay, redirige a login.php y el script muere aquí.
require_login(); 

// 3. MOSTRAR LA PÁGINA
// Si el script llega aquí, el usuario SÍ está logueado.
// Con la función headerHtml() mostramos el HTML creado dentro de utils.php
headerHtml('');

?>

<div class="dashboard-welcome">
    <div class="welcome-text">
        <h2 class="welcome-title">¡Hola de nuevo, <span class="text-highlight"><?= h($_SESSION['user_nombre_usuario']); ?></span>! 👋</h2>
        <p class="welcome-subtitle">Te damos la bienvenida a tu centro de control en <strong>Pharmasphere</strong>.</p>
    </div>
    <div class="welcome-date">
        <i class="fa-regular fa-calendar"></i>
        <span><?= date('d \d\e F \d\e Y'); ?></span>
    </div>
</div>

<div class="dashboard-layout">
    
    <div class="dash-main-col">
        
        <div class="dash-card featured-card">
            <div class="card-header">
                <h3><i class="fa-solid fa-bell text-warning"></i> Novedades y Avisos</h3>
            </div>
            <div class="card-body">
                <ul class="notification-list">
                    <li class="notification-item unread">
                        <div class="notif-icon bg-blue-light"><i class="fa-solid fa-box-open text-primary"></i></div>
                        <div class="notif-content">
                            <strong>Nuevo lote recibido</strong>
                            <p>Se ha actualizado el inventario con nuevas unidades en almacén.</p>
                        </div>
                        <span class="notif-time">Reciente</span>
                    </li>
                    <li class="notification-item">
                        <div class="notif-icon bg-green-light"><i class="fa-solid fa-tags text-success"></i></div>
                        <div class="notif-content">
                            <strong>Nuevas recomendaciones</strong>
                            <p>Revisa la sección de productos destacados del mes.</p>
                        </div>
                        <span class="notif-time">Ayer</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="dash-card">
            <div class="card-header">
                <h3><i class="fa-solid fa-compass"></i> Explorar Inventario</h3>
            </div>
            <div class="category-grid">
                <a href="items_list.php" class="cat-card">
                    <div class="cat-icon"><i class="fa-solid fa-pills"></i></div>
                    <h4>Medicamentos</h4>
                    <p>Con y sin receta</p>
                </a>
                <a href="items_list.php" class="cat-card">
                    <div class="cat-icon"><i class="fa-solid fa-leaf"></i></div>
                    <h4>Parafarmacia</h4>
                    <p>Salud natural</p>
                </a>
                <a href="items_list.php" class="cat-card">
                    <div class="cat-icon"><i class="fa-solid fa-pump-soap"></i></div>
                    <h4>Higiene</h4>
                    <p>Cuidado personal</p>
                </a>
                <a href="items_list.php" class="cat-card">
                    <div class="cat-icon"><i class="fa-solid fa-kit-medical"></i></div>
                    <h4>Primeros Auxilios</h4>
                    <p>Botiquín básico</p>
                </a>
            </div>
        </div>
    </div>

    <div class="dash-side-col">
        
        <div class="dash-card profile-summary-card">
            <div class="profile-avatar">
                <i class="fa-solid fa-user-nurse"></i>
            </div>
            <h4><?= h($_SESSION['user_nombre_usuario'] ?? 'Usuario'); ?></h4>
            <span class="badge-role"><?= h($_SESSION['user_rol'] ?? 'Usuario'); ?></span>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-label">Último acceso</span>
                    <span class="stat-val"><?= date('H:i'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Estado</span>
                    <span class="stat-val text-success"><i class="fa-solid fa-circle"></i> Conectado</span>
                </div>
            </div>
            
            <a href="preferencias.php" class="btn-dash btn-outline w-100">
                <i class="fa-solid fa-gear"></i> Ajustes de perfil
            </a>
        </div>

        <div class="dash-card">
            <div class="card-header">
                <h3><i class="fa-solid fa-bolt"></i> Acciones</h3>
            </div>
            <div class="action-list">
                <a href="items_list.php" class="action-item">
                    <div class="action-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <div class="action-text">
                        <strong>Buscar Producto</strong>
                        <span>Consultar precios y stock</span>
                    </div>
                    <i class="fa-solid fa-chevron-right action-arrow"></i>
                </a>
                
                <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'Administrador'): ?>
                <a href="items_form.php" class="action-item action-primary">
                    <div class="action-icon"><i class="fa-solid fa-plus"></i></div>
                    <div class="action-text">
                        <strong>Nuevo Producto</strong>
                        <span>Añadir al catálogo</span>
                    </div>
                    <i class="fa-solid fa-chevron-right action-arrow"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php
// ==========================================
// SECCIÓN EXCLUSIVA PARA ADMINISTRADORES
// ==========================================
if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'Administrador') {

    // Consultas a la base de datos
    $totalUsuarios  = (int)$pdo->query("SELECT COUNT(*) FROM USUARIO")->fetchColumn();
    $totalProductos = (int)$pdo->query("SELECT COUNT(*) FROM PRODUCTO")->fetchColumn();
    $totalMarcas    = (int)$pdo->query("SELECT COUNT(*) FROM MARCA")->fetchColumn();
    
    // Consulta para sacar los 3 productos con menos stock (Advertencia real)
    $bajosStock = $pdo->query("SELECT NOMBRE, STOCK_DISPONIBLE FROM PRODUCTO ORDER BY STOCK_DISPONIBLE ASC LIMIT 3")->fetchAll();
?>

<div class="admin-section-divider">
    <hr>
    <span>Zona de Administración</span>
</div>

<div class="dashboard-layout">
    <div class="dash-main-col">
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
                <div class="kpi-data">
                    <span class="kpi-title">Usuarios</span>
                    <span class="kpi-value"><?= h($totalUsuarios) ?></span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fa-solid fa-pills"></i></div>
                <div class="kpi-data">
                    <span class="kpi-title">Productos</span>
                    <span class="kpi-value"><?= h($totalProductos) ?></span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fa-solid fa-tag"></i></div>
                <div class="kpi-data">
                    <span class="kpi-title">Marcas</span>
                    <span class="kpi-value"><?= h($totalMarcas) ?></span>
                </div>
            </div>
        </div>

        <div class="dash-card chart-container">
            <div class="card-header">
                <h3><i class="fa-solid fa-chart-column"></i> Resumen de Entidades</h3>
            </div>
            <div style="position: relative; height:250px; width:100%;">
                <canvas id="adminChart"></canvas>
            </div>
        </div>
    </div>

    <div class="dash-side-col">
        <div class="dash-card warning-card">
            <div class="card-header">
                <h3><i class="fa-solid fa-triangle-exclamation text-danger"></i> Stock Crítico</h3>
            </div>
            <div class="card-body">
                <p class="text-muted" style="margin-bottom: 15px; font-size: 0.9rem;">Productos que requieren reposición inmediata:</p>
                <ul class="critical-stock-list">
                    <?php foreach ($bajosStock as $item): ?>
                    <li>
                        <span class="item-name"><?= h($item['NOMBRE']) ?></span>
                        <span class="item-stock badge-danger"><?= h($item['STOCK_DISPONIBLE']) ?> ud.</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('adminChart');
// Obtenemos el color primario dinámicamente según el tema claro/oscuro
const colorPrimario = getComputedStyle(document.documentElement).getPropertyValue('--color-primario').trim() || '#007BFF';

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Usuarios', 'Productos', 'Marcas'],
        datasets: [{
            label: 'Total',
            data: [<?= (int)$totalUsuarios ?>, <?= (int)$totalProductos ?>, <?= (int)$totalMarcas ?>],
            backgroundColor: colorPrimario,
            borderRadius: 6,
            barThickness: 40
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#e0e0e0' } },
            x: { grid: { display: false } }
        }
    }
});
</script>

<?php
}
// 5. CIERRE DE LA PÁGINA
footerHtml(); 
?>