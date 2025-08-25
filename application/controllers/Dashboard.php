<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        
        if (!$this->session->userdata('rol_actual')) {
            redirect('auth/seleccionar_rol');
        }
    }

    public function index() {
        $rol_actual = $this->session->userdata('rol_actual');
        
        switch ($rol_actual) {
            case 3:
                redirect('dashboard/admin');
                break;
            case 1:
                redirect('dashboard/cliente');
                break;
            case 2:
                redirect('dashboard/empleado');
                break;
            default:
                redirect('dashboard/cliente');
                break;
        }
    }

    public function admin() {
        if ($this->session->userdata('rol_actual') != 3) {
            redirect('dashboard');
        }
        
        $data['titulo'] = 'Panel de Administración';
        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/admin', $data);
        $this->load->view('templates/footer');
    }

    public function cliente() {
        if ($this->session->userdata('rol_actual') != 1) {
            redirect('dashboard');
        }
        
        $data['titulo'] = 'Panel de Cliente';
        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/cliente', $data);
        $this->load->view('templates/footer');
    }

    public function empleado() {
        if ($this->session->userdata('rol_actual') != 2) {
            redirect('dashboard');
        }
        
        $data['titulo'] = 'Panel de Empleado';
        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/empleado', $data);
        $this->load->view('templates/footer');
    }
}