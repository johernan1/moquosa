<?php

// Suponemos que las funciones están en una clase llamada `Procesador` que se encuentra en otro archivo.
require 'Procesador.php';  // Incluye el archivo con la clase y métodos definidos

// Crear una instancia de la clase Procesador
$procesador = new Procesador();
$f1 = new F();

//-------------------------------------------------------------------------------
// Abrir archivo de log
$f = fopen('log.txt', 'w');
//-------------------------------------------------------------------------------

// Obtener argumentos de línea de comandos
if ($argc >= 3) {
    $archivoSOL = $argv[1];
    $archivoRES = $argv[2];

    echo "Argumento 1: $archivoSOL\n";
    echo "Argumento 2: $archivoRES\n";
    
    $archivoSOL = file_get_contents("$archivoSOL");
    $archivoRES = file_get_contents("$archivoRES");
    
} else {
    echo "Se deben proporcionar al menos dos argumentos en la línea de comandos.\n";
    exit(1);
}

//-------------------------------------------------------------------------------
// Obtener unidades de los archivos SOL y RES
list($unidadesF_SOL, $unidadesL_SOL) = $procesador->obtener_unidades($archivoSOL);
list($unidadesF_RES, $unidadesL_RES) = $procesador->obtener_unidades($archivoRES);

//-------------------------------------------------------------------------------
// Escribir en el archivo de log
fwrite($f, "----------------------------------------------------------------\n");
fwrite($f, "CHEQUEO GEOMETRÍA\n");
fwrite($f, "----------------------------------------------------------------\n");


$patron = '/Joint=.*Xor/';  // Definir el patrón a buscar

// Buscar coordenadas de nudos en archivos SOL y RES
$joint_coordinates_SOL = $procesador->buscar_patron_SAP_tokens($archivoSOL, $patron, $procesador->tokens_joint_coordinates);
$joint_coordinates_normalized_SOL = $procesador->normalizar_joint_coordinates($joint_coordinates_SOL, $unidadesL_SOL);

$joint_coordinates_RES = $procesador->buscar_patron_SAP_tokens($archivoRES, $patron, $procesador->tokens_joint_coordinates);
$joint_coordinates_normalized_RES = $procesador->normalizar_joint_coordinates($joint_coordinates_RES, $unidadesL_RES);

// Chequear coordenadas de nudos normalizadas
if (!$procesador->chk_joint_coordinates($joint_coordinates_normalized_SOL, $joint_coordinates_normalized_RES, $f1)) {
    fwrite($f, $f1->val_sin_br()); $f1->val='';
    exit(0);
}
fwrite($f, $f1->val_sin_br()); $f1->val='';

//-------------------------------------------------------------------------------
// Escribir en el archivo de log
fwrite($f, "----------------------------------------------------------------\n");
fwrite($f, "CHEQUEO TOPOLOGÍA\n");
fwrite($f, "----------------------------------------------------------------\n");

$patron = '/Frame=.*JointI/';  // Definir el patrón a buscar

// Buscar conectividad de marcos en archivos SOL y RES
$frame_connectivity_SOL = $procesador->buscar_patron_SAP_tokens($archivoSOL, $patron, $procesador->tokens_frame_connectivity);
$frame_connectivity_RES = $procesador->buscar_patron_SAP_tokens($archivoRES, $patron, $procesador->tokens_frame_connectivity);

// print_object("\n".'$frame_connectivity_SOL'."\n");
// print_object($frame_connectivity_SOL);
// print_object("\n".'$frame_connectivity_RES'."\n");
// print_object($frame_connectivity_RES);
// $procesador->printL($frame_connectivity_SOL,"\n".'$frame_connectivity_SOL----------------------');
// $procesador->printL($frame_connectivity_RES,"\n".'$frame_connectivity_RES----------------------');


// Normalizar conectividad de marcos
$frame_connectivity_normalized_SOL = $procesador->normalizar_frame_connectivity($frame_connectivity_SOL, $joint_coordinates_normalized_SOL);
$frame_connectivity_normalized_RES = $procesador->normalizar_frame_connectivity($frame_connectivity_RES, $joint_coordinates_normalized_RES);
//$procesador->printL($frame_connectivity_normalized_SOL,"\n".'$frame_connectivity_SOL----------------------NORMALIZADO');
//$procesador->printL($frame_connectivity_normalized_RES,"\n".'$frame_connectivity_RES----------------------NORMALIZADO');

// Chequear conectividad de marcos
$procesador->chk_frame_connectivity($frame_connectivity_normalized_SOL, $frame_connectivity_normalized_RES, $f1);
fwrite($f, $f1->val_sin_br());
//-------------------------------------------------------------------------------
// Cerrar archivo de log
fclose($f);

?>
