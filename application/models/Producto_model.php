<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_productos() {
        return $this->db->get('productos')->result();
    }

    public function crear_producto($data) {
        $this->db->insert('productos', $data);
        return $this->db->insert_id();
    }

    public function actualizar_producto($id_producto, $data) {
        $this->db->where('id_producto', $id_producto)->update('productos', $data);
        return true;
    }

    public function cambiar_estado($id_producto, $activo) {
        $this->db->where('id_producto', $id_producto)->update('productos', ['activo' => $activo]);
        return true;
    }

    // Obtener todos los productos activos
    public function get_productos_activos() {
        return $this->db->where('activo', 1)->get('productos')->result();
    }

    // Obtener producto por ID
    public function get_producto($id_producto) {
        return $this->db->where('id_producto', $id_producto)->where('activo', 1)->get('productos')->row();
    }

    // Buscar productos
    public function buscar_productos($termino) {
        $this->db->where('activo', 1);
        $this->db->group_start();
            $this->db->like('nombre', $termino);
            $this->db->or_like('descripcion', $termino);
        $this->db->group_end();
        return $this->db->get('productos')->result();
    }


    // Actualizar stock
    public function actualizar_stock($id_producto, $cantidad) {
        $this->db->set('stock', 'stock + ' . $cantidad, FALSE);
        $this->db->where('id_producto', $id_producto);
        return $this->db->update('productos');
    }

    // Obtener productos destacados
public function get_productos_destacados($limite = 5) {
    $this->db->where('activo', 1);
    // $this->db->where('destacado', 1); // quitar esta línea
    $this->db->order_by('fecha_alta', 'DESC');
    $this->db->limit($limite);
    return $this->db->get('productos')->result();
}


}
?>