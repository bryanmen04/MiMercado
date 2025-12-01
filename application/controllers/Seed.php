<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seed extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->library('session');
    }

    // Ejecutar desde navegador: http://localhost/Mimercado/index.php/seed/inicializar
    public function inicializar() {
        $summary = ['categorias' => [], 'productos' => [], 'errors' => []];

        // Verificar tablas necesarias
        if (!$this->db->table_exists('CatCategorias')) {
            $summary['errors'][] = 'Tabla CatCategorias no encontrada';
        }
        if (!$this->db->table_exists('productos')) {
            $summary['errors'][] = 'Tabla productos no encontrada';
        }
        if (!empty($summary['errors'])) {
            // Mostrar y terminar
            echo '<h3>Seed abortado - tablas faltantes</h3><ul>';
            foreach ($summary['errors'] as $e) echo '<li>'.htmlspecialchars($e).'</li>';
            echo '</ul>';
            echo '<p>Ejecuta el archivo db/schema.sql para crear las tablas o revisa tu base de datos.</p>';
            return;
        }

        // Categorías a crear
        $cats = [
            'Lácteos' => 'Productos lácteos (leche, queso, yogur, etc.)',
            'Limpieza' => 'Productos de limpieza y desinfección'
        ];

        foreach ($cats as $name => $desc) {
            try {
                $row = $this->db->get_where('CatCategorias', ['tNombre' => $name])->row();
                if ($row) {
                    $cat_id = $row->eCodCategoria;
                    $summary['categorias'][] = "$name (ya existía id=$cat_id)";
                } else {
                    $ins = ['tNombre' => $name, 'tDescripcion' => $desc, 'tCodEstatus' => 'AC'];
                    $ok = $this->db->insert('CatCategorias', $ins);
                    $err = $this->db->error();
                    if (!$ok) {
                        $msg = isset($err['message']) ? $err['message'] : 'insert_failed';
                        log_message('error', 'Seed::inicializar - fallo crear categoria '.$name.': '.$msg);
                        $summary['categorias'][] = "$name error: $msg";
                        $summary['errors'][] = "CatCategorias: $name -> $msg";
                        continue;
                    }
                    $cat_id = $this->db->insert_id();
                    $summary['categorias'][] = "$name creado id=$cat_id";
                }

                // Productos de ejemplo por categoría
                $prods = ($name === 'Lácteos') ? [
                    ['Leche entera 1L', 'Leche pasteurizada 1 litro', 20.00, 50],
                    ['Queso fresco 250g', 'Queso fresco de vaca 250 gramos', 45.00, 30],
                    ['Yogur natural 125g', 'Yogur natural sin azúcar', 8.50, 100],
                ] : [
                    ['Detergente líquido 1L', 'Detergente concentrado para ropa', 35.00, 40],
                    ['Cloro 1L', 'Desinfectante clorado 1 litro', 18.00, 60],
                    ['Jabón multiusos 500g', 'Jabón en barra para limpieza general', 12.50, 80],
                ];

                foreach ($prods as $p) {
                    $nombre = $p[0];
                    try {
                        $exists = $this->db->get_where('productos', ['tNombre' => $nombre])->row();
                        if ($exists) {
                            $summary['productos'][] = "$nombre (ya existe id={$exists->id})";
                            continue;
                        }

                        $insert = [
                            'tNombre' => $nombre,
                            'tDescripcion' => $p[1],
                            'dPrecio' => $p[2],
                            'eStock' => $p[3],
                            'eCodCategoria' => $cat_id,
                            'tCodEstatus' => 'AC'
                        ];

                        $ok2 = $this->db->insert('productos', $insert);
                        $err2 = $this->db->error();
                        if (!$ok2) {
                            $msg2 = isset($err2['message']) ? $err2['message'] : 'insert_failed';
                            log_message('error', 'Seed::inicializar - fallo insert producto '.$nombre.': '.$msg2);
                            $summary['productos'][] = "$nombre error: $msg2";
                            $summary['errors'][] = "productos: $nombre -> $msg2";
                        } else {
                            $summary['productos'][] = "$nombre creado id=".$this->db->insert_id();
                        }
                    } catch (Exception $exProd) {
                        log_message('error', 'Seed::inicializar - excepción producto '.$nombre.': '.$exProd->getMessage());
                        $summary['productos'][] = "$nombre excepción: ".$exProd->getMessage();
                        $summary['errors'][] = "productos_excepcion: $nombre -> ".$exProd->getMessage();
                    }
                }
            } catch (Exception $ex) {
                log_message('error', 'Seed::inicializar - excepción categoria '.$name.': '.$ex->getMessage());
                $summary['categorias'][] = "$name excepción: ".$ex->getMessage();
                $summary['errors'][] = "categoria_excepcion: $name -> ".$ex->getMessage();
            }
        }

        // Mostrar resumen detallado
        echo '<h3>Seed ejecutado</h3><ul>';
        foreach ($summary['categorias'] as $c) {
            echo '<li>Categoria: '.htmlspecialchars($c).'</li>';
        }
        foreach ($summary['productos'] as $p) {
            echo '<li>Producto: '.htmlspecialchars($p).'</li>';
        }
        echo '</ul>';

        if (!empty($summary['errors'])) {
            echo '<h4>Errores</h4><ul>';
            foreach ($summary['errors'] as $err) {
                echo '<li>'.htmlspecialchars($err).'</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Todo creado correctamente.</p>';
        }

        echo '<p><a href="'.site_url('productos/listar').'">Ver productos</a></p>';
    }
}
