<?php

if (!trait_exists('restraints')) {
    trait restraints {


        /**
         * Normaliza las asignaciones de restricciones basadas en las coordenadas normalizadas de los nudos.
         *
         * @param array $restraint_assignments Asignaciones de restricciones a normalizar.
         * @param array $joint_coordinates_normalized Coordenadas normalizadas de los nudos.
         * @return array La lista de asignaciones de restricciones normalizadas ordenada.
         */
        public function normalizar_restraint_assignments($restraint_assignments, $joint_coordinates_normalized) {
            $col_j_c_Joint = array_search('Joint', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Norm_Joint = array_search('Norm_Joint', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Norm_XorR = array_search('Norm_XorR', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Norm_Y = array_search('Norm_Y', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Norm_Z = array_search('Norm_Z', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Orig_XorR = array_search('Orig_XorR', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Orig_Y = array_search('Orig_Y', $this->tokens_joint_coordinates_normalized);
            $col_j_c_Orig_Z = array_search('Orig_Z', $this->tokens_joint_coordinates_normalized);

            $col_r_a_Joint = array_search('Joint', $this->tokens_restraint_assignments);

            // Se extrae la primera columna de joint_coordinates_normalized
            // Esta lista permite localizar fácilmente la fila del nudo correspondiente
            $n_joint_coordinates = array_column($joint_coordinates_normalized, 0);

            // Se añaden varias columnas:
            // Primero el id de la columna
            if (!in_array("Norm_Joint", $this->tokens_restraint_assignments_normalized)) {
                $this->tokens_restraint_assignments_normalized = array_merge($this->tokens_restraint_assignments_normalized, [
                    "Norm_Joint", "Norm_XorR", "Norm_Y", "Norm_Z",
                    "Orig_XorR", "Orig_Y", "Orig_Z"
                ]);
            }

            // Y ahora se añaden los valores de cada columna    
            foreach ($restraint_assignments as &$linea) {
                $nodoi = $linea[$col_r_a_Joint];
                $nodoI = array_search($nodoi, $n_joint_coordinates);
            
                $norm_nodoi = $joint_coordinates_normalized[$nodoI][$col_j_c_Norm_Joint];
                $norm_xi = $joint_coordinates_normalized[$nodoI][$col_j_c_Norm_XorR];
                $norm_yi = $joint_coordinates_normalized[$nodoI][$col_j_c_Norm_Y];
                $norm_zi = $joint_coordinates_normalized[$nodoI][$col_j_c_Norm_Z];
                $orig_xi = $joint_coordinates_normalized[$nodoI][$col_j_c_Orig_XorR];
                $orig_yi = $joint_coordinates_normalized[$nodoI][$col_j_c_Orig_Y];
                $orig_zi = $joint_coordinates_normalized[$nodoI][$col_j_c_Orig_Z];

                array_push($linea, $norm_nodoi, $norm_xi, $norm_yi, $norm_zi, $orig_xi, $orig_yi, $orig_zi);
            }

            return $this->ordenar_lista_lista_coli_colj_colk(
                $restraint_assignments,
                $this->n_tokens_restraint_assignments,
                $this->n_tokens_restraint_assignments,
                $this->n_tokens_restraint_assignments
            );
        }
    }
}
