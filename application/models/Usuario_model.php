<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Registrar usuario
    public function registrar_usuario($correo, $contrasena) {
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $data = array(
            'correo' => $correo,
            'contrasena' => $contrasena_hash,
            'activo' => true
        );
        $this->db->insert('usuarios', $data);
        return $this->db->insert_id();
    }

    // Registrar datos personales del usuario
    public function registrar_datos_usuario($id_usuario, $datos) {
        $data = array(
            'id_usuario' => $id_usuario,
            'nombre' => $datos['nombre'],
            'apellido_paterno' => $datos['apellido_paterno'],
            'apellido_materno' => $datos['apellido_materno'],
            'edad' => $datos['edad'],
            'telefono' => $datos['telefono'],
            'direccion' => $datos['direccion'],
            'activo' => true
        );
        return $this->db->insert('datos_usuario', $data);
    }

    // Crear usuario completo
    public function crear_usuario($usuario, $datos) {
        $id = $this->registrar_usuario($usuario['correo'], $usuario['contrasena']);
        $this->registrar_datos_usuario($id, $datos);
        return $id;
    }

    // Asignar rol al usuario
    public function asignar_rol($id_usuario, $id_rol) {
        $this->db->where('id_usuario', $id_usuario);
        $this->db->update('usuario_roles', ['activo'=>0]); // desactiva roles anteriores
        $data = array(
            'id_usuario' => $id_usuario,
            'id_rol' => $id_rol,
            'activo' => true
        );
        return $this->db->insert('usuario_roles', $data);
    }

    // Verificar login
    public function verificar_login($correo, $contrasena) {
        $this->db->where('correo', $correo);
        $this->db->where('activo', 1);
        $query = $this->db->get('usuarios');
        if ($query->num_rows() == 1) {
            $usuario = $query->row();
            if (password_verify($contrasena, $usuario->contrasena)) {
                return $usuario;
            }
        }
        return false;
    }

    // Obtener roles de usuario activos
    public function obtener_roles_usuario($id_usuario) {
        $this->db->select('r.id_rol, r.nombre_rol');
        $this->db->from('usuario_roles ur');
        $this->db->join('roles r', 'ur.id_rol = r.id_rol');
        $this->db->where('ur.id_usuario', $id_usuario);
        $this->db->where('ur.activo', 1);
        $query = $this->db->get();
        return $query->result();
    }

    // Obtener datos del usuario
    public function obtener_datos_usuario($id_usuario) {
        $this->db->where('id_usuario', $id_usuario);
        $this->db->where('activo', 1);
        return $this->db->get('datos_usuario')->row();
    }

    // Actualizar datos personales
    public function actualizar_datos($id_usuario, $datos) {
        $this->db->where('id_usuario', $id_usuario);
        return $this->db->update('datos_usuario', $datos);
    }

    // Actualizar usuario (correo)
    public function actualizar_usuario($id_usuario, $usuario, $datos) {
        $this->db->where('id_usuario', $id_usuario);
        $this->db->update('usuarios', $usuario);
        $this->actualizar_datos($id_usuario, $datos);
        return true;
    }

    // Cambiar estado usuario
    public function cambiar_estado($id_usuario, $activo) {
        $this->db->where('id_usuario', $id_usuario);
        $this->db->update('usuarios', ['activo'=>$activo]);
    }

    // Obtener todos los usuarios con su rol activo
    public function get_usuarios() {
        $this->db->select('u.id_usuario, u.correo, u.activo, d.nombre, d.apellido_paterno, d.apellido_materno, r.id_rol');
        $this->db->from('usuarios u');
        $this->db->join('datos_usuario d', 'u.id_usuario = d.id_usuario');
        $this->db->join('usuario_roles ur', 'u.id_usuario = ur.id_usuario AND ur.activo=1');
        $this->db->join('roles r', 'ur.id_rol = r.id_rol');
        $this->db->where('u.activo',1);
        return $this->db->get()->result();
    }

    // Obtener roles disponibles
    public function get_roles() {
        return $this->db->get('roles')->result();
    }

    // Obtener un solo usuario (para editar)
    public function get_usuario($id_usuario) {
        $this->db->select('u.id_usuario, u.correo, u.activo, d.nombre, d.apellido_paterno, d.apellido_materno, d.edad, d.telefono, d.direccion, r.id_rol');
        $this->db->from('usuarios u');
        $this->db->join('datos_usuario d', 'u.id_usuario = d.id_usuario');
        $this->db->join('usuario_roles ur', 'u.id_usuario = ur.id_usuario AND ur.activo=1', 'left');
        $this->db->join('roles r', 'ur.id_rol = r.id_rol', 'left');
        $this->db->where('u.id_usuario', $id_usuario);
        return $this->db->get()->row();
    }

    // ===== MÉTODOS NUEVOS PARA EL PANEL DE EMPLEADO =====

    /**
     * Obtener usuarios por rol específico
     */
    public function get_usuarios_por_rol($id_rol) {
        $this->db->select('u.id_usuario, u.correo, d.nombre, d.apellido_paterno, d.apellido_materno');
        $this->db->from('usuarios u');
        $this->db->join('datos_usuario d', 'u.id_usuario = d.id_usuario');
        $this->db->join('usuario_roles ur', 'u.id_usuario = ur.id_usuario AND ur.activo=1');
        $this->db->where('ur.id_rol', $id_rol);
        $this->db->where('u.activo', 1);
        return $this->db->get()->result();
    }

    /**
     * Obtener clientes (usuarios con rol de cliente)
     */
    public function get_clientes() {
        return $this->get_usuarios_por_rol(1); // ID 1 = Cliente
    }

    /**
     * Obtener empleados (usuarios con rol de empleado)
     */
    public function get_empleados() {
        return $this->get_usuarios_por_rol(2); // ID 2 = Empleado
    }

    /**
     * Contar total de usuarios por rol
     */
    public function count_usuarios_por_rol($id_rol) {
        $this->db->from('usuario_roles ur');
        $this->db->join('usuarios u', 'ur.id_usuario = u.id_usuario');
        $this->db->where('ur.id_rol', $id_rol);
        $this->db->where('ur.activo', 1);
        $this->db->where('u.activo', 1);
        return $this->db->count_all_results();
    }

    /**
     * Contar total de clientes
     */
    public function count_clientes() {
        return $this->count_usuarios_por_rol(1);
    }

    /**
     * Contar total de empleados
     */
    public function count_empleados() {
        return $this->count_usuarios_por_rol(2);
    }

    /**
     * Buscar usuarios por nombre, apellido o correo
     */
    public function buscar_usuarios($termino) {
        $this->db->select('u.id_usuario, u.correo, d.nombre, d.apellido_paterno, d.apellido_materno, r.nombre_rol');
        $this->db->from('usuarios u');
        $this->db->join('datos_usuario d', 'u.id_usuario = d.id_usuario');
        $this->db->join('usuario_roles ur', 'u.id_usuario = ur.id_usuario AND ur.activo=1');
        $this->db->join('roles r', 'ur.id_rol = r.id_rol');
        $this->db->where('u.activo', 1);
        $this->db->group_start();
        $this->db->like('d.nombre', $termino);
        $this->db->or_like('d.apellido_paterno', $termino);
        $this->db->or_like('d.apellido_materno', $termino);
        $this->db->or_like('u.correo', $termino);
        $this->db->group_end();
        return $this->db->get()->result();
    }

    /**
     * Verificar si el correo ya existe
     */
    public function correo_existe($correo, $id_excluir = null) {
        $this->db->where('correo', $correo);
        $this->db->where('activo', 1);
        
        if ($id_excluir) {
            $this->db->where('id_usuario !=', $id_excluir);
        }
        
        return $this->db->count_all_results('usuarios') > 0;
    }

    /**
     * Obtener estadísticas de usuarios
     */
    public function get_estadisticas_usuarios() {
        $estadisticas = array(
            'total_usuarios' => $this->db->where('activo', 1)->count_all_results('usuarios'),
            'total_clientes' => $this->count_clientes(),
            'total_empleados' => $this->count_empleados(),
            'usuarios_nuevos_mes' => $this->db->where('activo', 1)
                                            ->where('MONTH(fecha_registro)', date('m'))
                                            ->where('YEAR(fecha_registro)', date('Y'))
                                            ->count_all_results('usuarios')
        );
        
        return $estadisticas;
    }

    /**
     * Obtener usuarios recientemente registrados
     */
    public function get_usuarios_recientes($limite = 5) {
        $this->db->select('u.id_usuario, u.correo, u.fecha_registro, d.nombre, d.apellido_paterno, r.nombre_rol');
        $this->db->from('usuarios u');
        $this->db->join('datos_usuario d', 'u.id_usuario = d.id_usuario');
        $this->db->join('usuario_roles ur', 'u.id_usuario = ur.id_usuario AND ur.activo=1');
        $this->db->join('roles r', 'ur.id_rol = r.id_rol');
        $this->db->where('u.activo', 1);
        $this->db->order_by('u.fecha_registro', 'DESC');
        $this->db->limit($limite);
        return $this->db->get()->result();
    }

    /**
     * Cambiar contraseña de usuario
     */
    public function cambiar_contrasena($id_usuario, $nueva_contrasena) {
        $contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        return $this->db->where('id_usuario', $id_usuario)
                       ->update('usuarios', ['contrasena' => $contrasena_hash]);
    }

    /**
     * Obtener historial de compras de un cliente
     */
    public function get_historial_compras($id_usuario) {
        $this->db->select('v.id_venta, v.total, v.fecha_venta, COUNT(dv.id_detalle) as total_productos');
        $this->db->from('ventas v');
        $this->db->join('detalle_ventas dv', 'v.id_venta = dv.id_venta');
        $this->db->where('v.id_cliente', $id_usuario);
        $this->db->where('v.activo', 1);
        $this->db->where('dv.activo', 1);
        $this->db->group_by('v.id_venta');
        $this->db->order_by('v.fecha_venta', 'DESC');
        return $this->db->get()->result();
    }
}
?>