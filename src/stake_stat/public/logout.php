<?php
session_start();
session_unset();    // Limpia todas las variables
session_destroy();  // Destruye la sesión por completo

// Redirigimos al inicio
header("Location: index.php");
exit();