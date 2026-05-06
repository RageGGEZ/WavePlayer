<?php
echo "<h2>Diagnóstico del formulario</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Método POST detectado</h3>";
    
    echo "<h4>Contenido de \$_POST:</h4>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h4>Contenido de \$_FILES:</h4>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    echo "<h4>Headers:</h4>";
    echo "<pre>";
    print_r(getallheaders());
    echo "</pre>";
} else {
    echo "<h3>No se recibió método POST</h3>";
    echo "<p>Método actual: " . $_SERVER['REQUEST_METHOD'] . "</p>";
}
?>