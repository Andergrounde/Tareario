<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\SubtareaModel;
use CodeIgniter\Controller;

class Aviso extends Controller
{
    public function revisar()
    {
        $session = session();
        $usuario_id = $session->get('usuario_id'); 

        $tareaModel = new TareaModel();
        $subtareaModel = new SubtareaModel();

        $tareas = $tareaModel->where('usuario_id', $usuario_id)->findAll();
        $avisos = [];

        $notificadas = $session->get('avisos_mostrados') ?? [];

        foreach ($tareas as $tarea) {
            if (empty($tarea['fecha_vencimiento'])) continue;

            $hoy = new \DateTime();
            $vencimiento = new \DateTime($tarea['fecha_vencimiento']);
            $dias_faltantes = (int)$hoy->diff($vencimiento)->format('%r%a');

            $clave = $tarea['id'] . '_' . $dias_faltantes;
            if (in_array($clave, $notificadas)) continue;

            if (in_array($dias_faltantes, [7, 3, 1])) {
                $avisos[] = [
                    'id' => $tarea['id'],
                    'asunto' => $tarea['asunto'],
                    'dias_faltantes' => $dias_faltantes,
                    'fecha_vencimiento' => $tarea['fecha_vencimiento']
                ];
                $notificadas[] = $clave;
            }
        }

        $session->set('avisos_mostrados', $notificadas);

        return $this->response->setJSON($avisos);
    }
}
