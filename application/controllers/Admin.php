<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Usuario_model');
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        if ($this->session->userdata('rol_actual') != 3) {
            $this->session->set_flashdata('error', 'No tienes permisos para acceder al panel de administrador');
            redirect('dashboard');
        }
    }

    // Dashborad
    public function index() {
        $data['titulo'] = 'Panel de Administrador';
        $data['rol_actual'] = $this->session->userdata('nombre_rol');
        $data['usuario'] = $this->session->userdata('correo');

        // Obtener lista de usuarios
        $data['usuarios'] = $this->Usuario_model->obtener_todos_usuarios();

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/admin', $data);
        $this->load->view('templates/footer');
    }

    // Desactivar usuario
    public function desactivar_usuario($id_usuario) {
        if ($this->Usuario_model->desactivar_usuario($id_usuario)) {
            $this->session->set_flashdata('msg', 'Usuario desactivado correctamente.');
        } else {
            $this->session->set_flashdata('error', 'No se pudo desactivar al usuario.');
        }
        redirect('admin');
    }

    // Activar usuario
    public function activar_usuario($id_usuario) {
        if ($this->Usuario_model->activar_usuario($id_usuario)) {
            $this->session->set_flashdata('msg', 'Usuario activado correctamente.');
        } else {
            $this->session->set_flashdata('error', 'No se pudo activar al usuario.');
        }
        redirect('admin');
    }
}
