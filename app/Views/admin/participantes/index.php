<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Carrera Aceros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .badge-estado { min-width: 80px; }
        .table-hover tbody tr:hover { background-color: rgba(0,0,0,.03); }
        .actions-column { width: 120px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard">🏁 Carrera Aceros</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/participantes">
                            <i class="bi bi-people-fill"></i> Participantes
                        </a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <span class="nav-link text-light">
                        <i class="bi bi-person-circle"></i> Administrador
                    </span>
                    <a class="nav-link" href="/logout">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <!-- Encabezado con botón -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3">
                    <i class="bi bi-people"></i> <?= $title ?>
                </h1>
                <p class="text-muted mb-0">Gestión completa de participantes inscritos</p>
            </div>
            <a href="/admin/participantes/create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Participante
            </a>
        </div>
        
        <!-- Mensajes de éxito/error -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Formulario de búsqueda -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-search"></i> Buscar y Filtrar
                </h5>
                <form method="get" action="/admin/participantes" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="q" class="form-control" 
                               placeholder="Nombre, email o teléfono"
                               value="<?= esc($termino ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="estado" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" <?= ($estado_filtro ?? '') == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="validado" <?= ($estado_filtro ?? '') == 'validado' ? 'selected' : '' ?>>Validado</option>
                            <option value="cancelado" <?= ($estado_filtro ?? '') == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="categoria" class="form-select">
                            <option value="">Todas las categorías</option>
                            <option value="5K" <?= ($categoria_filtro ?? '') == '5K' ? 'selected' : '' ?>>5K</option>
                            <option value="10K" <?= ($categoria_filtro ?? '') == '10K' ? 'selected' : '' ?>>10K</option>
                            <option value="Infantil" <?= ($categoria_filtro ?? '') == 'Infantil' ? 'selected' : '' ?>>Infantil</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Tabla de participantes -->
        <div class="card">
            <div class="card-body">
                <?php if (empty($participantes)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-people display-1 text-muted"></i>
                        <h4 class="mt-3">No hay participantes registrados</h4>
                        <p class="text-muted">Comienza agregando tu primer participante</p>
                        <a href="/admin/participantes/create" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Agregar Primer Participante
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Contacto</th>
                                    <th>Edad</th>
                                    <th>Categoría</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th class="actions-column">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($participantes as $p): ?>
                                <tr>
                                    <td class="fw-bold">#<?= $p['id'] ?></td>
                                    <td>
                                        <strong><?= esc($p['nombre_completo']) ?></strong><br>
                                        <small class="text-muted">
                                            <i class="bi bi-gender-<?= $p['genero'] == 'M' ? 'male' : ($p['genero'] == 'F' ? 'female' : 'trans') ?>"></i>
                                            <?= $p['genero'] == 'M' ? 'Masculino' : ($p['genero'] == 'F' ? 'Femenino' : 'Otro') ?>
                                            <?= $p['talla_playera'] ? ' | Talla: ' . $p['talla_playera'] : '' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <i class="bi bi-envelope"></i> <?= esc($p['email']) ?><br>
                                        <i class="bi bi-telephone"></i> <?= $p['telefono'] ? esc($p['telefono']) : '<span class="text-muted">No especificado</span>' ?>
                                    </td>
                                    <td><?= $p['edad'] ?> años</td>
                                    <td>
                                        <span class="badge bg-info"><?= esc($p['categoria']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill badge-estado bg-<?= 
                                            $p['estado'] == 'validado' ? 'success' : 
                                            ($p['estado'] == 'pendiente' ? 'warning' : 'danger')
                                        ?>">
                                            <?= ucfirst($p['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?= date('d/m/Y', strtotime($p['fecha_registro'])) ?><br>
                                            <span class="text-muted"><?= date('H:i', strtotime($p['fecha_registro'])) ?></span>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/admin/participantes/edit/<?= $p['id'] ?>" 
                                               class="btn btn-outline-primary" 
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="/admin/participantes/delete/<?= $p['id'] ?>" 
                                               class="btn btn-outline-danger"
                                               onclick="return confirm('¿Estás seguro de eliminar a <?= addslashes($p['nombre_completo']) ?>?')"
                                               title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Contador de resultados -->
                    <div class="mt-3 text-muted">
                        <i class="bi bi-info-circle"></i> 
                        Mostrando <?= count($participantes) ?> participante(s)
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Función para confirmar eliminación
        function confirmDelete(nombre) {
            return confirm('¿Estás seguro de eliminar a ' + nombre + '?');
        }
        
        // Auto-ocultar alertas después de 5 segundos
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>