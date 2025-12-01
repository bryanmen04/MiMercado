<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Ventas_m');
        $this->load->model('Productos_m');
        $this->load->library('session');
        $this->load->helper(array('url','form'));
        $this->load->database();

        // Verificar conexión
        if (!$this->db->initialize()) {
            log_message('error', 'No se pudo conectar a la base de datos: ' . $this->db->error()['message']);
            show_error('Error de conexión a la base de datos');
        }
    }

    // Mostrar formulario nueva venta
    public function nueva() {
        // Obtener productos usando el método del modelo
        $data['productos'] = $this->Productos_m->obtener_productos_venta();
        
        $this->load->view('Encabezado/header');
        $this->load->view('Ventas/nueva', $data);
        $this->load->view('Encabezado/footer');
    }

    // Registrar venta (recibe POST)
    public function registrar() {
        // Obtener valores del POST usando los nombres correctos del formulario
        $eCodProducto = $this->input->post('eCodProducto');
        $eCantidad = intval($this->input->post('cantidad'));
        
        // Log para debugging
        log_message('debug', 'Datos recibidos - Producto: ' . $eCodProducto . ', Cantidad: ' . $eCantidad);
        
        // Verificar producto y stock
        $producto = $this->db->select('eCodProducto, tNombre, dPrecio, eStock')
                            ->from('TblProductos')
                            ->where('eCodProducto', $eCodProducto)
                            ->where('tCodEstatus', 'AC')
                            ->get()
                            ->row();

        // Log del resultado de la consulta
        log_message('debug', 'Producto encontrado: ' . print_r($producto, TRUE));
        
        if (!$producto) {
            $this->session->set_flashdata('error', 'El producto no existe');
            redirect('ventas/nueva');
            return;
        }

        if ($producto->eStock < $eCantidad) {
            $this->session->set_flashdata('error', 'Stock insuficiente. Disponible: ' . $producto->eStock);
            redirect('ventas/nueva');
            return;
        }

        // Calcular total
        $dTotal = $eCantidad * $producto->dPrecio;

        // Datos para insertar
        $venta = array(
            'eCodProducto' => $eCodProducto,
            'eCantidad' => $eCantidad,
            'dTotal' => $dTotal,
            'dFecha' => date('Y-m-d'),
            'tCodEstatus' => 'AC'
        );

        // Log de la venta a insertar
        log_message('debug', 'Datos de venta a insertar: ' . print_r($venta, TRUE));

        // Iniciar transacción
        $this->db->trans_start();

        try {
            // Insertar venta
            $this->db->insert('TblVentas', $venta);

            // Actualizar stock
            $this->db->set('eStock', 'eStock - ' . $eCantidad, FALSE);
            $this->db->where('eCodProducto', $eCodProducto);
            $this->db->update('TblProductos');

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Error en la transacción');
            }

            $this->session->set_flashdata('success', 'Venta registrada correctamente');

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al procesar venta: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al procesar la venta');
        }

        redirect('ventas/nueva');
    }

    // Mostrar reporte de ventas
    public function reporte() {
        $dFechaInicio = $this->input->get('desde');
        $dFechaFin = $this->input->get('hasta');

        // Debug: verificar estructura de tablas
        log_message('debug', 'Estructura TblProductos: ' . print_r($this->db->list_fields('TblProductos'), true));
        log_message('debug', 'Estructura TblVentas: ' . print_r($this->db->list_fields('TblVentas'), true));

        $this->db->select('v.eCodVenta, v.dFecha, v.eCantidad, v.dTotal, 
                          p.tNombre as tNombreProducto, p.dPrecio as dPrecioUnitario');
        $this->db->from('TblVentas v');
        $this->db->join('TblProductos p', 'p.eCodProducto = v.eCodProducto');
        $this->db->where('v.tCodEstatus', 'AC');
        
        if ($dFechaInicio && $dFechaFin) {
            $this->db->where('v.dFecha >=', $dFechaInicio);
            $this->db->where('v.dFecha <=', $dFechaFin);
        }

        $query = $this->db->get();
        
        if ($query === FALSE) {
            log_message('error', 'Error en consulta: ' . print_r($this->db->error(), true));
            $data['ventas'] = array();
        } else {
            $data['ventas'] = $query->result();
            log_message('debug', 'Resultados: ' . print_r($data['ventas'], true));
        }

        $this->load->view('Encabezado/header');
        $this->load->view('Ventas/reporte', $data);
        $this->load->view('Encabezado/footer');
    }

    public function exportar_pdf() {
        // Cargar librería TCPDF
        $this->load->library('pdf');

        $fecha_inicio = $this->input->get('desde');
        $fecha_fin = $this->input->get('hasta');

        // Usar las tablas y columnas estandarizadas
        $this->db->select('v.*, p.tNombre as nombre_producto, p.dPrecio as precio_unitario');
        $this->db->from('TblVentas v');
        $this->db->join('TblProductos p', 'p.eCodProducto = v.eCodProducto', 'left');
        $this->db->where('v.tCodEstatus', 'AC');

        if ($fecha_inicio && $fecha_fin) {
            $this->db->where('v.dFecha >=', $fecha_inicio);
            $this->db->where('v.dFecha <=', $fecha_fin);
        }

        $ventas = $this->db->get()->result();

        // Crear PDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('MiMercado');
        $pdf->SetAuthor('MiMercado');
        $pdf->SetTitle('Reporte de Ventas');
        $pdf->AddPage();

        $html = $this->load->view('Ventas/reporte_pdf', ['ventas' => $ventas], true);
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Reporte_Ventas.pdf', 'D');
    }

    public function exportar_excel() {
        // Cargar librería PhpSpreadsheet
        $this->load->library('phpspreadsheet');

        $fecha_inicio = $this->input->get('desde');
        $fecha_fin = $this->input->get('hasta');

        $this->db->select('v.dFecha as fecha, p.tNombre as producto, v.eCantidad as cantidad, p.dPrecio as precio_unitario, v.dTotal as total');
        $this->db->from('TblVentas v');
        $this->db->join('TblProductos p', 'p.eCodProducto = v.eCodProducto', 'left');
        $this->db->where('v.tCodEstatus', 'AC');

        if ($fecha_inicio && $fecha_fin) {
            $this->db->where('v.dFecha >=', $fecha_inicio);
            $this->db->where('v.dFecha <=', $fecha_fin);
        }

        $ventas = $this->db->get()->result();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'Fecha');
        $sheet->setCellValue('B1', 'Producto');
        $sheet->setCellValue('C1', 'Cantidad');
        $sheet->setCellValue('D1', 'Precio Unit.');
        $sheet->setCellValue('E1', 'Total');

        // Datos
        $row = 2;
        foreach ($ventas as $venta) {
            $sheet->setCellValue('A'.$row, $venta->fecha);
            $sheet->setCellValue('B'.$row, $venta->producto);
            $sheet->setCellValue('C'.$row, $venta->cantidad);
            $sheet->setCellValue('D'.$row, $venta->precio_unitario);
            $sheet->setCellValue('E'.$row, $venta->total);
            $row++;
        }

        foreach(range('A','E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte_Ventas.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    // Método index para que /ventas no devuelva 404
    public function index() {
        // Cambia a $this->reporte(); si prefieres que /ventas muestre el reporte por defecto
        $this->nueva();
    }
}