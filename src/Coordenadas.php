<?php

class Coordenadas
{
    public static $iniciourl = "http://dev.virtualearth.net/REST/v1/Locations/ES/Coruna/";
    public static $finurl = "?include=ciso2&maxResults=1&c=es&key=AuclIH7tjOOM0U1sN23KcivEu101gRo9PTWX5wv-CoUYf7G-ujoaBZ7bPIWy9D49";
    public $coordenadas;
    public $url;

    public function __construct()
    {
        $num = func_num_args();
        if ($num == 1) {
            $dir = str_replace(" ", "%20", func_get_arg(0));
            $this->url = self::$iniciourl . "$dir" . self::$finurl;
        }
    }
    public function getCoordenadas()
    {
        $salida = file_get_contents($this->url);
        $salida1 = json_decode($salida, true);
        return $salida1["resourceSets"][0]["resources"][0]["point"]["coordinates"];
    }
    public function ordenarEnvios($dato)
    {
        //Ponemos las coordenadas del alamacen por ejemplo '43.3556698,-8.4059463' como inicio y fin de la ruta
        $base = "http://dev.virtualearth.net/REST/v1/Routes/driving?c=es&wayPoint.0=43.3556698,-8.4059463&";
        $puntos = explode("|", $dato);
        $num = 1;
        $trozo = "";
        for ($i = 0; $i < count($puntos); $i++) {
            $trozo .= "wayPoint." . $num++ . "=" . $puntos[$i] . "&";
        }
        $trozo .= "wayPoint." . $num . "=43.3556698,-8.4059463&optimize=distance&optWp=true&routeAttributes=routePath&key=AuclIH7tjOOM0U1sN23KcivEu101gRo9PTWX5wv-CoUYf7G-ujoaBZ7bPIWy9D49";
        $url = $base . $trozo;
        $salida = file_get_contents($url);
        $salida1 = json_decode($salida, true);
        $wayp = $salida1["resourceSets"][0]["resources"][0]['waypointsOrder'];
        //quitamos el primero y el ultimo (inicio y fin) (El almacen)
        array_shift($wayp);
        array_pop($wayp);

        for ($i = 0; $i < count($wayp); $i++) {
            $resp[] = substr(strstr($wayp[$i], '.'), 1);
        }
        return $resp;
    }

       /*public function getMapa($lat,$lon){
        $urlInicio = "http://dev.virtualearth.net/REST/v1/Imagery/Map/RoadOnDemand/";
        $finUrl = "/18?mapSize=700,500&pp=47.645523,-122.139059&mapLayer=Basemap,Buildings&key=AuclIH7tjOOM0U1sN23KcivEu101gRo9PTWX5wv-CoUYf7G-ujoaBZ7bPIWy9D49";
        $num = func_num_args();
        if ($num == 2) {
            $lat1 = str_replace(" ", "%20", func_get_arg(0));
            $lon1 = str_replace(" ", "%20", func_get_arg(1));
            $urlDir = $urlInicio . "$lat1," . "$lon1" . $finUrl;
        }
        $mapResul = "<img id='mapimg' src='$urlDir'>";
        return $mapResul;
    }
     public function pintarMapaRuta($stringwps){
        $inicioUrl = "http://dev.virtualearth.net/REST/v1/Imagery/Map/CanvasLight/Routes?wp.0=43.3556698,-8.4059463";
        $finUrl = "=43.3556698,-8.4059463&mapSize=700,475&key=AuclIH7tjOOM0U1sN23KcivEu101gRo9PTWX5wv-CoUYf7G-ujoaBZ7bPIWy9D49";
        $num = func_num_args();
        if ($num == 1) {
            $stringwps1 = str_replace(" ", "%20", func_get_arg(0));
            $url = $inicioUrl . "$stringwps1" . $finUrl;
        }
        $mapRoute = "<img src='$url'></img>";
        return $mapRoute;
    }*/
}

