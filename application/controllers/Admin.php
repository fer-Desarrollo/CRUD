<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in') || $this->session->userdata('rol_actual') != 3) {
            redirect('dashboard');
        }
        $this->load->model('Usuario_model');
        $this->load->model('Producto_model');
        $this->load->model('Venta_model');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['usuario'] = $this->Usuario_model->get_usuario($this->session->userdata('id_usuario'));
        $data['titulo'] = "Panel de Administrador";
        $this->load->view('templates/header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('templates/footer');
    }

    public function productos() {
        $data['productos'] = $this->Producto_model->get_productos();
        $data['titulo'] = "Gestionar Productos";
        $this->load->view('templates/header', $data);
        $this->load->view('admin/productos', $data);
        $this->load->view('templates/footer');
    }

    public function reportes() {
        $data['reportes'] = $this->Venta_model->get_reportes_ventas();
        $data['titulo'] = "Reportes de Ventas";
        $this->load->view('templates/header', $data);
        $this->load->view('admin/reportes', $data);
        $this->load->view('templates/footer');
 
    }
    // Guardar usuario
public function guardar_usuario() {
    $id = $this->input->post('id_usuario');
    $usuario = [
        'correo' => $this->input->post('correo'),
        'contrasena' => password_hash('123456', PASSWORD_DEFAULT)
    ];
    $datos = [
        'nombre'=>$this->input->post('nombre'),
        'apellido_paterno'=>$this->input->post('apellido_paterno'),
        'apellido_materno'=>$this->input->post('apellido_materno'),
        'edad'=>$this->input->post('edad'),
        'telefono'=>$this->input->post('telefono'),
        'direccion'=>$this->input->post('direccion')
    ];
    $rol = $this->input->post('rol');

    if($id){
        $this->Usuario_model->actualizar_usuario($id, ['correo'=>$this->input->post('correo')], $datos);
        $this->Usuario_model->asignar_rol($id, $rol);
        echo json_encode(['success'=>true, 'message'=>'Usuario actualizado']);
    }else{
        $id_nuevo = $this->Usuario_model->crear_usuario($usuario, $datos);
        $this->Usuario_model->asignar_rol($id_nuevo, $rol);
        echo json_encode(['success'=>true, 'message'=>'Usuario creado']);
    }
}

// Cambiar estado
public function cambiar_estado_usuario($id_usuario, $activo){
    $this->Usuario_model->cambiar_estado($id_usuario, $activo);
    echo json_encode(['success'=>true]);
}

// Obtener datos para edición
public function usuarios(){
    if($this->input->post('id_usuario')){
        $usuario = $this->Usuario_model->get_usuario($this->input->post('id_usuario'));
        echo json_encode($usuario);
        return;
    }

    $data['usuarios'] = $this->Usuario_model->get_usuarios();
    $data['roles'] = $this->Usuario_model->get_roles();
    $data['titulo'] = "Gestionar Usuarios";
    $this->load->view('templates/header', $data);
    $this->load->view('admin/usuarios', $data);
    $this->load->view('templates/footer');
}

}
?>
