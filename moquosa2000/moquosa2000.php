<?php

//Definicion de la clase Moquosa2000

include 'chk_all.php';
include 'share.php'; 
include_once 'myio.php';
include_once 'joints.php'; 
include_once 'chk.php'; 
include_once 'share_chk.php';
include_once 'connectivity.php';
include_once 'properties.php';
include_once 'restraints.php';
include_once 'joint_loads.php';
include_once 'frame_loads_distributed.php';
include_once 'frame_loads_point.php';



class Moquosa2000 {
    use chk_all, share, myio, joints, chk, share_chk, connectivity,
        properties, restraints,
        joint_loads, frame_loads_distributed, frame_loads_point ;  
   
    public function __construct() {
        $this->miconstructor();
    }
}



class moquosaF {
    // Propiedad para almacenar el texto
    public $val = '';

    // Método para añadir texto a la propiedad $val
    public function write($texto) {
        $this->val .= $texto.'<br>';  // Añadir el texto al final de $val
    }
}



