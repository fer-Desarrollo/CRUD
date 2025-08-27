<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rol_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function obtener_roles() {
        $query = $this->db->get('roles');
        return $query->result();
    }

    public function obtener_rol($id_rol) {
        $this->db->where('id_rol', $id_rol);
        $query = $this->db->get('roles');
        return $query->row();
    }

    // ===== MÉTODOS NUEVOS PARA EL PANEL DE EMPLEADO =====

    /**
     * Obtener roles activos (todos los roles están activos por defecto en tu estructura)
     */
    public function get_roles_activos() {
        return $this->db->get('roles')->result();
    }

    /**
     * Verificar si un rol existe
     */
    public function rol_existe($id_rol) {
        return $this->db->where('id_rol', $id_rol)
                       ->count_all_results('roles') > 0;
    }

    /**
     * Obtener nombre del rol por ID
     */
    public function get_nombre_rol($id_rol) {
        $rol = $this->obtener_rol($id_rol);
        return $rol ? $rol->nombre_rol : 'Sin rol';
    }

    /**
     * Obtener usuarios por rol
     */
    public function get_usuarios_por_rol($id_rol) {
        $this->db->select('u.id_usuario, u.correo, d.nombre, d.apellido_paterno, d.apellido_materno');
        $this->db->from('usuarios u');
        $this->db->join('datos_usuario d', 'u.id_usuario = d.id_usuario');
        $this->db->join('usuario_roles ur', 'u.id_usuario = ur.id_usuario AND ur.activo = 1');
        $this->db->where('ur.id_rol', $id_rol);
        $this->db->where('u.activo', 1);
        return $this->db->get()->result();
    }

    /**
     * Contar usuarios por rol
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
     * Obtener estadísticas de roles
     */
    public function get_estadisticas_roles() {
        $roles = $this->obtener_roles();
        $estadisticas = array();
        
        foreach ($roles as $rol) {
            $estadisticas[$rol->nombre_rol] = $this->count_usuarios_por_rol($rol->id_rol);
        }
        
        return $estadisticas;
    }

    /**
     * Crear nuevo rol (si necesitas esta funcionalidad)
     */
    public function crear_rol($nombre_rol) {
        $data = array('nombre_rol' => $nombre_rol);
        return $this->db->insert('roles', $data);
    }

    /**
     * Actualizar rol
     */
    public function actualizar_rol($id_rol, $nombre_rol) {
        return $this->db->where('id_rol', $id_rol)
                       ->update('roles', array('nombre_rol' => $nombre_rol));
    }

    /**
     * Eliminar rol (solo si no tiene usuarios asignados)
     */
    public function eliminar_rol($id_rol) {
        // Verificar si el rol tiene usuarios asignados
        $usuarios_asignados = $this->count_usuarios_por_rol($id_rol);
        
        if ($usuarios_asignados == 0) {
            return $this->db->where('id_rol', $id_rol)->delete('roles');
        }
        
        return false;
    }

    /**
     * Obtener roles con cantidad de usuarios
     */
    public function get_roles_con_conteo() {
        $this->db->select('r.*, COUNT(ur.id_usuario) as total_usuarios');
        $this->db->from('roles r');
        $this->db->join('usuario_roles ur', 'r.id_rol = ur.id_rol AND ur.activo = 1', 'left');
        $this->db->join('usuarios u', 'ur.id_usuario = u.id_usuario AND u.activo = 1', 'left');
        $this->db->group_by('r.id_rol');
        return $this->db->get()->result();
    }
}
?>