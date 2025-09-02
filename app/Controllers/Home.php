<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        $auth = service('authentication');

        if ($auth->check()) {
            return redirect()->to('/admin/dashboard');
        }

        // Simple redirect to login page
        return redirect()->to('/login');
    }
}
