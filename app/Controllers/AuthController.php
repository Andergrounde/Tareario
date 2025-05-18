<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;

class AuthController extends Controller
{
    protected $helpers = ['form', 'url'];
    protected $session;
    protected $usuarioModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->usuarioModel = new UsuarioModel();
    }

    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('principal');
        }
        return view('auth/login');
    }

    public function registro()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('principal');
        }
        return view('auth/register');
    }

    public function procesarRegistro()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'nombre' => [
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'El nombre es obligatorio.',
                    'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                    'max_length' => 'El nombre no puede exceder los 50 caracteres.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[usuarios.email]',
                'errors' => [
                    'required' => 'El email es obligatorio.',
                    'valid_email' => 'Por favor, introduce un email válido.',
                    'is_unique' => 'Este email ya está registrado. Intenta con otro.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'La contraseña es obligatoria.',
                    'min_length' => 'La contraseña debe tener al menos 6 caracteres.'
                ]
            ],
            'password_confirm' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'La confirmación de contraseña es obligatoria.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nombre' => $this->request->getVar('nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
            'password' => $this->request->getVar('password'),
            'fecha_registro' => date('Y-m-d H:i:s'),
            'es_admin' => 0
        ];

        if ($this->usuarioModel->save($data)) {
            $this->session->setFlashdata('success', '¡Registro exitoso! Ahora puedes iniciar sesión.');
            return redirect()->to('login');
        } else {
            $this->session->setFlashdata('error', 'Hubo un problema al registrar el usuario. Inténtalo de nuevo.');
            return redirect()->back()->withInput();
        }
    }

    public function procesarLogin()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'El email es obligatorio.',
                    'valid_email' => 'Por favor, introduce un email válido.'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'La contraseña es obligatoria.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->usuarioModel->getUserByEmail($email);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $userData = [
                    'user_id' => $user['id'],
                    'nombre' => $user['nombre'],
                    'email' => $user['email'],
                    'es_admin' => $user['es_admin'],
                    'isLoggedIn' => TRUE
                ];
                $this->session->set($userData);
                return redirect()->to('principal')->with('success', '¡Bienvenido de nuevo, ' . esc($user['nombre']) . '!');
            } else {
                $this->session->setFlashdata('error', 'El email o la contraseña son incorrectos.');
                return redirect()->back()->withInput();
            }
        } else {
            $this->session->setFlashdata('error', 'El email o la contraseña son incorrectos.');
            return redirect()->back()->withInput();
        }
    }

    public function perfil()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('info', 'Debes iniciar sesión para ver tu perfil.');
        }

        $loggedInUserId = $this->session->get('user_id');
        $usuario = $this->usuarioModel->find($loggedInUserId);

        if (!$usuario) {
            $this->session->destroy();
            return redirect()->to(site_url('login'))->with('error', 'Error al cargar los datos del usuario. Por favor, inicia sesión de nuevo.');
        }

        $data['usuario'] = $usuario;

        return view('auth/perfil', $data);
    }

    public function actualizarPerfil()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('info', 'Debes iniciar sesión para actualizar tu perfil.');
        }

        $loggedInUserId = $this->session->get('user_id');
        $formUserId = $this->request->getPost('id');

        if ($loggedInUserId != $formUserId) {
            log_message('warning', 'Intento de actualización de perfil no autorizado. User ID logeado: ' . $loggedInUserId . ', User ID en formulario: ' . $formUserId);
            return redirect()->back()->with('error', 'Intento de actualización no autorizado.');
        }

        $usuario = $this->usuarioModel->find($loggedInUserId);

        if (!$usuario) {
            $this->session->destroy();
            return redirect()->to(site_url('login'))->with('error', 'Error al cargar los datos del usuario. Por favor, inicia sesión de nuevo.');
        }

        $rules = [
            'nombre' => [
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'El nombre es obligatorio.',
                    'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                    'max_length' => 'El nombre no puede exceder los 50 caracteres.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[usuarios.email,id,' . $loggedInUserId . ']',
                'errors' => [
                    'required' => 'El email es obligatorio.',
                    'valid_email' => 'Por favor, introduce un email válido.',
                    'is_unique' => 'Este email ya está registrado por otro usuario.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'nombre' => $this->request->getPost('nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'email' => $this->request->getPost('email', FILTER_SANITIZE_EMAIL),
        ];

        if ($this->usuarioModel->update($loggedInUserId, $updateData)) {
            $this->session->set('nombre', $updateData['nombre']);
            $this->session->set('email', $updateData['email']);

            return redirect()->back()->with('success', 'Perfil actualizado exitosamente.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Hubo un problema al actualizar el perfil. Inténtalo de nuevo.');
        }
    }

    public function actualizarPerfilContraseña()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('info', 'Debes iniciar sesión para actualizar tu perfil.');
        }

        $loggedInUserId = $this->session->get('user_id');
        $formUserId = $this->request->getPost('id');

        if ($loggedInUserId != $formUserId) {
            log_message('warning', 'Intento de actualización de perfil no autorizado. User ID logeado: ' . $loggedInUserId . ', User ID en formulario: ' . $formUserId);
            return redirect()->back()->with('error', 'Intento de actualización no autorizado.');
        }

        $usuario = $this->usuarioModel->find($loggedInUserId);

        if (!$usuario) {
            $this->session->destroy();
            return redirect()->to(site_url('login'))->with('error', 'Error al cargar los datos del usuario. Por favor, inicia sesión de nuevo.');
        }

        $rules = [
            'nombre' => [
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'El nombre es obligatorio.',
                    'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                    'max_length' => 'El nombre no puede exceder los 50 caracteres.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[usuarios.email,id,' . $loggedInUserId . ']',
                'errors' => [
                    'required' => 'El email es obligatorio.',
                    'valid_email' => 'Por favor, introduce un email válido.',
                    'is_unique' => 'Este email ya está registrado por otro usuario.'
                ]
            ]
        ];

        if (
            $this->request->getPost('password_actual') ||
            $this->request->getPost('password_nueva') ||
            $this->request->getPost('password_confirm')
        ) {
            $rules['password_actual'] = [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Debes ingresar tu contraseña actual.'
                ]
            ];
            $rules['password_nueva'] = [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'La nueva contraseña es obligatoria.',
                    'min_length' => 'La nueva contraseña debe tener al menos 6 caracteres.'
                ]
            ];
            $rules['password_confirm'] = [
                'rules' => 'required|matches[password_nueva]',
                'errors' => [
                    'required' => 'Debes confirmar la nueva contraseña.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'nombre' => $this->request->getPost('nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'email' => $this->request->getPost('email', FILTER_SANITIZE_EMAIL),
        ];

        if ($this->request->getPost('password_nueva')) {
            $passwordActual = $this->request->getPost('password_actual');
            $usuario = $this->usuarioModel->find($loggedInUserId);

            if (!password_verify($passwordActual, $usuario['password'])) {
                return redirect()->back()->withInput()->with('error', 'La contraseña actual es incorrecta.');
            }

            $updateData['password'] = password_hash($this->request->getPost('password_nueva'), PASSWORD_DEFAULT);
        }

        if ($this->usuarioModel->update($loggedInUserId, $updateData)) {
            $this->session->set('nombre', $updateData['nombre']);
            $this->session->set('email', $updateData['email']);

            return redirect()->back()->with('success', 'Perfil actualizado exitosamente.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Hubo un problema al actualizar el perfil. Inténtalo de nuevo.');
        }
    }

    public function logout()
    {
        $this->session->remove('notificacion_mostrada');

    
        $this->session->destroy();

        return redirect()->to('login')->with('success', 'Has cerrado sesión exitosamente.');
    }
}
