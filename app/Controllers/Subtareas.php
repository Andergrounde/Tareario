<?php

namespace App\Controllers;

use App\Models\SubtareaModel;
use CodeIgniter\Controller;

class Subtareas extends Controller
{
    protected $subtareaModel;

    public function __construct()
    {
        $this->subtareaModel = new SubtareaModel();
    }

   public function editar($id = null)
{
    if ($id === null) {
        return redirect()->to('/tareas/compartidasConmigo'); 
    }

    $subtarea = $this->subtareaModel->find($id);

    if (!$subtarea) {
        return redirect()->to('/tareas/compartidasConmigo')->with('error', 'Subtarea no encontrada.');
    }

    $data = [
        'subtarea' => $subtarea
    ];

    return view('modificarSubtareas', $data);
}



    public function actualizar()
    {
        $request = \Config\Services::request();

        $id = $request->getPost('id');
        $descripcion = $request->getPost('descripcion');
        $prioridad = $request->getPost('prioridad');
        $fecha_vencimiento = $request->getPost('fecha_vencimiento');

       
        if (empty($id) || empty($descripcion) || empty($prioridad) || empty($fecha_vencimiento)) {
            return redirect()->back()->withInput()->with('error', 'Por favor complete todos los campos.');
        }

        $data = [
            'descripcion' => $descripcion,
            'prioridad' => $prioridad,
            'fecha_vencimiento' => $fecha_vencimiento
        ];

        $actualizado = $this->subtareaModel->update($id, $data);

        if ($actualizado) {
            return redirect()->to('/tareas/compartidas')->with('success', 'Subtarea actualizada correctamente.');
        } else {
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar la subtarea.');
        }
    }
}
