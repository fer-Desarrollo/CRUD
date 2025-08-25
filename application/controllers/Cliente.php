<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Verificar autenticación y rol de cliente
        if (!$this->session->userdata('logged_in') || $this->session->userdata('rol_actual') != 1) {
            redirect('dashboard');
        }

        // Cargar modelos
        $this->load->model('Usuario_model');
        $this->load->model('Producto_model');
        $this->load->model('Venta_model');
        $this->load->library('form_validation');
    }

    // Catálogo de productos
    public function productos() {
        $id_usuario = $this->session->userdata('id_usuario');
        $data['usuario']   = $this->Usuario_model->obtener_datos_usuario($id_usuario);
        $data['titulo']    = 'Catálogo de Productos';
        $data['productos'] = $this->Producto_model->get_productos_activos();

        $this->load->view('templates/header', $data);
        $this->load->view('cliente/productos', $data);
        $this->load->view('templates/footer');
    }

    // Compra directa (1 unidad)
    public function comprar($id_producto) {
        $producto = $this->Producto_model->get_producto($id_producto);

        if (!$producto || $producto->stock < 1) {
            $this->session->set_flashdata('error', 'Producto no disponible');
            redirect('cliente/productos');
        }

        // Crear venta
        $venta_id = $this->Venta_model->crear_venta(
            $this->session->userdata('id_usuario'),
            $producto->precio
        );

        if ($venta_id) {
            $this->Venta_model->crear_detalle_venta($venta_id, $id_producto, 1, $producto->precio);
            $this->Producto_model->actualizar_stock($id_producto, -1);
            $this->session->set_flashdata('exito', 'Compra realizada con éxito');
        } else {
            $this->session->set_flashdata('error', 'Error al procesar la compra');
        }

        redirect('cliente/mis_compras');
    }

    // Historial de compras
    public function mis_compras() {
        $id_usuario = $this->session->userdata('id_usuario');
        $data['usuario'] = $this->Usuario_model->obtener_datos_usuario($id_usuario);
        $data['titulo']  = 'Mis Compras';
        $data['compras'] = $this->Venta_model->get_ventas_usuario($id_usuario);

        $this->load->view('templates/header', $data);
        $this->load->view('cliente/mis_compras', $data);
        $this->load->view('templates/footer');
    }

    // Perfil
    public function perfil() {
        $id_usuario = $this->session->userdata('id_usuario');
        $data['usuario'] = $this->Usuario_model->obtener_datos_usuario($id_usuario);
        $data['titulo']  = 'Mi Perfil';

        $this->load->view('templates/header', $data);
        $this->load->view('cliente/perfil', $data);
        $this->load->view('templates/footer');
    }

    // Actualizar perfil
    public function actualizar_perfil() {
        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('apellido_paterno', 'Apellido Paterno', 'required');
        $this->form_validation->set_rules('apellido_materno', 'Apellido Materno', 'required');
        $this->form_validation->set_rules('edad', 'Edad', 'required|numeric');
        $this->form_validation->set_rules('telefono', 'Teléfono', 'required');
        $this->form_validation->set_rules('direccion', 'Dirección', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->perfil();
        } else {
            $datos = array(
                'nombre'           => $this->input->post('nombre'),
                'apellido_paterno' => $this->input->post('apellido_paterno'),
                'apellido_materno' => $this->input->post('apellido_materno'),
                'edad'             => $this->input->post('edad'),
                'telefono'         => $this->input->post('telefono'),
                'direccion'        => $this->input->post('direccion')
            );

            if ($this->Usuario_model->actualizar_datos($this->session->userdata('id_usuario'), $datos)) {
                $this->session->set_flashdata('exito', 'Perfil actualizado con éxito');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar el perfil');
            }

            redirect('cliente/perfil');
        }
    }

    // Datos para dashboard (en JSON)
    public function obtener_datos_dashboard() {
        $id_usuario = $this->session->userdata('id_usuario');

        $data = array(
            'compras_recientes'   => $this->Venta_model->get_ventas_recientes($id_usuario, 5),
            'total_compras'       => $this->Venta_model->get_total_ventas_usuario($id_usuario),
            'productos_destacados'=> $this->Producto_model->get_productos_destacados(3)
        );

        echo json_encode($data);
    }

    // Comprar producto con cantidad personalizada ()
    public function comprar_producto() {
        $id_producto = $this->input->post('id_producto');
        $cantidad    = $this->input->post('cantidad');
        $id_usuario  = $this->session->userdata('id_usuario');

        if (!$id_producto || !$cantidad || $cantidad < 1) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }

        $producto = $this->Producto_model->get_producto($id_producto);
        if (!$producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }

        if ($producto->stock < $cantidad) {
            echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
            return;
        }

        $total    = $producto->precio * $cantidad;
        $venta_id = $this->Venta_model->crear_venta($id_usuario, $total);

        if ($venta_id) {
            $this->Venta_model->crear_detalle_venta($venta_id, $id_producto, $cantidad, $producto->precio);
            $this->Producto_model->actualizar_stock($id_producto, -$cantidad);
            echo json_encode(['success' => true, 'message' => 'Compra realizada con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al procesar la compra']);
        }
    }


    public function buscar_productos() {
        $termino   = $this->input->post('termino');
        $productos = $this->Producto_model->buscar_productos($termino);
        echo json_encode($productos);
    }

 public function eliminar_compra($id_detalle)
    {
    $this->load->model('Venta_model');

    if ($this->Venta_model->eliminar_detalle_venta($id_detalle)) {
        $this->session->set_flashdata('exito', 'Producto eliminado de tus compras.');
    } else {
        $this->session->set_flashdata('error', 'No se pudo eliminar la compra.');
    }

    redirect('cliente/mis_compras');
    }

}
