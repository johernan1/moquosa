<?php

if (!trait_exists('joints')) {
    trait joints {
        // Se ordenan los nudos en funcion de X Y Z
        // Se añaden 7 columnas
        // col 0+n_tokens_joint_coordinates-> nuevo numero del nudo
        // col 1+n_tokens_joint_coordinates-> coor x referida al cdg en m
        // col 2+n_tokens_joint_coordinates-> coor y referida al cdg en m
        // col 3+n_tokens_joint_coordinates-> coor z referida al cdg en m
        // col 4+n_tokens_joint_coordinates-> coor x original en m
        // col 5+n_tokens_joint_coordinates-> coor y original en m
        // col 6+n_tokens_joint_coordinates-> coor z original en m

        // Se actualiza tokens_joint_coordinates_normalized, de este modo se puede
        // obtener los indices anteriores escribiendo
        // array_search('Norm_Joint', tokens_joint_coordinates_normalized)
        // array_search('Norm_XorR', tokens_joint_coordinates_normalized)
        // array_search('Norm_Y', tokens_joint_coordinates_normalized)
        // array_search('Norm_Z', tokens_joint_coordinates_normalized)
        // array_search('Orig_XorR', tokens_joint_coordinates_normalized)
        // array_search('Orig_Y', tokens_joint_coordinates_normalized)
        // array_search('Orig_Z', tokens_joint_coordinates_normalized)

        function normalizar_joint_coordinates($joint_coordinates, $unidades, $EscM = 1) {
            //global $tokens_joint_coordinates_normalized, $tokens_joint_coordinates, $n_tokens_joint_coordinates;
    
            // Identificación de las columnas de interés
            $colx = array_search('XorR', $this->tokens_joint_coordinates);
            $coly = array_search('Y', $this->tokens_joint_coordinates);
            $colz = array_search('Z', $this->tokens_joint_coordinates);

            // print_object('normalizar_joint_coordinates');
            // print_object('$colx');
            // print_object($colx);
            // print_object('$coly');
            // print_object($coly);
            // print_object('$colz');
            // print_object($colz);
            // Se ordena 
            $joint_coordinates_sort = $this->ordenar_lista_lista_coli_colj_colk($joint_coordinates, $colx, $coly, $colz);

            // print_object('coordenadas ordenadas');
            // print_object($joint_coordinates_sort);
            
            list($xcdg, $ycdg, $zcdg) = $this->obtener_cdg_joint_coordinates($joint_coordinates_sort);

            // print_object('cdg coordenadas');
            // print_object([$xcdg, $ycdg, $zcdg]);

            $factorunidades = $this->obtener_factor_cambio_unidades_longitud($unidades) * $EscM;
    
            // Se añaden varias columnas:
            // Primero el id de la columna
            if (!in_array("Norm_Joint", $this->tokens_joint_coordinates_normalized)) {
                $this->tokens_joint_coordinates_normalized[] = "Norm_Joint";
                $this->tokens_joint_coordinates_normalized[] = "Norm_XorR";
                $this->tokens_joint_coordinates_normalized[] = "Norm_Y";
                $this->tokens_joint_coordinates_normalized[] = "Norm_Z";
                $this->tokens_joint_coordinates_normalized[] = "Orig_XorR";
                $this->tokens_joint_coordinates_normalized[] = "Orig_Y";
                $this->tokens_joint_coordinates_normalized[] = "Orig_Z";
            }

            // print_object('tokens y tokens normailzados');
            // print_object($this->tokens_joint_coordinates);
            // print_object($this->tokens_joint_coordinates_normalized);
            // Y ahora se añaden los valores de cada columna    
            $i = 1;
            foreach ($joint_coordinates_sort as &$linea) {
                $linea[] = $i;
                $linea[] = round((floatval($linea[$colx]) - $xcdg) * $factorunidades, 6);
                $linea[] = round((floatval($linea[$coly]) - $ycdg) * $factorunidades, 6);
                $linea[] = round((floatval($linea[$colz]) - $zcdg) * $factorunidades, 6);
                $linea[] = round((floatval($linea[$colx])) * $factorunidades, 6);
                $linea[] = round((floatval($linea[$coly])) * $factorunidades, 6);
                $linea[] = round((floatval($linea[$colz])) * $factorunidades, 6);
                $i++;
            }
    
            // echo "JOINTS.PHP " . print_r($joint_coordinates_sort, true);
            return $joint_coordinates_sort;
        }

        // varios "obtener" auto explicativos
        function obtener_cdg_joint_coordinates($joint_coordinates) {
            //global $tokens_joint_coordinates;
    
            $colx = array_search('XorR', $this->tokens_joint_coordinates);
            $coly = array_search('Y', $this->tokens_joint_coordinates);
            $colz = array_search('Z', $this->tokens_joint_coordinates);

            $xcdg = 0;
            $ycdg = 0;
            $zcdg = 0;
            foreach ($joint_coordinates as $linea) {
                $xcdg += floatval($linea[$colx]);
                $ycdg += floatval($linea[$coly]);
                $zcdg += floatval($linea[$colz]);
            }

            $ncoor = count($joint_coordinates);  
            return [$xcdg / $ncoor, $ycdg / $ncoor, $zcdg / $ncoor];
        }

        function obtener_Lx_joint_coordinates($joint_coordinates) {
            //global $tokens_joint_coordinates;
            $col = array_search('XorR', $this->tokens_joint_coordinates);
            return round($this->obtener_max_min_coli($joint_coordinates, $col), 6);
        }

        function obtener_Ly_joint_coordinates($joint_coordinates) {
            //global $tokens_joint_coordinates;
            $col = array_search('Y', $this->tokens_joint_coordinates);
            return round($this->obtener_max_min_coli($joint_coordinates, $col), 6);
        }

        function obtener_Lz_joint_coordinates($joint_coordinates) {
            //global $tokens_joint_coordinates;
            $col = array_search('Z', $this->tokens_joint_coordinates);
            return round($this->obtener_max_min_coli($joint_coordinates, $col), 6);
        }

        function obtener_Lx_joint_coordinates_normalized($joint_coordinates) {
            //global $n_tokens_joint_coordinates;
            $col = 1 + $this->n_tokens_joint_coordinates;
            return round($this->obtener_max_min_coli($joint_coordinates, $col), 6);
        }

        function obtener_Ly_joint_coordinates_normalized($joint_coordinates) {
            global $n_tokens_joint_coordinates;
            $col = 2 + $this->n_tokens_joint_coordinates;
            return round($this->obtener_max_min_coli($joint_coordinates, $col), 6);
        }

        function obtener_Lz_joint_coordinates_normalized($joint_coordinates) {
            //global $n_tokens_joint_coordinates;
            $col = 3 + $this->n_tokens_joint_coordinates;
            return round($this->obtener_max_min_coli($joint_coordinates, $col), 6);
        }


    }
}
?>
