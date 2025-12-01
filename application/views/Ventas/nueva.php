<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if (!isset($productos)) $productos = array();
?>
// Evitar warning si no hay timezone configurada en php.ini
if (!ini_get('date.timezone') || ini_get('date.timezone') === '') {
    date_default_timezone_set('America/Mexico_City'); // ajusta a tu zona si prefieres otra
}
?> 
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Nueva Venta</h2>
            </header>
            <div class="panel-body">
                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= $this->session->flashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= $this->session->flashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form class="form-horizontal form-bordered" method="post" action="<?= base_url('ventas/registrar') ?>">
                    <div class="form-group">
                        <label for="producto_id" class="col-md-3 control-label">Producto <span class="required">*</span></label>
                        <div class="col-md-6">
                            <select class="form-control" name="eCodProducto" id="producto_id" required>
                                <option value="">Seleccione un producto</option>
                                <?php foreach($productos as $producto): ?>
                                    <option value="<?= $producto->id ?>" 
                                            data-precio="<?= $producto->dPrecio ?>"
                                            data-stock="<?= $producto->eStock ?>">
                                        <?= $producto->nombre ?> - 
                                        Stock: <?= $producto->eStock ?> - 
                                        Precio: $<?= number_format($producto->dPrecio, 2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cantidad" class="col-md-3 control-label">Cantidad <span class="required">*</span></label>
                        <div class="col-md-6">
                            <input type="number" class="form-control" name="cantidad" id="cantidad" required min="1">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fecha" class="col-md-3 control-label">Fecha <span class="required">*</span></label>
                        <div class="col-md-6">
                            <input id="fecha" type="date" class="form-control" name="fecha" required 
                                   value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="total" class="col-md-3 control-label">Total</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="total" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-3">
                            <button type="submit" class="btn btn-primary">Registrar Venta</button>
                            <a href="<?= base_url('ventas/reporte') ?>" class="btn btn-default">Ver Reporte</a>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<script>
// Asegurar que jQuery esté disponible
if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function($) {
        function calcularTotal() {
            var producto = $('#producto_id option:selected');
            var cantidad = parseInt($('#cantidad').val()) || 0;
            var precio = parseFloat(producto.data('precio')) || 0;
            $('#total').val('$' + (precio * cantidad).toFixed(2));
        }

        $('#producto_id, #cantidad').on('change', function() {
            var producto = $('#producto_id option:selected');
            var cantidad = parseInt($('#cantidad').val()) || 0;
            var stock = parseInt(producto.data('stock')) || 0;

            if(cantidad > stock) {
                alert('La cantidad excede el stock disponible');
                $('#cantidad').val(stock);
            }
            calcularTotal();
        });

        $('form').on('submit', function(e) {
            var producto = $('#producto_id option:selected');
            var cantidad = parseInt($('#cantidad').val()) || 0;
            var stock = parseInt(producto.data('stock')) || 0;

            if(cantidad <= 0) {
                alert('La cantidad debe ser mayor a 0');
                e.preventDefault();
                return false;
            }

            if(cantidad > stock) {
                alert('La cantidad excede el stock disponible');
                e.preventDefault();
                return false;
            }
        });
    });
} else {
    console.error('jQuery no está cargado');
}
</script>