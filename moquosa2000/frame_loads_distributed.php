<?php

if (!trait_exists(' frame_loads_distributed')) {
    trait  frame_loads_distributed {


            // Método para normalizar frame_loads_distributed
    public function normalizar_frame_loads_distributed($frame_loads_distributed,
                                                       $frame_connectivity_normalized,
                                                       $unidadesL, $unidadesF, $EscM = 1, $EscF = 1, $phpSemanal = null) {

        //echo "normalizar_frame_loads_distributed EscM=$EscM, EscF=$EscF\n";exit(1);
        // echo  "tokens_frame_connectivity_normalized\n";
        // echo  "frame_loads_distributed\n";
        
        // Definir índices de columnas
        $col_Frame            = array_search('Frame', $this->tokens_frame_loads_distributed);
        $col_RelDistA         = array_search('RelDistA', $this->tokens_frame_loads_distributed);
        $col_RelDistB         = array_search('RelDistB', $this->tokens_frame_loads_distributed);
        $col_FOverLA          = array_search('FOverLA', $this->tokens_frame_loads_distributed);
        $col_FOverLB          = array_search('FOverLB', $this->tokens_frame_loads_distributed);
        $col_Type             = array_search('Type', $this->tokens_frame_loads_distributed);
        $col_Dir              = array_search('Dir', $this->tokens_frame_loads_distributed);
        
        // Frame connectivity columns
        $col_JointI_Orig_XorR = array_search('JointI_Orig_XorR', $this->tokens_frame_connectivity_normalized);
        $col_JointI_Orig_Y    = array_search('JointI_Orig_Y', $this->tokens_frame_connectivity_normalized);
        $col_JointI_Orig_Z    = array_search('JointI_Orig_Z', $this->tokens_frame_connectivity_normalized);
        $col_JointJ_Orig_XorR = array_search('JointJ_Orig_XorR', $this->tokens_frame_connectivity_normalized);
        $col_JointJ_Orig_Y    = array_search('JointJ_Orig_Y', $this->tokens_frame_connectivity_normalized);
        $col_JointJ_Orig_Z    = array_search('JointJ_Orig_Z', $this->tokens_frame_connectivity_normalized);
        $col_Norm_Lx          = array_search('Norm_Lx', $this->tokens_frame_connectivity_normalized);
        $col_Norm_Ly          = array_search('Norm_Ly', $this->tokens_frame_connectivity_normalized);
        $col_Norm_Lz          = array_search('Norm_Lz', $this->tokens_frame_connectivity_normalized);
        $col_Norm_L           = array_search('Norm_L', $this->tokens_frame_connectivity_normalized);
        $col_Norm_IxJ         = array_search('Norm_IxJ', $this->tokens_frame_connectivity_normalized); // Indica si la barra se ha reorientado
        
        // Definir factores de conversión de unidades
        $factorunidadesF = $this->obtener_factor_cambio_unidades_fuerza($unidadesF) * $EscF;
        $factorunidadesL = $this->obtener_factor_cambio_unidades_longitud($unidadesL) * $EscM;
        
        // Se extrae la primera columna de frame_connectivity_normalized
        $n_frame_connectivity = [];
        foreach ($frame_connectivity_normalized as $lista) {
            $n_frame_connectivity[] = $lista[0];
        }

        // Añadir varias columnas
        if (!in_array("Norm_Frame", $this->tokens_frame_loads_distributed_normalized)) {
            array_push($this->tokens_frame_loads_distributed_normalized, "Norm_Frame", "JointI_Orig_XorR", "JointI_Orig_Y",
                       "JointI_Orig_Z", "JointJ_Orig_XorR", "JointJ_Orig_Y", "JointJ_Orig_Z", "Norm_IxJ",
                       "Norm_Lx", "Norm_Ly", "Norm_Lz", "Norm_L", "Norm_FOverA", "Norm_FOverB", "Norm_R", "Norm_e");
        }

        // echo  '$this->tokens_frame_loads_distributed_normalized';
        // print_r( $this->tokens_frame_loads_distributed_normalized);
        
        // Añadir valores a cada columna
        foreach ($frame_loads_distributed as &$I_frame_loads_distributed) {
            // echo  "I_frame_loads_distributed:::";
            // print_r($I_frame_loads_distributed);
            // echo  "frame_connectivity_normalized\n";
            $this->printL($frame_connectivity_normalized);
           
            //
            $i_frame = $I_frame_loads_distributed[$col_Frame];  // Nombre de la barra original
            $I_frame = array_search($i_frame, $n_frame_connectivity);  // Id normalizado

            // echo  "i_frame=$i_frame;I_frame=$I_frame\n";
            // print_r($n_frame_connectivity);
            // exit(1);

            // Obtener valores de conectividad
            $JointI_Orig_XorR = $frame_connectivity_normalized[$I_frame][$col_JointI_Orig_XorR];
            $JointI_Orig_Y    = $frame_connectivity_normalized[$I_frame][$col_JointI_Orig_Y];
            $JointI_Orig_Z    = $frame_connectivity_normalized[$I_frame][$col_JointI_Orig_Z];
            $JointJ_Orig_XorR = $frame_connectivity_normalized[$I_frame][$col_JointJ_Orig_XorR];
            $JointJ_Orig_Y    = $frame_connectivity_normalized[$I_frame][$col_JointJ_Orig_Y];
            $JointJ_Orig_Z    = $frame_connectivity_normalized[$I_frame][$col_JointJ_Orig_Z];
            $norm_Lx          = $frame_connectivity_normalized[$I_frame][$col_Norm_Lx];
            $norm_Ly          = $frame_connectivity_normalized[$I_frame][$col_Norm_Ly];
            $norm_Lz          = $frame_connectivity_normalized[$I_frame][$col_Norm_Lz];
            $norm_L           = $frame_connectivity_normalized[$I_frame][$col_Norm_L];
            $norm_IxJ         = $frame_connectivity_normalized[$I_frame][$col_Norm_IxJ];
            $norm_Lxy         = sqrt($norm_Lx * $norm_Lx + $norm_Ly * $norm_Ly);
            $norm_Lxz         = sqrt($norm_Lx * $norm_Lx + $norm_Lz * $norm_Lz);
            $norm_Lyz         = sqrt($norm_Ly * $norm_Ly + $norm_Lz * $norm_Lz);
            //$this->printL($frame_connectivity_normalized,'$frame_connectivity_normalized');
            //print_r($frame_connectivity_normalized[$I_frame]);
            //echo "i_frame=$i_frame, I_frame=$I_frame, norm_L=$norm_L, col_Norm_L=$col_Norm_L";
            //exit(1);

            // Esto es el TODO DE PYTHON. De momento se queda comentado
            // // Calcular FOverA y FOverB
            // $factorunidadesF_ = $EscF != 1 && is_callable($phpSemanal) ?
            //                     $phpSemanal(floatval($I_frame_loads_distributed[$col_FOverLA]), $factorunidadesF, $factorunidadesL, $EscF) :
            //                     $factorunidadesF;
            $factorunidadesF_=$factorunidadesF; //OJO, OJO ajustar si se programa lo anterior

            $norm_FOverLA = floatval($I_frame_loads_distributed[$col_FOverLA]) * $factorunidadesF_ / $factorunidadesL;
            $norm_FOverLB = floatval($I_frame_loads_distributed[$col_FOverLB]) * $factorunidadesF_ / $factorunidadesL;
            $Dir          = $I_frame_loads_distributed[$col_Dir];
            $RelDistA     = floatval($I_frame_loads_distributed[$col_RelDistA]);
            $RelDistB     = floatval($I_frame_loads_distributed[$col_RelDistB]);

            $incL = ($RelDistB - $RelDistA);
            $s_med = ($RelDistB + $RelDistA) / 2;
            $F_med = ($norm_FOverLA + $norm_FOverLB) / 2 * $incL;
            $M_med = (1/2 * ($norm_FOverLA - $norm_FOverLB) / 2 * ($incL / 2)) * (2/3 * $incL);

            // Cálculo de norm_R y norm_e según la dirección
            $norm_R = 0;
            $norm_e = 0;
            if ($Dir == '"GravProj"') {
                $norm_R = -$F_med * $norm_Lxy;
                if ($norm_R != 0) $norm_e = $s_med + ($M_med * $norm_Lxy) / $norm_R;
            } elseif ($Dir == '"ZProj"') {
                $norm_R = $F_med * $norm_Lxy;
                if ($norm_R != 0) $norm_e = $s_med + ($M_med * $norm_Lxy) / $norm_R;
            } elseif ($Dir == '"XProj"') {
                $norm_R = $F_med * $norm_Lyz;
                if ($norm_R != 0) $norm_e = $s_med + ($M_med * $norm_Lyz) / $norm_R;
            } elseif ($Dir == '"YProj"') {
                $norm_R = $F_med * $norm_Lxz;
                if ($norm_R != 0) $norm_e = $s_med + ($M_med * $norm_Lxz) / $norm_R;
            } elseif ($Dir == 'Gravity') {
                $norm_R = -$F_med * $norm_L;
                if ($norm_R != 0) $norm_e = $s_med + ($M_med * $norm_L) / $norm_R;
            } elseif (in_array($Dir, ['Z', 'X', 'Y', '1', '2', '3'])) {
                $norm_R = $F_med * $norm_L;
                if ($norm_R != 0) $norm_e = $s_med + ($M_med * $norm_L * $norm_L) / $norm_R;
            }

            //FALTA ELSE
            //       else:
            //print ("ERROR EN CARGAS DISTRIBUIDAS. Dir ", Dir, " no definido")
            //sys.exit()

            // Añadir los valores calculados a la fila de frame_loads_distributed
            array_push($I_frame_loads_distributed, $I_frame, $JointI_Orig_XorR, $JointI_Orig_Y, $JointI_Orig_Z,
                       $JointJ_Orig_XorR, $JointJ_Orig_Y, $JointJ_Orig_Z, $norm_IxJ, $norm_Lx, $norm_Ly, $norm_Lz,
                       $norm_L, $norm_FOverLA, $norm_FOverLB, $norm_R, $norm_e);
            // print_r("Se amplia I_frame_loads_distributed");
            // print_r($I_frame_loads_distributed);
        }

        return $frame_loads_distributed;
    }

    // // Método para obtener el factor de cambio de unidades de fuerza
    // private function obtener_factor_cambio_unidades_fuerza($unidades) {
    //     switch ($unidades) {
    //         case 'KN': return 1000;
    //         case 'N':  return 1;
    //         case 'MN': return 1000000;
    //         default:   return 1;
    //     }
    // }

    // // Método para obtener el factor de cambio de unidades de longitud
    // private function obtener_factor_cambio_unidades_longitud($unidades) {
    //     switch ($unidades) {
    //         case 'M':  return 1;
    //         case 'CM': return 0.01;
    //         case 'MM': return 0.001;
    //         default:   return 1;
    //     }
    // }

        
    // Obtener lista con la resultante de F de un estado de cargas
    // Además se devuelve una lista con los nombres de los patrones de carga
    // del modelo
        public function obtener_resultante_frame_loads_distributed($frame_loads_distributed_normalized, $tokens_frame_loads_distributed_normalized) { //, $tokens_frame_loads_distributed) {

        // Imprime información para depuración
        // echo  "-------obtener_resultante_frame_loads_distributed-------\n";
        // echo  "tokens_frame_loads_distributed_normalized = ";
        // print_r($tokens_frame_loads_distributed_normalized);
        //echo "tokens_frame_loads_distributed = ";
        //print_r($tokens_frame_loads_distributed);

        // Encuentra los índices correspondientes a las columnas necesarias
        $col_LoadPat = array_search('LoadPat', $tokens_frame_loads_distributed_normalized);
        $col_norm_R = array_search('Norm_R', $tokens_frame_loads_distributed_normalized);
        $col_Dir = array_search('Dir', $tokens_frame_loads_distributed_normalized);

        // Obtiene los patrones de carga asociados a las cargas distribuidas
        $LoadPat_frame_loads = $this->obtener_LoadPat_frame_loads_distributed($frame_loads_distributed_normalized);

        // Inicializa el array de resultados
        $Resul = array();
        foreach ($LoadPat_frame_loads as $lista) {
            // Rx   Ry   Rz   Mx   My   Mz
            $Resul[] = array(0.0, 0.0, 0.0, 0.0, 0.0, 0.0);
        }

        // Recorre las cargas distribuidas y acumula las fuerzas en el array resultante
        foreach ($frame_loads_distributed_normalized as $I_frame_loads_distributed) {
            $LoadPat = $I_frame_loads_distributed[$col_LoadPat];
            $norm_R = $I_frame_loads_distributed[$col_norm_R];
            $LoadPatIndex = array_search($LoadPat, $LoadPat_frame_loads);

            $Dir = $I_frame_loads_distributed[$col_Dir];
            // echo  "Dir=$Dir. norm_R=$norm_R, col_norm_R=$col_norm_R, $LoadPat\n";

            if ($Dir == '"GravProj"' || $Dir == 'Gravity' || $Dir == '"ZProj"' || $Dir == 'Z') {
                $Resul[$LoadPatIndex][2] += $norm_R; // Incrementa en Rz
            }
            if ($Dir == '"XProj"' || $Dir == 'X') {
                $Resul[$LoadPatIndex][0] += $norm_R; // Incrementa en Rx
            }
            if ($Dir == '"YProj"' || $Dir == 'Y') {
                $Resul[$LoadPatIndex][1] += $norm_R; // Incrementa en Ry
            }
        }

        // echo  "-------FIN de obtener_resultante_frame_loads_distributed-------\n";
        // print_r($Resul);
        // Retorna el resultado y los patrones de carga
        return array($Resul, $LoadPat_frame_loads);
    }

            // Método para obtener lista con los patrones de carga de un modelo
    public function obtener_LoadPat_frame_loads_distributed($frame_loads_distributed) {
        // Obtener la columna de 'LoadPat' a partir de los tokens
        $col_LoadPat = array_search('LoadPat', $this->tokens_frame_loads_distributed_normalized);
        
        $LoadPat_frame_loads = [];
        
        // Iterar sobre la lista de cargas distribuidas
        foreach ($frame_loads_distributed as $lista) {
            // Añadir el valor de la columna 'LoadPat' a la lista
            $LoadPat_frame_loads[] = $lista[$col_LoadPat];
        }
        
        // Eliminar duplicados con array_unique y ordenar
        $LoadPat_frame_loads = array_unique($LoadPat_frame_loads);
        sort($LoadPat_frame_loads);
        
        // Retornar la lista de patrones de carga ordenada
        return $LoadPat_frame_loads;
    }
    }
}
