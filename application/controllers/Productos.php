<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Productos_m');
        $this->load->library('session');
        $this->load->helper(array('url','form'));
        $this->load->database();
    }

    // Agregar método index para evitar 404 cuando se visita /productos
    public function index() {
        // Puedes cambiar a 'crear' si prefieres que la página por defecto sea el formulario
        $this->listar();
    }

    // Alias para compatibilidad: /productos/nuevo -> crear()
    public function nuevo() {
        $this->crear();
    }

    // Mostrar formulario para crear producto
    public function crear() {
        $data['categorias'] = $this->Productos_m->obtener_categorias();
        $this->load->view('Encabezado/header');
        $this->load->view('Productos/crear', $data);
        $this->load->view('Encabezado/footer');
    }

    // Guardar producto (recibe POST)
    public function guardar() {
        if (!$this->input->post()) {
            show_404();
            return;
        }

        $post = $this->input->post();
        $nombre = trim($post['tNombre']);

        // Comprobar duplicado antes de insertar
        if ($this->Productos_m->producto_por_nombre($nombre)) {
            $this->session->set_flashdata('error', 'Ya existe un producto con ese nombre.');
            redirect('productos/crear');
            return;
        }

        $datos = array(
            'tNombre' => $nombre,
            'tDescripcion' => trim($post['tDescripcion']),
            'dPrecio' => (float) $post['dPrecio'],
            'eStock' => (int) $post['eStock'],
            'eCodCategoria' => !empty($post['eCodCategoria']) ? $post['eCodCategoria'] : NULL,
            'tCodEstatus' => 'AC'
        );

        $result = $this->Productos_m->registrar_producto($datos);

        if (is_array($result)) {
            if ($result['success']) {
                $this->session->set_flashdata('success', 'Producto creado correctamente. ID: '.$result['insert_id']);
            } else {
                // En development mostramos error completo, en production solo mensaje genérico
                $error_msg = (defined('ENVIRONMENT') && ENVIRONMENT === 'development') 
                    ? $result['error']
                    : 'Error al crear el producto';
                
                log_message('error', 'Productos::guardar - Error: '.json_encode($result));
                $this->session->set_flashdata('error', $error_msg);
            }
        } else {
            log_message('error', 'Productos::guardar - Respuesta inesperada: '.json_encode($result));
            $this->session->set_flashdata('error', 'Error inesperado al crear el producto');
        }

        redirect('productos/crear');
    }

    // Listar productos activos
    public function listar() {
        $this->load->view('Encabezado/header');
        $this->db->select('eCodProducto as id, tNombre as nombre, tDescripcion as descripcion, dPrecio as precio, eStock as stock');
        $productos = $this->db->where('tCodEstatus', 'AC')->get('TblProductos')->result();
        $this->load->view('Productos/listar', ['productos' => $productos]);
        $this->load->view('Encabezado/footer');
    }
}