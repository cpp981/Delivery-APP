<?php
class Altitud {
public static $inicioURL = "http://dev.virtualearth.net/REST/v1/Elevation/List?points=";
public static $finURL = "&heights=sealevel&c=es-ES&key=AuclIH7tjOOM0U1sN23KcivEu101gRo9PTWX5wv-CoUYf7G-ujoaBZ7bPIWy9D49";
public $altitud;
public $url;

public function __construct()
{
    $num = func_num_args();
    if ($num == 2) {
        $dir1 = str_replace(" ", "%20", func_get_arg(0));
        $dir2 = str_replace(" ", "%20", func_get_arg(1));
        $this->url = self::$inicioURL . "$dir1". "," . "$dir2" . self::$finURL;
    }
}

    public function getAltitude() {
        $resul = file_get_contents($this->url);
        $resul1 = json_decode($resul, true);
        $altOut = $resul1["resourceSets"][0]["resources"][0]["elevations"];
        return $altOut;
    }
}