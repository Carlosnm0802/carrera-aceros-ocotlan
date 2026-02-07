<?php

namespace App\Controllers;

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
        
        return view('admin/dashboard', [
            'title' => 'Dashboard - Carrera Aceros'
        ]);
    }
}