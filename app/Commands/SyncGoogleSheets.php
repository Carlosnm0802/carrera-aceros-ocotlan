<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\GoogleSheetsService;

class SyncGoogleSheets extends BaseCommand
{
    protected $group = 'Google Sheets';
    protected $name = 'google:sync';
    protected $description = 'Sincroniza participantes desde Google Sheets';
    
    protected $usage = 'google:sync [--test] [--range=]';
    protected $arguments = [];
    protected $options = [
        'test' => 'Probar conexión sin sincronizar',
        'range' => 'Rango específico de la hoja (ej: A:G)',
    ];
    
    public function run(array $params)
    {
        CLI::write(' Iniciando sincronización con Google Sheets', 'blue');
        CLI::newLine();
        
        $service = new GoogleSheetsService();
        
        // Opción: Probar conexión
        if (isset($params['test']) || CLI::getOption('test')) {
            $this->testConnection($service);
            return;
        }
        
        // Opción: Rango personalizado
        $range = CLI::getOption('range') ?: 'A:G';
        
        CLI::write(' Obteniendo datos de Google Sheets...', 'yellow');
        
        // Sincronizar datos
        $result = $service->syncFromSheets($range);
        
        // Mostrar resultados
        CLI::newLine();
        CLI::write('📋 RESULTADOS DE SINCRONIZACIÓN', 'green');
        CLI::write(str_repeat('─', 40), 'white');
        
        if ($result['success']) {
            CLI::write('✅ ' . $result['message'], 'green');
            CLI::write('📥 Nuevos: ' . $result['imported'], 'white');
            CLI::write('🔄 Actualizados: ' . $result['updated'], 'white');
            CLI::write('⏭️  Omitidos: ' . $result['skipped'], 'white');
            CLI::write('📈 Total filas procesadas: ' . $result['total_rows'], 'white');
        } else {
            CLI::write('❌ ' . $result['message'], 'red');
        }
        
        // Mostrar errores si existen
        if (!empty($result['errors'])) {
            CLI::newLine();
            CLI::write('⚠️  ERRORES ENCONTRADOS:', 'yellow');
            foreach ($result['errors'] as $error) {
                CLI::write('   • ' . $error, 'yellow');
            }
        }
        
        CLI::newLine();
        CLI::write(' Sincronización completada', 'blue');
        
        // Guardar log de sincronización
        $this->saveSyncLog($result);
    }
    
    /**
     * Probar conexión con Google Sheets
     */
    private function testConnection(GoogleSheetsService $service)
    {
        CLI::write('🔍 Probando conexión con Google Sheets...', 'yellow');
        
        $result = $service->testConnection();
        
        CLI::newLine();
        
        if ($result['success']) {
            CLI::write('✅ ' . $result['message'], 'green');
            CLI::write('📋 Encabezados encontrados:', 'white');
            
            foreach ($result['headers'] as $index => $header) {
                CLI::write('   ' . ($index + 1) . '. ' . $header, 'white');
            }
            
            CLI::write('🆔 Spreadsheet ID: ' . $result['spreadsheet_id'], 'white');
        } else {
            CLI::write('❌ ' . $result['message'], 'red');
            CLI::write('💡 Verifica:', 'yellow');
            CLI::write('   1. Archivo de credenciales en google_credentials/', 'white');
            CLI::write('   2. Google Sheets API habilitada', 'white');
            CLI::write('   3. Spreadsheet ID en .env', 'white');
            CLI::write('   4. Hoja compartida con el service account', 'white');
        }
    }
    
    /**
     * Guarda un log de la sincronización
     */
    private function saveSyncLog(array $result)
    {
        $logPath = WRITEPATH . 'logs/google-sync.log';
        $logMessage = sprintf(
            "[%s] %s | Nuevos: %d | Actualizados: %d | Omitidos: %d\n",
            date('Y-m-d H:i:s'),
            $result['message'],
            $result['imported'],
            $result['updated'],
            $result['skipped']
        );
        
        file_put_contents($logPath, $logMessage, FILE_APPEND);
    }
}