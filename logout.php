<?php
session_start();
session_destroy();
header("Location: Inicio Sesion.html");
exit();
?>