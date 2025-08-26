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

}
?>
