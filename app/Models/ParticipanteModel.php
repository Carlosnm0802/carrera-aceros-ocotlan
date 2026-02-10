<?php

namespace App\Models;

use CodeIgniter\Model;

class ParticipanteModel extends Model
{
    protected $table = 'participantes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nombre_completo',
        'email',
        'telefono',
        'edad',
        'genero',
        'categoria',
        'talla_playera',
        'estado',
        'fecha_registro',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function buscar(?string $termino, ?string $estado, ?string $categoria): array
    {
        $builder = $this->builder();

        if (!empty($termino)) {
            $builder->groupStart()
                ->like('nombre_completo', $termino)
                ->orLike('email', $termino)
                ->orLike('telefono', $termino)
                ->groupEnd();
        }

        if (!empty($estado)) {
            $builder->where('estado', $estado);
        }

        if (!empty($categoria)) {
            $builder->where('categoria', $categoria);
        }

        return $builder
            ->orderBy('fecha_registro', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Metodo para obtener estadisticas mejoradas
    public function getEstadisticas(): array
    {
        $db = \Config\Database::connect();

        // Obtener datos base
        $total = $this->countAll();

        // Estadisticas por estado
        $por_estado = $db->table('participantes')
            ->select('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->getResultArray();

        // Estadisticas por categoria
        $por_categoria = $db->table('participantes')
            ->select('categoria, COUNT(*) as total')
            ->groupBy('categoria')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();

        // Estadisticas por genero
        $por_genero = $db->table('participantes')
            ->select('genero, COUNT(*) as total')
            ->groupBy('genero')
            ->get()
            ->getResultArray();

        // Estadisticas por dia (ultimos 7 dias)
        $ultimos_7_dias = $db->table('participantes')
            ->select("DATE(fecha_registro) as fecha, COUNT(*) as total")
            ->where('fecha_registro >=', date('Y-m-d', strtotime('-7 days')))
            ->groupBy("DATE(fecha_registro)")
            ->orderBy('fecha', 'ASC')
            ->get()
            ->getResultArray();

        // Estadisticas por grupo de edad
        $por_edad = $db->table('participantes')
            ->select("
            CASE 
                WHEN edad < 18 THEN 'Menores (0-17)'
                WHEN edad BETWEEN 18 AND 25 THEN 'Jovenes (18-25)'
                WHEN edad BETWEEN 26 AND 35 THEN 'Adultos (26-35)'
                WHEN edad BETWEEN 36 AND 50 THEN 'Adultos (36-50)'
                ELSE 'Mayores (51+)'
            END as grupo_edad,
            COUNT(*) as total
        ")
            ->groupBy('grupo_edad')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();

        // Estadisticas por talla de playera
        $por_talla = $db->table('participantes')
            ->select('talla_playera, COUNT(*) as total')
            ->where('talla_playera IS NOT NULL')
            ->where('talla_playera !=', '')
            ->groupBy('talla_playera')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();

        // Participantes recientes
        $recientes = $this->orderBy('fecha_registro', 'DESC')
            ->findAll(10);

        return [
            'total' => $total,
            'por_estado' => $por_estado,
            'por_categoria' => $por_categoria,
            'por_genero' => $por_genero,
            'ultimos_7_dias' => $ultimos_7_dias,
            'por_edad' => $por_edad,
            'por_talla' => $por_talla,
            'recientes' => $recientes,

            // Datos formateados para graficos
            'datos_grafico_categoria' => [
                'labels' => array_column($por_categoria, 'categoria'),
                'data' => array_column($por_categoria, 'total'),
            ],
            'datos_grafico_estado' => [
                'labels' => array_column($por_estado, 'estado'),
                'data' => array_column($por_estado, 'total'),
            ],
            'datos_grafico_genero' => [
                'labels' => array_column($por_genero, 'genero'),
                'data' => array_column($por_genero, 'total'),
            ],
            'datos_grafico_ultimos_dias' => [
                'labels' => array_column($ultimos_7_dias, 'fecha'),
                'data' => array_column($ultimos_7_dias, 'total'),
            ],
            'datos_grafico_edad' => [
                'labels' => array_column($por_edad, 'grupo_edad'),
                'data' => array_column($por_edad, 'total'),
            ],
        ];
    }
    /**
 * Métricas avanzadas para dashboard
 */
public function getMetricasAvanzadas()
{
    $db = \Config\Database::connect();
    
    // Estadísticas por hora del día
    $inscripciones_por_hora = $db->table('participantes')
        ->select("HOUR(fecha_registro) as hora, COUNT(*) as total")
        ->where('fecha_registro >=', date('Y-m-d 00:00:00', strtotime('-7 days')))
        ->groupBy('HOUR(fecha_registro)')
        ->orderBy('hora', 'ASC')
        ->get()
        ->getResultArray();
    
    // Tendencias semanales
    $tendencias_semanales = $db->table('participantes')
        ->select("
            YEARWEEK(fecha_registro, 1) as semana,
            DATE_FORMAT(MIN(fecha_registro), '%d/%m/%Y') as fecha_inicio,
            DATE_FORMAT(MAX(fecha_registro), '%d/%m/%Y') as fecha_fin,
            COUNT(*) as total,
            SUM(CASE WHEN estado = 'validado' THEN 1 ELSE 0 END) as validados,
            SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes
        ")
        ->where('fecha_registro >=', date('Y-m-d', strtotime('-30 days')))
        ->groupBy('YEARWEEK(fecha_registro, 1)')
        ->orderBy('semana', 'DESC')
        ->limit(4)
        ->get()
        ->getResultArray();
    
    // Estadísticas por edad y categoría
    $edad_por_categoria = $db->table('participantes')
        ->select("
            categoria,
            ROUND(AVG(edad), 1) as edad_promedio,
            MIN(edad) as edad_minima,
            MAX(edad) as edad_maxima,
            COUNT(*) as total
        ")
        ->groupBy('categoria')
        ->orderBy('categoria', 'ASC')
        ->get()
        ->getResultArray();
    
    // Participantes por talla y género
    $talla_por_genero = $db->table('participantes')
        ->select("
            genero,
            talla_playera,
            COUNT(*) as total
        ")
        ->where('talla_playera IS NOT NULL')
        ->where('talla_playera !=', '')
        ->groupBy('genero, talla_playera')
        ->orderBy('genero', 'ASC')
        ->orderBy('total', 'DESC')
        ->get()
        ->getResultArray();
    
    // Crecimiento diario
    $crecimiento_diario = $db->table('participantes')
        ->select("
            DATE(fecha_registro) as fecha,
            COUNT(*) as inscritos_dia,
            (SELECT COUNT(*) FROM participantes p2 
             WHERE DATE(p2.fecha_registro) <= DATE(fecha_registro)) as acumulado
        ")
        ->where('fecha_registro >=', date('Y-m-d', strtotime('-14 days')))
        ->groupBy('DATE(fecha_registro)')
        ->orderBy('fecha', 'ASC')
        ->get()
        ->getResultArray();
    
    // Métricas de rendimiento (últimas 24 horas)
    $ultimas_24h = $db->table('participantes')
        ->select("
            HOUR(fecha_registro) as hora,
            COUNT(*) as total
        ")
        ->where('fecha_registro >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
        ->groupBy('HOUR(fecha_registro)')
        ->orderBy('hora', 'ASC')
        ->get()
        ->getResultArray();
    
    // Previsión de inscripciones (simple)
    $total_por_dia = $db->table('participantes')
        ->select("
            DAYNAME(fecha_registro) as dia_semana,
            COUNT(*) as total
        ")
        ->groupBy('DAYNAME(fecha_registro)')
        ->orderBy('FIELD(dia_semana, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")')
        ->get()
        ->getResultArray();
    
    return [
        'inscripciones_por_hora' => $inscripciones_por_hora,
        'tendencias_semanales' => $tendencias_semanales,
        'edad_por_categoria' => $edad_por_categoria,
        'talla_por_genero' => $talla_por_genero,
        'crecimiento_diario' => $crecimiento_diario,
        'ultimas_24h' => $ultimas_24h,
        'total_por_dia' => $total_por_dia,
        
        // Datos formateados para gráficos
        'datos_hora_chart' => [
            'labels' => array_map(function($h) { 
                return sprintf('%02d:00', $h['hora']); 
            }, $inscripciones_por_hora),
            'data' => array_column($inscripciones_por_hora, 'total')
        ],
        'datos_tendencia_chart' => [
            'labels' => array_map(function($t) { 
                return 'Sem ' . substr($t['semana'], 4); 
            }, $tendencias_semanales),
            'data_total' => array_column($tendencias_semanales, 'total'),
            'data_validados' => array_column($tendencias_semanales, 'validados')
        ],
        'datos_crecimiento_chart' => [
            'labels' => array_map(function($c) { 
                return date('d/m', strtotime($c['fecha'])); 
            }, $crecimiento_diario),
            'inscritos' => array_column($crecimiento_diario, 'inscritos_dia'),
            'acumulado' => array_column($crecimiento_diario, 'acumulado')
        ]
    ];
}

/**
 * Obtener reporte personalizado
 */
public function getReportePersonalizado($filtros = [])
{
    $builder = $this->builder();
    
    // Aplicar filtros
    if (!empty($filtros['fecha_inicio'])) {
        $builder->where('DATE(fecha_registro) >=', $filtros['fecha_inicio']);
    }
    
    if (!empty($filtros['fecha_fin'])) {
        $builder->where('DATE(fecha_registro) <=', $filtros['fecha_fin']);
    }
    
    if (!empty($filtros['estado'])) {
        $builder->where('estado', $filtros['estado']);
    }
    
    if (!empty($filtros['categoria'])) {
        $builder->where('categoria', $filtros['categoria']);
    }
    
    if (!empty($filtros['genero'])) {
        $builder->where('genero', $filtros['genero']);
    }
    
    if (!empty($filtros['edad_min']) && is_numeric($filtros['edad_min'])) {
        $builder->where('edad >=', $filtros['edad_min']);
    }
    
    if (!empty($filtros['edad_max']) && is_numeric($filtros['edad_max'])) {
        $builder->where('edad <=', $filtros['edad_max']);
    }
    
    // Ordenar
    $orden = !empty($filtros['orden']) ? $filtros['orden'] : 'fecha_registro DESC';
    $builder->orderBy($orden);
    
    // Límite
    $limite = !empty($filtros['limite']) ? (int)$filtros['limite'] : 1000;
    
    return $builder->get($limite)->getResultArray();
    }
}