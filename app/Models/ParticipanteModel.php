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
}