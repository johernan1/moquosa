<?php
//-------------------------------------------------------------------------------
function print_object($object) {
        print_r($object);
    }

class F {
    // Propiedad para almacenar el texto
    public $val = '';

    // Método para añadir texto a la propiedad $val
    public function write($texto) {
        $this->val .= $texto.'<br>';  // Añadir el texto al final de $val
    }

    public function add($texto) {
        $this->val .= $texto;  // Añadir el texto al final de $val
    }
    public function val_sin_br() {
        return str_replace('<br>', "\n", $this->val);
    }
}

// Abrir archivo para escritura
$logFile = fopen('log.txt', 'w');
$f1 = new F();
//-------------------------------------------------------------------------------
// Verificar que hay al menos 4 argumentos
if ($argc >= 5) {
    $archivoSOL = $argv[1];
    $archivoRES = $argv[2];
    $EscL = $argv[3];
    $EscF = $argv[4];

    echo "Argumento 1: $archivoSOL\n";
    echo "Argumento 2: $archivoRES\n";
} else {
    echo "Se deben proporcionar al menos cuatro argumentos en la línea de comandos.\n";
    exit(1);
}
    
    $archivoSOL = file_get_contents("$archivoSOL");
    $archivoRES = file_get_contents("$archivoRES");

// // Incluir el archivo con la función chk_all
// include '../chk_all.php';
// include '../share.php';    
// //include_once '../trait_depuracion_mocodesapo.php'; //añadido en ay.sh
// include_once '../myio.php';
// include_once '../joints.php'; 
// include_once '../chk.php'; 
// include_once '../share_chk.php';
// include_once '../connectivity.php';
// include_once '../properties.php';
// include_once '../restraints.php';
// include_once '../joint_loads.php';
// include_once '../frame_loads_distributed.php';
// include_once '../frame_loads_point.php';


// // Definir la clase que usará el trait
// class Moquosa2000 {
//     use chk_all, share, myio, joints, chk, share_chk, connectivity, properties, restraints,
//         joint_loads, frame_loads_distributed, frame_loads_point ;  
   
//     public function __construct() {
//         $this->miconstructor();
//     }
// }
include_once '../moquosa2000.php';

// Crear una instancia de la clase
$miMicosa2000 = new Moquosa2000();

// Llamar a la función chk_all desde el trait (ajusta si hay un método específico)
$miMicosa2000->chk_all($archivoSOL, $archivoRES, $f1, (float)$EscL, (float)$EscF);
fwrite($logFile, $f1->val_sin_br()); $f1->val='';
//-------------------------------------------------------------------------------
// Cerrar archivo
fclose($logFile);
// // Convertir a float y llamar a la función chk_all
// chk_all($archivoSOL, $archivoRES, $logFile, (float)$EscL, (float)$EscF);

// //-------------------------------------------------------------------------------
// // Cerrar archivo
// fclose($logFile);
// ?>
