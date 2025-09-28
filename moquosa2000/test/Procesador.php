<?php
function print_object($object) {
        print_r($object);
    }
//include_once '../trait_depuracion_mocodesapo.php'; //añadido en ay.sh
include_once '../share.php'; 
include_once '../chk_all.php';
include_once '../myio.php';
include_once '../joints.php'; 
include_once '../chk.php'; 
include_once '../share_chk.php';
include_once '../connectivity.php';
include_once '../properties.php';
include_once '../restraints.php';
include_once '../joint_loads.php';
include_once '../frame_loads_distributed.php';
include_once '../frame_loads_point.php';

class Procesador {
    //use trait_question_mocodesapo; //añadido en ay.sh
    use share;
    use chk_all;
    use myio;
    use joints;
    use chk;
    use share_chk;
    use connectivity;
    use properties;
    use restraints;
    use joint_loads;
    use frame_loads_distributed;
    use frame_loads_point;

    public function __construct() {
        $this->miconstructor();
  
    }
}

class F {
    // Propiedad para almacenar el texto
    public $val = '';

    // Método para añadir texto a la propiedad $val
    public function write($texto) {
        $this->val .= $texto.'<br>';  // Añadir el texto al final de $val
    }
    // Método para añadir texto a la propiedad $val
    public function add($texto) {
        $this->val .= $texto;  // Añadir el texto al final de $val
    }

    public function val_sin_br() {
        return str_replace('<br>', "\n", $this->val);
    }
}


?>
