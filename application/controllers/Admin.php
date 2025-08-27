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

public function guardar_producto() {
    $id = $this->input->post('id_producto');
    $nombre = trim($this->input->post('nombre'));
    $precio = floatval($this->input->post('precio'));
    $stock = intval($this->input->post('stock'));
    $descripcion = trim($this->input->post('descripcion'));
    $imagen = $this->input->post('imagen') ?: 'https://via.placeholder.com/150';

    // Validaciones
    if(empty($nombre)) {
        echo json_encode(['success'=>false, 'message'=>'El nombre es obligatorio']);
        return;
    }
    if($precio <= 0) {
        echo json_encode(['success'=>false, 'message'=>'El precio debe ser mayor a 0']);
        return;
    }
    if($stock < 0) {
        echo json_encode(['success'=>false, 'message'=>'El stock no puede ser negativo']);
        return;
    }

    $data = [
        'nombre' => $nombre,
        'precio' => $precio,
        'stock' => $stock,
        'descripcion' => $descripcion,
        'imagen' => $imagen,
        'activo' => 1
    ];

    if($id) {
        $this->Producto_model->actualizar_producto($id, $data);
        echo json_encode(['success'=>true, 'message'=>'Producto actualizado']);
    } else {
        $this->Producto_model->crear_producto($data);
        echo json_encode(['success'=>true, 'message'=>'Producto creado']);
    }
}


    public function cambiar_estado_producto($id_producto, $activo) {
        $this->Producto_model->cambiar_estado($id_producto, $activo);
        echo json_encode(['success'=>true]);
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
