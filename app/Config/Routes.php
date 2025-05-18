<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('insertarTarea', 'Home::insertarTarea');
$routes->get('tareas', 'bdTareario::index');
$routes->get('inicio', 'bdTareario::principal');
$routes->get('principal', 'Home::index');
$routes->get('login', 'AuthController::login', ['as' => 'login']); // 'as' le da un nombre a la ruta
$routes->post('auth/procesarLogin', 'AuthController::procesarLogin');
$routes->get('registro', 'AuthController::registro', ['as' => 'registro']);
$routes->post('auth/procesarRegistro', 'AuthController::procesarRegistro');
$routes->get('logout', 'AuthController::logout');
$routes->get('/', 'Home::index');
$routes->get('/tareas', 'Home::index');
$routes->post('tareas/store', 'Tareas::store');


$routes->post('tareas/storesubtarea', 'Tareas::storeSubtarea');

$routes->post('tareas/completesubtarea', 'Tareas::completeSubtarea');//capaz borrar
$routes->post('tareas/completetask', 'Tareas::completeTask');//capaz borrar

$routes->get('tareas/archivadas', 'Tareas::archivadas');
$routes->get('usuario/perfil', 'AuthController::perfil');
$routes->post('usuario/actualizar', 'AuthController::actualizarPerfil');
$routes->post('tareas/updateColor', 'Tareas::updateColor');
$routes->post('tareas/completesubtareaenproceso', 'Tareas::completeSubtareaEnProceso');

$routes->get('tareas/compartir/(:num)', 'Tareas::compartir/$1');
$routes->post('tareas/procesarCompartir', 'Tareas::procesarCompartir');

$routes->get('tareas/compartidasConmigo', 'Tareas::compartidasConmigo');
$routes->get('compartidasConmigo', 'Tareas::compartidasConmigo');
$routes->post('tareas/completeSubtareaEnProcesoCompartida', 'Tareas::completeSubtareaEnProcesoCompartida');
$routes->post('tareas/completeSubtareaCompartida', 'Tareas::completeSubtareaCompartida');

$routes->post('tareas/completesubtareaInicio', 'Tareas::completesubtareaInicio');
$routes->post('tareas/completesubtareaenprocesoInicio', 'Tareas::completesubtareaenprocesoInicio');

$routes->get('modificar/inicio/(:num)', 'Modificar::inicio/$1');
$routes->post('modificar/guardar/(:num)', 'Modificar::guardar/$1');
$routes->post('subtarea/guardar/(:num)', 'Modificar::guardar_subtarea/$1');

$routes->get('eliminar/inicio/(:num)', 'Eliminar::confirmar/$1');
$routes->post('eliminar/tarea/(:num)', 'Eliminar::tarea/$1');
$routes->post('eliminar/subtarea/(:num)', 'Eliminar::subtarea/$1');

$routes->get('modificar/inicioS/(:num)', 'Modificar::inicioS/$1');
$routes->get('subtareas/menu/(:num)', 'Subtareas::editar/$1');
$routes->post('subtareas/actualizar', 'Subtareas::actualizar');

$routes->post('recordatorios/crearRecordatorio', 'Tareas::crearRecordatorio');
