<?php

namespace App\Controllers;

use App\Services\GoogleSheetsService;
use CodeIgniter\API\ResponseTrait;

class SyncController extends BaseController
{
    use ResponseTrait;
    
    protected $googleSheetsService;
    
    public function __construct()
    {
        $this->googleSheetsService = new GoogleSheetsService();
        helper(['form', 'url']);
    }
    
    /**
     * Página de sincronización manual
     */
    public function index()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
        
        // Probar conexión
        $testResult = $this->googleSheetsService->testConnection();
        $sheetStats = $this->googleSheetsService->getSheetStats();
        
        $data = [
            'title' => 'Sincronización Google Sheets',
            'testResult' => $testResult,
            'sheetStats' => $sheetStats,
            'spreadsheetId' => getenv('GOOGLE_SHEETS_ID'),
            'lastSync' => $this->getLastSyncTime()
        ];
        
        return view('admin/sync/index', $data);
    }
    
    /**
     * Ejecutar sincronización manual
     */
    public function syncNow()
    {
        if (!auth()->loggedIn()) {
            return $this->failUnauthorized('Debe iniciar sesión');
        }
        
        // Verificar si es AJAX
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/sync');
        }
        
        // Obtener rango del formulario
        $range = $this->request->getPost('range') ?: (getenv('GOOGLE_SHEETS_RANGE') ?: 'A:H');
        
        // Ejecutar sincronización
        $result = $this->googleSheetsService->syncFromSheets($range);
        
        // Guardar tiempo de sincronización
        $this->saveSyncTime();
        
        return $this->respond([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => [
                'imported' => $result['imported'],
                'updated' => $result['updated'],
                'skipped' => $result['skipped'],
                'total_rows' => $result['total_rows'] ?? 0
            ]
        ]);
    }
    
    /**
     * Probar conexión
     */
    public function testConnection()
    {
        if (!auth()->loggedIn()) {
            return $this->failUnauthorized('Debe iniciar sesión');
        }
        
        $result = $this->googleSheetsService->testConnection();
        
        return $this->respond([
            'success' => $result['success'],
            'message' => $result['message'],
            'headers' => $result['headers'] ?? []
        ]);
    }
    
    /**
     * Obtener última sincronización
     */
    private function getLastSyncTime()
    {
        $syncFile = WRITEPATH . 'logs/google-sync.log';
        
        if (!file_exists($syncFile)) {
            return null;
        }
        
        $lines = file($syncFile, FILE_IGNORE_NEW_LINES);
        $lastLine = end($lines);
        
        if ($lastLine) {
            preg_match('/\[([^\]]+)\]/', $lastLine, $matches);
            return $matches[1] ?? null;
        }
        
        return null;
    }
    
    /**
     * Guardar tiempo de sincronización
     */
    private function saveSyncTime()
    {
        $syncFile = WRITEPATH . 'logs/sync-time.json';
        $data = [
            'last_sync' => date('Y-m-d H:i:s'),
            'user_id' => auth()->id()
        ];
        
        file_put_contents($syncFile, json_encode($data));
    }
}