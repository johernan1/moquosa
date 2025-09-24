<?php

if (!trait_exists(' frame_loads_point')) {
    trait  frame_loads_point {

            public function normalizar_frame_loads_point($frame_loads_point, $frame_connectivity_normalized, $unidadesL, $unidadesF, $EscM = 1, $EscF = 1) {
        // echo  "normalizar_frame_loads_point\n";
        // echo  "tokens_frame_connectivity_normalized: ", json_encode($this->tokens_frame_connectivity_normalized), "\n";
        // echo  "frame_loads_point: ", json_encode($this->tokens_frame_loads_point), "\n";
        
        $col_Frame            = array_search('Frame', $this->tokens_frame_loads_point);
        $col_RelDist          = array_search('RelDist', $this->tokens_frame_loads_point);
        $col_Force            = array_search('Force', $this->tokens_frame_loads_point);
        $col_Type             = array_search('Type', $this->tokens_frame_loads_point);
        $col_Dir              = array_search('Dir', $this->tokens_frame_loads_point);
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
        $col_Norm_IxJ         = array_search('Norm_IxJ', $this->tokens_frame_connectivity_normalized);
        
        $factorunidadesF = $this->obtener_factor_cambio_unidades_fuerza($unidadesF) * $EscF;
        $factorunidadesL = $this->obtener_factor_cambio_unidades_longitud($unidadesL) * $EscM;
        
        // Crear diccionario para localizar la fila de la barra
        $n_frame_connectivity = [];
        foreach ($frame_connectivity_normalized as $lista) {
            $n_frame_connectivity[] = $lista[0];
        }

        // Añadir varias columnas si no existen
        if (!in_array("Norm_Frame", $this->tokens_frame_loads_point_normalized)) {
            array_push($this->tokens_frame_loads_point_normalized, "Norm_Frame", "JointI_Orig_XorR", "JointI_Orig_Y", "JointI_Orig_Z", 
                       "JointJ_Orig_XorR", "JointJ_Orig_Y", "JointJ_Orig_Z", "Norm_IxJ", "Norm_Lx", "Norm_Ly", "Norm_Lz", 
                       "Norm_L", "Norm_Force", "Norm_R", "Norm_e");
        }

        // Añadir valores a cada fila
        foreach ($frame_loads_point as &$I_frame_loads_point) {
            // echo  "I_frame_loads_point: ", json_encode($I_frame_loads_point), "\n";
            $i_frame = $I_frame_loads_point[$col_Frame];
            $I_frame = array_search($i_frame, $n_frame_connectivity);
            // echo  "i_frame, I_frame: ", $i_frame, ", ", $I_frame, "\n";
          
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
            
            $norm_Force = floatval($I_frame_loads_point[$col_Force]) * $factorunidadesF;
            $Dir        = $I_frame_loads_point[$col_Dir];
            $RelDist    = floatval($I_frame_loads_point[$col_RelDist]);

            $F_med = $norm_Force;
            $norm_e = 0;

            if ($Dir == 'Gravity') {
                $norm_R = -$F_med;
                if ($norm_R != 0) {
                    $norm_e = $RelDist;
                }
            } elseif (in_array($Dir, ['Z', 'X', 'Y', '1', '2', '3'])) {
                $norm_R = $F_med;
                if ($norm_R != 0) {
                    $norm_e = $RelDist;
                }
            } else {
                // echo  "ERROR EN CARGAS DISTRIBUIDAS. Dir ", $Dir, " no definido\n";
                exit();
            }

            array_push($I_frame_loads_point, $I_frame, $JointI_Orig_XorR, $JointI_Orig_Y, $JointI_Orig_Z, $JointJ_Orig_XorR,
                        $JointJ_Orig_Y, $JointJ_Orig_Z, $norm_IxJ, $norm_Lx, $norm_Ly, $norm_Lz, $norm_L, $norm_Force, $norm_R, $norm_e);
        }

        $col_LoadPat = array_search('LoadPat', $this->tokens_frame_loads_point_normalized);
        // echo  "normalizar_frame_loads_point.col_LoadPat: ", $col_LoadPat, "\n";
        
        return $this->ordenar_lista_lista_strcoli_colj_colk($frame_loads_point, $col_LoadPat, $this->n_tokens_frame_loads_point, $this->n_tokens_frame_loads_point);
    }

    public function obtener_LoadPat_frame_loads_point($frame_loads_point) {
        $col_LoadPat = array_search('LoadPat', $this->tokens_frame_loads_point_normalized);
        $LoadPat_frame_loads = [];

        foreach ($frame_loads_point as $lista) {
            $LoadPat_frame_loads[] = $lista[$col_LoadPat];
        }

        $LoadPat_frame_loads = array_unique($LoadPat_frame_loads);
        sort($LoadPat_frame_loads);
        return $LoadPat_frame_loads;
    }

    public function obtener_resultante_frame_loads_point($frame_loads_point_normalized) {
        // echo  "tokens_frame_loads_point_normalized: ", json_encode($this->tokens_frame_loads_point_normalized), "\n";
        // echo  "tokens_frame_loads_point: ", json_encode($this->tokens_frame_loads_point), "\n";

        $col_LoadPat = array_search('LoadPat', $this->tokens_frame_loads_point_normalized);
        $col_norm_R  = array_search('Norm_R', $this->tokens_frame_loads_point_normalized);
        $col_Dir     = array_search('Dir', $this->tokens_frame_loads_point_normalized);

        $LoadPat_frame_loads = $this->obtener_LoadPat_frame_loads_point($frame_loads_point_normalized);
        $Resul = array_fill(0, count($LoadPat_frame_loads), [0.0, 0.0, 0.0, 0.0, 0.0, 0.0]);

        foreach ($frame_loads_point_normalized as $I_frame_loads_point) {
            $LoadPat = $I_frame_loads_point[$col_LoadPat];
            $norm_R  = $I_frame_loads_point[$col_norm_R];
            $LoadPatIndex = array_search($LoadPat, $LoadPat_frame_loads);
            $Dir = $I_frame_loads_point[$col_Dir];

            if (in_array($Dir, ['"GravProj"', 'Gravity', '"ZProj"', 'Z'])) {
                $Resul[$LoadPatIndex][2] += $norm_R;
            }
            if (in_array($Dir, ['"XProj"', 'X'])) {
                $Resul[$LoadPatIndex][0] += $norm_R;
            }
            if (in_array($Dir, ['"YProj"', 'Y'])) {
                $Resul[$LoadPatIndex][1] += $norm_R;
            }
        }

        return [$Resul, $LoadPat_frame_loads];
    }
    }
}
