<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Productos</h2>
            </header>
            <div class="panel-body">
                <div class="mb-md">
                    <a href="<?= base_url('productos/nuevo') ?>" class="btn btn-primary">Nuevo Producto</a>
                </div>

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

                <table class="table table-bordered table-striped" id="datatable-default">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($productos as $producto): ?>
                            <tr>
                                <td><?= $producto->eCodProducto ?></td>
                                <td><?= $producto->tNombre ?></td>
                                <td><?= $producto->tCategoria ?></td>
                                <td>$<?= number_format($producto->dPrecio, 2) ?></td>
                                <td><?= $producto->eStock ?></td>
                                <td>
                                    <a href="<?= base_url('productos/editar/'.$producto->id) ?>" class="btn btn-primary btn-sm">
                                        <i class="fa fa-pencil"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#datatable-default').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            }
        });
    });
</script>