Instrucciones para el módulo de Combos

Tablas necesarias:

1) Tabla `combos`:

CREATE TABLE combos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  imagen_path VARCHAR(255),
  creado_por INT DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

2) Tabla `combo_items`:

CREATE TABLE combo_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  combo_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  FOREIGN KEY (combo_id) REFERENCES combos(id) ON DELETE CASCADE,
  INDEX (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

Notas:
- `combos` guarda el precio del paquete (puede ser independiente del sumatorio de productos).
- `combo_items` indica cuáles productos forman parte del combo y la cantidad de cada uno.

Permisos de uploads:
- Asegúrate que la carpeta `uploads/productos/` exista y sea escribible por el usuario del servidor web.

Archivos añadidos:
- php/modulos/admin/catalogo_combos.php (interfaz)
- scripts/catalogo_combos.js (cliente)
- php/procesos/admin/herbalife/guardar_combo.php (handler)

Pruebas rápidas:
- Crear las tablas con las consultas SQL.
- Abrir `php/modulos/admin/catalogo_combos.php` en el navegador.
- Crear un combo seleccionando productos.

"