<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\UsuarioModel;
use App\Models\SubtareaModel;
use CodeIgniter\Controller;

class Home extends BaseController
{
    protected $session;
    protected $tareaModel;
    protected $usuarioModel;
    protected $subtareaModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->usuarioModel = new UsuarioModel();
        $this->tareaModel = new TareaModel();
        $this->subtareaModel = new SubtareaModel();
    }

    public function index(): string
{
    $datosUsuarios = $this->usuarioModel->getUsuarios();

    $data = [
        'datosUsuarios' => $datosUsuarios,
        'datosTareas' => [], 
        'mostrar_notificacion' => false, 
        'recordatorios' => [], 
    ];

    if ($this->session->get('isLoggedIn') == TRUE && $this->session->has('user_id')) {
        $loggedInUserId = $this->session->get('user_id');
        $tareasUsuario = $this->tareaModel->getTareasByUserId($loggedInUserId);

        $tareasActivas = [];
        foreach ($tareasUsuario as $tarea) {
            $taskEstado = is_object($tarea) ? ($tarea->estado ?? null) : ($tarea['estado'] ?? null);
            if ($taskEstado !== 'completada') {
                $tareasActivas[] = $tarea;
            }
        }

        foreach ($tareasActivas as &$tarea) {
            $tarea['subtareas'] = $this->subtareaModel->getSubtareasByTareaId($tarea['id']);
        }
        unset($tarea);

        $data['datosTareas'] = $tareasActivas;

        $data['usuario'] = (object)[
            'id' => $loggedInUserId,
            'nombre' => $this->session->get('nombre')
        ];

 
        if (!$this->session->has('recordatorio_mostrado')) {
   
            $recordatorios = $this->tareaModel
                ->where('id', $loggedInUserId)
                ->where('fecha_recordatorio IS NOT NULL')
                ->where('fecha_recordatorio !=', '0000-00-00')
                ->where('fecha_recordatorio <=', date('Y-m-d'))
                ->where('estado !=', 'completada')
                ->findAll();

            if (!empty($recordatorios)) {
                $data['mostrar_notificacion'] = true;
                $data['recordatorios'] = $recordatorios;
                $this->session->set('recordatorio_mostrado', true);
            }
        }
    }

    return view('principal', $data);
}

}
