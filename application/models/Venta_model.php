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
}
?>
