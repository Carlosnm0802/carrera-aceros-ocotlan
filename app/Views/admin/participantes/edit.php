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
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0">
                        <i class="bi bi-person-check"></i> <?= $title ?>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill"></i> 
                        Editando participante: <strong>#<?= $participante['id'] ?> - <?= esc($participante['nombre_completo']) ?></strong>
                    </div>
                    
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
                    
                    <form method="post" action="/admin/participantes/update/<?= $participante['id'] ?>" class="mt-3">
                        <div class="row">
                            <!-- Columna izquierda -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre_completo" class="form-label required">Nombre completo</label>
                                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                                           value="<?= old('nombre_completo', $participante['nombre_completo']) ?>" 
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label required">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= old('email', $participante['email']) ?>" 
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?= old('telefono', $participante['telefono']) ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edad" class="form-label required">Edad</label>
                                    <input type="number" class="form-control" id="edad" name="edad" 
                                           value="<?= old('edad', $participante['edad']) ?>" 
                                           min="1" max="120" 
                                           required>
                                </div>
                            </div>
                            
                            <!-- Columna derecha -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="genero" class="form-label required">Género</label>
                                    <select class="form-select" id="genero" name="genero" required>
                                        <option value="M" <?= old('genero', $participante['genero']) == 'M' ? 'selected' : '' ?>>Masculino</option>
                                        <option value="F" <?= old('genero', $participante['genero']) == 'F' ? 'selected' : '' ?>>Femenino</option>
                                        <option value="Otro" <?= old('genero', $participante['genero']) == 'Otro' ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="categoria" class="form-label required">Categoría</label>
                                    <select class="form-select" id="categoria" name="categoria" required>
                                        <option value="5K" <?= old('categoria', $participante['categoria']) == '5K' ? 'selected' : '' ?>>5K</option>
                                        <option value="10K" <?= old('categoria', $participante['categoria']) == '10K' ? 'selected' : '' ?>>10K</option>
                                        <option value="Infantil" <?= old('categoria', $participante['categoria']) == 'Infantil' ? 'selected' : '' ?>>Infantil</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="talla_playera" class="form-label">Talla de playera</label>
                                    <select class="form-select" id="talla_playera" name="talla_playera">
                                        <option value="">Sin talla especificada</option>
                                        <option value="XS" <?= old('talla_playera', $participante['talla_playera']) == 'XS' ? 'selected' : '' ?>>XS</option>
                                        <option value="S" <?= old('talla_playera', $participante['talla_playera']) == 'S' ? 'selected' : '' ?>>S</option>
                                        <option value="M" <?= old('talla_playera', $participante['talla_playera']) == 'M' ? 'selected' : '' ?>>M</option>
                                        <option value="L" <?= old('talla_playera', $participante['talla_playera']) == 'L' ? 'selected' : '' ?>>L</option>
                                        <option value="XL" <?= old('talla_playera', $participante['talla_playera']) == 'XL' ? 'selected' : '' ?>>XL</option>
                                        <option value="XXL" <?= old('talla_playera', $participante['talla_playera']) == 'XXL' ? 'selected' : '' ?>>XXL</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado del registro</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="pendiente" <?= old('estado', $participante['estado']) == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                        <option value="validado" <?= old('estado', $participante['estado']) == 'validado' ? 'selected' : '' ?>>Validado</option>
                                        <option value="cancelado" <?= old('estado', $participante['estado']) == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información adicional -->
                        <div class="alert alert-secondary mt-3">
                            <small>
                                <strong>Información adicional:</strong><br>
                                • Registrado el: <?= date('d/m/Y H:i', strtotime($participante['fecha_registro'])) ?><br>
                                • Última actualización: <?= date('d/m/Y H:i', strtotime($participante['updated_at'] ?? $participante['created_at'])) ?>
                            </small>
                        </div>
                        
                        <!-- Botones -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between">
                                <a href="/admin/participantes" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                                <div>
                                    <a href="/admin/participantes/delete/<?= $participante['id'] ?>" 
                                       class="btn btn-outline-danger"
                                       onclick="return confirm('¿Estás seguro de eliminar a <?= addslashes($participante['nombre_completo']) ?>?')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-check-circle"></i> Actualizar Participante
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>