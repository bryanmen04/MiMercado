<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configuraciones_m extends CI_Model {
	function __construct() {
		parent::__construct();
	}

	public function con_correos($aFiltro = false) {
		$tQuery = 	" SELECT cc.*, ".
						" ce.tNombre as tEmpresa ".
					" FROM cog_correos cc ".
						" LEFT JOIN cat_empresas ce ON ce.eCodEmpresa = cc.eCodEmpresa ";
		$tQuery .=	(isset($aFiltro['eCodEmpresa'])	? " WHERE cc.eCodEmpresa = ".$aFiltro['eCodEmpresa'] : "");
		$tQuery .=	" ORDER BY cc.eCodCorreo ASC ";

		$query = $this->db->query($tQuery);
		if ($query->num_rows()>0) {
			return $query->result();
		}
	}

	public function con_logeventos($aFiltro = false) {
		$tQuery =	" SELECT cu.tNombre as tUsuario, ple.eCodEvento, ple.tEvento, ".
						" ce.tNombre as tTipoEvento, ce.tNombreCorto as tTipoEventoCorto, ".
						" ce.tIcono as tIconoEvento, ".
						" DATE_FORMAT(ple.fhFechaRegistro,'%d/%m/%Y %H:%i:%s') as fhFechaRegistro ".
					" FROM pro_logseventos ple ".
						" LEFT JOIN cat_eventos ce on ce.eCodEvento = ple.eCodEvento ".
						" LEFT JOIN cat_usuarios cu on cu.eCodUsuario = ple.eCodUsuario ".
					" WHERE ple.tCodEstatus = 'AC' ";
		$tQuery .=	(isset($aFiltro['eCodEvento'])		? " AND ple.eCodEvento = ".$aFiltro['eCodEvento']		: "");
		$tQuery .=	(isset($aFiltro['eCodUsuario'])		? " AND cu.eCodUsuario = ".$aFiltro['eCodUsuario']		: "");
		$tQuery .=	(isset($aFiltro['tEvento'])			? " AND ple.tEvento LIKE '%".$aFiltro['tEvento']."%'"	: "");
		$tQuery .=	(isset($aFiltro['fhFechaInicio'])	? " AND ple.fhFechaRegistro BETWEEN '".$aFiltro['fhFechaInicio']." 00:00:00' AND '".$aFiltro['fhFechaFinal']." 23:59:59'" : "");
		$tQuery .=	" ORDER BY ple.eCodLogEvento DESC ";

		//print_r($tQuery);
		$query = $this->db->query($tQuery);
		if ($query->num_rows()>0) {
			return $query->result();
		}
	}

	public function con_lognotificaciones($aFiltro = false) {
		$tQuery =	" SELECT pln.*, cu.tNombre as tUsuario, pln.eCodNotificacion, pln.tNotificacion, ".
						" cn.tNombre as tTipoNotificacion, cn.tNombreCorto as tTipoNotificacionCorto, ".
						" cn.tCodIcono as tIconoNotificacion, pln.fhFechaRegistro, ".
						" DATE_FORMAT(pln.fhFechaRegistro,'%d/%m/%Y %H:%i:%s') as fhFechaNotificacion ".
					" FROM pro_logsnotificaciones pln ".
						" LEFT JOIN cat_notificaciones cn on cn.eCodNotificacion = pln.eCodNotificacion ".
						" LEFT JOIN cat_usuarios cu on cu.eCodUsuario = pln.eCodUsuario ".
					" WHERE pln.tCodEstatus = 'AC' ";
		$tQuery .=	(isset($aFiltro['eCodNotificacion'])	? " AND pln.eCodNotificacion = ".$aFiltro['eCodNotificacion']	: "");
		$tQuery .=	(isset($aFiltro['eCodUsuario'])			? " AND cu.eCodUsuario = ".$aFiltro['eCodUsuario']				: "");
		$tQuery .=	(isset($aFiltro['eCodPerfil'])			? " AND pln.eCodPerfil =".$aFiltro['eCodPerfil']				: "");
		$tQuery .=	" ORDER BY pln.fhFechaRegistro DESC LIMIT 1 ";

		$query = $this->db->query($tQuery);
		if ($query->num_rows()>0) {
			return $query->result();
		}
	}


}
?>