-- Run this in your MySQL database to create the solicitudes_productos table and combos tables if not present

CREATE TABLE IF NOT EXISTS combos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  imagen_path VARCHAR(255),
  creado_por INT DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS combo_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  combo_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  FOREIGN KEY (combo_id) REFERENCES combos(id) ON DELETE CASCADE,
  INDEX (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS solicitudes_productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  tipo ENUM('producto','combo') NOT NULL,
  referencia_id INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  metodo_pago_id INT DEFAULT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  observaciones TEXT,
  estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  INDEX (usuario_id),
  INDEX (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
