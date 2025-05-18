<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Form extends Controller
{
    public function __construct()
    {
        helper(['form', 'url']);
    }

    public function index()
    {
        helper('form');
        return view('regist'); 
    }

    public function exito()
    {
        helper('form');
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nombre'   => 'required|min_length[3]|max_length[12]|alpha_numeric_space',
            'email'    => 'required|valid_email|is_unique[usuarios.email]',
            'password' => 'required|min_length[1]',
        ], [
            'nombre' => [
                'required'    => 'El nombre es obligatorio',
                'min_length'  => 'Mínimo 3 caracteres',
                'alpha_numeric_space' => 'Solo letras, números y espacios'
            ],
            'email' => [
                'required'    => 'El email es obligatorio',
                'valid_email' => 'Formato inválido',
                'is_unique'   => 'Este email ya está registrado'
            ],
            'password' => [
                'required'    => 'La contraseña es obligatoria',
                'min_length'  => 'Mínimo 1 caracteres'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Si pasa la validación, se muestran los datos
        $data = [
            'nombre'   => $this->request->getPost('nombre'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        return view('formsuccess', $data);
    }
}
