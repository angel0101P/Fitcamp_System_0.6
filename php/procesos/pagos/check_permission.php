<?php
// check_permissions.php
echo "<pre>";

// Directorio actual
echo "Directorio actual: " . __DIR__ . "\n";
echo "Permisos: " . substr(sprintf('%o', fileperms(__DIR__)), -4) . "\n\n";

// Directorio de uploads
$uploads_dir = __DIR__ . '/../../uploads/comprobantes/';
echo "Uploads dir: $uploads_dir\n";
echo "Existe: " . (file_exists($uploads_dir) ? 'SI' : 'NO') . "\n";

if (file_exists($uploads_dir)) {
    echo "Permisos: " . substr(sprintf('%o', fileperms($uploads_dir)), -4) . "\n";
    echo "Escribible: " . (is_writable($uploads_dir) ? 'SI' : 'NO') . "\n";
    echo "Dueño: " . fileowner($uploads_dir) . "\n";
    echo "Grupo: " . filegroup($uploads_dir) . "\n";
}

echo "\nConfiguración PHP:\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";

echo "\nUsuario/Apache:\n";
echo "Usuario PHP: " . get_current_user() . "\n";
echo "Usuario proceso: " . exec('whoami') . "\n";

// Intentar crear un archivo de prueba
$test_file = __DIR__ . '/test_write.txt';
if (file_put_contents($test_file, 'test')) {
    echo "\n✓ Se pudo escribir archivo de prueba\n";
    unlink($test_file);
} else {
    echo "\n✗ NO se pudo escribir archivo de prueba\n";
}

echo "</pre>";
?>