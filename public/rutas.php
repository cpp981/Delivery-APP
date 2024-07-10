<?php
session_start();
require '../src/xajax_core/xajax.inc.php';
$xajax = new xajax('../src/Tools.php');
$xajax->configure('javascript URI', '../src/');
$xajax->register(XAJAX_FUNCTION, 'pintarMapaRuta');
//$xajax->configure('debug', false);
$xajax->processRequest();

//Recuperamos las coordenadas enviadas en el orden del reparto para la ruta.
 if(isset($_POST['puntosRuta'])){
    //var_dump($_POST['puntosRuta']);
    $_SESSION['puntosR'] = unserialize($_POST['puntosRuta'], ['allowed_classes' => false]);
    echo "<br>";
   // var_dump($_SESSION['puntosR']);
 }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <!--Fontawesome CDN-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
        integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <title>Ruta</title>
    <script type="text/javascript" src="http://www.bing.com/maps/sdk/mapcontrol?callback=getRouteMap&type=route"></script>
    <?php $xajax->printJavascript(); ?>
    <script src="../js/funciones.js"></script>
</head>
<body style="background:#00bfa5;">

    <?php
    //Guardamos en los input hidden las coordenadas ordenadas por ruta para que JS las envie
    $id = 0;
    $wp = 1;
    foreach($_SESSION['puntosR'] as $k=>$v){
        global $id;
        global $wp;
        //Dividimos las coordenadas en Lat y Long para poder procesarlas en el mapa
        $puntos = explode(",", $v);
        echo "<input type='hidden' class='wpLat' id='$id' value='$puntos[0]'>";
        echo "<input type='hidden' class='wpLon' id='$id' value='$puntos[1]'>";
        echo "<input type='hidden' class='wp' id='$id' value='$v'>";
        $id++;
        $wp++;
    }
    //value='&wp.$wp=$v'
    ?>
     <div id="mapin" class="container mt-4">
            <div id="mapR" style="width:800px;height:500px;" class="mt-3 mx-auto">
            </div>
            <div class="d-flex justify-content-center align-items-center">
            <a href="repartos.php" class="btn btn-warning mt-3">Volver</a>
            </div>
        </div>
</body>
</html>