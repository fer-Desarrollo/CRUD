<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Obtener producto por ID
    public function get_producto_by_id($id_producto) {
        $this->db->where('id_producto', $id_producto);
        $this->db->where('activo', true);
        return $this->db->get('productos')->row();
    }

    // Actualizar stock
    public function actualizar_stock($id_producto, $cantidad) {
        $this->db->set('stock', 'stock - ' . (int)$cantidad, FALSE);
        $this->db->where('id_producto', $id_producto);
        $this->db->update('productos');
        return $this->db->affected_rows();
    }

    // Buscar productos
    public function buscar_productos($termino) {
        $this->db->where('activo', true);
        $this->db->where('stock >', 0);
        $this->db->group_start();
        $this->db->like('nombre', $termino);
        $this->db->or_like('descripcion', $termino);
        $this->db->group_end();
        return $this->db->get('productos')->result();
    }
    public function get_productos_activos() {
    $this->db->where('activo', true);
    $this->db->order_by('nombre', 'ASC');
    return $this->db->get('productos')->result();
}
}
?>