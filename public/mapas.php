<?php
require '../src/xajax_core/xajax.inc.php';
$xajax = new xajax('../src/Tools.php');
$xajax->register(XAJAX_FUNCTION, 'getMapa');
$xajax->configure('javascript URI', '../src/');
//$xajax->configure('debug', false);
$xajax->processRequest();
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
    <title>Mapa</title>
    <?php $xajax->printJavascript(); ?>
    <script src="../js/funciones.js"></script>
    <script type="text/javascript" src="http://www.bing.com/maps/sdk/mapcontrol?callback=GetMap&key=AuclIH7tjOOM0U1sN23KcivEu101gRo9PTWX5wv-CoUYf7G-ujoaBZ7bPIWy9D49"></script>
</head>
<body style="background:#00bfa5;" onload="return getMap();">
<?php
    //Recogemos los datos y los guardamos en un hidden para llamar a la función getMap mediante JS
    if(isset($_GET['lat']) && isset($_GET['lon'])){
        $coorLat = $_GET['lat'];
        $coorLong = $_GET['lon'];
        $coor = $coorLat.",".$coorLong;
        echo "<input type='hidden' id='coordenada' value={$coor}>";
        echo "<input type='hidden' id='coorLat' value={$coorLat}>";
        echo "<input type='hidden' id='coorLon' value={$coorLong}>";
    }
?>
<div class="container mt-4">
    <div id="myMap" style="width:800px;height:500px;" class="mt-3 mx-auto">
    </div>
    <div class="d-flex justify-content-center align-items-center">
        <a href="repartos.php" class="btn btn-warning mt-3">Volver</a>
    </div>
    </div>
</body>
</html>