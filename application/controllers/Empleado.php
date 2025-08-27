<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empleado extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Producto_model');
        $this->load->model('Venta_model');
        $this->load->model('Usuario_model');
        $this->load->model('Rol_model');
        $this->load->library('session');
        
        // Verificar autenticación y rol de empleado
        $this->verificar_acceso();
    }

    private function verificar_acceso()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        
        if ($this->session->userdata('rol_actual') != 2) {
            redirect('dashboard');
        }
    }

    public function index()
    {
        // Redirigir al dashboard principal del empleado
        redirect('dashboard/empleado');
    }


    public function actualizar_stock($id)
    {
    if (!$this->input->is_ajax_request()) {
        show_error('Acceso no permitido');
    }

    $producto = $this->Producto_model->get_producto($id);

    if (!$producto) {
        echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
        return;
    }

    $nuevo_stock = $this->input->post('stock');
    if ($nuevo_stock === null || !is_numeric($nuevo_stock)) {
        echo json_encode(['status' => 'error', 'message' => 'Stock inválido']);
        return;
    }

    if ($this->Producto_model->actualizar_stock($id, $nuevo_stock)) {
        echo json_encode(['status' => 'success', 'message' => 'Stock actualizado', 'stock' => $nuevo_stock]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar stock']);
    }
    }



    // Gestión de productos
    public function productos()
    {
        $data = array(
            'titulo' => 'Gestión de Productos',
            'productos' => $this->Producto_model->get_productos_activos()
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('empleado/productos', $data);
        $this->load->view('templates/footer');
    }

    public function crear_producto()
    {
        if ($this->input->post()) {
            $data = array(
                'imagen' => $this->input->post('imagen'),
                'nombre' => $this->input->post('nombre'),
                'descripcion' => $this->input->post('descripcion'),
                'precio' => $this->input->post('precio'),
                'stock' => $this->input->post('stock')
            );
            
            if ($this->Producto_model->crear_producto($data)) {
                $this->session->set_flashdata('success', 'Producto creado exitosamente');
                redirect('empleado/productos');
            } else {
                $this->session->set_flashdata('error', 'Error al crear el producto');
            }
        }
        
        $data = array('titulo' => 'Crear Producto');
        $this->load->view('templates/header', $data);
        $this->load->view('empleado/crear_producto', $data);
        $this->load->view('templates/footer');
    }

    public function editar_producto($id)
    {
        $producto = $this->Producto_model->get_producto($id);
        
        if (!$producto) {
            $this->session->set_flashdata('error', 'Producto no encontrado');
            redirect('empleado/productos');
        }
        
        if ($this->input->post()) {
            $data = array(
                'imagen' => $this->input->post('imagen'),
                'nombre' => $this->input->post('nombre'),
                'descripcion' => $this->input->post('descripcion'),
                'precio' => $this->input->post('precio'),
                'stock' => $this->input->post('stock')
            );
            
            if ($this->Producto_model->actualizar_producto($id, $data)) {
                $this->session->set_flashdata('success', 'Producto actualizado exitosamente');
                redirect('empleado/productos');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar el producto');
            }
        }
        
        $data = array(
            'titulo' => 'Editar Producto',
            'producto' => $producto
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('empleado/editar_producto', $data);
        $this->load->view('templates/footer');
    }

    public function eliminar_producto($id)
    {
        if ($this->Producto_model->cambiar_estado($id, 0)) {
            $this->session->set_flashdata('success', 'Producto eliminado exitosamente');
        } else {
            $this->session->set_flashdata('error', 'Error al eliminar el producto');
        }
        
        redirect('empleado/productos');
    }

    // Ver ventas
    public function ventas()
    {
        $data = array(
            'titulo' => 'Historial de Ventas',
            'ventas' => $this->Venta_model->get_todas_ventas()
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('empleado/ventas', $data);
        $this->load->view('templates/footer');
    }

public function detalle_venta($id_venta)
{
    $venta = $this->Venta_model->get_detalle_venta_completo($id_venta);
    $productos = $this->Venta_model->get_productos_venta($id_venta);

    if (!$venta) {
        // Si es AJAX, devolvemos JSON de error
        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Venta no encontrada']);
            return;
        }
        $this->session->set_flashdata('error', 'Venta no encontrada');
        redirect('empleado/ventas');
    }

    if ($this->input->is_ajax_request()) {
        $data = [
            'venta' => $venta,
            'productos' => $productos
        ];
        $this->load->view('empleado/partial_detalle_venta', $data);
        return;
    }
    $data = [
        'titulo' => 'Detalle de Venta #' . $id_venta,
        'venta' => $venta,
        'productos' => $productos
    ];

    $this->load->view('templates/header', $data);
    $this->load->view('empleado/detalle_venta', $data);
    $this->load->view('templates/footer');
}


    // Gestión de inventario
    public function inventario()
    {
        $data = array(
            'titulo' => 'Gestión de Inventario',
            'productos' => $this->Producto_model->get_productos_activos()
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('empleado/inventario', $data);
        $this->load->view('templates/footer');
    }

    // Atención a clientes
    public function atender_clientes()
    {
        $data = array(
            'titulo' => 'Atención a Clientes',
            'clientes' => $this->Usuario_model->get_clientes()
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('empleado/atencion_clientes', $data);
        $this->load->view('templates/footer');
    }

    // Reportes y estadísticas
    public function reportes()
    {
        $data = array(
            'titulo' => 'Reportes y Estadísticas',
            'estadisticas_ventas' => $this->Venta_model->get_estadisticas_ventas(),
            'estadisticas_productos' => $this->Producto_model->get_estadisticas(),
            'ventas_mensuales' => $this->Venta_model->get_ventas_mensuales()
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('empleado/reportes', $data);
        $this->load->view('templates/footer');
    }
}