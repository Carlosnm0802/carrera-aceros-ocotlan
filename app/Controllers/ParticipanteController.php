<?php

namespace App\Controllers;

use App\Models\ParticipanteModel;

class ParticipanteController extends BaseController
{
    // Instancia del modelo
    protected $participanteModel;
    
    // Constructor: se ejecuta automáticamente
    public function __construct()
    {
        $this->participanteModel = new ParticipanteModel();
        helper(['form', 'url']); // Carga helpers para formularios y URLs
    }
    
    // Listar todos los participantes
public function index()
{
    // Verificar autenticación
    if (!auth()->loggedIn()) {
        return redirect()->to('/login');
    }
    
    // Obtener parámetros de búsqueda
    $termino = $this->request->getGet('q');
    $estado = $this->request->getGet('estado');
    $categoria = $this->request->getGet('categoria');
    
    // Inicializar modelo
    $participantes = [];
    
    // Aplicar búsqueda si hay parámetros
    if (!empty($termino) || !empty($estado) || !empty($categoria)) {
        $participantes = $this->participanteModel->buscar($termino, $estado, $categoria);
    } else {
        // Obtener todos ordenados por fecha más reciente
        $participantes = $this->participanteModel
            ->orderBy('fecha_registro', 'DESC')
            ->findAll();
    }
    
    $data = [
        'title' => 'Participantes Inscritos',
        'participantes' => $participantes,
        'termino' => $termino,
        'estado_filtro' => $estado,
        'categoria_filtro' => $categoria,
        'total_resultados' => count($participantes)
    ];
    
    return view('admin/participantes/index', $data);
}
    
    // Mostrar formulario para crear nuevo participante
    public function create()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
        
        $data = [
            'title' => 'Nuevo Participante',
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/participantes/create', $data);
    }
    
    // Guardar nuevo participante
    public function store()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
        
        // Reglas de validación
        $rules = [
            'nombre_completo' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|is_unique[participantes.email]',
            'edad' => 'required|numeric|greater_than[0]|less_than[120]',
            'genero' => 'required|in_list[M,F,Otro]',
            'categoria' => 'required'
        ];
        
        // Validar los datos
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Preparar datos para guardar
        $data = [
            'nombre_completo' => $this->request->getPost('nombre_completo'),
            'email' => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'edad' => $this->request->getPost('edad'),
            'genero' => $this->request->getPost('genero'),
            'categoria' => $this->request->getPost('categoria'),
            'talla_playera' => $this->request->getPost('talla_playera'),
            'estado' => $this->request->getPost('estado') ?? 'pendiente',
            'fecha_registro' => date('Y-m-d H:i:s')
        ];
        
        // Intentar guardar
        try {
            if ($this->participanteModel->save($data)) {
                return redirect()->to('/admin/participantes')
                    ->with('success', 'Participante registrado exitosamente');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error al registrar participante');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    // Mostrar formulario para editar participante
    public function edit($id)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
        
        // Buscar participante por ID
        $participante = $this->participanteModel->find($id);
        
        if (!$participante) {
            return redirect()->to('/admin/participantes')
                ->with('error', 'Participante no encontrado');
        }
        
        $data = [
            'title' => 'Editar Participante',
            'participante' => $participante,
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/participantes/edit', $data);
    }
    
    // Actualizar participante existente
    public function update($id)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
        
        // Reglas de validación (permite el mismo email para este ID específico)
        $rules = [
            'nombre_completo' => 'required|min_length[3]|max_length[255]',
            'email' => "required|valid_email|is_unique[participantes.email,id,$id]",
            'edad' => 'required|numeric|greater_than[0]|less_than[120]',
            'genero' => 'required|in_list[M,F,Otro]',
            'categoria' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Preparar datos para actualizar
        $data = [
            'id' => $id,
            'nombre_completo' => $this->request->getPost('nombre_completo'),
            'email' => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'edad' => $this->request->getPost('edad'),
            'genero' => $this->request->getPost('genero'),
            'categoria' => $this->request->getPost('categoria'),
            'talla_playera' => $this->request->getPost('talla_playera'),
            'estado' => $this->request->getPost('estado')
        ];
        
        // Intentar actualizar
        try {
            if ($this->participanteModel->save($data)) {
                return redirect()->to('/admin/participantes')
                    ->with('success', 'Participante actualizado exitosamente');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error al actualizar participante');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    // Eliminar participante (soft delete)
    public function delete($id)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
        
        if ($this->participanteModel->delete($id)) {
            return redirect()->to('/admin/participantes')
                ->with('success', 'Participante eliminado exitosamente');
        } else {
            return redirect()->to('/admin/participantes')
                ->with('error', 'Error al eliminar participante');
        }
    }
}