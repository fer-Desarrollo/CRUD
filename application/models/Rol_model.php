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
}