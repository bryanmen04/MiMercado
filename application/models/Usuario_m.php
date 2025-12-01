<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_m extends CI_Model {

	private $tablas = [
		'TblUsuarios',
		'CatUsuarios',
		'usuarios',
		'Usuarios',
		'users',
		'app_users'
	];

	private $pkCandidates = [
		'eCodUsuario',
		'id',
		'iId',
		'user_id',
		'codigo'
	];

	public function __construct() {
		parent::__construct();
	}

	// Intenta borrar el usuario por id. Retorna array estandarizado.
	public function delete_user($ident) {
		if ($ident === '' || $ident === null) {
			return ['success' => false, 'message' => 'ID vacío', 'table' => null, 'pk' => null];
		}

		$id_safe = $this->db->escape_str($ident);
		log_message('debug', "Usuario_m::delete_user - intentando eliminar id: {$id_safe}");

		foreach ($this->tablas as $tabla) {
			if (!$this->db->table_exists($tabla)) {
				continue;
			}

			$fields = $this->db->list_fields($tabla);

			foreach ($this->pkCandidates as $pk) {
				if (in_array($pk, $fields)) {
					// Intento de borrado
					$this->db->trans_start();
					$this->db->where($pk, $id_safe)->delete($tabla);
					$this->db->trans_complete();

					if ($this->db->trans_status() === FALSE) {
						$err = $this->db->error();
						$msg = isset($err['message']) ? $err['message'] : 'Error desconocido al eliminar';
						log_message('error', "Usuario_m::delete_user - fallo eliminando en {$tabla} ({$pk}): {$msg}");
						return ['success' => false, 'message' => $msg, 'table' => $tabla, 'pk' => $pk];
					}

					$affected = $this->db->affected_rows();
					if ($affected > 0) {
						log_message('info', "Usuario_m::delete_user - eliminado en {$tabla} ({$pk}={$id_safe})");
						return ['success' => true, 'message' => "Registro eliminado (tabla {$tabla}, {$pk} = {$id_safe}).", 'table' => $tabla, 'pk' => $pk];
					} else {
						// No afectó filas: seguir probando otras tablas
						log_message('debug', "Usuario_m::delete_user - no afectó filas en {$tabla} para {$pk}={$id_safe}");
						continue;
					}
				}
			}
		}

		log_message('debug', "Usuario_m::delete_user - no se encontró registro para id={$id_safe} en tablas candidatas");
		return ['success' => false, 'message' => 'No se encontró el registro para eliminar en tablas candidatas', 'table' => null, 'pk' => null];
	}
}
