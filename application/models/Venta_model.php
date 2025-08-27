<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venta_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    

    public function get_todas_ventas() {
        $this->db->select('ventas.id_venta, ventas.id_cliente, ventas.fecha_venta, ventas.total, usuarios.correo as cliente_nombre');
        $this->db->from('ventas');
        $this->db->join('usuarios', 'usuarios.id_usuario = ventas.id_cliente', 'left');
        $this->db->where('ventas.activo', 1);
        $this->db->order_by('ventas.fecha_venta', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Obtener el detalle completo de una venta (cabecera)
     */
    public function get_detalle_venta_completo($id_venta) {
        $this->db->select('ventas.id_venta, ventas.id_cliente, ventas.fecha_venta, ventas.total, usuarios.correo as cliente_nombre');
        $this->db->from('ventas');
        $this->db->join('usuarios', 'usuarios.id_usuario = ventas.id_cliente', 'left');
        $this->db->where('ventas.id_venta', $id_venta);
        $this->db->where('ventas.activo', 1);
        return $this->db->get()->row();
    }

    /**
     * Obtener los productos de una venta
     */
    public function get_productos_venta($id_venta) {
        $this->db->select('productos.nombre, detalle_ventas.cantidad, detalle_ventas.precio_unitario, detalle_ventas.subtotal');
        $this->db->from('detalle_ventas');
        $this->db->join('productos', 'productos.id_producto = detalle_ventas.id_producto', 'left');
        $this->db->where('detalle_ventas.id_venta', $id_venta);
        $this->db->where('detalle_ventas.activo', 1);
        return $this->db->get()->result();
    }

    /**
     * Opcional: estadísticas de ventas
     */
    public function get_estadisticas_ventas() {
        $this->db->select('COUNT(id_venta) as total_ventas, SUM(total) as total_ingresos');
        $this->db->from('ventas');
        $this->db->where('activo', 1);
        return $this->db->get()->row();
    }

public function count_ventas_hoy() {
    $hoy = date('Y-m-d');
    return $this->db->where('DATE(fecha_venta)', $hoy)
                   ->where('activo', 1)
                   ->count_all_results('ventas');
}

public function get_total_ventas_hoy() {
    $hoy = date('Y-m-d');
    $this->db->select_sum('total');
    $this->db->where('DATE(fecha_venta)', $hoy);
    $this->db->where('activo', 1);
    $query = $this->db->get('ventas');
    return $query->row()->total ?? 0;
}

public function get_ventas_hoy() {
    $hoy = date('Y-m-d');
    $this->db->select('v.*, du.nombre as cliente_nombre, du.apellido_paterno as cliente_apellido');
    $this->db->from('ventas v');
    $this->db->join('datos_usuario du', 'v.id_cliente = du.id_usuario');
    $this->db->where('DATE(v.fecha_venta)', $hoy);
    $this->db->where('v.activo', 1);
    $this->db->order_by('v.fecha_venta', 'DESC');
    return $this->db->get()->result();
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

    public function get_total_ventas_usuario($id_usuario) {
        $this->db->select_sum('total');
        $this->db->where('id_cliente', $id_usuario);
        $query = $this->db->get('ventas');
        return $query->row()->total ?? 0;
    }

    public function get_ventas_por_fecha($fecha_inicio, $fecha_fin) {
        $this->db->select('
            v.*,
            du.nombre as cliente_nombre,
            du.apellido_paterno as cliente_apellido
        ');
        $this->db->from('ventas v');
        $this->db->join('datos_usuario du', 'v.id_cliente = du.id_usuario');
        $this->db->where('v.fecha_venta >=', $fecha_inicio . ' 00:00:00');
        $this->db->where('v.fecha_venta <=', $fecha_fin . ' 23:59:59');
        $this->db->where('v.activo', 1);
        $this->db->order_by('v.fecha_venta', 'DESC');
        return $this->db->get()->result();
    }

    public function get_ventas_mensuales() {
        $this->db->select('
            MONTH(fecha_venta) as mes,
            YEAR(fecha_venta) as año,
            COUNT(*) as total_ventas,
            SUM(total) as monto_total
        ');
        $this->db->from('ventas');
        $this->db->where('activo', 1);
        $this->db->group_by('YEAR(fecha_venta), MONTH(fecha_venta)');
        $this->db->order_by('año DESC, mes DESC');
        $this->db->limit(12);
        return $this->db->get()->result();
    }

    /**
     * Cancelar/eliminar venta (eliminación lógica)
     */
    public function cancelar_venta($id_venta) {
        // Primero desactivar la venta
        $this->db->where('id_venta', $id_venta);
        $this->db->update('ventas', ['activo' => 0]);
        
        // Luego desactivar los detalles de la venta
        $this->db->where('id_venta', $id_venta);
        return $this->db->update('detalle_ventas', ['activo' => 0]);
    }
}
?>