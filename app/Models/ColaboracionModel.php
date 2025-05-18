<?php

namespace App\Models;

use CodeIgniter\Model;

class ColaboracionModel extends Model
{
    protected $table            = 'colaboraciones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['id_tarea', 'id_colaborador', 'id_subtarea', 'fecha_creacion'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fecha_creacion';
    protected $updatedField  = '';

    protected $validationRules      = [
        'id_tarea'       => 'required|integer',
        'id_colaborador' => 'required|integer',
        'id_subtarea'    => 'permit_empty|integer',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function crearColaboracion(int $idTarea, int $idColaborador, ?int $idSubtarea = null)
    {
        $existe = $this->where('id_tarea', $idTarea)
                       ->where('id_colaborador', $idColaborador);

        if ($idSubtarea === null) {
            $existe->where('id_subtarea IS NULL');
        } else {
            $existe->where('id_subtarea', $idSubtarea);
        }

        if ($existe->first()) {
            return false;
        }

        $data = [
            'id_tarea'       => $idTarea,
            'id_colaborador' => $idColaborador,
            'id_subtarea'    => $idSubtarea,
        ];

        return $this->insert($data);
    }

    public function getColaboracionesPorTarea(int $idTarea): array
    {
        return $this->select('colaboraciones.*, usuarios.email as colaborador_email, usuarios.nombre as colaborador_nombre')
                    ->join('usuarios', 'usuarios.id = colaboraciones.id_colaborador')
                    ->where('colaboraciones.id_tarea', $idTarea)
                    ->findAll();
    }

    public function getTareasCompartidasConUsuario(int $idColaborador): array
    {
        $colaboraciones = $this->where('id_colaborador', $idColaborador)
                               ->orderBy('id_tarea', 'ASC')
                               ->orderBy('id_subtarea', 'ASC NULLS LAST')
                               ->findAll();

        if (empty($colaboraciones)) {
            return [];
        }

        $tareasCompartidas = [];
        $subtareaModel = new SubtareaModel();
        $tareaModel = new TareaModel();
        $usuarioModel = new UsuarioModel();

        foreach ($colaboraciones as $colaboracion) {
            $idTarea = $colaboracion['id_tarea'];

            if (!isset($tareasCompartidas[$idTarea])) {
                $detalleTarea = $tareaModel->find($idTarea);
                if ($detalleTarea) {
                    $creador = $usuarioModel->find($detalleTarea['id_usuario']);

                    $tareasCompartidas[$idTarea] = [
                        'id_colaboracion_principal' => ($colaboracion['id_subtarea'] === null) ? $colaboracion['id'] : null,
                        'id_tarea'              => $idTarea,
                        'asunto_tarea'          => $detalleTarea['asunto'],
                        'descripcion_tarea'     => $detalleTarea['descripcion'],
                        'prioridad_tarea'       => $detalleTarea['prioridad'],
                        'estado_tarea'          => $detalleTarea['estado'],
                        'color'                 => $detalleTarea['color'],
                        'fecha_vencimiento_tarea' => $detalleTarea['fecha_vencimiento'],
                        'creador_id'            => $detalleTarea['id_usuario'],
                        'subtareas_colaborador' => [],
                        'comparte_tarea_completa' => ($colaboracion['id_subtarea'] === null)
                    ];
                }
            }

            if ($colaboracion['id_subtarea'] !== null && isset($tareasCompartidas[$idTarea])) {
                $detalleSubtarea = $subtareaModel->find($colaboracion['id_subtarea']);
                if ($detalleSubtarea) {
                    if ($detalleSubtarea['id_tarea'] == $idTarea) {
                        $tareasCompartidas[$idTarea]['subtareas_colaborador'][] = [
                            'id_colaboracion'          => $colaboracion['id'],
                            'id_subtarea'              => $detalleSubtarea['id'],
                            'descripcion_subtarea'     => $detalleSubtarea['descripcion'],
                            'estado_subtarea'          => $detalleSubtarea['estado'],
                            'prioridad_subtarea'       => $detalleSubtarea['prioridad'],
                            'fecha_vencimiento_subtarea'=> $detalleSubtarea['fecha_vencimiento'],
                        ];
                    }
                }
            } elseif ($colaboracion['id_subtarea'] === null && isset($tareasCompartidas[$idTarea])) {
                $tareasCompartidas[$idTarea]['comparte_tarea_completa'] = true;
                if ($tareasCompartidas[$idTarea]['id_colaboracion_principal'] === null) {
                    $tareasCompartidas[$idTarea]['id_colaboracion_principal'] = $colaboracion['id'];
                }
            }
        }
        return array_values($tareasCompartidas);
    }

    public function isUserCollaboratorOnTask(int $idUsuario, int $idTarea): bool
    {
        return $this->where('id_colaborador', $idUsuario)
                    ->where('id_tarea', $idTarea)
                    ->countAllResults() > 0;
    }

    public function isUserCollaboratorForSubtask(int $idUsuario, int $idSubtarea, ?int $idColaboracion = null): bool
    {
        $builder = $this->where('id_colaborador', $idUsuario)
                         ->where('id_subtarea', $idSubtarea);
        if ($idColaboracion !== null) {
            $builder->where('id', $idColaboracion);
        }
        return $builder->countAllResults() > 0;
    }
}
