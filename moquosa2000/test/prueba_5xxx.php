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

$EscM = 0.989;
$EscF = 1.1;
$EscM = 1;
$EscF = 1;

// Verificar si se proporcionaron las escalas desde la línea de comandos
if ($argc >= 5) {
    $EscM = floatval($argv[3]);
    $EscF = floatval($argv[4]);
}

//-------------------------------------------------------------------------------
// Chequeo Geometría
fwrite($f, "----------------------------------------------------------------\n");
fwrite($f, "CHEQUEO GEOMETRÍA\n");
fwrite($f, "----------------------------------------------------------------\n");
$patron = '/Joint=.*Xor/';  

$joint_coordinates_SOL = $procesador->buscar_patron_SAP_tokens($archivoSOL, $patron, $procesador->tokens_joint_coordinates);
$joint_coordinates_normalized_SOL = $procesador->normalizar_joint_coordinates($joint_coordinates_SOL, $unidadesL_SOL, $EscM);
$joint_coordinates_RES = $procesador->buscar_patron_SAP_tokens($archivoRES, $patron, $procesador->tokens_joint_coordinates);
$joint_coordinates_normalized_RES = $procesador->normalizar_joint_coordinates($joint_coordinates_RES, $unidadesL_RES);


$procesador->chk_joint_coordinates($joint_coordinates_normalized_SOL, $joint_coordinates_normalized_RES, $f1);
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
fwrite($f, "----------------------------------------------------------------\n");
fwrite($f, "CHEQUEO DOF\n");
fwrite($f, "----------------------------------------------------------------\n");

$patron = '/(UX=Y|UX=N)/';
$DOF_SOL = $procesador->buscar_patron_SAP_tokens($archivoSOL, $patron, $procesador->tokens_DOF);
$DOF_RES = $procesador->buscar_patron_SAP_tokens($archivoRES, $patron, $procesador->tokens_DOF);

$procesador->chk_DOF($DOF_SOL, $DOF_RES, $f1);
fwrite($f, $f1->val_sin_br()); $f1->val='';


//-------------------------------------------------------------------------------
fwrite($f, "----------------------------------------------------------------\n");
fwrite($f, "CHEQUEO APOYOS\n");
fwrite($f, "----------------------------------------------------------------\n");
$patron = '/Joint.*(U1=N|U1=Y)/';

$restraint_assignments_SOL = $procesador->buscar_patron_SAP_tokens($archivoSOL, $patron, $procesador->tokens_restraint_assignments);
$restraint_assignments_RES = $procesador->buscar_patron_SAP_tokens($archivoRES, $patron, $procesador->tokens_restraint_assignments);

$restraint_assignments_normalized_SOL = $procesador->normalizar_restraint_assignments($restraint_assignments_SOL, $joint_coordinates_normalized_SOL);
$restraint_assignments_normalized_RES = $procesador->normalizar_restraint_assignments($restraint_assignments_RES, $joint_coordinates_normalized_RES);

$procesador->chk_restraint_assignments($restraint_assignments_normalized_SOL, $restraint_assignments_normalized_RES, $DOF_SOL, $f1);
fwrite($f, $f1->val_sin_br()); $f1->val='';

//-------------------------------------------------------------------------------
fwrite($f, "----------------------------------------------------------------\n");
fwrite($f, "CHEQUEO CARGAS EN NUDOS\n");
fwrite($f, "----------------------------------------------------------------\n");
$patron = '/Joint.*LoadPat/';

$joint_loads_force_SOL = $procesador->buscar_patron_SAP_tokens($archivoSOL, $patron, $procesador->tokens_joint_loads_force);
$joint_loads_force_RES = $procesador->buscar_patron_SAP_tokens($archivoRES, $patron, $procesador->tokens_joint_loads_force);

if ($joint_loads_force_SOL || $joint_loads_force_RES) {
    $joint_loads_force_normalized_SOL = normalizar_joint_loads_force($joint_loads_force_SOL, $joint_coordinates_normalized_SOL, $unidadesL_SOL, $unidadesF_SOL, $EscM, $EscF);
    $joint_loads_force_normalized_RES = normalizar_joint_loads_force($joint_loads_force_RES, $joint_coordinates_normalized_RES, $unidadesL_RES, $unidadesF_RES);

    chk_joint_loads($joint_loads_force_normalized_SOL, $joint_loads_force_normalized_RES, $DOF_SOL, $f);
}

// Chequeo de cargas en barras
fwrite($f, "----------------------------------------------------------------\n");
fwrite($f, "CHEQUEO CARGAS EN BARRAS\n");
fwrite($f, "----------------------------------------------------------------\n");

$patron = '/Frame.*LoadPat.*Type=Force/';

$frame_loads_distributed_SOL = buscar_patron_SAP_tokens($archivoSOL, $patron, $tokens_frame_loads_distributed);
$frame_loads_distributed_RES = buscar_patron_SAP_tokens($archivoRES, $patron, $tokens_frame_loads_distributed);

$frame_loads_distributed_normalized_SOL = normalizar_frame_loads_distributed($frame_loads_distributed_SOL, $frame_connectivity_normalized_SOL, $unidadesL_SOL, $unidadesF_SOL, $EscM, $EscF);
$frame_loads_distributed_normalized_RES = normalizar_frame_loads_distributed($frame_loads_distributed_RES, $frame_connectivity_normalized_RES, $unidadesL_RES, $unidadesF_RES, 1, 1);

chk_frame_loads_distributed($frame_loads_distributed_normalized_SOL, $frame_loads_loads_distributed_normalized_RES, $tokens_frame_loads_distributed_normalized, obtener_resultante_frame_loads_distributed, $DOF_SOL, $f);
                            
