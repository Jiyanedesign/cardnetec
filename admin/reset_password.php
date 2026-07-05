<?php
require_once '../db.php';

$email = 'admin@cardnet.ec';
$plain_password = 'CardNetSecure2026!';
$hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

try {
    // Verificar si el usuario existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios_admin WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Actualizar contraseña
        $update = $pdo->prepare("UPDATE usuarios_admin SET password = ? WHERE email = ?");
        $update->execute([$hashed_password, $email]);
        echo "<h2>Contraseña actualizada correctamente para admin@cardnet.ec</h2>";
        echo "<p>Nueva contraseña: <strong>" . htmlspecialchars($plain_password) . "</strong></p>";
    } else {
        // Crear el usuario si no existe
        $insert = $pdo->prepare("INSERT INTO usuarios_admin (name, email, password) VALUES (?, ?, ?)");
        $insert->execute(['CardNet Admin', $email, $hashed_password]);
        echo "<h2>Usuario creado correctamente: admin@cardnet.ec</h2>";
        echo "<p>Contraseña: <strong>" . htmlspecialchars($plain_password) . "</strong></p>";
    }
    
    echo "<p><a href='login.php'>Volver al Login de Administrador</a></p>";
} catch (PDOException $e) {
    echo "<h2>Error al actualizar: " . $e->getMessage() . "</h2>";
}
