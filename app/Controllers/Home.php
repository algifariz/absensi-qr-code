<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (auth()->loggedIn()) {
            return redirect()->to('admin/dashboard');
        }

        return redirect()->to('login');
    }
}
