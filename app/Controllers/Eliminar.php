<?php

namespace App\Controllers;
use App\Models\TareaModel;
use App\Models\SubtareaModel;

class Eliminar extends BaseController
{
    public function confirmar($id)
    {
        $tareaModel = new TareaModel();
        $subtareaModel = new SubtareaModel();

        $tarea = $tareaModel->find($id);
        if (!$tarea) {
            return redirect()->to('/')->with('error', 'Tarea no encontrada.');
        }

        $subtareas = $subtareaModel->getSubtareasByTareaId($id);

        return view('eliminar', [
            'tarea' => $tarea,
            'subtareas' => $subtareas,
        ]);
    }

    public function tarea($id)
    {
        $tareaModel = new TareaModel();
        $tarea = $tareaModel->find($id);

        if (!$tarea) {
            return redirect()->to('/')->with('error', 'Tarea no encontrada.');
        }

        $tareaModel->delete($id); 
        return redirect()->to('/')->with('success', 'Tarea eliminada correctamente.');
    }

    public function subtarea($id)
{
    $subtareaModel = new SubtareaModel();
    $subtarea = $subtareaModel->find($id);

    if (!$subtarea) {
        return redirect()->back()->with('error', 'Subtarea no encontrada.');
    }

    $id_tarea = $subtarea['id_tarea'];
    $subtareaModel->delete($id);

    return redirect()->to('eliminar/inicio/' . $id_tarea)->with('success', 'Subtarea eliminada correctamente.');
}
}
