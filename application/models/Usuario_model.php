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

    // Asignar rol al usuario
    public function asignar_rol($id_usuario, $id_rol) {
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

    // Obtener roles de usuario (solo roles activos)
    public function obtener_roles_usuario($id_usuario) {
        $this->db->select('r.id_rol, r.nombre_rol');
        $this->db->from('usuario_roles ur');
        $this->db->join('roles r', 'ur.id_rol = r.id_rol');
        $this->db->where('ur.id_usuario', $id_usuario);
        $this->db->where('ur.activo', 1);
        
        $query = $this->db->get();
        return $query->result();
    }

    // Obtener datos del usuario (solo activos)
    public function obtener_datos_usuario($id_usuario) {
        $this->db->where('id_usuario', $id_usuario);
        $this->db->where('activo', 1);
        $query = $this->db->get('datos_usuario');
        return $query->row();
    }

    // Método para eliminar usuario 
    public function desactivar_usuario($id_usuario) {
        $this->db->where('id_usuario', $id_usuario);
        return $this->db->update('usuarios', array('activo' => 0));
    }

    // Obtener todos los usuarios
    public function obtener_todos_usuarios() {
        $query = $this->db->get('usuarios');
        return $query->result();
    }

    // Activar usuario
    public function activar_usuario($id_usuario) {
        $this->db->where('id_usuario', $id_usuario);
        return $this->db->update('usuarios', array('activo' => 1));
    }


}
