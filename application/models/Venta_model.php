<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venta_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Crear nueva venta
    public function crear_venta($id_cliente, $total) {
        $data = [
            'id_cliente' => $id_cliente,
            'total' => $total
        ];
        $this->db->insert('ventas', $data);
        return $this->db->insert_id();
    }

    // Agregar detalle de venta
    public function agregar_detalle_venta($id_venta, $id_producto, $cantidad, $precio_unitario, $subtotal) {
        $data = [
            'id_venta' => $id_venta,
            'id_producto' => $id_producto,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'subtotal' => $subtotal
        ];
        return $this->db->insert('detalle_ventas', $data);
    }

    // Obtener ventas por cliente
    public function get_ventas_por_cliente($id_cliente) {
        $this->db->select('v.*, COUNT(dv.id_detalle) as total_items');
        $this->db->from('ventas v');
        $this->db->join('detalle_ventas dv', 'v.id_venta = dv.id_venta', 'left');
        $this->db->where('v.id_cliente', $id_cliente);
        $this->db->where('v.activo', true);
        $this->db->group_by('v.id_venta');
        $this->db->order_by('v.fecha_venta', 'DESC');
        return $this->db->get()->result();
    }

    // Obtener detalle de venta
    public function get_detalle_venta($id_venta) {
        $this->db->select('dv.*, p.nombre as producto_nombre, p.imagen');
        $this->db->from('detalle_ventas dv');
        $this->db->join('productos p', 'dv.id_producto = p.id_producto');
        $this->db->where('dv.id_venta', $id_venta);
        $this->db->where('dv.activo', true);
        return $this->db->get()->result();
    }

    // Obtener venta por ID
    public function get_venta_by_id($id_venta) {
        $this->db->where('id_venta', $id_venta);
        $this->db->where('activo', true);
        return $this->db->get('ventas')->row();
    }
}
?>