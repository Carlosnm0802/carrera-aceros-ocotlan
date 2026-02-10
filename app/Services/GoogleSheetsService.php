<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use App\Models\ParticipanteModel;
use CodeIgniter\I18n\Time;

class GoogleSheetsService
{
    protected $client;
    protected $service;
    protected $participanteModel;
    protected $spreadsheetId;
    protected $sheetName;
    
    // Colores para logs
    const COLORS = [
        'success' => "\033[32m", // Verde
        'error' => "\033[31m",   // Rojo
        'info' => "\033[34m",    // Azul
        'warning' => "\033[33m", // Amarillo
        'reset' => "\033[0m"     // Reset
    ];
    
    public function __construct()
    {
        $this->participanteModel = new ParticipanteModel();
        $this->spreadsheetId = getenv('GOOGLE_SHEETS_ID') ?: '';
        $this->sheetName = getenv('GOOGLE_SHEETS_SHEET') ?: 'Form_Responses';
        $this->initializeClient();
    }
    
    /**
     * Inicializa el cliente de Google API
     */
    private function initializeClient()
    {
        try {
            $this->client = new Client();
            
            // Ruta al archivo de credenciales
            $credentialsPath = ROOTPATH . 'google_credentials/service-account.json';
            
            if (!file_exists($credentialsPath)) {
                throw new \Exception('Archivo de credenciales no encontrado: ' . $credentialsPath);
            }
            
            // Configurar cliente
            $this->client->setAuthConfig($credentialsPath);
            $this->client->addScope(Sheets::SPREADSHEETS);
            $this->client->setAccessType('offline');
            
            // Crear servicio
            $this->service = new Sheets($this->client);
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error al inicializar cliente Google Sheets: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene datos de una hoja de cálculo
     */
    public function getSheetData($range = 'A:H')
    {
        try {
            if (!$this->service) {
                throw new \Exception('Servicio no inicializado');
            }
            
            if (empty($this->spreadsheetId)) {
                throw new \Exception('Spreadsheet ID no configurado');
            }
            
            $response = $this->service->spreadsheets_values->get(
                $this->spreadsheetId,
                $this->buildRange($range)
            );
            return $response->getValues();
            
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            log_message('error', 'Error al obtener datos de Google Sheets: ' . $msg);

            // Fallback: si es error de rango, intenta con la primera hoja disponible
            if (stripos($msg, 'Unable to parse range') !== false) {
                try {
                    $meta = $this->service->spreadsheets->get($this->spreadsheetId);
                    $sheets = $meta->getSheets();
                    if (!empty($sheets)) {
                        $first = $sheets[0]->getProperties()->getTitle();
                        $safeSheet = str_replace("'", "''", $first);
                        $fallbackRange = "'{$safeSheet}'!{$range}";
                        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $fallbackRange);
                        return $response->getValues();
                    }
                } catch (\Exception $e2) {
                    log_message('error', 'Fallback Sheets failed: ' . $e2->getMessage());
                }
            }

            return [];
        }
    }
    
    /**
     * Sincroniza datos desde Google Sheets a la base de datos
     */
    public function syncFromSheets($range = 'A:H')
    {
        $rows = $this->getSheetData($range);
        
        if (empty($rows)) {
            return [
                'success' => false,
                'message' => 'No se encontraron datos en la hoja',
                'imported' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [],
                'total_rows' => 0
            ];
        }
        
        // Remover encabezados (primera fila) y normalizar espacios
        $headers = array_map('trim', array_shift($rows));
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        
        foreach ($rows as $index => $row) {
            try {
                // Asegurar que la fila tenga suficientes columnas
                $row = array_pad($row, count($headers), '');
                
                // Mapear columnas
                $data = array_combine($headers, $row);
                
                // Normalizar datos
                $participanteData = $this->normalizeData($data);
                
                if (!$this->validateParticipanteData($participanteData)) {
                    $skipped++;
                    $errors[] = "Fila " . ($index + 2) . ": Datos inválidos";
                    continue;
                }
                
                // Buscar si ya existe por email
                $existing = $this->participanteModel
                    ->where('email', $participanteData['email'])
                    ->first();
                
                if ($existing) {
                    // Actualizar existente
                    $participanteData['id'] = $existing['id'];
                    $participanteData['updated_at'] = Time::now();
                    
                    if ($this->participanteModel->save($participanteData)) {
                        $updated++;
                    } else {
                        $skipped++;
                        $errors[] = "Fila " . ($index + 2) . ": Error al actualizar";
                    }
                } else {
                    // Insertar nuevo
                    if ($this->participanteModel->insert($participanteData)) {
                        $imported++;
                    } else {
                        $skipped++;
                        $errors[] = "Fila " . ($index + 2) . ": Error al insertar";
                    }
                }
                
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = "Fila " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        return [
            'success' => ($imported + $updated) > 0,
            'message' => sprintf(
                'Sincronización completada: %d nuevos, %d actualizados, %d omitidos',
                $imported,
                $updated,
                $skipped
            ),
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
            'total_rows' => count($rows)
        ];
    }
    
    /**
     * Normaliza datos de Google Sheets a nuestro formato
     */
    private function normalizeData(array $data): array
    {
        // Mapear nombres de columnas (ajusta según tu Google Form)
        $mappedData = [
            'nombre_completo' => $data['Nombre completo'] ?? $data['nombre'] ?? '',
            'email' => strtolower(trim($data['Email'] ?? $data['Correo electrónico'] ?? '')),
            'telefono' => $data['Teléfono'] ?? $data['telefono'] ?? '',
            'edad' => (int)($data['Edad'] ?? $data['edad'] ?? 0),
            'genero' => $this->normalizeGenero($data['Género'] ?? $data['genero'] ?? ''),
            'categoria' => $this->normalizeCategoria($data['Categoría'] ?? $data['categoria'] ?? '5K'),
            'talla_playera' => strtoupper(trim($data['Talla de playera'] ?? $data['talla'] ?? '')),
            'estado' => 'pendiente', // Por defecto pendiente
            'fecha_registro' => Time::now()->toDateTimeString()
        ];
        
        return $mappedData;
    }
    
    /**
     * Normaliza el campo género
     */
    private function normalizeGenero(string $genero): string
    {
        $genero = strtoupper(trim($genero));
        
        if (in_array($genero, ['M', 'MASCULINO', 'HOMBRE', 'MALE'])) {
            return 'M';
        }
        
        if (in_array($genero, ['F', 'FEMENINO', 'MUJER', 'FEMALE'])) {
            return 'F';
        }
        
        return 'Otro';
    }
    
    /**
     * Normaliza la categoría
     */
    private function normalizeCategoria(string $categoria): string
    {
        $categoria = strtoupper(trim($categoria));
        
        if (strpos($categoria, '10') !== false) {
            return '10K';
        }
        
        if (strpos($categoria, '5') !== false) {
            return '5K';
        }
        
        if (strpos($categoria, 'INFANT') !== false) {
            return 'Infantil';
        }
        
        return '5K'; // Por defecto
    }
    
    /**
     * Valida los datos del participante
     */
    private function validateParticipanteData(array $data): bool
    {
        // Validaciones básicas
        if (empty($data['nombre_completo']) || strlen($data['nombre_completo']) < 3) {
            return false;
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        if ($data['edad'] < 1 || $data['edad'] > 120) {
            return false;
        }
        
        return true;
    }

    private function buildRange(string $range): string
    {
        if (strpos($range, '!') !== false) {
            return $range;
        }

        $sheet = trim((string) $this->sheetName);
        if ($sheet === '') {
            return $range;
        }

        // Quote sheet name to handle spaces/special chars or trailing spaces
        $safeSheet = str_replace("'", "''", $sheet);
        return "'{$safeSheet}'!{$range}";
    }
    
    /**
     * Prueba la conexión con Google Sheets
     */
    public function testConnection(): array
    {
        try {
            if (!$this->service) {
                return [
                    'success' => false,
                    'message' => 'Servicio no inicializado'
                ];
            }
            // Obtener nombres de hojas disponibles
            $meta = $this->service->spreadsheets->get($this->spreadsheetId);
            $sheets = array_map(
                fn($s) => $s->getProperties()->getTitle(),
                $meta->getSheets()
            );

            // Intentar leer encabezados de la primera hoja disponible
            $firstSheet = $sheets[0] ?? $this->sheetName;
            $safeSheet = str_replace("'", "''", $firstSheet);
            $range = "'{$safeSheet}'!A1:H1";
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            $data = $response->getValues();

            return [
                'success' => true,
                'message' => 'Conexión exitosa con Google Sheets',
                'headers' => $data[0] ?? [],
                'spreadsheet_id' => $this->spreadsheetId,
                'sheets' => $sheets,
                'used_sheet' => $firstSheet
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtiene estadísticas de la hoja
     */
    public function getSheetStats(): array
    {
        $data = $this->getSheetData();
        
        if (empty($data)) {
            return [
                'total_rows' => 0,
                'headers' => [],
                'last_sync' => null
            ];
        }
        
        return [
            'total_rows' => count($data) - 1, // Excluye encabezados
            'headers' => $data[0] ?? [],
            'sample_data' => array_slice($data, 1, 3), // Primeras 3 filas de datos
            'last_column' => chr(65 + count($data[0]) - 1) // Letra de última columna
        ];
    }
    
    /**
     * Mensaje formateado para CLI
     */
    public function cliMessage(string $message, string $type = 'info'): string
    {
        $color = self::COLORS[$type] ?? self::COLORS['info'];
        $reset = self::COLORS['reset'];
        
        return $color . $message . $reset;
    }
}