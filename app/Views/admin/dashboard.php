<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: bold; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard">
                <i class="bi bi-speedometer2"></i> 🏁 Carrera Aceros
            </a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link text-light">Administrador</span>
                <a class="nav-link" href="/logout">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <h1 class="card-title">Dashboard</h1>
                        <p class="card-text text-muted">Sistema de gestión de inscripciones</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-primary">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <i class="bi bi-people"></i> Participantes
                        </h5>
                        <p class="card-text">Gestión completa de inscritos: ver, agregar, editar y eliminar.</p>
                        <a href="#" class="btn btn-primary">Gestionar participantes</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-secondary">
                    <div class="card-body">
                        <h5 class="card-title text-secondary">
                            <i class="bi bi-bar-chart"></i> Estadísticas
                        </h5>
                        <p class="card-text">Métricas y gráficos del evento: categorías, edades, géneros.</p>
                        <a href="#" class="btn btn-secondary">Ver estadísticas</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="bi bi-gear"></i> Configuración
                        </h5>
                        <p class="card-text">Ajustes del sistema, sincronización y preferencias.</p>
                        <a href="#" class="btn btn-success">Configurar</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-list-check"></i> Progreso del sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Base de datos configurada
                        </div>
                        <span class="badge bg-success">Completado</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Autenticación funcionando
                        </div>
                        <span class="badge bg-success">Completado</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-arrow-clockwise text-warning me-2"></i>
                            CRUD participantes
                        </div>
                        <span class="badge bg-warning">Próximo</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-arrow-clockwise text-warning me-2"></i>
                            Integración Google Forms
                        </div>
                        <span class="badge bg-warning">Próximo</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-body">
                <h6>Información del sistema:</h6>
                <ul class="mb-0">
                    <li>Usuario: <strong>admin@carreraceros.com</strong></li>
                    <li>Tablas creadas: <strong>participantes, users, auth_*</strong></li>
                    <li>Ruta del proyecto: <code>C:\xampp\htdocs\carrera-aceros-ocotlan</code></li>
                </ul>
            </div>
        </div>
    </div>
    
    <footer class="mt-5 py-3 text-center text-muted border-top">
        <div class="container">
            <small>Sistema de Gestión de Inscripciones - Carrera Aceros Ocotlán © 2024</small>
        </div>
    </footer>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>