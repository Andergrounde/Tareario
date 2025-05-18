<?php

namespace App\Controllers;

use App\Models\TareaModel;


class Aviso extends BaseController{
    
    function index(){
    $modeloTareas = new TareaModel();


    $todasLasTareas = $modeloTareas
        ->where('estado !=', 'completada')
        ->findAll();

   
    $tareasConFecha = array_filter($todasLasTareas, function ($tarea) {
        return !empty($tarea['fecha_vencimiento']) && $tarea['fecha_vencimiento'] !== '0000-00-00';
    });

   
    usort($tareasConFecha, function ($a, $b) {
        return strtotime($a['fecha_vencimiento']) - strtotime($b['fecha_vencimiento']);
    });


    $tareasProximas = array_slice($tareasConFecha, 0,7);

    $datos = [
        'datosTareas' => $todasLasTareas,
        'tareasProximas' => $tareasProximas,
    ];

    return view('principal', $datos);
}
}