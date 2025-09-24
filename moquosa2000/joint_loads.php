<?php

if (!trait_exists('joint_loads')) {
    trait joint_loads {
        public function normalizar_joint_loads_force($joint_loads_forces, $joint_coordinates_normalized, $unidadesL, $unidadesF, $EscM = 1, $EscF = 1) {
            // Columnas de índices
            $col_j_c_Joint      = array_search('Joint', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Norm_Joint = array_search('Norm_Joint', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Norm_XorR  = array_search('Norm_XorR', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Norm_Y     = array_search('Norm_Y', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Norm_Z     = array_search('Norm_Z', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Orig_XorR  = array_search('Orig_XorR', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Orig_Y     = array_search('Orig_Y', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Orig_Z     = array_search('Orig_Z', $this->tokens_joint_coordinates_normalized);
            $col_LoadPat        = array_search('LoadPat', $this->tokens_joint_loads_force_normalized);
            $col_j_l_f_Joint    = array_search('Joint', $this->tokens_joint_loads_force_normalized);
            $col_j_l_f_F1       = array_search('F1', $this->tokens_joint_loads_force_normalized);
            $col_j_l_f_F2       = array_search('F2', $this->tokens_joint_loads_force_normalized);
            $col_j_l_f_F3       = array_search('F3', $this->tokens_joint_loads_force_normalized);
            $col_j_l_f_M1       = array_search('M1', $this->tokens_joint_loads_force_normalized);
            $col_j_l_f_M2       = array_search('M2', $this->tokens_joint_loads_force_normalized);
            $col_j_l_f_M3       = array_search('M3', $this->tokens_joint_loads_force_normalized);

            // Factores de unidades
            $factorunidadesF = $this->obtener_factor_cambio_unidades_fuerza($unidadesF) * $EscF;
            $factorunidadesL = $this->obtener_factor_cambio_unidades_longitud($unidadesL) * $EscM;

            // Se extrae la primera columna de joint_coordinates_normalized
            // Esta lista permite localizar fácilmente la fila del nudo correspondiente
            $n_joint_coordinates = [];
            foreach ($joint_coordinates_normalized as $lista) {
                $n_joint_coordinates[] = $lista[0];
            }

            // Se añaden varias columnas si no existen
            if (!in_array("Norm_Joint", $this->tokens_joint_loads_force_normalized)) {
                array_push($this->tokens_joint_loads_force_normalized, "Norm_Joint", "Norm_XorR", "Norm_Y", "Norm_Z", "Orig_XorR", "Orig_Y", "Orig_Z", "Norm_F1", "Norm_F2", "Norm_F3", "Norm_M1", "Norm_M2", "Norm_M3");
            }

            // Añadir valores de cada columna
            foreach ($joint_loads_forces as &$linea) {
                $nodoi = $linea[$col_j_l_f_Joint];
                $nodoI = array_search($nodoi, $n_joint_coordinates);
                $norm_nodoi = $joint_coordinates_normalized[$nodoI][$col_j_c_Norm_Joint];
                $norm_xi = $joint_coordinates_normalized[$nodoI][$col_j_c_Norm_XorR];
                $norm_yi = $joint_coordinates_normalized[$nodoI][$col_j_c_Norm_Y];
                $norm_zi = $joint_coordinates_normalized[$nodoI][$col_j_c_Norm_Z];
                $orig_xi = $joint_coordinates_normalized[$nodoI][$col_j_c_Orig_XorR];
                $orig_yi = $joint_coordinates_normalized[$nodoI][$col_j_c_Orig_Y];
                $orig_zi = $joint_coordinates_normalized[$nodoI][$col_j_c_Orig_Z];

                $norm_F1 = floatval($linea[$col_j_l_f_F1]) * $factorunidadesF;
                $norm_F2 = floatval($linea[$col_j_l_f_F2]) * $factorunidadesF;
                $norm_F3 = floatval($linea[$col_j_l_f_F3]) * $factorunidadesF;
                $norm_M1 = floatval($linea[$col_j_l_f_M1]) * $factorunidadesF * $factorunidadesL;
                $norm_M2 = floatval($linea[$col_j_l_f_M2]) * $factorunidadesF * $factorunidadesL;
                $norm_M3 = floatval($linea[$col_j_l_f_M3]) * $factorunidadesF * $factorunidadesL;

                // Ajuste para estados virtuales 1kN
                if ($linea[$col_LoadPat] == "1kN") {
                    $norm_F1 /= $EscF;
                    $norm_F2 /= $EscF;
                    $norm_F3 /= $EscF;
                }

                array_push($linea, $norm_nodoi, $norm_xi, $norm_yi, $norm_zi, $orig_xi, $orig_yi, $orig_zi, $norm_F1, $norm_F2, $norm_F3, $norm_M1, $norm_M2, $norm_M3);
            }

            // Ordenar la lista según los criterios
            return $this->ordenar_lista_lista_strcoli_colj_colk($joint_loads_forces, $col_LoadPat,
                                              $this->n_tokens_joint_loads_force,
                                              $this->n_tokens_joint_loads_force) ;
        }

        // Obtener lista con los patrones de carga de un modelo
        public function obtener_LoadPat($joint_loads_forces) {
            $col_LoadPat = array_search('LoadPat', $this->tokens_joint_loads_force_normalized);
            $LoadPat_joint_loads = [];
            foreach ($joint_loads_forces as $lista) {
                $LoadPat_joint_loads[] = $lista[$col_LoadPat];
            }
            $LoadPat_joint_loads = array_unique($LoadPat_joint_loads);
            sort($LoadPat_joint_loads);
            return $LoadPat_joint_loads;
        }

        // Obtener lista con la resultante de F y M de un estado de cargas
        // Además se devuelve una lista con los nombres de los patrones de carga del modelo
        public function obtener_resultante_joint_loads_forces($joint_loads_forces_normalized) {
            $col_LoadPat = array_search('LoadPat', $this->tokens_joint_loads_force_normalized);
            $col_x_norm = array_search('Norm_XorR', $this->tokens_joint_loads_force_normalized);
            $col_y_norm = array_search('Norm_Y', $this->tokens_joint_loads_force_normalized);
            $col_z_norm = array_search('Norm_Z', $this->tokens_joint_loads_force_normalized);

            $col_x_orig = array_search('Orig_XorR', $this->tokens_joint_loads_force_normalized);
            $col_y_orig = array_search('Orig_Y', $this->tokens_joint_loads_force_normalized);
            $col_z_orig = array_search('Orig_Z', $this->tokens_joint_loads_force_normalized);

            $col_F1 = array_search('Norm_F1', $this->tokens_joint_loads_force_normalized);
            $col_F2 = array_search('Norm_F2', $this->tokens_joint_loads_force_normalized);
            $col_F3 = array_search('Norm_F3', $this->tokens_joint_loads_force_normalized);
            $col_M1 = array_search('Norm_M1', $this->tokens_joint_loads_force_normalized);
            $col_M2 = array_search('Norm_M2', $this->tokens_joint_loads_force_normalized);
            $col_M3 = array_search('Norm_M3', $this->tokens_joint_loads_force_normalized);

            $LoadPat_joint_loads = $this->obtener_LoadPat($joint_loads_forces_normalized);
            $Resul = [];
            foreach ($LoadPat_joint_loads as $lista) {
                $Resul[] = [0.0, 0.0, 0.0, 0.0, 0.0, 0.0]; // Rx, Ry, Rz, Mx, My, Mz
            }

            foreach ($joint_loads_forces_normalized as $lista) {
                $LoadPat = $lista[$col_LoadPat];
                $LoadPatIndex = array_search($LoadPat, $LoadPat_joint_loads);

                $F1 = floatval($lista[$col_F1]);
                $F2 = floatval($lista[$col_F2]);
                $F3 = floatval($lista[$col_F3]);
                $M1 = floatval($lista[$col_M1]);
                $M2 = floatval($lista[$col_M2]);
                $M3 = floatval($lista[$col_M3]);
                $xO = $lista[$col_x_orig];
                $yO = $lista[$col_y_orig];
                $zO = $lista[$col_z_orig];
                $xN = $lista[$col_x_norm];
                $yN = $lista[$col_y_norm];
                $zN = $lista[$col_z_norm];

                $Resul[$LoadPatIndex][0] += $F1;
                $Resul[$LoadPatIndex][1] += $F2;
                $Resul[$LoadPatIndex][2] += $F3;
                $Resul[$LoadPatIndex][3] += $M1 + $F2 * ($zN) - $F3 * ($yN);
                $Resul[$LoadPatIndex][4] += $M2 + $F3 * ($xN) + $F1 * ($zN);
                $Resul[$LoadPatIndex][5] += $M3 + $F1 * ($yN) - $F2 * ($xN);
            }

            return [$Resul, $LoadPat_joint_loads];
        }
    }
}
