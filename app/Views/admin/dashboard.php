<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #4e73df;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --dark: #5a5c69;
        }
        
        body { 
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .stat-card { 
            border-radius: 0.35rem;
            border-left: 0.25rem solid;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .stat-card:hover { 
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .stat-icon { 
            font-size: 2rem;
            opacity: 0.7;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 1rem;
        }
        
        .card-chart {
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-chart .card-header {
            border-bottom: 1px solid #e3e6f0;
            background-color: #fff;
            padding: 1rem 1.35rem;
        }
        
        .badge-estado {
            padding: 0.4em 0.8em;
            font-weight: 500;
        }
        
        .table-recent {
            font-size: 0.9rem;
        }
        
        .table-recent th {
            border-top: none;
            font-weight: 600;
            color: #5a5c69;
        }
        
        .sidebar {
            background: #fff;
            border-right: 1px solid #e3e6f0;
            min-height: calc(100vh - 56px);
        }
        
        .sidebar .nav-link {
            color: #858796;
            padding: 0.75rem 1rem;
            border-left: 0.25rem solid transparent;
        }
        
        .sidebar .nav-link.active {
            color: #4e73df;
            background-color: #f8f9fc;
            border-left-color: #4e73df;
        }
        
        .sidebar .nav-link:hover {
            color: #4e73df;
            background-color: #f8f9fc;
        }
    </style>
</head>
<body>
    <!-- Navbar Principal -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin/dashboard">
                <i class="bi bi-speedometer2 me-2"></i> 
                <span class="fw-bold">🏁 Carrera Aceros</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/sync">
                            <i class="bi bi-cloud-arrow-down me-2"></i> Sincronizar
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> Administrador
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar d-md-block">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="/admin/dashboard">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/participantes">
                                <i class="bi bi-people me-2"></i> Participantes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-graph-up me-2"></i> Reportes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/sync">
                                <i class="bi bi-cloud-arrow-down me-2"></i> Sincronizar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-gear me-2"></i> Configuración
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Información del sistema -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="fw-bold">Info del Sistema</h6>
                        <small class="d-block text-muted">
                            <i class="bi bi-database me-1"></i> 
                            Participantes: <?= $estadisticas['total'] ?>
                        </small>
                        <small class="d-block text-muted mt-1">
                            <i class="bi bi-calendar3 me-1"></i> 
                            <?= date('d/m/Y') ?>
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Contenido Principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Encabezado -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="bi bi-speedometer2 text-primary"></i> Dashboard
                    </h1>
                    <div class="btn-toolbar">
                        <button id="actualizarGraficos" class="btn btn-sm btn-outline-primary me-2">
                            <i class="bi bi-arrow-clockwise"></i> Actualizar
                        </button>
                        <button class="btn btn-sm btn-primary">
                            <i class="bi bi-download"></i> Exportar Reporte
                        </button>
                    </div>
                </div>
                
                <!-- Estadísticas Rápidas -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-primary">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                            Total Inscritos
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800">
                                            <?= $estadisticas['total'] ?>
                                        </div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <i class="bi bi-arrow-up text-success me-1"></i>
                                            <span>Total de participantes</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people-fill stat-icon text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-success">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                            Validados
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800">
                                            <?php 
                                                $validados = 0;
                                                foreach($estadisticas['por_estado'] as $estado) {
                                                    if($estado['estado'] == 'validado') {
                                                        $validados = $estado['total'];
                                                        break;
                                                    }
                                                }
                                                echo $validados;
                                            ?>
                                        </div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <i class="bi bi-check-circle text-success me-1"></i>
                                            <span>Confirmados</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-check-circle-fill stat-icon text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-warning">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                            Pendientes
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800">
                                            <?php 
                                                $pendientes = 0;
                                                foreach($estadisticas['por_estado'] as $estado) {
                                                    if($estado['estado'] == 'pendiente') {
                                                        $pendientes = $estado['total'];
                                                        break;
                                                    }
                                                }
                                                echo $pendientes;
                                            ?>
                                        </div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <i class="bi bi-clock text-warning me-1"></i>
                                            <span>Por validar</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-clock-history stat-icon text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-info">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                            Promedio Edad
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800">
                                            <?php
                                                // Calcular promedio de edad
                                                $db = \Config\Database::connect();
                                                $edad = $db->table('participantes')
                                                    ->selectAvg('edad')
                                                    ->get()
                                                    ->getRowArray();
                                                echo isset($edad['edad']) ? round($edad['edad'], 1) . ' años' : 'N/A';
                                            ?>
                                        </div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <i class="bi bi-person text-info me-1"></i>
                                            <span>Edad promedio</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-person-bounding-box stat-icon text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección de Gráficos -->
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <div class="card card-chart">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold">
                                    <i class="bi bi-calendar-week me-1"></i> Inscripciones por Día (Últimos 7 días)
                                </h6>
                                <button class="btn btn-sm btn-outline-secondary toggle-grafico" data-grafico="graficoDiario">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="graficoDiario"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card card-chart">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold">
                                    <i class="bi bi-pie-chart me-1"></i> Distribución por Categoría
                                </h6>
                                <button class="btn btn-sm btn-outline-secondary toggle-grafico" data-grafico="graficoCategoria">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="graficoCategoria"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="card card-chart">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold">
                                    <i class="bi bi-bar-chart me-1"></i> Distribución por Estado
                                </h6>
                                <button class="btn btn-sm btn-outline-secondary toggle-grafico" data-grafico="graficoEstado">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="graficoEstado"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="card card-chart">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold">
                                    <i class="bi bi-radar me-1"></i> Perfil por Grupos de Edad
                                </h6>
                                <button class="btn btn-sm btn-outline-secondary toggle-grafico" data-grafico="graficoRadar">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="graficoRadar"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de Participantes Recientes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 fw-bold">
                            <i class="bi bi-clock-history me-1"></i> Participantes Recientes
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-recent">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Categoría</th>
                                        <th>Edad</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($estadisticas['recientes'])): ?>
                                        <?php foreach($estadisticas['recientes'] as $p): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($p['nombre_completo']) ?></strong>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= esc($p['email']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= esc($p['categoria']) ?></span>
                                            </td>
                                            <td><?= $p['edad'] ?></td>
                                            <td>
                                                <span class="badge badge-estado bg-<?= 
                                                    $p['estado'] == 'validado' ? 'success' : 
                                                    ($p['estado'] == 'pendiente' ? 'warning' : 'danger')
                                                ?>">
                                                    <?= ucfirst($p['estado']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?= date('d/m/Y H:i', strtotime($p['fecha_registro'])) ?></small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-people display-6 d-block mb-2"></i>
                                                No hay participantes registrados
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="/admin/participantes" class="btn btn-sm btn-primary">
                                Ver todos los participantes <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas Detalladas -->
                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0 fw-bold">
                                    <i class="bi bi-gender-ambiguous me-1"></i> Distribución por Género
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Género</th>
                                                <th>Cantidad</th>
                                                <th>Porcentaje</th>
                                                <th>Progreso</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($estadisticas['por_genero'] as $gen): 
                                                $porcentaje = $estadisticas['total'] > 0 ? ($gen['total'] / $estadisticas['total']) * 100 : 0;
                                                $nombre = $gen['genero'] == 'M' ? 'Masculino' : 
                                                         ($gen['genero'] == 'F' ? 'Femenino' : 'Otro');
                                                $color = $gen['genero'] == 'M' ? 'primary' : 
                                                        ($gen['genero'] == 'F' ? 'danger' : 'warning');
                                            ?>
                                            <tr>
                                                <td>
                                                    <i class="bi bi-gender-<?= $gen['genero'] == 'M' ? 'male' : ($gen['genero'] == 'F' ? 'female' : 'trans') ?> text-<?= $color ?> me-1"></i>
                                                    <?= $nombre ?>
                                                </td>
                                                <td><strong><?= $gen['total'] ?></strong></td>
                                                <td><?= number_format($porcentaje, 1) ?>%</td>
                                                <td width="40%">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-<?= $color ?>" 
                                                             style="width: <?= $porcentaje ?>%"
                                                             role="progressbar"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0 fw-bold">
                                    <i class="bi bi-tshirt me-1"></i> Distribución por Talla de Playera
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($estadisticas['por_talla'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Talla</th>
                                                    <th>Cantidad</th>
                                                    <th>Porcentaje</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($estadisticas['por_talla'] as $talla): 
                                                    $porcentaje = $estadisticas['total'] > 0 ? ($talla['total'] / $estadisticas['total']) * 100 : 0;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-secondary"><?= $talla['talla_playera'] ?></span>
                                                    </td>
                                                    <td><strong><?= $talla['total'] ?></strong></td>
                                                    <td><?= number_format($porcentaje, 1) ?>%</td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center py-3">
                                        <i class="bi bi-tshirt display-6 d-block mb-2"></i>
                                        No hay tallas especificadas
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light border-top">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 text-muted">
                    <small>
                        Sistema de Gestión de Inscripciones - Carrera Aceros Ocotlán © 2024
                    </small>
                </div>
                <div class="col-md-6 text-end text-muted">
                    <small>
                        <i class="bi bi-cpu"></i> PHP <?= phpversion() ?> | 
                        <i class="bi bi-code-slash"></i> CodeIgniter 4 |
                        <i class="bi bi-database"></i> MySQL
                    </small>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para los gráficos -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============ CONFIGURACIÓN GLOBAL DE CHART.JS ============
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        Chart.defaults.color = '#6c757d';
        
        // ============ 1. GRÁFICO DE PASTEL - DISTRIBUCIÓN POR CATEGORÍA ============
        const ctxCategoria = document.getElementById('graficoCategoria').getContext('2d');
        const graficoCategoria = new Chart(ctxCategoria, {
            type: 'pie',
            data: {
                labels: <?= json_encode($estadisticas['datos_grafico_categoria']['labels']) ?>,
                datasets: [{
                    data: <?= json_encode($estadisticas['datos_grafico_categoria']['data']) ?>,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // ============ 2. GRÁFICO DE BARRAS - DISTRIBUCIÓN POR ESTADO ============
        const ctxEstado = document.getElementById('graficoEstado').getContext('2d');
        const graficoEstado = new Chart(ctxEstado, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_map(function($estado) {
                    return ucfirst($estado);
                }, $estadisticas['datos_grafico_estado']['labels'])) ?>,
                datasets: [{
                    label: 'Participantes',
                    data: <?= json_encode($estadisticas['datos_grafico_estado']['data']) ?>,
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',   // Pendiente - Amarillo
                        'rgba(40, 167, 69, 0.8)',   // Validado - Verde
                        'rgba(220, 53, 69, 0.8)'    // Cancelado - Rojo
                    ],
                    borderColor: [
                        'rgb(255, 193, 7)',
                        'rgb(40, 167, 69)',
                        'rgb(220, 53, 69)'
                    ],
                    borderWidth: 1,
                    borderRadius: 5,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        
        // ============ 3. GRÁFICO DE LÍNEAS - INSCRIPCIONES POR DÍA ============
        const ctxDiario = document.getElementById('graficoDiario').getContext('2d');
        const graficoDiario = new Chart(ctxDiario, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_map(function($fecha) {
                    return date('d/m', strtotime($fecha));
                }, $estadisticas['datos_grafico_ultimos_dias']['labels'])) ?>,
                datasets: [{
                    label: 'Inscripciones por día',
                    data: <?= json_encode($estadisticas['datos_grafico_ultimos_dias']['data']) ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        
        // ============ 4. GRÁFICO DE RADAR - PERFIL POR EDAD ============
        const ctxRadar = document.getElementById('graficoRadar').getContext('2d');
        const graficoRadar = new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: <?= json_encode($estadisticas['datos_grafico_edad']['labels']) ?>,
                datasets: [{
                    label: 'Distribución por edad',
                    data: <?= json_encode($estadisticas['datos_grafico_edad']['data']) ?>,
                    backgroundColor: 'rgba(54, 185, 204, 0.2)',
                    borderColor: 'rgba(54, 185, 204, 1)',
                    pointBackgroundColor: 'rgba(54, 185, 204, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(54, 185, 204, 1)',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // ============ FUNCIONALIDAD ADICIONAL ============
        
        // Botón para actualizar gráficos
        document.getElementById('actualizarGraficos').addEventListener('click', function() {
            this.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Actualizando...';
            this.disabled = true;
            
            // Simular actualización
            setTimeout(() => {
                location.reload();
            }, 1000);
        });
        
        // Mostrar/Ocultar gráficos
        document.querySelectorAll('.toggle-grafico').forEach(button => {
            button.addEventListener('click', function() {
                const graficoId = this.dataset.grafico;
                const canvas = document.getElementById(graficoId);
                const icon = this.querySelector('i');
                
                if (canvas.style.display === 'none') {
                    canvas.style.display = 'block';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    canvas.style.display = 'none';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        });
        
        // Animación para las tarjetas estadísticas
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1)';
            });
        });
    });
    </script>
</body>
</html>