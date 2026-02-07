<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Carrera Aceros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .required:after { content: " *"; color: red; }
        .form-card { max-width: 800px; margin: 0 auto; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard">🏁 Carrera Aceros</a>
            <div class="navbar-nav">
                <a class="nav-link" href="/admin/participantes">
                    <i class="bi bi-arrow-left"></i> Volver a participantes
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="form-card">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-person-plus"></i> <?= $title ?>
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <h5><i class="bi bi-exclamation-triangle-fill"></i> Errores de validación:</h5>
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="/admin/participantes/store" class="mt-3">
                        <div class="row">
                            <!-- Columna izquierda -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre_completo" class="form-label required">Nombre completo</label>
                                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                                           value="<?= old('nombre_completo') ?>" 
                                           placeholder="Ej: Juan Pérez Rodríguez"
                                           required
                                           autofocus>
                                    <div class="form-text">Nombre completo del participante</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label required">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= old('email') ?>" 
                                           placeholder="ejemplo@correo.com"
                                           required>
                                    <div class="form-text">Correo electrónico válido y único</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?= old('telefono') ?>" 
                                           placeholder="Ej: 555-123-4567">
                                    <div class="form-text">Teléfono de contacto (opcional)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edad" class="form-label required">Edad</label>
                                    <input type="number" class="form-control" id="edad" name="edad" 
                                           value="<?= old('edad') ?>" 
                                           min="1" max="120" 
                                           required>
                                    <div class="form-text">Edad del participante (1-120 años)</div>
                                </div>
                            </div>
                            
                            <!-- Columna derecha -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="genero" class="form-label required">Género</label>
                                    <select class="form-select" id="genero" name="genero" required>
                                        <option value="">Seleccionar género...</option>
                                        <option value="M" <?= old('genero') == 'M' ? 'selected' : '' ?>>
                                            <i class="bi bi-gender-male"></i> Masculino
                                        </option>
                                        <option value="F" <?= old('genero') == 'F' ? 'selected' : '' ?>>
                                            <i class="bi bi-gender-female"></i> Femenino
                                        </option>
                                        <option value="Otro" <?= old('genero') == 'Otro' ? 'selected' : '' ?>>
                                            <i class="bi bi-gender-trans"></i> Otro
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="categoria" class="form-label required">Categoría</label>
                                    <select class="form-select" id="categoria" name="categoria" required>
                                        <option value="">Seleccionar categoría...</option>
                                        <option value="5K" <?= old('categoria') == '5K' ? 'selected' : '' ?>>5K (5 kilómetros)</option>
                                        <option value="10K" <?= old('categoria') == '10K' ? 'selected' : '' ?>>10K (10 kilómetros)</option>
                                        <option value="Infantil" <?= old('categoria') == 'Infantil' ? 'selected' : '' ?>>Infantil</option>
                                    </select>
                                    <div class="form-text">Distancia que correrá el participante</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="talla_playera" class="form-label">Talla de playera</label>
                                    <select class="form-select" id="talla_playera" name="talla_playera">
                                        <option value="">Seleccionar talla...</option>
                                        <option value="XS" <?= old('talla_playera') == 'XS' ? 'selected' : '' ?>>XS (Extra Small)</option>
                                        <option value="S" <?= old('talla_playera') == 'S' ? 'selected' : '' ?>>S (Small)</option>
                                        <option value="M" <?= old('talla_playera') == 'M' ? 'selected' : '' ?>>M (Medium)</option>
                                        <option value="L" <?= old('talla_playera') == 'L' ? 'selected' : '' ?>>L (Large)</option>
                                        <option value="XL" <?= old('talla_playera') == 'XL' ? 'selected' : '' ?>>XL (Extra Large)</option>
                                        <option value="XXL" <?= old('talla_playera') == 'XXL' ? 'selected' : '' ?>>XXL (Double Extra Large)</option>
                                    </select>
                                    <div class="form-text">Talla para la playera del evento (opcional)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado del registro</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="pendiente" selected>Pendiente de validación</option>
                                        <option value="validado">Validado (inscripción confirmada)</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                    <div class="form-text">Estado inicial del registro</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between">
                                <a href="/admin/participantes" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Registrar Participante
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        <i class="bi bi-info-circle"></i> 
                        Los campos marcados con * son obligatorios. El sistema validará que el email sea único.
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validación básica del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const edad = document.getElementById('edad').value;
            if (edad < 1 || edad > 120) {
                alert('La edad debe estar entre 1 y 120 años');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>