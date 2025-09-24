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

// Verifica si se han pasado al menos dos argumentos en la línea de comandos
if ($argc >= 3) { // $argc cuenta el número de argumentos, incluido el nombre del script
    $archivoSOL = $argv[1]; // Primer argumento
    $archivoRES = $argv[2]; // Segundo argumento

    echo "Argumento 1: " . $archivoSOL . "\n";
    echo "Argumento 2: " . $archivoRES . "\n";
    
    $archivoSOL = file_get_contents("$archivoSOL");
    $archivoRES = file_get_contents("$archivoRES");
} else {
    echo "Se deben proporcionar al menos dos argumentos en la línea de comandos.\n";
    exit(1); // Salida con error
}

//-------------------------------------------------------------------------------
// Obtener unidades de los archivos SOL y RES
list($unidadesF_SOL, $unidadesL_SOL) = $procesador->obtener_unidades($archivoSOL);
list($unidadesF_RES, $unidadesL_RES) =  $procesador->obtener_unidades($archivoRES);

//-------------------------------------------------------------------------------
// Chequeo Geometría
fwrite($f, "----------------------------------------------------------------\n");
fwrite($f, "CHEQUEO GEOMETRÍA\n");
fwrite($f, "----------------------------------------------------------------\n");
$patron = '/Joint=.*Xor/';  

$joint_coordinates_SOL = $procesador->buscar_patron_SAP_tokens($archivoSOL, $patron, $procesador->tokens_joint_coordinates);
$joint_coordinates_normalized_SOL = $procesador->normalizar_joint_coordinates($joint_coordinates_SOL, $unidadesL_SOL);
$joint_coordinates_RES = $procesador->buscar_patron_SAP_tokens($archivoRES, $patron, $procesador->tokens_joint_coordinates);
$joint_coordinates_normalized_RES = $procesador->normalizar_joint_coordinates($joint_coordinates_RES, $unidadesL_RES);


$procesador->chk_joint_coordinates($joint_coordinates_normalized_SOL, $joint_coordinates_normalized_RES, $f1);

// Las dos listas que siguen están ordenadas como la original (como SAP)
// y contienen los datos de la normalizada.
// Con ellas se accede inmediatamente a los datos normalizados sabiendo la
// numeración original del nudo.
// Son las que se usan para normalizar fácimente la topología
//
// $joint_coordinates_normalized_SOL_I = ordenar_lista_lista_coli_colj_colk($joint_coordinates_normalized_SOL, 0, 0, 0);
// $joint_coordinates_normalized_RES_I = ordenar_lista_lista_coli_colj_colk($joint_coordinates_normalized_RES, 0, 0, 0);
fwrite($f, $f1->val_sin_br()); $f1->val='';

//-------------------------------------------------------------------------------
// Chequeo Topología
fwrite($f, "----------------------------------------------------------------\n");
fwrite($f, "CHEQUEO TOPOLOGÍA\n");
fwrite($f, "----------------------------------------------------------------\n");
$patron = '/Frame=.*JointI/';  

$frame_connectivity_SOL = $procesador->buscar_patron_SAP_tokens($archivoSOL, $patron, $procesador->tokens_frame_connectivity);
$frame_connectivity_RES = $procesador->buscar_patron_SAP_tokens($archivoRES, $patron, $procesador->tokens_frame_connectivity);

$frame_connectivity_normalized_SOL = $procesador->normalizar_frame_connectivity($frame_connectivity_SOL, $joint_coordinates_normalized_SOL);
$frame_connectivity_normalized_RES = $procesador->normalizar_frame_connectivity($frame_connectivity_RES, $joint_coordinates_normalized_RES);

$procesador->chk_frame_connectivity($frame_connectivity_normalized_SOL, $frame_connectivity_normalized_RES, $f1);
fwrite($f, $f1->val_sin_br()); $f1->val='';

//-------------------------------------------------------------------------------
// Chequeo Secciones
fwrite($f, "----------------------------------------------------------------\n");
fwrite($f, "CHEQUEO SECCIONES\n");
fwrite($f, "----------------------------------------------------------------\n");
$patron = '/SectionName=.*Material/';  

$section_properties_SOL = $procesador->buscar_patron_SAP_tokens($archivoSOL, $patron, $procesador->tokens_section_properties);
$section_properties_RES = $procesador->buscar_patron_SAP_tokens($archivoRES, $patron, $procesador->tokens_section_properties);
//print_object($procesador->tokens_section_properties);
//$procesador->printL($section_properties_SOL,'----------$section_properties_SOL');

$section_properties_normalized_SOL = $procesador->normalizar_section_properties($section_properties_SOL, $unidadesL_SOL);
$section_properties_normalized_RES = $procesador->normalizar_section_properties($section_properties_RES, $unidadesL_RES);

$patron = '/Frame=.*AnalSect=/';
$frame_section_assignments_SOL = $procesador->buscar_patron_SAP_tokens($archivoSOL, $patron, $procesador->tokens_frame_section_assignments);
$frame_section_assignments_RES = $procesador->buscar_patron_SAP_tokens($archivoRES, $patron, $procesador->tokens_frame_section_assignments);

$procesador->add_section_frame_connectivity_normalized($frame_connectivity_normalized_SOL, $frame_section_assignments_SOL, $section_properties_normalized_SOL);
$procesador->add_section_frame_connectivity_normalized($frame_connectivity_normalized_RES, $frame_section_assignments_RES, $section_properties_normalized_RES);

$procesador->chk_frame_section_properties($frame_connectivity_normalized_SOL, $frame_connectivity_normalized_RES, $f1);
fwrite($f, $f1->val_sin_br()); $f1->val='';
//-------------------------------------------------------------------------------

?>
