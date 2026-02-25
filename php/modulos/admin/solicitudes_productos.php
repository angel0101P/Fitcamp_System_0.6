<?php
session_start();
require_once '../../config/conexion.php';

if (!isset($_SESSION['usuario_user'])) {
    header('Location: ../../../auth/index_login.php'); exit();
}

$titulo = 'Solicitudes de Productos';

// obtener solicitudes
$res = @mysqli_query($conexion, "SELECT s.id, s.usuario_id, s.tipo, s.referencia_id, s.cantidad, s.total, s.estado, s.created_at, u.usuario_user
    FROM solicitudes_productos s
    LEFT JOIN usuario u ON u.id = s.usuario_id
    ORDER BY s.created_at DESC LIMIT 200");

$solicitudes = [];
if ($res) while ($r = mysqli_fetch_assoc($res)) $solicitudes[] = $r;

mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $titulo; ?> - Admin</title>
<link rel="stylesheet" href="../../styles/styles_admin.css">
</head>
<body>
<nav class="sidebar">
    <div class="user-section"><div class="profile-img-container"><img src="../../images/Fitcamp_Logo.png"></div><h3>ADMIN-Fitcamp</h3><p>Administrador</p></div>
</nav>
<div class="content" style="padding:24px;">
    <div class="card modulo-container">
        <div class="modulo-header"><h2><?php echo $titulo; ?></h2></div>
        <div>
            <?php if (empty($solicitudes)): ?>
                <div class="estado-vacio"><i class="fas fa-box-open"></i><h3>No hay solicitudes</h3></div>
            <?php else: ?>
                <table style="width:100%; color:white;">
                    <thead><tr><th>ID</th><th>Usuario</th><th>Tipo</th><th>Ref</th><th>Cantidad</th><th>Total</th><th>Estado</th><th>Fecha</th></tr></thead>
                    <tbody>
                    <?php foreach ($solicitudes as $s): ?>
                        <tr>
                            <td><?php echo $s['id']; ?></td>
                            <td><?php echo $s['usuario_user'] ?? $s['usuario_id']; ?></td>
                            <td><?php echo $s['tipo']; ?></td>
                            <td><?php echo $s['referencia_id']; ?></td>
                            <td><?php echo $s['cantidad']; ?></td>
                            <td><?php echo number_format($s['total'],2); ?></td>
                            <td><?php echo $s['estado']; ?></td>
                            <td><?php echo $s['created_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
