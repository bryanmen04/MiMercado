<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Evitar la advertencia de timezone cuando se use date()
if (!ini_get('date.timezone') || ini_get('date.timezone') === '') {
    date_default_timezone_set('America/Mexico_City'); // cambia a tu zona si es necesario
}

if (!isset($ventas)) $ventas = [];
$dTotalGeneral = 0;
?>

<div class="container">
    <h2>Reporte de Ventas</h2>
    
    <div class="row mb-3">
        <div class="col-md-8">
            <form class="form-inline" method="get">
                <div class="form-group mx-2">
                    <label for="desde">Desde: </label>
                    <input type="date" class="form-control" id="desde" name="desde" 
                           value="<?= $this->input->get('desde') ?>">
                </div>
                <div class="form-group mx-2">
                    <label for="hasta">Hasta: </label>
                    <input type="date" class="form-control" id="hasta" name="hasta" 
                           value="<?= $this->input->get('hasta') ?>">
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($ventas)): ?>
                <?php foreach($ventas as $venta): ?>
                    <?php $dTotalGeneral += $venta->dTotal; ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($venta->dFecha)) ?></td>
                        <td><?= $venta->tNombreProducto ?></td>
                        <td><?= $venta->eCantidad ?></td>
                        <td>$<?= number_format($venta->dPrecioUnitario, 2) ?></td>
                        <td>$<?= number_format($venta->dTotal, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No hay ventas registradas</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Total General:</strong></td>
                <td><strong>$<?= number_format($dTotalGeneral, 2) ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <?php
    $qs = '';
    if ($this->input->get('desde')) {
        $qs = '?desde='.urlencode($this->input->get('desde')).'&hasta='.urlencode($this->input->get('hasta'));
    }
    ?>
    <a href="<?= site_url('ventas/exportar_pdf').$qs ?>" class="btn btn-danger" target="_blank">Exportar PDF</a>
    <a href="<?= site_url('ventas/exportar_excel').$qs ?>" class="btn btn-success">Exportar Excel</a>
</div>

<script>
$(document).ready(function() {
    $('#datatable-default').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        },
        dom: 'Bfrtip',
        buttons: []
    });

    $('form').on('submit', function(e) {
        var inicio = new Date($('input[name="fecha_inicio"]').val());
        var fin = new Date($('input[name="fecha_fin"]').val());
        
        if(inicio > fin) {
            alert('La fecha de inicio no puede ser mayor a la fecha final');
            e.preventDefault();
            return false;
        }
    });
});

function exportarPDF() {
    // TODO: Implementar exportación a PDF usando la librería FPDF
    alert('Función en desarrollo');
}

function exportarExcel() {
    // TODO: Implementar exportación a Excel usando la librería PHPExcel
    alert('Función en desarrollo');
}
</script>