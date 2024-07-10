<?php
include '../src/Tasks.php';
require '../src/xajax_core/xajax.inc.php';

$service = new Google_Service_Tasks($client);

$xajax = new xajax('../src/Tools.php');
$xajax->register(XAJAX_FUNCTION, 'ordenarEnvios');
$xajax->register(XAJAX_FUNCTION, 'getMapa');


$xajax->configure('javascript URI', '../src/');
//$xajax->configure('debug', false);
$xajax->processRequest();

function getListasTareas()
{
    global $service;
    $optParams = ['maxResults' => 100];
    $results = $service->tasklists->listTasklists($optParams);
    return $results;
}

function getTareas($id)
{
    global $service;
    $res1 = $service->tasks->listTasks($id);
    return $res1;
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
    <!-- Jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
          integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <title>Repartos</title>
    <?php $xajax->printJavascript(); ?>
    <script src="../js/funciones.js"></script>
</head>

<body style="background:#00bfa5;">
<?php
$_SESSION['notesReparto'] = [];
if ((isset($_POST['lat']))) {
    $note = $_POST['lat'] . "," . $_POST['lon'];
    $_SESSION['notesReparto'] = $note . " |";
    $dirMap = $_POST['dir']." Coruna";
    $title = ucwords($_POST['prod']) . " ." . ucwords($_POST['dir']) . ", A Coruña.";
    $idLt = $_POST['idLTarea'];
    unset($_SESSION[$idLt]);
    //guardamos la tarea
    $op = ['title' => $title, 'notes' => $note];
    $tarea = new Google_Service_Tasks_Task($op);
    try {
        $res = $service->tasks->insert($idLt, $tarea);
    } catch (Google_Exception $ex) {
        die("Error al guardar la tarea: " . $ex);
    }
    unset($_POST['lat']);
}
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        //Borrar lista de tareas
        case 'blt':

            try {
                $service->tasklists->delete($_GET['idlt']);
            } catch (Google_Exception $ex) {
                die("Error al borrar la lista de tareas: " . $ex);
            }
            unset($_SESSION[$_GET['idlt']]);
            unset($_SESSION['notesReparto']);
            break;
            //Borrar Tarea
        case 'bt':
            try {
                $service->tasks->delete($_GET['idlt'], $_GET['idt']);
            } catch (Google_Exception $ex) {
                die("Error al borrar la tarea: " . $ex);
            }
            unset($_SESSION[$_GET['idlt']]);
            break;
            //Nueva Lista de Tareas
        case 'nlt':
            if($_GET['title'] == NULL) break;
            //Controlamos que no puede haber 2 repartos el mismo día
            $listas = getListasTareas();
            foreach ($listas->getItems() as $lista) {
                $fechas [] = $lista->getTitle();
            }
            foreach($fechas as $k=>$v){
                if($v == $_GET['title']){
                    echo "<script>alert('Ya existe un reparto para ese día !!!');</script>";
                    break 2;
                }
                //if($_GET['title'] == NULL) break 2;
            }
            //Si en la fecha que introduce no hay reparto, se crea nueva lista de tareas.
            $opciones = ["title" => $_GET['title']];
            $taskList = new Google_Service_Tasks_TaskList($opciones);
            $lista = getListasTareas();
            foreach($lista->getItems() as $listaid){
                $arrayN[] = $listaid->getId();
            }
            try {
                $service->tasklists->insert($taskList);
            } catch (Google_Exception $ex) {
                die("Error al crear una lista de tareas: " . $ex);
            }
            $_SESSION['idLista'] = $arrayN;
            break;
            //Borrar lista ordenada de envíos
        case 'oce':
            $id_lista = $_GET['idlt'];
            unset($_SESSION[$id_lista]);
            break;
            //Ordenar Envíos
        case 'oEnvios':
            $apos = $_GET['pos'];
            $id_lista = $_GET['idLt'];
            unset($_SESSION['idLt']);
            //Obtenemos todas las tareas de esta lista de tareas
            $tareas = getTareas($id_lista);
            foreach ($apos as $k => $v) {
                //los envios me los manda ordenados del 1 al n
                //en php los array empiezan por cero, poe eso restamos 1
                //asi el envio 1 pasa a ser el 0, el 2 el 1 ...
                $p = $v - 1;
                $arrayO[$k] = $tareas->getItems()[$p]->getTitle();
                //Almacenamos las coordenadas en el mismo orden que se ordenan los envíos para enviarlas por POST a rutas.php
                $arrayN[$k] = $tareas->getItems()[$p]->getNotes();
            }
            $_SESSION[$id_lista] = $arrayO;
            $_SESSION['wps'.$id_lista] = $arrayN;
    }
}
?>
<h4 class="text-center mt-3">Gestión de Pedidos</h4>
<div class="container mt-4" style='width:80rem;'>
    <form id="ffecha" action='<?php echo $_SERVER['PHP_SELF'] ?>' method='get'>
        <div class="row">
            <div class="col-md-3 mb-2">
                <?php
                    $listas = getListasTareas();
                ?>
                <button id="crearLista" type='submit' class="btn btn-info" onclick="return comprobarFecha();"><i class='fas fa-plus mr-1'></i>Nueva Lista de Reparto
                </button>
            </div>
            <input type='hidden' name='action' value='nlt'>
            <div class="col-md-4">
                <input type="date" class="form form-control" id="title" name="title" required>
            </div>
        </div>
    </form>
    <?php
    $listas = getListasTareas();
    //Establece la/s lista/s
    foreach ($listas->getItems() as $lista) {
        //Mostramos la fecha en cada lista en el formato d/m/Y
        $date = $lista->getTitle();
        $dateOut = date('d/m/Y', strtotime($date));
     
        if ($lista->getTitle() == "My Tasks" || $lista->getTitle() == "Mis tareas") continue;
        echo "<table class='table mt-2' id='{$lista->getId()}'>\n";
        echo "<thead class='bg-secondary'>\n";
        echo "<tr>\n";
        echo "<th scope='col' style='width:42rem;'>Repartos {$dateOut}</th>\n";
        echo "<th scope='col' class='text-right'>\n";
        echo "<a href='envio.php?id={$lista->getId()}' class='btn btn-info mr-2 btn-sm'><i class='fas fa-plus mr-1'></i>Nuevo</a>\n";
        echo "<button class='btn btn-success mr-2 btn-sm' onclick=\"ordenarEnvios('{$lista->getId()}');\"><i class='fas fa-sort mr-1'></i>Ordenar</button>\n";
        echo "<button class='btn btn-primary mr-2 btn-sm' onclick=\"ocultarLista('{$lista->getId()}');\"><i class='fas fa-eye-slash mr-1'></i>Ocultar orden</button>\n";
        echo "<a href='repartos.php?action=blt&idlt={$lista->getId()}' class='btn btn-danger btn-sm' onclick=\"return confirm('¿Borrar Lista?')\"><i class='fas fa-trash mr-1'></i>Borrar</a>\n";
        echo "</th></tr>\n";
        echo "</thead>\n";
        echo "<tbody style='font-size:0.8rem'>\n";
        $tareas = getTareas($lista->getId());
        //Obtiene el ID de la lista de tareas
        //var_dump($lista->getId());
        //Establece las tareas dentro de la/s lista/s
        foreach ($tareas->getItems() as $tarea) {
            echo "<tr>\n";
            echo "<th scope='row'>{$tarea->getTitle()} ({$tarea->getNotes()})\n";
            //Guardamos las coordenadas en el input hidden
            echo "<input type='hidden' id='coor' value='{$tarea->getNotes()}'></th>\n";
            //Dividimos las coordenadas para enviarlas por GET a la página mapas
            $cadena = $tarea->getNotes();
            $porciones  = explode(",", $cadena); 
            echo "<th scope='row' class='text-right'>\n<a href='repartos.php?action=bt&idlt={$lista->getId()}&idt={$tarea->getId()}' class='btn btn-danger btn-sm' onclick=\"return confirm('¿Borrar Tarea?')\">";
            echo "<i class='fas fa-trash mr-1'></i>Borrar</a>\n";
            echo "<a href='mapas.php?lat={$porciones[0]}&lon={$porciones[1]}' class='btn btn-info ml-2 btn-sm'><i class='fas fa-map mr-1'></i>Mapa</a>\n</th>\n";
            echo "</tr>\n";
            //Guardamos las coordenadas de los productos que están en las tareas.
            //$_SESSION['notesReparto'] .= $tarea->getNotes();
        }
        echo "</tbody>\n";
        echo "</table>\n";
        echo "<br>";
       
        if (isset($_SESSION[$lista->getId()])) {
            
            echo "<div id='rutaOrden{$lista->getId()}' class='container mt-2 mb-2 {$lista->getId()}' style='font-size:0.8rem'>";
            echo "<ul class='list-group'>";
            
            //Se muestran las tareas ordenadas para su posterior envío a la ruta.
                foreach ($_SESSION[$lista->getId()] as $k => $v) {
                        
                echo "<li class='list-group-item list-group-item-info'>" . ($k + 1) . ".- " . $v . "</li>";
            } 
                
            $wpsOrden = serialize($_SESSION['wps'.$lista->getId()]);
            echo "<div class='text-center mt-2'>";
            //Creamos el formulario para enviar por POST el array con los puntos para la ruta.
            echo "<form id='formrutas{$lista->getId()}' action='rutas.php' method='POST'>";
            //Guardamos en el input hidden las coordenadas ordenadas por ruta
            echo "<input type='hidden' id='puntosRuta{$lista->getId()}' name='puntosRuta' value='{$wpsOrden}'>";
            echo "<a class='btn btn-info text-white' onclick=\"enviarRuta('{$lista->getId()}');\"><i class='fas fa-route mr-2'></i>Ver Ruta en Mapa</a>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
        }
    }
    ?>
</div>
</body>
</html>