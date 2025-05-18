<?php namespace App\Models;

use CodeIgniter\Model;

class TareaModel extends Model
{
    protected $table      = 'tareas';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['asunto', 'descripcion', 'prioridad', 'fecha_vencimiento', 'fecha_recordatorio', 'texto_recordatorio' , 'id_usuario', 'estado', 'color', 'archivada'];

    protected bool $allowEmptyInserts = false;

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getArchivedTareasByUserId(int $userId)
    {
        return $this->where('id_usuario', $userId)
                    ->where('estado', 'completada')
        
                    ->findAll();
    }

    public function getTareasByUserId(int $userId)
    {
        return $this->where('id_usuario', $userId)
                    ->where('estado !=', 'completada')
     
                    ->findAll();
    }

    public function obtenerRecordatoriosParaHoy()
    {
        return $this->where('fecha_recordatorio IS NOT NULL')
            ->where('fecha_recordatorio <=', date('Y-m-d'))
            ->where('estado !=', 'completada')
            ->findAll();
    }

    public function updateTaskStatusBasedOnSubtasks(int $idTarea): string
    {
        $subtareaModel = new SubtareaModel();
        $subtareas = $subtareaModel->getSubtareasByTareaId($idTarea);

        $tareaActual = $this->find($idTarea);
        if (!$tareaActual) {
            return 'desconocido'; 
        }
        $estadoTareaActual = $tareaActual['estado'];

        if (empty($subtareas)) {
            return $estadoTareaActual;
        }

        $totalSubtareas = count($subtareas);
        $completadas = 0;
        $enProceso = 0;
        $definidas = 0;

        foreach ($subtareas as $subtarea) {
            switch ($subtarea['estado']) {
                case 'completada':
                    $completadas++;
                    break;
                case 'en_proceso':
                    $enProceso++;
                    break;
                case 'definida':
                    $definidas++;
                    break;
            }
        }

        $nuevoEstado = $estadoTareaActual;

        if ($completadas === $totalSubtareas) {
            $nuevoEstado = 'completada';
        } elseif ($enProceso > 0 || ($completadas > 0 && $completadas < $totalSubtareas)) {
            $nuevoEstado = 'en_proceso';
        } elseif ($definidas === $totalSubtareas) {
            $nuevoEstado = 'definida';
        }

        if ($nuevoEstado !== $estadoTareaActual) {
            $this->update($idTarea, ['estado' => $nuevoEstado]);
        }
        return $nuevoEstado;
    }
}
