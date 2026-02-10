<?php

namespace App\Controllers;

use App\Models\ParticipanteModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReporteController extends BaseController
{
    protected $participanteModel;
    
    public function __construct()
    {
        $this->participanteModel = new ParticipanteModel();
        helper(['form', 'url']);
    }
    
    /**
     * Vista principal de reportes
     */
    public function index()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
        
        $data = [
            'title' => 'Reportes Avanzados',
            'total_participantes' => $this->participanteModel->countAll(),
            'categorias' => ['5K', '10K', 'Infantil'],
            'estados' => ['pendiente', 'validado', 'cancelado'],
            'generos' => ['M', 'F', 'Otro']
        ];
        
        return view('admin/reportes/index', $data);
    }
    
    /**
     * Generar reporte con filtros
     */
    public function generar()
    {
        if (!auth()->loggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No autenticado'
            ]);
        }
        
        // Obtener filtros del formulario
        $filtros = [
            'fecha_inicio' => $this->request->getPost('fecha_inicio'),
            'fecha_fin' => $this->request->getPost('fecha_fin'),
            'estado' => $this->request->getPost('estado'),
            'categoria' => $this->request->getPost('categoria'),
            'genero' => $this->request->getPost('genero'),
            'edad_min' => $this->request->getPost('edad_min'),
            'edad_max' => $this->request->getPost('edad_max'),
            'orden' => $this->request->getPost('orden'),
            'limite' => $this->request->getPost('limite')
        ];
        
        // Obtener datos filtrados
        $datos = $this->participanteModel->getReportePersonalizado($filtros);
        
        // Estadísticas del reporte
        $estadisticas = [
            'total' => count($datos),
            'por_estado' => [],
            'por_categoria' => [],
            'por_genero' => [],
            'edad_promedio' => 0,
            'edad_minima' => 0,
            'edad_maxima' => 0
        ];
        
        if (!empty($datos)) {
            // Calcular estadísticas
            $edades = array_column($datos, 'edad');
            $estadisticas['edad_promedio'] = round(array_sum($edades) / count($edades), 1);
            $estadisticas['edad_minima'] = min($edades);
            $estadisticas['edad_maxima'] = max($edades);
            
            // Contar por estado
            $estados = array_column($datos, 'estado');
            $estadisticas['por_estado'] = array_count_values($estados);
            
            // Contar por categoría
            $categorias = array_column($datos, 'categoria');
            $estadisticas['por_categoria'] = array_count_values($categorias);
            
            // Contar por género
            $generos = array_column($datos, 'genero');
            $estadisticas['por_genero'] = array_count_values($generos);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'participantes' => $datos,
                'estadisticas' => $estadisticas,
                'filtros' => $filtros,
                'fecha_generacion' => date('d/m/Y H:i:s')
            ]
        ]);
    }
    
    /**
     * Exportar reporte a Excel
     */
    public function exportarExcel()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
        
        // Obtener filtros
        $filtros = $this->request->getGet();
        $datos = $this->participanteModel->getReportePersonalizado($filtros);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Título
        $sheet->setCellValue('A1', 'REPORTE DE PARTICIPANTES - CARRERA ACEROS OCOTLÁN');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Información del reporte
        $sheet->setCellValue('A2', 'Fecha de generación: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getFont()->setItalic(true);
        
        // Filtros aplicados
        $fila = 3;
        if (!empty($filtros)) {
            $sheet->setCellValue('A' . $fila, 'Filtros aplicados:');
            $fila++;
            
            foreach ($filtros as $key => $value) {
                if (!empty($value)) {
                    $sheet->setCellValue('A' . $fila, ucfirst($key) . ': ' . $value);
                    $fila++;
                }
            }
            $fila++;
        }
        
        // Encabezados de la tabla
        $headers = [
            'ID', 'Nombre Completo', 'Email', 'Teléfono', 
            'Edad', 'Género', 'Categoría', 'Talla', 
            'Estado', 'Fecha Registro'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $fila, $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        
        // Estilo encabezados
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2c3e50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A' . $fila . ':J' . $fila)->applyFromArray($headerStyle);
        
        $fila++;
        
        // Datos
        foreach ($datos as $participante) {
            $sheet->setCellValue('A' . $fila, $participante['id']);
            $sheet->setCellValue('B' . $fila, $participante['nombre_completo']);
            $sheet->setCellValue('C' . $fila, $participante['email']);
            $sheet->setCellValue('D' . $fila, $participante['telefono']);
            $sheet->setCellValue('E' . $fila, $participante['edad']);
            $sheet->setCellValue('F' . $fila, $this->getGeneroTexto($participante['genero']));
            $sheet->setCellValue('G' . $fila, $participante['categoria']);
            $sheet->setCellValue('H' . $fila, $participante['talla_playera']);
            $sheet->setCellValue('I' . $fila, ucfirst($participante['estado']));
            $sheet->setCellValue('J' . $fila, $participante['fecha_registro']);
            $fila++;
        }
        
        // Resumen estadístico
        $fila += 2;
        $sheet->setCellValue('A' . $fila, 'RESUMEN ESTADÍSTICO');
        $sheet->mergeCells('A' . $fila . ':B' . $fila);
        $sheet->getStyle('A' . $fila)->getFont()->setBold(true);
        
        $fila++;
        $sheet->setCellValue('A' . $fila, 'Total de participantes:');
        $sheet->setCellValue('B' . $fila, count($datos));
        
        // Generar archivo
        $filename = 'reporte_participantes_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
    
    /**
     * Exportar reporte a PDF
     */
public function exportarPDF()
{
    if (!auth()->loggedIn()) {
        return redirect()->to('/login');
    }
    
    // Redirigir a CSV mientras se deshabilita PDF
    $query = $this->request->getGet();
    return redirect()->to('/admin/reportes/exportar-csv' . (empty($query) ? '' : ('?' . http_build_query($query))));
}

    /**
     * Exportar reporte a CSV
     */
    public function exportarCSV()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
        
        // Obtener filtros
        $filtros = $this->request->getGet();
        $datos = $this->participanteModel->getReportePersonalizado($filtros);
        
        // Encabezados del CSV
        $headers = [
            'ID', 'Nombre Completo', 'Email', 'Teléfono',
            'Edad', 'Género', 'Categoría', 'Talla',
            'Estado', 'Fecha Registro'
        ];
        
        $filename = 'reporte_participantes_' . date('Y-m-d_His') . '.csv';
        
        // Enviar cabeceras
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        // Añadir BOM para compatibilidad con Excel
        echo "\xEF\xBB\xBF";
        
        // Escribir encabezados
        fputcsv($output, $headers);
        
        // Escribir datos
        foreach ($datos as $p) {
            fputcsv($output, [
                $p['id'],
                $p['nombre_completo'],
                $p['email'],
                $p['telefono'],
                $p['edad'],
                $this->getGeneroTexto($p['genero']),
                $p['categoria'],
                $p['talla_playera'],
                ucfirst($p['estado']),
                $p['fecha_registro'],
            ]);
        }
        
        fclose($output);
        exit();
    }
    
    /**
     * Generar HTML para el reporte PDF
     */
    private function generarHTMLReporte($datos, $filtros)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Participantes</title>
            <style>
                body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; }
                h1 { color: #2c3e50; text-align: center; margin-bottom: 5px; }
                .subtitle { text-align: center; color: #7f8c8d; margin-bottom: 20px; }
                .info-box { background-color: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
                .filtros { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th { background-color: #2c3e50; color: white; padding: 10px; text-align: left; }
                td { padding: 8px; border-bottom: 1px solid #ddd; }
                tr:nth-child(even) { background-color: #f8f9fa; }
                .badge { padding: 3px 8px; border-radius: 3px; font-size: 11px; }
                .badge-success { background-color: #28a745; color: white; }
                .badge-warning { background-color: #ffc107; color: white; }
                .badge-danger { background-color: #dc3545; color: white; }
                .resumen { margin-top: 30px; padding: 15px; background-color: #e9ecef; border-radius: 5px; }
                .footer { margin-top: 30px; text-align: center; color: #6c757d; font-size: 11px; }
            </style>
        </head>
        <body>
            <h1>REPORTE DE PARTICIPANTES</h1>
            <div class="subtitle">Carrera Aceros Ocotlán - Generado el ' . date('d/m/Y H:i:s') . '</div>
            
            <div class="info-box">
                <strong>Información del Reporte</strong><br>
                Total de registros: ' . count($datos) . '<br>';
        
        if (!empty($filtros)) {
            $html .= '<br><strong>Filtros aplicados:</strong><br>';
            foreach ($filtros as $key => $value) {
                if (!empty($value)) {
                    $html .= ucfirst($key) . ': ' . htmlspecialchars($value) . '<br>';
                }
            }
        }
        
        $html .= '</div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Edad</th>
                        <th>Género</th>
                        <th>Categoría</th>
                        <th>Talla</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($datos as $p) {
            $estadoClass = $p['estado'] == 'validado' ? 'badge-success' : 
                          ($p['estado'] == 'pendiente' ? 'badge-warning' : 'badge-danger');
            
            $html .= '
                    <tr>
                        <td>' . $p['id'] . '</td>
                        <td>' . htmlspecialchars($p['nombre_completo']) . '</td>
                        <td>' . htmlspecialchars($p['email']) . '</td>
                        <td>' . htmlspecialchars($p['telefono']) . '</td>
                        <td>' . $p['edad'] . '</td>
                        <td>' . $this->getGeneroTexto($p['genero']) . '</td>
                        <td>' . htmlspecialchars($p['categoria']) . '</td>
                        <td>' . htmlspecialchars($p['talla_playera']) . '</td>
                        <td><span class="badge ' . $estadoClass . '">' . ucfirst($p['estado']) . '</span></td>
                        <td>' . date('d/m/Y H:i', strtotime($p['fecha_registro'])) . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                Sistema de Gestión de Inscripciones - Carrera Aceros Ocotlán<br>
                Página {PAGENO} de {nbpg}
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Convertir código de género a texto
     */
    private function getGeneroTexto($genero)
    {
        $generos = [
            'M' => 'Masculino',
            'F' => 'Femenino',
            'Otro' => 'Otro'
        ];
        
        return $generos[$genero] ?? $genero;
    }
}