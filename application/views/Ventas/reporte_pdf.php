<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h1>Reporte de Ventas</h1>
<table border="1" cellpadding="5">
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
        <?php 
        $total = 0;
        foreach ($ventas as $venta): 
            $total += $venta->total;
        ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($venta->fecha)) ?></td>
                <td><?= $venta->nombre_producto ?></td>
                <td><?= $venta->cantidad ?></td>
                <td>$<?= number_format($venta->precio_unitario, 2) ?></td>
                <td>$<?= number_format($venta->total, 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" align="right"><strong>Total General:</strong></td>
            <td><strong>$<?= number_format($total, 2) ?></strong></td>
        </tr>
    </tfoot>
</table>
