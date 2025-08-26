<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venta_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Obtener ventas de un usuario con detalles
    public function get_ventas_usuario($id_usuario) {
        $this->db->select('
            v.id_venta, 
            v.total, 
            v.fecha_venta, 
            dv.id_detalle, 
            dv.cantidad, 
            dv.precio_unitario, 
            p.nombre as producto, 
            p.imagen as producto_imagen
        ');
        $this->db->from('ventas v');
        $this->db->join('detalle_ventas dv', 'v.id_venta = dv.id_venta');
        $this->db->join('productos p', 'dv.id_producto = p.id_producto');
        $this->db->where('v.id_cliente', $id_usuario);
        $this->db->where('v.activo', 1);
        $this->db->where('dv.activo', 1);
        $this->db->order_by('v.fecha_venta', 'DESC');

        return $this->db->get()->result();
    }

    // Desactivar detalle de venta 
    public function eliminar_detalle_venta($id_detalle) {
        $this->db->where('id_detalle', $id_detalle);
        return $this->db->update('detalle_ventas', ['activo' => 0]);
    }

    // Crear venta
    public function crear_venta($id_cliente, $total) {
        $data = [
            'id_cliente'  => $id_cliente,
            'total'       => $total,
            'fecha_venta' => date('Y-m-d H:i:s'),
            'activo'      => 1
        ];
        $this->db->insert('ventas', $data);
        return $this->db->insert_id();
    }

    // Crear detalle de venta
    public function crear_detalle_venta($id_venta, $id_producto, $cantidad, $precio_unitario) {
        $data = [
            'id_venta'       => $id_venta,
            'id_producto'    => $id_producto,
            'cantidad'       => $cantidad,
            'precio_unitario'=> $precio_unitario,
            'activo'         => 1
        ];
        return $this->db->insert('detalle_ventas', $data);
    }

        // Últimas compras
    public function get_ventas_recientes($id_usuario, $limite = 5) {
        $this->db->select('v.fecha_venta, dv.cantidad, dv.precio_unitario as subtotal, p.nombre as producto_nombre');
        $this->db->from('ventas v');
        $this->db->join('detalle_ventas dv', 'v.id_venta = dv.id_venta');
        $this->db->join('productos p', 'dv.id_producto = p.id_producto');
        $this->db->where('v.id_cliente', $id_usuario);
        $this->db->order_by('v.fecha_venta', 'DESC');
        $this->db->limit($limite);
        return $this->db->get()->result();
    }

    // Total de compras de un usuario
    public function get_total_ventas_usuario($id_usuario) {
        $this->db->select_sum('total');
        $this->db->where('id_cliente', $id_usuario);
        $query = $this->db->get('ventas');
        return $query->row()->total ?? 0;
    }

}
?>
