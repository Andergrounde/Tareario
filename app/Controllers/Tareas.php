<?php 

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\SubtareaModel;
use App\Models\UsuarioModel;
use App\Models\ColaboracionModel;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Events\Events;

class Tareas extends Controller
{
    use ResponseTrait;

    protected $tareaModel;
    protected $subtareaModel;
    protected $usuarioModel;
    protected $colaboracionModel;
    protected $session;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->tareaModel = new TareaModel();
        $this->subtareaModel = new SubtareaModel();
        $this->usuarioModel = new UsuarioModel(); 
        $this->colaboracionModel = new ColaboracionModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $data = [];
        $session = \Config\Services::session();

        if ($session->get('isLoggedIn') == TRUE && $session->has('user_id')) {
            $loggedInUserId = $session->get('user_id');
            $activeTasks = $this->tareaModel->getTareasByUserId($loggedInUserId);

            foreach ($activeTasks as &$tarea) {
                 $tarea['subtareas'] = $this->subtareaModel->getSubtareasByTareaId($tarea['id']);
            }
            unset($tarea);

            $data['datosTareas'] = $activeTasks;
            $data['usuario'] = (object)[
                'id' => $loggedInUserId,
                'nombre' => $session->get('nombre')
            ];
        } else {
            return redirect()->to(site_url('login'))->with('info', 'Debes iniciar sesión para ver tus tareas.');
        }

