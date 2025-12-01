<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container">
    <h2>Lista de Productos</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($productos)): ?>
            <?php foreach($productos as $producto): ?>
                <tr>
                    <td><?= $producto->id ?></td>
                    <td><?= htmlspecialchars($producto->nombre) ?></td>
                    <td><?= htmlspecialchars($producto->descripcion) ?></td>
                    <td>$<?= number_format($producto->precio, 2) ?></td>
                    <td><?= $producto->stock ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No hay productos registrados</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="<?= site_url('productos/crear') ?>" class="btn btn-primary">Nuevo Producto</a>
</div>
