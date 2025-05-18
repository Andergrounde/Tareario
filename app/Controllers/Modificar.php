<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\SubtareaModel;
use App\Models\UsuarioModel; // Importar el modelo de Usuario
use App\Models\ColaboracionModel; // Importar el modelo de Colaboracion
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait; // Import ResponseTrait for JSON responses
use CodeIgniter\Events\Events; // Import Events class for logging queries

class Modificar extends Controller{
    use ResponseTrait; // Use ResponseTrait

    protected $tareaModel;
    protected $subtareaModel;
    protected $usuarioModel; // Propiedad para el modelo de Usuario
    protected $colaboracionModel; // Propiedad para el modelo de Colaboracion
    protected $session;
    // Carga los helpers necesarios declarando la propiedad $helpers
    protected $helpers = ['form', 'url'];




public function inicio($id)
{
    $tareaModel = new TareaModel();
    $subtareaModel = new subtareaModel();

    $tarea = $tareaModel->find($id);

    if (!$tarea) {
        return redirect()->to('/')->with('error', 'Tarea no encontrada');
    }

    $subtareas = $subtareaModel->getSubtareasByTareaId($id);

    return view('modificar', [
        'tarea' => $tarea,
        'subtareas' => $subtareas
    ]);
}

public function inicioS($id)
{
    $tareaModel = new TareaModel();
    $subtareaModel = new subtareaModel();

    $tarea = $tareaModel->find($id);

    if (!$tarea) {
        return redirect()->to('/')->with('error', 'Tarea no encontrada');
    }

    $subtareas = $subtareaModel->getSubtareasByTareaId($id);

    return view('modificar', [
        'tarea' => $tarea,
        'subtareas' => $subtareas
    ]);
}

    public function guardar($id)
    {
        $modelo = new TareaModel();

        // Validamos si la tarea existe
        $tareaExistente = $modelo->find($id);
        if (!$tareaExistente) {
            return redirect()->to('/')->with('error', 'Tarea no encontrada');
        }

        // Validación
        $validation = \Config\Services::validation();
        $validation->setRules([
            'asunto' => 'required|min_length[3]',
            'descripcion' => 'required|min_length[5]',
            'prioridad' => 'required|in_list[baja,normal,alta]',
            'fecha_vencimiento' => 'permit_empty|valid_date[Y-m-d]',
            'fecha_recordatorio' => 'permit_empty|valid_date[Y-m-d]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        // Guardamos los datos
        $datos = [
            'asunto' => $this->request->getPost('asunto'),
            'descripcion' => $this->request->getPost('descripcion'),
            'prioridad' => $this->request->getPost('prioridad'),
            'fecha_vencimiento' => $this->request->getPost('fecha_vencimiento'),
            'fecha_recordatorio' => $this->request->getPost('fecha_recordatorio'),
        ];

        $modelo->update($id, $datos);

        return redirect()->to(site_url('modificar/inicio/' . $id))->with('success', 'Tarea actualizada correctamente');

    }

    
    public function guardar_subtarea($id = null)
{
    if ($id === null) {
        return redirect()->back()->with('error', 'ID de subtarea no proporcionado.');
    }

    $subtareaModel = new SubtareaModel();

    // Obtener los datos del formulario
    $descripcion = $this->request->getPost('descripcion');
    $prioridad = $this->request->getPost('prioridad');
    $fecha_vencimiento = $this->request->getPost('fecha_vencimiento');

    // Validar si al menos uno fue enviado (opcional)
    if (!$descripcion && !$prioridad && !$fecha_vencimiento) {
        return redirect()->back()->with('error', 'No se enviaron datos para actualizar.');
    }

    // Crear array solo con datos válidos (evita sobrescribir con nulos)
    $datos = [];
    if ($descripcion !== null) $datos['descripcion'] = $descripcion;
    if ($prioridad !== null) $datos['prioridad'] = $prioridad;
    if ($fecha_vencimiento !== null) $datos['fecha_vencimiento'] = $fecha_vencimiento;

    // Ejecutar update
    $subtareaModel->update($id, $datos);

    return redirect()->to(site_url('modificar/inicio/' . $id))->with('success', 'Subtarea actualizada correctamente');

}


    


}