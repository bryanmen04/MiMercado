<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas_m extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function obtener_ventas($dFechaInicio = false, $dFechaFin = false) {
        $this->db->select('v.eCodVenta, v.dFecha, v.eCantidad, v.dTotal, 
                          p.tNombre as tNombreProducto, p.dPrecio as dPrecioUnitario');
        $this->db->from('TblVentas v');
        $this->db->join('productos p', 'p.id = v.eCodProducto');
        $this->db->where('v.tCodEstatus', 'AC');
        
        if($dFechaInicio && $dFechaFin) {
            $this->db->where('v.dFecha >=', $dFechaInicio);
            $this->db->where('v.dFecha <=', $dFechaFin);
        }
        
        return $this->db->get()->result();
    }

    public function registrar_venta($datos) {
        $venta = array(
            'eCodProducto' => $datos['eCodProducto'],
            'eCantidad' => $datos['eCantidad'],
            'dTotal' => $datos['dTotal'],
            'dFecha' => $datos['dFecha'],
            'tCodEstatus' => 'AC'
        );

        $this->db->trans_start();
        $this->db->insert('TblVentas', $venta);
        
        // Actualizar stock
        $this->db->set('eStock', 'eStock - ' . $datos['eCantidad'], FALSE);
        $this->db->where('id', $datos['eCodProducto']);
        $this->db->update('productos');
        
        $this->db->trans_complete();
        
        return $this->db->trans_status() ? $this->db->insert_id() : false;
    }

    // Obtener total de ventas (versión segura)
    public function obtener_total_ventas($fecha_inicio = false, $fecha_fin = false) {
        // Verificar existencia de tabla
        if (!$this->db->table_exists('ventas')) {
            log_message('error', 'Ventas_m::obtener_total_ventas - tabla "ventas" no encontrada en la BD: '.$this->db->database);
            return 0;
        }

        $this->db->select_sum('total');
        $this->db->where('tCodEstatus', 'AC');

        if ($fecha_inicio && $fecha_fin) {
            $this->db->where('fecha >=', $fecha_inicio);
            $this->db->where('fecha <=', $fecha_fin);
        }

        // Ejecutar con db_debug deshabilitado para evitar pantalla de error
        $db_debug = isset($this->db->db_debug) ? $this->db->db_debug : TRUE;
        $this->db->db_debug = FALSE;
        $query = $this->db->get('ventas');
        $this->db->db_debug = $db_debug;

        if ($query === FALSE) {
            $err = $this->db->error();
            log_message('error', 'Ventas_m::obtener_total_ventas - consulta fallida: '. (isset($err['message']) ? $err['message'] : 'unknown'));
            return 0;
        }

        $row = $query->row();
        if (!is_object($row) || !isset($row->total)) {
            return 0;
        }

        return (float) $row->total;
    }

    // Obtener ventas por producto (versión segura)
    public function ventas_por_producto($producto_id) {
        // Verificar existencia de tabla
        if (!$this->db->table_exists('ventas')) {
            log_message('error', 'Ventas_m::ventas_por_producto - tabla "ventas" no encontrada en la BD: '.$this->db->database);
            return (object)['total_vendido' => 0, 'total_ingreso' => 0];
        }

        $this->db->select('SUM(cantidad) as total_vendido, SUM(total) as total_ingreso');
        $this->db->where('producto_id', $producto_id);
        $this->db->where('tCodEstatus', 'AC');

        // Ejecutar con db_debug deshabilitado
        $db_debug = isset($this->db->db_debug) ? $this->db->db_debug : TRUE;
        $this->db->db_debug = FALSE;
        $query = $this->db->get('ventas');
        $this->db->db_debug = $db_debug;

        if ($query === FALSE) {
            $err = $this->db->error();
            log_message('error', 'Ventas_m::ventas_por_producto - consulta fallida: '. (isset($err['message']) ? $err['message'] : 'unknown'));
            return (object)['total_vendido' => 0, 'total_ingreso' => 0];
        }

        $row = $query->row();
        if (!is_object($row)) {
            return (object)['total_vendido' => 0, 'total_ingreso' => 0];
        }

        $row->total_vendido = (float) ($row->total_vendido ?: 0);
        $row->total_ingreso = (float) ($row->total_ingreso ?: 0);

        return $row;
    }
}