        return view('principal', $data);
    }

    public function crearRecordatorio()
    {
        $tareaModel = new TareaModel();
        $tareaId = $this->request->getPost('tarea_id');
        $fecha = $this->request->getPost('fecha_recordatorio');
        $texto = $this->request->getPost('texto_recordatorio');

        if ($fecha < date('Y-m-d')) {
            return redirect()->back()->with('error', 'La fecha del recordatorio debe ser futura.');
        }

        $tareaModel->update($tareaId, [
            'fecha_recordatorio' => $fecha,
            'texto_recordatorio' => $texto
        ]);

        return redirect()->back()->with('success', 'Recordatorio guardado correctamente.');
    }

    public function store()
    {
         function generateLightColor() {
             $color = '#';
             for ($i = 0; $i < 3; $i++) {
                 $colorValue = mt_rand(160, 255);
                 $color .= str_pad(dechex($colorValue), 2, '0', STR_PAD_LEFT);
             }
             return $color;
         }

         $session = session();

         $userId = 101;
         if ($session->get('isLoggedIn') == TRUE && $session->has('user_id')) {
              $userId = $session->get('user_id');
         } else {
             return redirect()->to(site_url('login'))->with('error', 'Debes iniciar sesión para crear tareas.');
         }

         $rules = [
             'asunto' => 'required|max_length[255]',
             'descripcion' => 'required',
             'prioridad' => 'required|in_list[baja,normal,alta]',
             'fecha_vencimiento' => 'permit_empty|valid_date',
         ];

         if (! $this->validate($rules)) {
             return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
         }

         $generatedColor = generateLightColor();

         $data = [
             'asunto'          => $this->request->getPost('asunto', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
             'descripcion'     => $this->request->getPost('descripcion', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
             'prioridad'       => $this->request->getPost('prioridad'),
             'fecha_vencimiento' => $this->request->getPost('fecha_vencimiento'),
             'id_usuario'      => $userId,
             'estado'          => 'definida',
             'color'           => $generatedColor,
             'archivada'       => 0,
         ];

         if ($this->tareaModel->insert($data)) {
             return redirect()->to(site_url('/'))->with('success', 'Tarea creada exitosamente!');
         } else {
             return redirect()->back()->withInput()->with('error', 'Hubo un problema al crear la tarea.');
         }
    }

    public function storeSubtareaVIEJO()
    {
        $idTarea = $this->request->getPost('id_tarea');
        $descripcion = $this->request->getPost('descripcion');
        $prioridad = $this->request->getPost('prioridad');
        $fechaVencimiento = $this->request->getPost('fecha_vencimiento');

        $rules = [
            'id_tarea' => 'required|is_natural_no_zero',
            'descripcion' => 'required',
            'prioridad' => 'required|in_list[baja,normal,alta]',
            'fecha_vencimiento' => 'permit_empty|valid_date',
        ];

         if (! $this->validate($rules)) {
             return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
         }

        $subtareaData = [
            'id_tarea'          => $idTarea,
            'descripcion'       => $descripcion,
            'prioridad'         => $prioridad,
            'fecha_vencimiento' => $fechaVencimiento,
            'estado'            => 'definida',
            'comentario'        => null      
        ];

        if ($this->subtareaModel->insert($subtareaData)) {
            $task = $this->tareaModel->find($idTarea);
            if ($task && (is_array($task) || is_object($task))) {
                $taskEstado = is_object($task) ? ($task->estado ?? null) : ($task['estado'] ?? null);
                if ($taskEstado === 'definida') {
                     $this->tareaModel->update($idTarea, ['estado' => 'en_proceso']);
                }
            }
            return redirect()->back()->with('success', 'Subtarea agregada exitosamente!');
        } else {
             return redirect()->back()->withInput()->with('error', 'Hubo un problema al agregar la subtarea.');
        }
    }

    public function storeSubtarea()
    {
        $subtareaModel = new SubtareaModel();

        $validationRules = [
            'id_tarea' => 'required|is_natural_no_zero',
            'descripcion' => 'required|string|min_length[3]',
            'prioridad' => 'required|in_list[baja,normal,alta]',
            'fecha_vencimiento' => 'permit_empty|valid_date'
        ];

        if (!$this->validate($validationRules)) {
            session()->setFlashdata('mensaje', 'Error en los datos de la subtarea.');
            return redirect()->back()->withInput();
        }

        $data = [
            'id_tarea' => $this->request->getPost('id_tarea'),
            'descripcion' => $this->request->getPost('descripcion'),
            'prioridad' => $this->request->getPost('prioridad'),
            'fecha_vencimiento' => $this->request->getPost('fecha_vencimiento'),
            'estado' => 'definida', 
            'comentario' => NULL
        ];

        if ($subtareaModel->insert($data)) {
            session()->setFlashdata('mensaje', 'Subtarea creada con éxito.');
        } else {
            session()->setFlashdata('mensaje', 'Error al crear la subtarea.');
        }

        return redirect()->to('/principal');
    }

    public function completeTask()
    {
         $taskId = $this->request->getPost('task_id');

         $rules = ['task_id' => 'required|is_natural_no_zero'];
         if (! $this->validate($rules)) {
             return redirect()->back()->with('error', 'ID de tarea inválido.');
         }

         $task = $this->tareaModel->find($taskId);

         if (!$task || (!is_array($task) && !is_object($task))) {
             return redirect()->back()->with('error', 'Tarea no encontrada o inválida.');
         }

         $subtasks = $this->subtareaModel->getSubtareasByTareaId($taskId);
         if (!empty($subtasks)) {
             return redirect()->back()->with('warning', 'Esta tarea tiene subtareas y no se puede marcar como completada directamente.');
         }

         $taskEstado = is_object($task) ? ($task->estado ?? null) : ($task['estado'] ?? null);

         if ($taskEstado === 'completada') {
              return redirect()->back()->with('info', 'La tarea ya estaba completada.');
         }

         $updateTaskData = ['estado' => 'completada'];
         if ($this->tareaModel->update($taskId, $updateTaskData)) {
              return redirect()->back()->with('success', 'Tarea marcada como completada.');
         } else {
              return redirect()->back()->with('error', 'Hubo un problema al marcar la tarea como completada.');
         }
     }



    public function updateColor()
{
    $taskId = $this->request->getPost('task_id');
    $color = $this->request->getPost('color');

    $rules = [
        'task_id' => 'required|is_natural_no_zero',
        'color' => 'required|regex_match[/^#([a-f0-9]{6}|[a-f0-9]{3})$/i]',
    ];

    if (! $this->validate($rules)) {
        return $this->failValidationErrors($this->validator->getErrors());
    }

    $task = $this->tareaModel->find($taskId);

    if (!$task || (!is_array($task) && !is_object($task))) {
        return $this->failNotFound('Tarea no encontrada o inválida.');
    }

    $loggedInUserId = $this->session->get('user_id');
    $taskUserId = is_object($task) ? ($task->id_usuario ?? null) : ($task['id_usuario'] ?? null);

    if (!$loggedInUserId || $loggedInUserId != $taskUserId) {
        log_message('warning', 'Intento de actualización de color no autorizado. User ID logeado: ' . $loggedInUserId . ', Tarea ID: ' . $taskId . ', Dueño Tarea ID: ' . $taskUserId);
        return $this->failUnauthorized('No tienes permiso para cambiar el color de esta tarea.');
    }

    $updateData = ['color' => $color];
    if ($this->tareaModel->update($taskId, $updateData)) {
        return $this->respond(['success' => true, 'message' => 'Color de tarea actualizado exitosamente.']);
    } else {
        return $this->failServerError('Hubo un problema al actualizar el color de la tarea.');
    }
}

public function archivadas()
{
    $data = [];
    $session = \Config\Services::session();

    if ($session->get('isLoggedIn') == TRUE && $session->has('user_id')) {
        $loggedInUserId = $session->get('user_id');

        $archivedTasks = $this->tareaModel->getArchivedTareasByUserId($loggedInUserId);

        foreach ($archivedTasks as &$tarea) {
            $tarea['subtareas'] = $this->subtareaModel->getSubtareasByTareaId($tarea['id']);
        }
        unset($tarea);

        $data['datosTareasArchivadas'] = $archivedTasks;

        $data['usuario'] = (object)[
            'id' => $loggedInUserId,
            'nombre' => $session->get('nombre')
        ];

        log_message('debug', 'Tareas::archivadas - Usuario logeado ID: ' . $loggedInUserId);
        log_message('debug', 'Tareas::archivadas - Número de tareas archivadas encontradas: ' . count($archivedTasks));
    } else {
        return redirect()->to(site_url('login'))->with('info', 'Debes iniciar sesión para ver las tareas archivadas.');
    }

    return view('archivadas', $data);
}

public function logout()
{
    log_message('debug', 'Cerrando sesión para el usuario ID: ' . $this->session->get('user_id'));
    $this->session->destroy();
    log_message('debug', 'Sesión destruida.');
    return redirect()->to('login')->with('success', 'Has cerrado sesión exitosamente.');
}

public function compartir(int $idTarea)
{
    $tarea = $this->tareaModel->find($idTarea);

    if (!$tarea) {
        session()->setFlashdata('error', 'Tarea no encontrada.');
        return redirect()->to(site_url('/'));
    }

    $subtareas = $this->subtareaModel->getSubtareasDeTarea($idTarea);

    $tareaData = is_object($tarea) ? (array) $tarea : $tarea;

    $session = session();
    $userData = null;

    if ($session->get('isLoggedIn')) {
        $userData = (object)['nombre' => $session->get('nombreUsuario') ?? 'Usuario'];
    }

    return view('compartir_tarea', [
        'tarea'     => $tareaData,
        'subtareas' => $subtareas,
        'usuario'   => $userData
    ]);
}



public function procesarCompartir()
{
    $session = session();
    $request = service('request');

    $rules = [
        'task_id' => 'required|is_natural_no_zero|is_not_unique[tareas.id]',
        'emails'  => 'required',
        'emails.*' => 'required|valid_email',
        'selected_subtasks.*' => 'permit_empty|is_natural_no_zero|is_not_unique[subtareas.id]'
    ];

    $messages = [
        'task_id' => [
            'required' => 'El ID de la tarea es obligatorio.',
            'is_natural_no_zero' => 'El ID de la tarea no es válido.',
            'is_not_unique' => 'La tarea especificada no existe.'
        ],
        'emails' => [
            'required' => 'Debe ingresar al menos un email para compartir.'
        ],
        'emails.*' => [
            'required' => 'El campo de email no puede estar vacío.',
            'valid_email' => 'Por favor, ingrese un email válido.'
        ],
        'selected_subtasks.*' => [
            'is_natural_no_zero' => 'El ID de la subtarea seleccionada no es válido.',
            'is_not_unique' => 'Una de las subtareas seleccionadas no existe.'
        ]
    ];

    if (!$this->validate($rules, $messages)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    $idTarea = $request->getPost('task_id');
    $emails = array_filter(array_unique((array)$request->getPost('emails')));
    $selectedSubtasksIds = (array)$request->getPost('selected_subtasks[]');

    $tareaPrincipal = $this->tareaModel->find($idTarea);

    if (!$tareaPrincipal) {
        return redirect()->back()->with('error', 'La tarea principal no fue encontrada.');
    }

    $successMessages = [];
    $warningMessages = [];
    $colaboracionesCreadas = 0;

    foreach ($emails as $email) {
        $colaborador = $this->usuarioModel->findByEmail(trim($email));

        if (!$colaborador) {
            $warningMessages[] = "El usuario con email '".esc(trim($email))."' no fue encontrado. No se pudo compartir con este usuario.";
            continue;
        }
        $idColaborador = is_object($colaborador) ? $colaborador->id : $colaborador['id'];

        if (!empty($selectedSubtasksIds)) {
            foreach ($selectedSubtasksIds as $idSubtarea) {

                $subtareaValida = $this->subtareaModel->findSubtareaValida((int)$idSubtarea, $idTarea);

                if (!$subtareaValida) {
                    $warningMessages[] = "La subtarea con ID ".esc($idSubtarea)." no es válida o no pertenece a la tarea '".esc($tareaPrincipal['asunto'])."'. No se compartió con ".esc(trim($email)).".";
                    continue;
                }

                $nombreSubtareaParaMensaje = esc($subtareaValida['descripcion'] ?? "Subtarea ID " . $idSubtarea);

                if ($this->colaboracionModel->crearColaboracion($idTarea, $idColaborador, (int)$idSubtarea)) {
                    $colaboracionesCreadas++;
                } else {
                    $warningMessages[] = "La subtarea '".$nombreSubtareaParaMensaje."' ya está compartida con ".esc(trim($email));
                }
            }
        } else {
            if ($this->colaboracionModel->crearColaboracion($idTarea, $idColaborador, null)) {
                $colaboracionesCreadas++;
            } else {
                $warningMessages[] = "La tarea principal '".esc($tareaPrincipal['asunto'])."' ya está compartida con ".esc(trim($email))." o hubo un error al intentar compartirla.";
            }
        }
    }

    if ($colaboracionesCreadas > 0) {
        $session->setFlashdata('success', "Se realizaron $colaboracionesCreadas colaboraciones exitosamente.");
    }

    if (!empty($warningMessages)) {
        $session->setFlashdata('warning', implode(' //// ', $warningMessages));
    }

    if (empty($emails) && $request->getPost('emails')) {
        $session->setFlashdata('warning', 'No se proporcionaron emails válidos para compartir.');
    }

    return redirect()->to('tareas/compartir/' . $idTarea);
}

   
public function compartidasConmigo()
{
    $session = session();
    $loggedInUserId = $session->get('user_id');

    if (!$loggedInUserId) {
        return redirect()->to(site_url('login'))->with('error', 'Debes iniciar sesión para ver las tareas compartidas.');
    }

    $datosTareasCompartidas = $this->colaboracionModel->getTareasCompartidasConUsuario($loggedInUserId);

    $usuarioActual = null;
    if ($session->get('nombre')) {
        $usuarioActual = (object)['nombre' => $session->get('nombre')];
    }

    $data = [
        'title' => 'Tareas Compartidas Conmigo',
        'usuario' => $usuarioActual,
        'datosTareasCompartidas' => $datosTareasCompartidas,
    ];

    return view('compartidas', $data);
}

public function completeSubtareaEnProcesoCompartida()
{
    $request = service('request');
    $session = session();
    $loggedInUserId = $session->get('user_id');

    $subtaskId = $request->getPost('subtask_id');
    $colaboracionId = $request->getPost('colaboracion_id');

    $subtarea = $this->subtareaModel->find($subtaskId);

    if ($subtarea && $subtarea['estado'] !== 'en_proceso') {
        $this->subtareaModel->update($subtaskId, ['estado' => 'en_proceso']);
    }

    return redirect()->to('/compartidasConmigo');
}

public function completeSubtareaCompartida()
{
    $request = service('request');
    $session = session();
    $loggedInUserId = $session->get('user_id');

    $subtaskId = $request->getPost('subtask_id');
    $colaboracionId = $request->getPost('colaboracion_id');

    $subtarea = $this->subtareaModel->find($subtaskId);

    if ($subtarea && $subtarea['estado'] !== 'completada') {
        $this->subtareaModel->update($subtaskId, ['estado' => 'completada']);
    }

    return redirect()->to('/compartidasConmigo');
}

public function completesubtareaInicio()
{
    $subtaskId = $this->request->getPost('subtask_id');

    $subtareaModel = new SubtareaModel();
    $tareaModel = new TareaModel();

    if ($subtareaModel->update($subtaskId, ['estado' => 'completada'])) {
        $subtarea = $subtareaModel->find($subtaskId);
        $idTarea = $subtarea['id_tarea'] ?? null;

        if ($idTarea) {
            $subtareas = $subtareaModel->where('id_tarea', $idTarea)->findAll();

            $todasCompletadas = true;
            $hayAlgunaCompletada = false;

            foreach ($subtareas as $st) {
                if ($st['estado'] !== 'completada') {
                    $todasCompletadas = false;
                } else {
                    $hayAlgunaCompletada = true;
                }
            }

            if ($todasCompletadas) {
                $tareaModel->update($idTarea, ['estado' => 'completada']);
                session()->setFlashdata('mensaje', 'Subtarea completada y tarea marcada como completada.');
            } elseif ($hayAlgunaCompletada) {
                $tareaModel->update($idTarea, ['estado' => 'en_proceso']);
                session()->setFlashdata('mensaje', 'Subtarea completada. Tarea marcada como en proceso.');
            } else {
                session()->setFlashdata('mensaje', 'Subtarea completada correctamente.');
            }
        } else {
            session()->setFlashdata('mensaje', 'No se encontró la tarea relacionada.');
        }
    } else {
        session()->setFlashdata('mensaje', 'Error al completar la subtarea.');
    }

    return redirect()->to('/principal');
}

public function completesubtareaenprocesoInicio()
{
    $subtaskId = $this->request->getPost('subtask_id');

    $subtareaModel = new SubtareaModel();

    if ($subtareaModel->update($subtaskId, ['estado' => 'en_proceso'])) {
        session()->setFlashdata('mensaje', 'Subtarea marcada como En Proceso.');
    } else {
        session()->setFlashdata('mensaje', 'Error al actualizar la subtarea.');
    }

    return redirect()->to('/principal');
}

public function completeSubtareaEnProceso()
{
    $subtaskId = $this->request->getPost('subtask_id');

    $rules = ['subtask_id' => 'required|is_natural_no_zero'];
    if (!$this->validate($rules)) {
        return redirect()->back()->with('error', 'ID de subtarea inválido.');
    }

    $subtarea = $this->subtareaModel->find($subtaskId);

    if (!$subtarea || (!is_array($subtarea) && !is_object($subtarea))) {
        return redirect()->back()->with('error', 'Subtarea no encontrada o inválida.');
    }

    $subtaskEstado = is_object($subtarea) ? ($subtarea->estado ?? null) : ($subtarea['estado'] ?? null);
    if ($subtaskEstado !== 'definida') {
        return redirect()->back()->with('info', 'La subtarea ya está en proceso o completada.');
    }

    $updateSubtareaData = ['estado' => 'en_proceso'];
    if ($this->subtareaModel->update($subtaskId, $updateSubtareaData)) {
        $taskId = is_object($subtarea) ? ($subtarea->id_tarea ?? null) : ($subtarea['id_tarea'] ?? null);

        if (!empty($taskId)) {
            $task = $this->tareaModel->find($taskId);
            if ($task && (is_array($task) || is_object($task))) {
                $taskEstado = is_object($task) ? ($task->estado ?? null) : ($task['estado'] ?? null);

                if ($taskEstado === 'definida') {
                    $this->tareaModel->update($taskId, ['estado' => 'en_proceso']);
                }
            }
        }

        return redirect()->back()->with('success', 'Subtarea marcada como En Proceso.');
    } else {
        return redirect()->back()->with('error', 'Hubo un problema al marcar la subtarea como En Proceso.');
    }
}

public function completeSubtarea()
{
    $subtaskId = $this->request->getPost('subtask_id');

    $rules = ['subtask_id' => 'required|is_natural_no_zero'];
    if (!$this->validate($rules)) {
        return $this->failValidationErrors(['subtask_id' => 'ID de subtarea inválido.']);
    }

    $subtarea = $this->subtareaModel->find($subtaskId);

    if (!$subtarea || (!is_array($subtarea) && !is_object($subtarea))) {
        return $this->failNotFound('Subtarea no encontrada o inválida.');
    }

    $subtaskEstado = is_object($subtarea) ? ($subtarea->estado ?? null) : ($subtarea['estado'] ?? null);
    if ($subtaskEstado === 'completada') {
        return $this->respond(['success' => false, 'message' => 'La subtarea ya estaba completada.']);
    }

    $updateSubtareaData = ['estado' => 'completada'];
    if ($this->subtareaModel->update($subtaskId, $updateSubtareaData)) {
        $taskId = is_object($subtarea) ? ($subtarea->id_tarea ?? null) : ($subtarea['id_tarea'] ?? null);

        if (empty($taskId)) {
            log_message('error', 'Subtarea ID ' . $subtaskId . ' marcada como completada, pero no se pudo obtener el ID de la tarea padre.');
            return $this->respond(['success' => true, 'message' => 'Subtarea marcada como completada, pero tarea padre no encontrada.']);
        }

        $task = $this->tareaModel->find($taskId);

        if ($task && (is_array($task) || is_object($task))) {
            $allSubtasks = $this->subtareaModel->getSubtareasByTareaId($taskId);
            $allCompleted = true;
            if (!empty($allSubtasks)) {
                foreach ($allSubtasks as $st) {
                    $subtaskState = is_object($st) ? ($st->estado ?? null) : ($st['estado'] ?? null);
                    if ($subtaskState !== 'completada') {
                        $allCompleted = false;
                        break;
                    }
                }
            } else {
                $allCompleted = false;
            }

            $updateTaskData = [];

            $taskEstado = is_object($task) ? ($task->estado ?? null) : ($task['estado'] ?? null);

            if ($allCompleted) {
                $updateTaskData['estado'] = 'completada';
                log_message('debug', 'Tarea ID ' . $taskId . ': Todas las subtareas completadas. Marcando tarea como completada.');
            }

            if (!empty($updateTaskData)) {
                $this->tareaModel->update($taskId, $updateTaskData);
            }

            return $this->respond(['success' => true, 'message' => 'Subtarea marcada como completada.']);
        } else {
            log_message('error', 'Subtarea ID ' . $subtaskId . ' marcada como completada, pero tarea padre ID ' . $taskId . ' no encontrada.');
            return $this->respond(['success' => true, 'message' => 'Subtarea marcada como completada, pero tarea padre no encontrada.']);
        }
    } else {
        return $this->failServerError('Hubo un problema al marcar la subtarea como completada.');
    }
}

    
    
}
