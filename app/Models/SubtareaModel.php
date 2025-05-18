<?php

namespace App\Models;

use CodeIgniter\Model;

class SubtareaModel extends Model
{
    protected $table = 'subtareas';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $allowedFields = [
        'id_tarea',
        'descripcion',
        'estado',
        'prioridad',
        'fecha_vencimiento',
        'comentario'
    ];

    public function getSubtareasByTareaId(int $tareaId)
    {
        return $this->where('id_tarea', $tareaId)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    public function getSubtareasDeTarea(int $id_tarea): array
    {
        return $this->where('id_tarea', $id_tarea)->findAll();
    }

    public function findSubtareaValida(int $id, int $id_tarea): ?array
    {
        return $this->where('id', $id)
                    ->where('id_tarea', $id_tarea)
                    ->first();
    }
}
