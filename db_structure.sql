-- Estructura de tablas para el sistema de inventario y ventas

-- Tablas de Catálogos
CREATE TABLE IF NOT EXISTS CatProductos (
    eCodProducto INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(100) NOT NULL,
    tDescripcion VARCHAR(200),
    dPrecio DECIMAL(10,2) NOT NULL,
    eStock INT NOT NULL DEFAULT 0,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC' COMMENT 'AC=Activo, CA=Cancelado',
    fhFechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fhFechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT IXtNombreProducto UNIQUE (tNombre)
);

CREATE TABLE IF NOT EXISTS CatCategorias (
    eCodCategoria INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(50) NOT NULL,
    tDescripcion VARCHAR(200),
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC' COMMENT 'AC=Activo, CA=Cancelado',
    fhFechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT IXtNombreCategoria UNIQUE (tNombre)
);

-- Tablas de Procesos (Bitácoras)
CREATE TABLE IF NOT EXISTS BitVentas (
    eCodVenta INT AUTO_INCREMENT PRIMARY KEY,
    eCodProducto INT NOT NULL,
    eCantidad INT NOT NULL,
    fhFechaVenta DATE NOT NULL,
    dPrecioUnitario DECIMAL(10,2) NOT NULL,
    dTotal DECIMAL(10,2) NOT NULL,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC' COMMENT 'AC=Activo, CA=Cancelado',
    fhFechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT FKBitVentasCatProductos00 FOREIGN KEY (eCodProducto) 
        REFERENCES CatProductos(eCodProducto)
);

-- Tabla de Relación
CREATE TABLE IF NOT EXISTS RelProductosCategorias (
    eCodProducto INT NOT NULL,
    eCodCategoria INT NOT NULL,
    fhFechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (eCodProducto, eCodCategoria),
    CONSTRAINT FKRelProductosCategoriasProductos00 FOREIGN KEY (eCodProducto) 
        REFERENCES CatProductos(eCodProducto) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FKRelProductosCategoriasCategorias00 FOREIGN KEY (eCodCategoria) 
        REFERENCES CatCategorias(eCodCategoria) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Insertar categorías básicas
INSERT INTO CatCategorias (tNombre, tDescripcion) VALUES 
('Abarrotes', 'Productos básicos y alimentos no perecederos'),
('Frutas y Verduras', 'Productos frescos del campo'),
('Carnicería', 'Carnes y derivados'),
('Lácteos', 'Leche y productos lácteos'),
('Limpieza', 'Productos de limpieza y aseo');

-- Procedimientos Almacenados
DELIMITER //

-- Procedimientos de Consulta
CREATE PROCEDURE stpConsultarProductos(
    IN _eCodProducto INT,
    IN _eCodCategoria INT,
    IN _bBajoStock BOOLEAN
)
BEGIN
    SELECT 
        p.eCodProducto,
        p.tNombre,
        p.tDescripcion,
        p.dPrecio,
        p.eStock,
        p.tCodEstatus,
        c.tNombre AS tCategoria
    FROM CatProductos p
    LEFT JOIN RelProductosCategorias pc ON p.eCodProducto = pc.eCodProducto
    LEFT JOIN CatCategorias c ON pc.eCodCategoria = c.eCodCategoria
    WHERE p.tCodEstatus = 'AC'
    AND (_eCodProducto IS NULL OR p.eCodProducto = _eCodProducto)
    AND (_eCodCategoria IS NULL OR pc.eCodCategoria = _eCodCategoria)
    AND (_bBajoStock IS NULL OR (_bBajoStock = 1 AND p.eStock <= 10));
END //

CREATE PROCEDURE stpConsultarVentas(
    IN _fhFechaInicio DATE,
    IN _fhFechaFin DATE
)
BEGIN
    SELECT 
        v.eCodVenta,
        v.fhFechaVenta,
        p.tNombre AS tProducto,
        v.eCantidad,
        v.dPrecioUnitario,
        v.dTotal
    FROM BitVentas v
    INNER JOIN CatProductos p ON v.eCodProducto = p.eCodProducto
    WHERE v.tCodEstatus = 'AC'
    AND (_fhFechaInicio IS NULL OR v.fhFechaVenta >= _fhFechaInicio)
    AND (_fhFechaFin IS NULL OR v.fhFechaVenta <= _fhFechaFin)
    ORDER BY v.fhFechaVenta DESC;
END //

-- Procedimientos de Inserción/Actualización
CREATE PROCEDURE stpInsertarProducto(
    IN _eCodProducto INT,
    IN _tNombre VARCHAR(100),
    IN _tDescripcion VARCHAR(200),
    IN _dPrecio DECIMAL(10,2),
    IN _eStock INT,
    IN _eCodCategoria INT
)
BEGIN
    IF _eCodProducto IS NULL THEN
        -- Insertar nuevo producto
        INSERT INTO CatProductos(tNombre, tDescripcion, dPrecio, eStock, tCodEstatus)
        VALUES (_tNombre, _tDescripcion, _dPrecio, _eStock, 'AC');
        
        SET _eCodProducto = LAST_INSERT_ID();
        
        -- Insertar relación con categoría
        IF _eCodCategoria IS NOT NULL THEN
            INSERT INTO RelProductosCategorias(eCodProducto, eCodCategoria)
            VALUES (_eCodProducto, _eCodCategoria);
        END IF;
    ELSE
        -- Actualizar producto existente
        UPDATE CatProductos 
        SET tNombre = _tNombre,
            tDescripcion = _tDescripcion,
            dPrecio = _dPrecio
        WHERE eCodProducto = _eCodProducto;
        
        IF _eCodCategoria IS NOT NULL THEN
            -- Actualizar categoría
            DELETE FROM RelProductosCategorias WHERE eCodProducto = _eCodProducto;
            INSERT INTO RelProductosCategorias(eCodProducto, eCodCategoria)
            VALUES (_eCodProducto, _eCodCategoria);
        END IF;
    END IF;
END //

CREATE PROCEDURE stpInsertarVenta(
    IN _eCodProducto INT,
    IN _eCantidad INT,
    IN _fhFechaVenta DATE
)
BEGIN
    DECLARE _dPrecioUnitario DECIMAL(10,2);
    DECLARE _dTotal DECIMAL(10,2);
    DECLARE _eStockActual INT;
    
    -- Obtener precio y stock actual
    SELECT dPrecio, eStock 
    INTO _dPrecioUnitario, _eStockActual
    FROM CatProductos 
    WHERE eCodProducto = _eCodProducto
    AND tCodEstatus = 'AC';
    
    -- Validar stock suficiente
    IF _eStockActual >= _eCantidad THEN
        SET _dTotal = _dPrecioUnitario * _eCantidad;
        
        START TRANSACTION;
        
        -- Registrar venta
        INSERT INTO BitVentas(
            eCodProducto, 
            eCantidad, 
            fhFechaVenta,
            dPrecioUnitario, 
            dTotal, 
            tCodEstatus
        )
        VALUES (
            _eCodProducto, 
            _eCantidad, 
            _fhFechaVenta,
            _dPrecioUnitario, 
            _dTotal, 
            'AC'
        );
        
        -- Actualizar stock
        UPDATE CatProductos 
        SET eStock = eStock - _eCantidad
        WHERE eCodProducto = _eCodProducto;
        
        COMMIT;
        
        SELECT 'OK' AS tResultado;
    ELSE
        SELECT 'Stock insuficiente' AS tError;
    END IF;
END //

DELIMITER ;