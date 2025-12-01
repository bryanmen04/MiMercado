<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title"><?= isset($producto) ? 'Editar Producto' : 'Nuevo Producto' ?></h2>
            </header>
            <div class="panel-body">
                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= $this->session->flashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form class="form-horizontal form-bordered" method="post" action="<?= base_url(isset($producto) ? 'productos/actualizar' : 'productos/guardar') ?>">
                    <?php if(isset($producto)): ?>
                        <input type="hidden" name="eCodProducto" value="<?= $producto->eCodProducto ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Nombre <span class="required">*</span></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="tNombre" required 
                                   value="<?= isset($producto) ? $producto->tNombre : set_value('tNombre') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Categoría <span class="required">*</span></label>
                        <div class="col-md-6">
                            <select class="form-control" name="eCodCategoria" required>
                                <option value="">Seleccione una categoría</option>
                                <?php foreach($categorias as $categoria): ?>
                                    <option value="<?= $categoria->eCodCategoria ?>" 
                                            <?= (isset($producto) && $producto->eCodCategoria == $categoria->eCodCategoria) ? 'selected' : '' ?>>
                                        <?= $categoria->tNombre ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Descripción</label>
                        <div class="col-md-6">
                            <textarea class="form-control" name="tDescripcion"><?= isset($producto) ? $producto->tDescripcion : set_value('tDescripcion') ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Precio <span class="required">*</span></label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="number" class="form-control" name="dPrecio" step="0.01" required 
                                       value="<?= isset($producto) ? $producto->dPrecio : set_value('dPrecio') ?>">
                            </div>
                        </div>
                    </div>

                    <?php if(!isset($producto)): ?>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Stock Inicial <span class="required">*</span></label>
                            <div class="col-md-6">
                                <input type="number" class="form-control" name="eStock" required 
                                       value="<?= set_value('eStock') ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-3">
                            <button type="submit" class="btn btn-primary">
                                <?= isset($producto) ? 'Actualizar' : 'Guardar' ?>
                            </button>
                            <a href="<?= base_url('productos') ?>" class="btn btn-default">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<script>
$(document).ready(function() {
    $('form').on('submit', function(e) {
        var precio = parseFloat($('input[name="precio"]').val());
        if(precio <= 0) {
            alert('El precio debe ser mayor a 0');
            e.preventDefault();
            return false;
        }

        var stock = $('input[name="stock"]');
        if(stock.length && parseInt(stock.val()) < 0) {
            alert('El stock no puede ser negativo');
            e.preventDefault();
            return false;
        }
    });
});
</script>