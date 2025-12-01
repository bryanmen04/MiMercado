<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos_m extends CI_Model {

    function __construct(){
        parent::__construct();
    }

    // Obtener todos los productos activos usando el procedimiento almacenado
    public function obtener_productos($eCodProducto = false, $eCodCategoria = false, $bBajoStock = false) {
        $sql = "CALL stpConsultarProductos(?, ?, ?)";
        $params = array(
            $eCodProducto ?: NULL,
            $eCodCategoria ?: NULL,
            $bBajoStock ?: NULL
        );

        $query = $this->db->query($sql, $params);
        $result = ($query !== FALSE) ? $query->result() : array();

        // Liberar recurso del objeto query de CI si existe
        if (is_object($query) && method_exists($query, 'free_result')) {
            $query->free_result();
        }

        // Liberar/consumir resultados pendientes de mysqli para evitar "Commands out of sync"
        $this->_flush_multi_results();

        return $result;
    }

    // Buscar producto por nombre (retorna id o FALSE)
    public function producto_por_nombre($tNombre) {
        $query = $this->db->select('eCodProducto')->get_where('TblProductos', array('tNombre' => $tNombre));
        if ($query && $query->num_rows() > 0) {
            return $query->row()->eCodProducto;
        }
        return FALSE;
    }

    // Registrar nuevo producto usando SP o fallback INSERT. Devuelve array estandarizado.
    public function registrar_producto($datos) {
        // Verificar duplicado
        if (!empty($datos['tNombre']) && $this->producto_por_nombre($datos['tNombre'])) {
            return ['success' => false, 'error' => 'Ya existe un producto con ese nombre', 'insert_id' => null];
        }

        // Preparar datos para insert
        $insert_data = array(
            'tNombre' => $datos['tNombre'],
            'tDescripcion' => $datos['tDescripcion'],
            'dPrecio' => $datos['dPrecio'],
            'eStock' => $datos['eStock'],
            'eCodCategoria' => isset($datos['eCodCategoria']) ? $datos['eCodCategoria'] : NULL,
            'tCodEstatus' => isset($datos['tCodEstatus']) ? $datos['tCodEstatus'] : 'AC'
        );

        // Intentar insert directo
        $this->db->db_debug = FALSE;
        $ok = $this->db->insert('TblProductos', $insert_data);
        $err = $this->db->error();
        $this->db->db_debug = TRUE;

        if (!$ok) {
            $mensaje = isset($err['message']) ? $err['message'] : 'Error desconocido';
            $codigo = isset($err['code']) ? $err['code'] : 0;
            log_message('error', "Productos_m::registrar_producto - Error ($codigo): $mensaje");
            return [
                'success' => false, 
                'error' => "Error al insertar producto: $mensaje (código: $codigo)",
                'insert_id' => null,
                'debug' => $err
            ];
        }

        $insert_id = $this->db->insert_id();
        if (!$insert_id) {
            log_message('error', 'Productos_m::registrar_producto - Insert OK pero no se obtuvo ID');
            return ['success' => false, 'error' => 'No se pudo obtener el ID del producto creado', 'insert_id' => null];
        }

        return ['success' => true, 'error' => null, 'insert_id' => $insert_id];
    }

    // Actualizar producto usando el procedimiento almacenado
    public function actualizar_producto($eCodProducto, $datos) {
        $sql = "CALL stpInsertarProducto(?, ?, ?, ?, ?, ?)";
        $params = array(
            $eCodProducto,
            $datos['tNombre'],
            $datos['tDescripcion'],
            $datos['dPrecio'],
            NULL, // eStock no se actualiza aquí
            $datos['eCodCategoria']
        );

        $query = $this->db->query($sql, $params);

        // Liberar recurso del objeto query de CI si existe
        if (is_object($query) && method_exists($query, 'free_result')) {
            $query->free_result();
        }

        // Limpiar resultados pendientes
        $this->_flush_multi_results();

        return $query;
    }

    // Obtener categorías
    public function obtener_categorias() {
        $query = $this->db->query("
            SELECT eCodCategoria, tNombre, tDescripcion 
            FROM CatCategorias 
            WHERE tCodEstatus = 'AC'
        ");
        
        if($query === FALSE) {
            // Si hay error, probablemente las tablas no existen
            return array();
        }
        
        return $query->result();
    }

    // Obtener productos formateados para vista de ventas
    public function obtener_productos_venta() {
        $this->db->select('eCodProducto, tNombre, tDescripcion, dPrecio, eStock');
        $this->db->where('tCodEstatus', 'AC');
        $this->db->where('eStock >', 0);
        
        $query = $this->db->get('TblProductos');
        
        if ($query === FALSE) {
            return array();
        }
        
        $productos = array();
        foreach ($query->result() as $row) {
            $producto = new stdClass();
            $producto->id = $row->eCodProducto;
            $producto->nombre = $row->tNombre;
            $producto->descripcion = $row->tDescripcion;
            $producto->dPrecio = $row->dPrecio;    // Mantener dPrecio como en la BD
            $producto->eStock = $row->eStock;       // Mantener eStock como en la BD
            $productos[] = $producto;
        }
        
        return $productos;
    }

    // Agregar método para limpiar resultados pendientes en mysqli (evita error 2014)
    private function _flush_multi_results() {
        $conn = isset($this->db->conn_id) ? $this->db->conn_id : NULL;
        if (!is_object($conn)) {
            return;
        }

        // Solo para mysqli driver: consumir/limpiar todos los resultados pendientes
        if (method_exists($conn, 'more_results')) {
            // Avanzar y liberar todos los resultados pendientes
            while ($conn->more_results()) {
                $conn->next_result();

                if (method_exists($conn, 'store_result')) {
                    $res = $conn->store_result();

                    // si es un mysqli_result, liberarlo correctamente
                    if (is_object($res)) {
                        if ($res instanceof \mysqli_result && method_exists($res, 'free')) {
                            $res->free();
                        } elseif (method_exists($res, 'free_result')) {
                            // casos de wrappers que implementen free_result()
                            $res->free_result();
                        }
                    }
                }
            }
        }
    }
}