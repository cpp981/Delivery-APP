//Controlamos que no se puedan insertar tareas antes de la fecha actual
function comprobarFecha(){
    document.getElementById('ffecha').addEventListener('submit',function(e){
        e.preventDefault();
        e.stopPropagation();
    });
   var hoy = new Date().getTime();
   var fechaForm = document.getElementById('title').value;
   var fechaFormConv = new Date(fechaForm);
   var fechaFormMils = fechaFormConv.getTime();
   if(fechaFormMils < hoy){
    alert("La fecha no puede ser inferior a la actual");
   }else{
    document.getElementById('ffecha').submit();
   }
}

function reanudarEventoSubmit(){
    document.getElementById('f1').submit();
}

//Enviamos mediante POST a rutas.php las coordenadas ordenadas de cada tarea de reparto.
function enviarRuta(id){
    var fijo = "formrutas";
    var salida = fijo.concat("",id);
    var form = document.getElementById(salida);
    form.submit();
}
//Obtenemos las coordenadas de la dirección insertada
function getCoordenadas() {
    document.getElementById('f1').addEventListener('submit',function(e){
        e.preventDefault();
    });
    var dir = document.getElementById('dir').value;
    var res = xajax.request({xjxfun: 'getCoordenadas'}, {mode: 'synchronous', parameters: [dir]});
    if (res == false) {
        alert("Coordenada erróneas, revíselas");
    }
    return res;
}

//Ocultar la lista de reparto
function ocultarLista(id){
    var fijo = "rutaOrden";
    var salida = fijo.concat(id);
    //alert(salida);
    var respuesta = xajax.request({xjxfun: "oceL"}, {mode: 'synchronous', parameters: [salida]});
    if(respuesta == false){
        alert("No se puede ocultar la lista.");
        return respuesta;
    }
    //Si obtenemos respuesta
    var url = "http://127.0.0.1/repartos/public/repartos.php";
    url += '?action=oce&idlt=' + id;
    window.location = url;
}

function ordenarEnvios(id) {
    var puntos = $("#" + id + " input:hidden").map(function () {
        return this.value;
    }).get().join("|");
    var respuesta = xajax.request({xjxfun: "ordenarEnvios"}, {mode: 'synchronous', parameters: [puntos]});
    if (respuesta == false) {
        alert("No se pudo ordenar el envio");
        return respuesta;
    }
    // Si obtuvimos una respuesta, reordenamos los envíos del reparto
    // Cogemos la URL base del documento, quitando los parámetros GET si los hay
    var url = "http://127.0.0.1/repartos/public/repartos.php";
    // Añadimos el código de la lista de reparto
    url += '?action=oEnvios&idLt=' + id;
    // Y un array con las nuevas posiciones que deben ocupar los envíos
    for (var r in respuesta) url += '&pos[]=' + respuesta[r];
    window.location = url;
}

//Nos devolverá algo como esto
//http://127.0.0.1/curso/tema8/repartos/public/repartos.php?action=oEnvios&idLt=T05iWTFRMUM4aFpqeVFIRQ&pos[]=2&pos[]=3&pos[]=1


//Funciones mapas V8Api

//Mapa orientado según coordenadas
function getMap(){
    var coorLa = document.getElementById('coorLat').value;
    var coorLo = document.getElementById('coorLon').value;
    var map = new Microsoft.Maps.Map('#myMap', {
    });
    map.setView({
        mapTypeId: Microsoft.Maps.MapTypeId.road,
        center: new Microsoft.Maps.Location(coorLa,coorLo),
        zoom: 17
    });
    return map;
}

//Pintar Rutas
function getRouteMap(){
    var latitudes = document.querySelectorAll('input.wpLat');
    var longitudes = document.querySelectorAll('input.wpLon');
  
    var map = new Microsoft.Maps.Map('#mapR',{
        credentials: 'AuclIH7tjOOM0U1sN23KcivEu101gRo9PTWX5wv-CoUYf7G-ujoaBZ7bPIWy9D49',
        center: new Microsoft.Maps.Location(43.3556698,-8.4059463),
        mapTypeId: Microsoft.Maps.MapTypeId.road,
        disableStreetside: true,
        zoom:15
    });
    Microsoft.Maps.loadModule('Microsoft.Maps.Directions', function(){
        var direct = new Microsoft.Maps.Directions.DirectionsManager(map);
        
        //WP Inicio
        var wpStart = new Microsoft.Maps.Directions.Waypoint({address: ".", location: new Microsoft.Maps.Location(43.3556698,-8.4059463)});
        direct.addWaypoint(wpStart);

        //WP Intermedios
        for(let i = 0; i < latitudes.length; i++){
            var wps = new Microsoft.Maps.Directions.Waypoint({address: ".", location: new Microsoft.Maps.Location(latitudes[i].value,longitudes[i].value)});
            direct.addWaypoint(wps);
        }

        //WP Final
        var wpFin = new Microsoft.Maps.Directions.Waypoint({address: ".", location: new Microsoft.Maps.Location(43.3556698,-8.4059463)});
        direct.addWaypoint(wpFin);

        direct.setRenderOptions({
            
        });
        //Calculamos la ruta entre los waypoints
        direct.calculateDirections();
       
    });
}
//Funciones para obtener mapa estático ApiREST, mediante XAJAX ponemos la imagen

/*function getMapa(){
    var dir = document.getElementById('coordenada').value;
    var arr = dir.split(",");
    var lat = arr[0];
    var lon = arr[1];
    var res = xajax.request({xjxfun: 'getMapa'}, {mode: 'synchronous', parameters: [lat,lon]});
    if(res == false){
        alert("Coordenadas erróneas");
    }
    return res;
} */


/*function getMapaRuta(){
    var coor = document.querySelectorAll('input.wp');
    var wps;
    var s = coor.length +1;
    var fin = s.toString();
    //alert(s);
    for(let i = 0; i < coor.length; i++){
        //alert(coor[i].value);
        wps = wps + coor[i].value;
    }
    var wpsnew = wps.replace("undefined","");
    var param = wpsnew.concat("&wp.",fin);
    //alert(param);
    var res = xajax.request({xjxfun: 'pintarMapaRuta'}, {mode: 'synchronous', parameters: [param]});
    if(res == false){
        alert("Coordenadas erróneas");
    }
    return res;
}*/