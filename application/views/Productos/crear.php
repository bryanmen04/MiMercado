<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
        <h2>Nuevo Producto</h2>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('productos/guardar') ?>">
            <div class="form-group">
                <label for="tNombre">Nombre</label>
                <input id="tNombre" type="text" name="tNombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tDescripcion">Descripción</label>
                <textarea id="tDescripcion" name="tDescripcion" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label for="dPrecio">Precio</label>
                <input id="dPrecio" type="number" step="0.01" name="dPrecio" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="eStock">Stock</label>
                <input id="eStock" type="number" name="eStock" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="eCodCategoria">Categoría</label>
                <select id="eCodCategoria" name="eCodCategoria" class="form-control">
                    <option value="">-- Seleccione --</option>
                    <?php foreach((array)$categorias as $c): ?>
                        <option value="<?= $c->eCodCategoria ?>"><?= $c->tNombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= site_url('productos/listar') ?>" class="btn btn-default">Ver Productos</a>
        </form>
    </div>
</div>
