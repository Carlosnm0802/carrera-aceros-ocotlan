<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Carrera Aceros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .card-reporte { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .card-reporte:hover { transform: translateY(-5px); }
        .filtro-section { background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .resultados-table { max-height: 500px; overflow-y: auto; }
        .estadistica-card { border-left: 4px solid; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .estadistica-card.total { border-left-color: #4e73df; background-color: #e8f4fd; }
        .estadistica-card.validados { border-left-color: #1cc88a; background-color: #e8f8f1; }
        .estadistica-card.pendientes { border-left-color: #f6c23e; background-color: #fff9e6; }
        .export-btn-group { display: flex; gap: 10px; }
        .loading-overlay { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(255,255,255,0.8); z-index: 9999; 
            display: none; justify-content: center; align-items: center; 
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard">🏁 Carrera Aceros</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/participantes">
                            <i class="bi bi-people"></i> Participantes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/sync">
                            <i class="bi bi-cloud-arrow-down"></i> Sincronizar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/reportes">
                            <i class="bi bi-graph-up"></i> Reportes
                        </a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <a class="nav-link" href="/logout">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6">
                    <i class="bi bi-graph-up text-primary"></i> <?= $title ?>
                </h1>
                <p class="text-muted">Genera reportes personalizados y exporta datos en diferentes formatos</p>
            </div>
        </div>
        
        <div class="row">
            <!-- Columna izquierda: Filtros -->
            <div class="col-lg-4">
                <div class="card card-reporte mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-funnel"></i> Filtros del Reporte
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="formFiltros">
                            <!-- Fechas -->
                            <div class="mb-3">
                                <label class="form-label">Rango de fechas</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                                    </div>
                                    <div class="col-6">
                                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                                    </div>
                                </div>
                                <div class="form-text">Dejar vacío para todas las fechas</div>
                            </div>
                            
                            <!-- Estado -->
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado">
                                    <option value="">Todos los estados</option>
                                    <?php foreach($estados as $estado): ?>
                                    <option value="<?= $estado ?>"><?= ucfirst($estado) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Categoría -->
                            <div class="mb-3">
                                <label class="form-label">Categoría</label>
                                <select class="form-select" name="categoria">
                                    <option value="">Todas las categorías</option>
                                    <?php foreach($categorias as $categoria): ?>
                                    <option value="<?= $categoria ?>"><?= $categoria ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Género -->
                            <div class="mb-3">
                                <label class="form-label">Género</label>
                                <select class="form-select" name="genero">
                                    <option value="">Todos los géneros</option>
                                    <?php foreach($generos as $genero): ?>
                                    <option value="<?= $genero ?>">
                                        <?= $genero == 'M' ? 'Masculino' : ($genero == 'F' ? 'Femenino' : 'Otro') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Edad -->
                            <div class="mb-3">
                                <label class="form-label">Rango de edad</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="edad_min" placeholder="Mínima" min="1" max="120">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="edad_max" placeholder="Máxima" min="1" max="120">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ordenamiento -->
                            <div class="mb-3">
                                <label class="form-label">Ordenar por</label>
                                <select class="form-select" name="orden">
                                    <option value="fecha_registro DESC">Fecha (más reciente primero)</option>
                                    <option value="fecha_registro ASC">Fecha (más antigua primero)</option>
                                    <option value="nombre_completo ASC">Nombre (A-Z)</option>
                                    <option value="nombre_completo DESC">Nombre (Z-A)</option>
                                    <option value="edad ASC">Edad (menor a mayor)</option>
                                    <option value="edad DESC">Edad (mayor a menor)</option>
                                </select>
                            </div>
                            
                            <!-- Límite -->
                            <div class="mb-3">
                                <label class="form-label">Límite de registros</label>
                                <select class="form-select" name="limite">
                                    <option value="50">50 registros</option>
                                    <option value="100">100 registros</option>
                                    <option value="250">250 registros</option>
                                    <option value="500">500 registros</option>
                                    <option value="1000" selected>Todos los registros</option>
                                </select>
                            </div>
                            
                            <!-- Botones -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Generar Reporte
                                </button>
                                <button type="button" id="btnLimpiar" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar Filtros
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Estadísticas rápidas -->
                <div class="card card-reporte">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-speedometer2"></i> Resumen General
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="estadistica-card total">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">Total de participantes</h6>
                                    <small class="text-muted">Registros en sistema</small>
                                </div>
                                <div class="fs-3 fw-bold"><?= number_format($total_participantes) ?></div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <div class="estadistica-card validados flex-fill">
                                <h6 class="mb-1">Validados</h6>
                                <div class="fs-5 fw-bold">
                                    <?php 
                                        $model = new \App\Models\ParticipanteModel();
                                        echo number_format($model->where('estado', 'validado')->countAllResults());
                                    ?>
                                </div>
                            </div>
                            <div class="estadistica-card pendientes flex-fill">
                                <h6 class="mb-1">Pendientes</h6>
                                <div class="fs-5 fw-bold">
                                    <?php 
                                        echo number_format($model->where('estado', 'pendiente')->countAllResults());
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i>
                                Los reportes se generan en tiempo real con los filtros aplicados
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna derecha: Resultados -->
            <div class="col-lg-8">
                <div class="card card-reporte mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-table"></i> Resultados del Reporte
                        </h5>
                        <div class="export-btn-group">
                            <button id="btnExportarExcel" class="btn btn-light btn-sm" disabled>
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                            <button id="btnExportarCSV" class="btn btn-light btn-sm" disabled>
                                <i class="bi bi-filetype-csv"></i> CSV
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Estado vacío -->
                        <div id="estadoVacio" class="text-center py-5">
                            <i class="bi bi-clipboard-data display-1 text-muted"></i>
                            <h4 class="mt-3">Sin datos para mostrar</h4>
                            <p class="text-muted">Aplica filtros y genera un reporte para ver los resultados</p>
                        </div>
                        
                        <!-- Estadísticas del reporte -->
                        <div id="estadisticasReporte" class="mb-4" style="display: none;">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">Total en reporte</h6>
                                            <h3 id="totalReporte" class="mb-0">0</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">Edad promedio</h6>
                                            <h3 id="edadPromedio" class="mb-0">0</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">Validados</h6>
                                            <h3 id="totalValidados" class="mb-0">0</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">Pendientes</h6>
                                            <h3 id="totalPendientes" class="mb-0">0</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabla de resultados -->
                        <div id="resultadosContainer" style="display: none;">
                            <div class="table-responsive resultados-table">
                                <table class="table table-hover" id="tablaResultados">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Edad</th>
                                            <th>Categoría</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cuerpoTabla">
                                        <!-- Los datos se cargan aquí dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted" id="infoReporte"></small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Información adicional -->
                <div class="card card-reporte">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle"></i> Información sobre los reportes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="bi bi-lightbulb text-warning"></i> Consejos</h6>
                                <ul class="small">
                                    <li>Usa filtros específicos para obtener datos más precisos</li>
                                    <li>Exporta a Excel para análisis avanzado</li>
                                    <li>Exporta a PDF para compartir con otros</li>
                                    <li>Guarda combinaciones de filtros frecuentes</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="bi bi-shield-check text-success"></i> Seguridad</h6>
                                <ul class="small">
                                    <li>Todos los datos son confidenciales</li>
                                    <li>Los reportes incluyen marca de tiempo</li>
                                    <li>Se registra quién genera cada reporte</li>
                                    <li>Exporta solo la información necesaria</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3">Generando reporte...</p>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables globales
        let filtrosActuales = {};
        let datosReporte = [];
        
        // Configurar datepickers
        flatpickr("#fecha_inicio", {
            locale: "es",
            dateFormat: "Y-m-d",
            maxDate: "today"
        });
        
        flatpickr("#fecha_fin", {
            locale: "es",
            dateFormat: "Y-m-d",
            maxDate: "today"
        });
        
        // Generar reporte
        document.getElementById('formFiltros').addEventListener('submit', function(e) {
            e.preventDefault();
            generarReporte();
        });
        
        // Limpiar filtros
        document.getElementById('btnLimpiar').addEventListener('click', function() {
            document.getElementById('formFiltros').reset();
            ocultarResultados();
        });
        
        // Exportar a Excel
        document.getElementById('btnExportarExcel').addEventListener('click', function() {
            exportarReporte('excel');
        });
        
        // Exportar a CSV
        document.getElementById('btnExportarCSV').addEventListener('click', function() {
            exportarReporte('csv');
        });
        
        // Función para generar reporte
        function generarReporte() {
            const form = document.getElementById('formFiltros');
            const formData = new FormData(form);
            
            // Convertir FormData a objeto
            filtrosActuales = {};
            formData.forEach((value, key) => {
                if (value) filtrosActuales[key] = value;
            });
            
            // Mostrar loading
            mostrarLoading(true);
            
            // Enviar solicitud AJAX
            fetch('/admin/reportes/generar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(filtrosActuales)
            })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);
                
                if (data.success) {
                    datosReporte = data.data;
                    mostrarResultados(data.data);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                mostrarLoading(false);
                alert('Error al generar el reporte: ' + error);
            });
        }
        
        // Función para mostrar resultados
        function mostrarResultados(data) {
            const estadoVacio = document.getElementById('estadoVacio');
            const resultadosContainer = document.getElementById('resultadosContainer');
            const estadisticasReporte = document.getElementById('estadisticasReporte');
            const cuerpoTabla = document.getElementById('cuerpoTabla');
            const infoReporte = document.getElementById('infoReporte');
            
            // Ocultar estado vacío, mostrar resultados
            estadoVacio.style.display = 'none';
            resultadosContainer.style.display = 'block';
            estadisticasReporte.style.display = 'block';
            
            // Actualizar estadísticas
            document.getElementById('totalReporte').textContent = data.estadisticas.total;
            document.getElementById('edadPromedio').textContent = data.estadisticas.edad_promedio;
            document.getElementById('totalValidados').textContent = data.estadisticas.por_estado.validado || 0;
            document.getElementById('totalPendientes').textContent = data.estadisticas.por_estado.pendiente || 0;
            
            // Limpiar tabla anterior
            cuerpoTabla.innerHTML = '';
            
            // Llenar tabla con nuevos datos
            data.participantes.forEach(participante => {
                const fila = document.createElement('tr');
                
                // Determinar clase para el estado
                let estadoClass = '';
                let estadoTexto = '';
                switch(participante.estado) {
                    case 'validado':
                        estadoClass = 'badge bg-success';
                        estadoTexto = 'Validado';
                        break;
                    case 'pendiente':
                        estadoClass = 'badge bg-warning';
                        estadoTexto = 'Pendiente';
                        break;
                    case 'cancelado':
                        estadoClass = 'badge bg-danger';
                        estadoTexto = 'Cancelado';
                        break;
                }
                
                // Mapear género
                let generoTexto = '';
                switch(participante.genero) {
                    case 'M':
                        generoTexto = 'Masculino';
                        break;
                    case 'F':
                        generoTexto = 'Femenino';
                        break;
                    case 'Otro':
                        generoTexto = 'Otro';
                        break;
                }
                
                fila.innerHTML = `
                    <td>${participante.id}</td>
                    <td><strong>${escapeHtml(participante.nombre_completo)}</strong></td>
                    <td>${escapeHtml(participante.email)}</td>
                    <td>${participante.edad}</td>
                    <td><span class="badge bg-info">${escapeHtml(participante.categoria)}</span></td>
                    <td><span class="${estadoClass}">${estadoTexto}</span></td>
                    <td><small>${formatearFecha(participante.fecha_registro)}</small></td>
                `;
                
                cuerpoTabla.appendChild(fila);
            });
            
            // Actualizar información del reporte
            infoReporte.innerHTML = `
                <i class="bi bi-calendar"></i> 
                Reporte generado el ${data.fecha_generacion} | 
                <i class="bi bi-filter"></i> 
                ${Object.keys(data.filtros).filter(k => data.filtros[k]).length} filtros aplicados
            `;
            
            // Habilitar botones de exportación
            document.getElementById('btnExportarExcel').disabled = false;
            document.getElementById('btnExportarCSV').disabled = false;
        }
        
        // Función para ocultar resultados
        function ocultarResultados() {
            document.getElementById('estadoVacio').style.display = 'block';
            document.getElementById('resultadosContainer').style.display = 'none';
            document.getElementById('estadisticasReporte').style.display = 'none';
            
            // Deshabilitar botones de exportación
            document.getElementById('btnExportarExcel').disabled = true;
            document.getElementById('btnExportarCSV').disabled = true;
        }
        
        // Función para exportar reporte
        function exportarReporte(formato) {
            if (!datosReporte || datosReporte.participantes.length === 0) {
                alert('No hay datos para exportar');
                return;
            }
            
            // Construir URL con filtros actuales
            const params = new URLSearchParams(filtrosActuales);
            const url = `/admin/reportes/exportar-${formato}?${params.toString()}`;
            
            // Abrir en nueva pestaña para descargar
            window.open(url, '_blank');
        }
        
        // Función para mostrar/ocultar loading
        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').style.display = mostrar ? 'flex' : 'none';
        }
        
        // Función auxiliar para escapar HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Función para formatear fecha
        function formatearFecha(fechaString) {
            const fecha = new Date(fechaString);
            return fecha.toLocaleDateString('es-MX', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Cargar reporte inicial si hay parámetros en la URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.toString()) {
            // Rellenar formulario con parámetros de URL
            urlParams.forEach((value, key) => {
                const input = document.querySelector(`[name="${key}"]`);
                if (input) input.value = value;
            });
            
            // Generar reporte automáticamente
            setTimeout(() => generarReporte(), 500);
        }
    });
    </script>
</body>
</html>