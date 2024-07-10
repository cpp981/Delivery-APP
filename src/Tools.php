<?php
require 'xajax_core/xajax.inc.php';
require 'Coordenadas.php';
require 'Altitud.php';
$xajax = new xajax();
$xajax->configure('javascript URI', './src/');
$xajax->register(XAJAX_FUNCTION, 'getCoordenadas');
$xajax->register(XAJAX_FUNCTION, 'ordenarEnvios');
//$xajax->register(XAJAX_FUNCTION, 'getMapa');
//$xajax->register(XAJAX_FUNCTION, 'pintarMapaRuta');
$xajax->register(XAJAX_FUNCTION, 'oceL');
$xajax->processRequest();

function getCoordenadas($dir)
{
    $resp = new xajaxResponse();
    $dir = trim($dir);
    if (strlen($dir) < 4) {
        $resp->setReturnValue(false);
        return $resp;
    }
    $c = new Coordenadas($dir);
    $lat = $c->getCoordenadas()[0];
    $lon = $c->getCoordenadas()[1];
    $alt = new Altitud($lat, $lon);
    $altura = $alt->getAltitude();

    $resp->assign('lat', 'value', $lat);
    $resp->assign('lon', 'value', $lon);
    $resp->assign('alt', 'value', $altura);
    $resp->setReturnValue(true);
    return $resp;
}
function oceL($id){
    $resp = new xajaxResponse();
    if(strlen(trim($id)) == 0){
        $resp->setReturnValue(false);
        return $resp;
    }
    $resp->assign($id,"innerHTML","");
    $resp->setReturnValue(true);
    return $resp;
}
function ordenarEnvios($puntos)
{
    $resp = new xajaxResponse();
    if (strlen(trim($puntos)) == 0) {
        $resp->setReturnValue(false);
        return $resp;
    }
    $c = new Coordenadas();
    $datos = $c->ordenarEnvios($puntos);
    $resp->setReturnValue($datos);
    return $resp;
}

/*function getMapa($lat,$lon){
    $resp = new xajaxResponse();
    if (strlen(trim($lat)) == 0 || strlen(trim($lon) == 0)) {
        $resp->setReturnValue(false);
        return $resp;
    }
    $c = new Coordenadas();
    $map = $c->getMapa($lat,$lon);
    $resp->assign("map","innerHTML", $map);
    $resp->setReturnValue($map);
    return $resp;
}
function pintarMapaRuta($wps){
    $resp = new xajaxResponse();
    $dir = trim($wps);
    if (strlen($dir) == 0) {
        $resp->setReturnValue(false);
        return $resp;
    }
    $c = new Coordenadas();
    $mapR = $c->pintarMapaRuta($wps);
    $resp->assign("mapR","innerHTML", $mapR);
    $resp->setReturnValue(true);
    return $resp;
}*/
