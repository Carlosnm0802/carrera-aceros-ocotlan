<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $users = auth()->getProvider();
        
        $user = new User([
            'username' => 'admin',
            'email'    => 'admin@carreraceros.com',
            'password' => 'Admin123!',
        ]);
        $users->save($user);
        
        $user = $users->findById($users->getInsertID());
        $user->addGroup('superadmin');
        $user->activate();
        
        echo "✅ Usuario admin creado:\n";
        echo "   Email: admin@carreraceros.com\n";
        echo "   Password: Admin123!\n";
    }
}