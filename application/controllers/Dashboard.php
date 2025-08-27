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
        
        // Cargar modelos necesarios
        $this->load->model('Usuario_model');
        $this->load->model('Producto_model');
        $this->load->model('Venta_model');
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
        
        // Obtener datos del usuario para pasar a la vista
        $id_usuario = $this->session->userdata('id_usuario');
        $data['usuario'] = $this->Usuario_model->obtener_datos_usuario($id_usuario);
        $data['titulo'] = 'Panel de Cliente';
        
        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/cliente', $data);
        $this->load->view('templates/footer');
    }

    public function empleado() {
        if ($this->session->userdata('rol_actual') != 2) {
            redirect('dashboard');
        }
        
        // Cargar datos para el dashboard del empleado
        $data = array(
            'titulo' => 'Panel de Empleado',
            'total_productos' => $this->Producto_model->count_productos_activos(),
            'total_ventas_hoy' => $this->Venta_model->count_ventas_hoy(),
            'monto_ventas_hoy' => $this->Venta_model->get_total_ventas_hoy(),
            'productos_bajo_stock' => $this->Producto_model->get_productos_bajo_stock(),
            'ventas_recientes' => $this->Venta_model->get_ventas_hoy()
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/empleado', $data);
        $this->load->view('templates/footer');
    }
}