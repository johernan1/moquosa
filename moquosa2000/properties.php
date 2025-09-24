<?php

if (!trait_exists('properties')) {
    trait properties {

        function normalizar_section_properties($section_properties, $unidades) {
            //global $tokens_section_properties, $tokens_section_properties_normalized;

            // Se obtiene el índice de las columnas 'Area' e 'I33' en tokens_section_properties
            $col_Area = array_search('Area', $this->tokens_section_properties);
            $col_I33 = array_search('I33', $this->tokens_section_properties);

            $factorunidades = $this->obtener_factor_cambio_unidades_longitud($unidades);
    
            // Se añaden varias columnas:
            // Primero el id de la columna
            if (!in_array("Norm_Area", $this->tokens_section_properties_normalized)) {
                $this->tokens_section_properties_normalized[] = "Norm_Area";
                $this->tokens_section_properties_normalized[] = "Norm_I33";
            }

            // Y ahora se añaden los valores de cada columna    
            foreach ($section_properties as &$linea) {
                $linea[] = round(floatval($linea[$col_Area]) * $factorunidades * $factorunidades, 10);
                $linea[] = round(floatval($linea[$col_I33]) * $factorunidades * $factorunidades * $factorunidades * $factorunidades, 12);
            }
    
            // print("JOINTS.PY", $tokens_section_properties_normalized);
            // print("JOINTS.PY", $tokens_section_properties);
    
            return $section_properties;  
        }
    }
}
