<?php

namespace App\Controllers;

use App\Models\ParticipanteModel;

class AdminController extends BaseController
{
    public function index()
    {
        return redirect()->to('/admin/dashboard');
    }
    
   public function dashboard()
    {
    // Verificar autenticación
    if (!auth()->loggedIn()) {
        return redirect()->to('/login');
    }
    
    $participanteModel = new ParticipanteModel();
    
    $data = [
        'title' => 'Dashboard - Carrera Aceros',
        'estadisticas' => $participanteModel->getEstadisticas(),
        'metricas_avanzadas' => $participanteModel->getMetricasAvanzadas()
    ];
     return view('admin/dashboard', $data);
     }
}