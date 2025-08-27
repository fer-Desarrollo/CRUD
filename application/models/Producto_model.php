<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
// Agregar estos métodos si no existen
public function count_productos_activos() {
    return $this->db->where('activo', 1)->count_all_results('productos');
}

public function get_productos_bajo_stock() {
    return $this->db->where('stock <', 10)
                   ->where('activo', 1)
                   ->get('productos')
                   ->result();
}

    public function get_productos() {
        return $this->db->get('productos')->result();
    }

    public function get_producto($id_producto) {
        return $this->db->where('id_producto', $id_producto)->get('productos')->row();
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


    // Buscar productos
    public function buscar_productos($termino) {
        $this->db->where('activo', 1);
        $this->db->group_start();
            $this->db->like('nombre', $termino);
            $this->db->or_like('descripcion', $termino);
        $this->db->group_end();
        return $this->db->get('productos')->result();
    }

    public function actualizar_stock($id_producto, $cantidad) {
        $this->db->set('stock', $cantidad); 
        $this->db->where('id_producto', $id_producto);
        return $this->db->update('productos');
    }


    // Obtener productos destacados
    public function get_productos_destacados($limite = 5) {
        $this->db->where('activo', 1);
        $this->db->order_by('fecha_alta', 'DESC');
        $this->db->limit($limite);
        return $this->db->get('productos')->result();
    }

    // ===== MÉTODOS NUEVOS PARA EL PANEL DE EMPLEADO =====


    /**
     * Obtener productos con filtros para paginación
     */
    public function get_productos_paginados($limit, $offset) {
        return $this->db->where('activo', 1)
                       ->order_by('nombre', 'ASC')
                       ->limit($limit, $offset)
                       ->get('productos')
                       ->result();
    }

    /**
     * Obtener productos por categoría (si tuvieras categorías)
     * Este método es por si decides agregar categorías después
     */
    public function get_productos_por_categoria($id_categoria) {
        return $this->db->where('activo', 1)
                       ->where('id_categoria', $id_categoria)
                       ->get('productos')
                       ->result();
    }

    /**
     * Verificar si existe un producto por nombre
     */
    public function existe_producto($nombre, $id_excluir = null) {
        $this->db->where('nombre', $nombre);
        $this->db->where('activo', 1);
        
        if ($id_excluir) {
            $this->db->where('id_producto !=', $id_excluir);
        }
        
        return $this->db->count_all_results('productos') > 0;
    }

    /**
     * Obtener productos ordenados por más vendidos
     * (Requiere join con detalle_ventas)
     */
    public function get_productos_mas_vendidos($limite = 10) {
        return $this->db->select('productos.*, SUM(detalle_ventas.cantidad) as total_vendido')
                       ->from('productos')
                       ->join('detalle_ventas', 'detalle_ventas.id_producto = productos.id_producto')
                       ->where('productos.activo', 1)
                       ->group_by('productos.id_producto')
                       ->order_by('total_vendido', 'DESC')
                       ->limit($limite)
                       ->get()
                       ->result();
    }

    /**
     * Actualizar imagen del producto
     */
    public function actualizar_imagen($id_producto, $imagen) {
        return $this->db->where('id_producto', $id_producto)
                       ->update('productos', ['imagen' => $imagen]);
    }

    /**
     * Obtener estadísticas de productos
     */
    public function get_estadisticas() {
        $estadisticas = array(
            'total_productos' => $this->count_productos_activos(),
            'productos_bajo_stock' => $this->db->where('stock <', 10)
                                             ->where('activo', 1)
                                             ->count_all_results('productos'),
            'productos_sin_stock' => $this->db->where('stock', 0)
                                            ->where('activo', 1)
                                            ->count_all_results('productos'),
            'productos_nuevos_mes' => $this->db->where('activo', 1)
                                             ->where('MONTH(fecha_alta)', date('m'))
                                             ->where('YEAR(fecha_alta)', date('Y'))
                                             ->count_all_results('productos')
        );
        
        return $estadisticas;
    }
}
?>