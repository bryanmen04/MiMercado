<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sesion extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('secciones_m');
		$this->load->model('catalogos_m');
		$this->load->model('catinserts_m');
		$this->load->model('inserts_m');
		$this->load->helper('date_helper');
		date_default_timezone_set('America/Mexico_City');
	}

	public function index() {
		$data['usuario'] = "Admin";
		$this->load->view('Principal/login', $data);
	}

	public function logout() {
		$this->session->sess_destroy();
		$this->session->unset_userdata('bSesion');
		$this->session->unset_userdata('eCodUsuario');
		$this->session->unset_userdata('eCodPerfil');
		$this->session->unset_userdata('tNombre');
		$this->session->unset_userdata('tEmpresa');
		$this->session->unset_userdata('eCodEmpresa');
		$this->session->unset_userdata('tDepartamento');
		$this->session->unset_userdata('eCodDepartamento');
		$this->session->unset_userdata('tPuesto');
		$this->session->unset_userdata('tPerfil');
		$this->session->unset_userdata('tCorreo');
		$this->session->unset_userdata('tImagen');
		redirect(base_url());
	}

	public function login() {

		$tCadenaUsuarioEspacios 	= str_replace(' ', '',$this->input->post("tUsuario"));
		$tCadenaUsuarioParentesis 	= str_replace(')', '',$tCadenaUsuarioEspacios);
		$tCadenaUsuarioApostrofes 	= str_replace("'", "",$tCadenaUsuarioParentesis);
		$tCadenaUsuarioNumeral 		= str_replace('#', '',$tCadenaUsuarioApostrofes);
		$tCadenaUsuarioFinal		= str_replace(';', '',$tCadenaUsuarioNumeral);
		
		$tCadenaPasswordEspacios 	= str_replace(' ', '',$this->input->post("tPassword"));
		$tCadenaPasswordParentesis 	= str_replace(')', '',$tCadenaPasswordEspacios);
		$tCadenaPasswordApostrofes 	= str_replace("'", "",$tCadenaPasswordParentesis);
		$tCadenaPasswordNumeral 	= str_replace('#', '',$tCadenaPasswordApostrofes);
		$tCadenaPasswordFinal		= str_replace(';', '',$tCadenaPasswordNumeral);

		$tUsuario		= $this->security->xss_clean(strip_tags($tCadenaUsuarioFinal));
		$tPassword		= sha1($this->security->xss_clean(strip_tags($tCadenaPasswordFinal)));
		$eCodUsuario	= false;
		$con_usuarios	= $this->catalogos_m->con_usuarios(false, false, false, false, $tUsuario, $tPassword, "'AC'");
		$tUsuarioCompleto			  = $this->input->post("tUsuario");
		$tPasswordCompleto 			  = $this->input->post("tPassword");


        if (isset($_SERVER["HTTP_CLIENT_IP"])){
            $direccionIP = $_SERVER["HTTP_CLIENT_IP"];
        }
        elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $direccionIP = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_X_FORWARDED"])){
            $direccionIP = $_SERVER["HTTP_X_FORWARDED"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){
            $direccionIP = $_SERVER["HTTP_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED"])){
            $direccionIP = $_SERVER["HTTP_FORWARDED"];
        }
        else{
            $direccionIP = $_SERVER["REMOTE_ADDR"];
        }

        $IPLocal = getHostByName(getHostName());


		if (isset($con_usuarios)) {

			foreach ($con_usuarios as $usr) { 

				echo "<div class=\"alert alert-success\">
						<strong>¡Bienvenido ".$usr->tNombre."!, redireccionando al sistema... </strong></div>";
				echo "<input type=\"hidden\" id=\"eExito\" name=\"eExito\" value=\"1\">";
				echo "<input type=\"hidden\" id=\"eCodUsuario\" name=\"eCodUsuario\" value=\"".$eCodUsuario."\">";

				$this->session->set_userdata('bSesion', true);
				$this->session->set_userdata('bAdmin', $usr->bAdmin);
				$this->session->set_userdata('eCodUsuario', $usr->eCodUsuario);
				$this->session->set_userdata('eCodPerfil', $usr->eCodPerfil);
				$this->session->set_userdata('tNombre', $usr->tNombre);
				$this->session->set_userdata('tEmpresa', $usr->tEmpresa);
				$this->session->set_userdata('eCodEmpresa', $usr->eCodEmpresa);
				$this->session->set_userdata('tDepartamento', $usr->tDepartamento);
				$this->session->set_userdata('eCodDepartamento', $usr->eCodDepartamento);
				$this->session->set_userdata('tPuesto', $usr->tPuesto);
				$this->session->set_userdata('tPerfil', $usr->tPerfil);
				$this->session->set_userdata('tCorreo', $usr->tCorreo);
				$this->session->set_userdata('tImagen', $usr->tImagen);

		        $aDataLogin['eCodUsuario']   = $eCodUsuario;
		        $aDataLogin['eCodEvento']    = 8;
				$aDataLogin['tEvento']       = 'El Usuario: '.$tUsuario.' accedió al sistema desde la IP Pública: '.$direccionIP.' | IP Local: '.$IPLocal;
        		$this->inserts_m->ins_log($aDataLogin);
			}

		} else {
			echo "<div class=\"alert alert-danger\"><strong>¡El usuario o contrase&ntilde;a no existen!... </strong></div>";
			echo "<input type=\"hidden\" id=\"eExito\" name=\"eExito\" value=\"0\">";
	        
	        $aDataLogueo['eCodUsuario']   = 0;
	        $aDataLogueo['eCodEvento']    = 8;
			$aDataLogueo['tEvento']       = 'Intentaron acceder al sistema con el Usuario: " '.$tUsuarioCompleto.' " | Contraseña: " '.$tPasswordCompleto.' " desde la IP Pública: '.$direccionIP.' | IP Local: '.$IPLocal;

	      	$this->inserts_m->ins_log($aDataLogueo);
		}
	}

	public function bloqueo(){
		$bSesion = $this->input->post("bSesion");

		$this->session->set_userdata('bSesion', false);

	}

	public function desbloqueo(){
		$tPassword 		= sha1($this->security->xss_clean(strip_tags($this->input->post("tPassword"))));
		$con_usuarios 	= $this->catalogos_m->con_usuarios($this->session->userdata('eCodUsuario'), false, false, false, false, $tPassword, "AC");

		if (isset($con_usuarios)){

			foreach ($con_usuarios as $usr) { 

				$this->session->set_userdata('bSesion', true);
				$this->session->set_userdata('bAdmin', $usr->bAdmin);
				$this->session->set_userdata('eCodUsuario', $usr->eCodUsuario);
				$this->session->set_userdata('eCodPerfil', $usr->eCodPerfil);
				$this->session->set_userdata('tNombre', $usr->tNombre);
				$this->session->set_userdata('tEmpresa', $usr->tEmpresa);
				$this->session->set_userdata('eCodEmpresa', $usr->eCodEmpresa);
				$this->session->set_userdata('tDepartamento', $usr->tDepartamento);
				$this->session->set_userdata('eCodDepartamento', $usr->eCodDepartamento);
				$this->session->set_userdata('tPuesto', $usr->tPuesto);
				$this->session->set_userdata('tPerfil', $usr->tPerfil);
				$this->session->set_userdata('tCorreo', $usr->tCorreo);
				$this->session->set_userdata('tImagen', $usr->tImagen);

				echo "<div class=\"alert alert-success\">
						<strong>¡Bienvenido de vuelta ".$usr->tNombre."!</strong></div>";
				echo "<input type=\"hidden\" id=\"eExito\" name=\"eExito\" value=\"1\">";
				echo "<input type=\"hidden\" id=\"eCodUsuario\" name=\"eCodUsuario\" value=\"".$usr->eCodUsuario."\">";
			}

		} else {
			echo "<div class=\"alert alert-danger\"><strong>¡La contrase&ntilde;a es incorrecta! </strong></div>";
			echo "<input type=\"hidden\" id=\"eExito\" name=\"eExito\" value=\"0\">";
		}
	}


	public function perfil($eCodUsuario = false){
		
		$data['con_menu']			= $this->secciones_m->con_menu($this->session->userdata("eCodPerfil"));
		$data['con_usuarios']		= $this->catalogos_m->con_usuarios($this->session->userdata("eCodUsuario"));
		$data['con_empresas']		= $this->catalogos_m->con_empresas();
		$data['con_perfiles']		= $this->catalogos_m->con_perfiles();
		$data['con_departamentos']	= $this->catalogos_m->con_departamentos();

		$this->load->view('Encabezado/header', $data);
		$this->load->view('Encabezado/menu');
		$this->load->view('Intranet/sesion_perfil', $data);
	}

}