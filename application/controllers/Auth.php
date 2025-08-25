<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Usuario_model');
        $this->load->model('Rol_model');
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    // Página de registro
    public function register() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->form_validation->set_rules('correo', 'Correo Electrónico', 'required|valid_email|is_unique[usuarios.correo]');
            $this->form_validation->set_rules('contrasena', 'Contraseña', 'required|min_length[6]');
            $this->form_validation->set_rules('confirmar_contrasena', 'Confirmar Contraseña', 'required|matches[contrasena]');
            $this->form_validation->set_rules('nombre', 'Nombre', 'required');
            $this->form_validation->set_rules('apellido_paterno', 'Apellido Paterno', 'required');
            $this->form_validation->set_rules('apellido_materno', 'Apellido Materno', 'required');
            $this->form_validation->set_rules('edad', 'Edad', 'required|numeric');

            if ($this->form_validation->run() === FALSE) {
                if ($this->input->is_ajax_request()) {
                    $this->_json_response('error', 'Errores de validación', ['errors' => $this->form_validation->error_array()]);
                } else {
                    $data['titulo'] = 'Registro de Usuario';
                    $this->load->view('auth/register', $data);
                }
                return;
            }

            // Correo y contraseña
            $id_usuario = $this->Usuario_model->registrar_usuario(
                $this->input->post('correo'),
                $this->input->post('contrasena')
            );

            //datos usuario

            if ($id_usuario) {
                $datos_usuario = [
                    'nombre' => $this->input->post('nombre'),
                    'apellido_paterno' => $this->input->post('apellido_paterno'),
                    'apellido_materno' => $this->input->post('apellido_materno'),
                    'edad' => $this->input->post('edad'),
                    'telefono' => $this->input->post('telefono'),
                    'direccion' => $this->input->post('direccion')
                ];

                $this->Usuario_model->registrar_datos_usuario($id_usuario, $datos_usuario);
                $this->Usuario_model->asignar_rol($id_usuario, 1); 

                if ($this->input->is_ajax_request()) {
                    $this->_json_response('success', 'Registro exitoso. Ahora puede iniciar sesión.', ['redirect' => site_url('auth/login')]);
                } else {
                    $this->session->set_flashdata('success', 'Registro exitoso. Ahora puede iniciar sesión.');
                    redirect('auth/login');
                }
            } else {
                if ($this->input->is_ajax_request()) {
                    $this->_json_response('error', 'Error en el registro. Intente nuevamente.');
                } else {
                    $this->session->set_flashdata('error', 'Error en el registro. Intente nuevamente.');
                    redirect('auth/register');
                }
            }
        } else {
            $data['titulo'] = 'Registro de Usuario';
            $this->load->view('auth/register', $data);
        }
    }

    // Página de login
    public function login() {
        // Si ya está logueado
        if ($this->session->userdata('logged_in')) {
            if ($this->input->is_ajax_request()) {
                $this->_json_response('info', 'Ya tienes sesión activa', ['redirect' => $this->_get_redirect_url()]);
            } else {
                redirect($this->_get_redirect_url());
            }
            return;
        }

        // Si es POST
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->form_validation->set_rules('correo', 'Correo Electrónico', 'required|valid_email');
            $this->form_validation->set_rules('contrasena', 'Contraseña', 'required');

            if (!$this->form_validation->run()) {
                if ($this->input->is_ajax_request()) {
                    $this->_json_response('error', 'Errores de validación', ['errors' => $this->form_validation->error_array()]);
                } else {
                    $this->session->set_flashdata('error', 'Errores de validación');
                    redirect('auth/login');
                }
                return;
            }

            $correo = $this->input->post('correo');
            $contrasena = $this->input->post('contrasena');
            $usuario = $this->Usuario_model->verificar_login($correo, $contrasena);

            if (!$usuario) {
                if ($this->input->is_ajax_request()) {
                    $this->_json_response('error', 'Usuario inactivo o credenciales inválidas');
                } else {
                    $this->session->set_flashdata('error', 'Usuario inactivo o credenciales inválidas');
                    redirect('auth/login');
                }
                return;
            }

            // Obtener roles
            $roles = $this->Usuario_model->obtener_roles_usuario($usuario->id_usuario);

            if (empty($roles)) {
                if ($this->input->is_ajax_request()) {
                    $this->_json_response('error', 'Su cuenta no tiene roles activos. Contacte al administrador.');
                } else {
                    $this->session->set_flashdata('error', 'Su cuenta no tiene roles activos. Contacte al administrador.');
                    redirect('auth/login');
                }
                return;
            }

            // Regenerar ID de sesión por seguridad
            $this->session->sess_regenerate(TRUE);

            // Datos básicos de sesión
            $user_data = [
                'id_usuario' => $usuario->id_usuario,
                'correo' => $usuario->correo,
                'logged_in' => true,
                'todos_roles' => $roles
            ];
            $this->session->set_userdata($user_data);

            // Si solo tiene un rol, fijarlo directamente
            if (count($roles) == 1) {
                $this->session->set_userdata([
                    'rol_actual' => $roles[0]->id_rol,
                    'nombre_rol' => $roles[0]->nombre_rol
                ]);
                
                $redirect_url = $this->_get_redirect_url();
                
                if ($this->input->is_ajax_request()) {
                    $this->_json_response('success', 'Inicio de sesión correcto', ['redirect' => $redirect_url]);
                } else {
                    redirect($redirect_url);
                }
            } else {
                // Si tiene varios roles, redirigir a selección
                if ($this->input->is_ajax_request()) {
                    $this->_json_response('success', 'Seleccione un rol', [
                        'redirect' => site_url('auth/seleccionar_rol'),
                        'roles' => $roles
                    ]);
                } else {
                    redirect('auth/seleccionar_rol');
                }
            }
        } else {
            // Si es GET, carga la vista normal
            $data['titulo'] = 'Iniciar Sesión';
            $this->load->view('auth/login', $data);
        }
    }

    public function seleccionar_rol() {
        if (!$this->session->userdata('logged_in')) {
            if ($this->input->is_ajax_request()) {
                $this->_json_response('error', 'Debe iniciar sesión primero', ['redirect' => site_url('auth/login')]);
            } else {
                redirect('auth/login');
            }
            return;
        }

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $rol_id = $this->input->post('rol_id');
            $roles = $this->session->userdata('todos_roles');

            foreach ($roles as $rol) {
                if ($rol->id_rol == $rol_id) {
                    $this->session->set_userdata([
                        'rol_actual' => $rol->id_rol,
                        'nombre_rol' => $rol->nombre_rol
                    ]);
                    
                    if ($this->input->is_ajax_request()) {
                        $this->_json_response('success', 'Rol seleccionado correctamente', ['redirect' => $this->_get_redirect_url()]);
                    } else {
                        redirect($this->_get_redirect_url());
                    }
                    return;
                }
            }

            if ($this->input->is_ajax_request()) {
                $this->_json_response('error', 'Rol inválido');
            } else {
                $this->session->set_flashdata('error', 'Rol inválido');
                redirect('auth/seleccionar_rol');
            }
        } else {
            $data['roles'] = $this->session->userdata('todos_roles');
            
            if ($this->input->is_ajax_request()) {
                $this->_json_response('success', 'Seleccione un rol', [
                    'html' => $this->load->view('auth/seleccionar_rol', $data, TRUE)
                ]);
            } else {
                $this->load->view('auth/seleccionar_rol', $data);
            }
        }
    }

    // cierre sesión
    public function logout() {
        $this->session->unset_userdata('logged_in');
        $this->session->unset_userdata('id_usuario');
        $this->session->unset_userdata('correo');
        $this->session->unset_userdata('rol_actual');
        $this->session->unset_userdata('nombre_rol');
        $this->session->unset_userdata('todos_roles');
        
        if ($this->input->is_ajax_request()) {
            $this->_json_response('success', 'Ha cerrado sesión correctamente', ['redirect' => site_url('auth/login')]);
        } else {
            $this->session->set_flashdata('logged_out', 'Ha cerrado sesión correctamente');
            redirect('auth/login');
        }
    }

    private function _json_response($status, $message, $data = []) {
        $response = array_merge([
            'status' => $status,
            'message' => $message
        ], $data);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    private function _get_redirect_url() {
        $rol_actual = $this->session->userdata('rol_actual');
        
        switch ($rol_actual) {
            case 1: // Cliente
                return site_url('dashboard/cliente');
            case 2:
                return site_url('dashboard/empleado'); 
               
            case 3: // Vendedor
                 return site_url('dashboard/admin');
            default:
                return site_url('auth/register');
        }
    }
}