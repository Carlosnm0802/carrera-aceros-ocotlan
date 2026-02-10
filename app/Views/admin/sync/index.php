<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Carrera Aceros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .connection-status { padding: 15px; border-radius: 5px; }
        .connection-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .connection-error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .sheet-preview { max-height: 300px; overflow-y: auto; }
        .sync-btn { transition: all 0.3s; }
        .sync-btn:hover { transform: scale(1.05); }
        .loading { display: none; }
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
                        <a class="nav-link" href="/admin/reportes">
                            <i class="bi bi-graph-up me-2"></i> Reportes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/sync">
                            <i class="bi bi-cloud-arrow-down"></i> Sincronizar
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
    
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6">
                    <i class="bi bi-cloud-arrow-down-fill text-primary"></i> <?= $title ?>
                </h1>
                <p class="text-muted">Sincronización con Google Sheets</p>
            </div>
        </div>
        
        <!-- Estado de conexión -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-wifi"></i> Estado de conexión
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="connection-status <?= $testResult['success'] ? 'connection-success' : 'connection-error' ?>">
                            <div class="d-flex align-items-center">
                                <i class="bi <?= $testResult['success'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill' ?> fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">
                                        <?= $testResult['success'] ? 'Conexión exitosa' : 'Error de conexión' ?>
                                    </h6>
                                    <p class="mb-0"><?= $testResult['message'] ?></p>
                                    
                                    <?php if ($testResult['success'] && !empty($testResult['headers'])): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">Encabezados detectados:</small>
                                            <div class="d-flex flex-wrap gap-2 mt-1">
                                                <?php foreach ($testResult['headers'] as $header): ?>
                                                    <span class="badge bg-info"><?= htmlspecialchars($header) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($testResult['success'] && !empty($testResult['sheets'])): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">Hojas disponibles:</small>
                                            <div class="d-flex flex-wrap gap-2 mt-1">
                                                <?php foreach ($testResult['sheets'] as $sheet): ?>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($sheet) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                            <small class="text-muted d-block mt-1">Usando: <code><?= htmlspecialchars($testResult['used_sheet'] ?? '') ?></code></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-link-45deg"></i> 
                                Spreadsheet ID: <code><?= htmlspecialchars($spreadsheetId) ?></code>
                            </small>
                            <?php if ($lastSync): ?>
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-clock-history"></i>
                                    Última sincronización: <?= $lastSync ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas de la hoja -->
        <?php if ($sheetStats['total_rows'] > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart"></i> Vista previa de datos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Se encontraron <strong><?= $sheetStats['total_rows'] ?></strong> registros en Google Sheets.
                            Última columna: <strong><?= $sheetStats['last_column'] ?></strong>
                        </div>
                        
                        <?php if (!empty($sheetStats['sample_data'])): ?>
                            <div class="table-responsive sheet-preview">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <?php foreach ($sheetStats['headers'] as $header): ?>
                                                <th><?= htmlspecialchars($header) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sheetStats['sample_data'] as $row): ?>
                                            <tr>
                                                <?php foreach ($row as $cell): ?>
                                                    <td><?= htmlspecialchars($cell) ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted">Mostrando 3 de <?= $sheetStats['total_rows'] ?> registros</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Panel de sincronización -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-arrow-repeat"></i> Sincronización manual
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="syncForm">
                            <div class="mb-3">
                                <label for="range" class="form-label">Rango de datos</label>
                                <input type="text" class="form-control" id="range" name="range" 
                                       value="A:H" placeholder="Ej: A:H, A1:Z100">
                                <div class="form-text">
                                    Especifica el rango de celdas a sincronizar. Por defecto: A:H (columnas A a H)
                                </div>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Nota:</strong> La sincronización importará nuevos registros y actualizará los existentes basándose en el email.
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="button" id="testBtn" class="btn btn-outline-primary">
                                    <i class="bi bi-wifi"></i> Probar conexión
                                </button>
                                <button type="submit" id="syncBtn" class="btn btn-primary sync-btn">
                                    <i class="bi bi-cloud-arrow-down"></i> Sincronizar ahora
                                </button>
                            </div>
                        </form>
                        
                        <!-- Loading -->
                        <div id="loading" class="loading mt-3">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <span id="loadingText">Procesando...</span>
                            </div>
                        </div>
                        
                        <!-- Resultados -->
                        <div id="results" class="mt-3"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle"></i> Información
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6>¿Cómo funciona?</h6>
                        <ol class="small">
                            <li>Conecta con Google Sheets usando Service Account</li>
                            <li>Lee los datos del formulario de inscripción</li>
                            <li>Importa nuevos participantes a la base de datos</li>
                            <li>Actualiza participantes existentes</li>
                            <li>Mantiene un registro de cambios</li>
                        </ol>
                        
                        <h6 class="mt-3">Comandos CLI disponibles</h6>
                        <div class="bg-dark text-light p-3 rounded">
                            <code class="small">
                                php spark google:sync<br>
                                php spark google:sync --test<br>
                                php spark google:sync --range="A:H"
                            </code>
                        </div>
                        
                        <div class="alert alert-success mt-3">
                            <i class="bi bi-lightbulb"></i>
                            <strong>Tip:</strong> Puedes programar sincronizaciones automáticas usando cron jobs.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const syncForm = document.getElementById('syncForm');
        const testBtn = document.getElementById('testBtn');
        const syncBtn = document.getElementById('syncBtn');
        const loading = document.getElementById('loading');
        const loadingText = document.getElementById('loadingText');
        const results = document.getElementById('results');
        
        // Probar conexión
        testBtn.addEventListener('click', function() {
            loadingText.textContent = 'Probando conexión...';
            loading.style.display = 'block';
            results.innerHTML = '';
            
            fetch('/admin/sync/test', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                
                if (data.success) {
                    results.innerHTML = `
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <strong>✅ Conexión exitosa</strong><br>
                            ${data.message}
                            ${data.headers ? `<br><small>Encabezados: ${data.headers.join(', ')}</small>` : ''}
                        </div>
                    `;
                } else {
                    results.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle-fill"></i>
                            <strong>❌ Error de conexión</strong><br>
                            ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                results.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle-fill"></i>
                        <strong>❌ Error</strong><br>
                        Error al probar conexión: ${error}
                    </div>
                `;
            });
        });
        
        // Sincronizar
        syncForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const range = document.getElementById('range').value;
            loadingText.textContent = 'Sincronizando datos...';
            loading.style.display = 'block';
            results.innerHTML = '';
            syncBtn.disabled = true;
            
            fetch('/admin/sync/sync-now', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    range: range
                })
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                syncBtn.disabled = false;
                
                if (data.success) {
                    results.innerHTML = `
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <strong>✅ Sincronización completada</strong><br>
                            ${data.message}<br>
                            <div class="mt-2">
                                <span class="badge bg-success">Nuevos: ${data.data.imported}</span>
                                <span class="badge bg-warning">Actualizados: ${data.data.updated}</span>
                                <span class="badge bg-secondary">Omitidos: ${data.data.skipped}</span>
                                <span class="badge bg-info">Total: ${data.data.total_rows}</span>
                            </div>
                        </div>
                    `;
                    
                    // Actualizar página después de 2 segundos
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    results.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle-fill"></i>
                            <strong>❌ Error en sincronización</strong><br>
                            ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                syncBtn.disabled = false;
                results.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle-fill"></i>
                        <strong>❌ Error</strong><br>
                        Error al sincronizar: ${error}
                    </div>
                `;
            });
        });
    });
    </script>
</body>
</html>