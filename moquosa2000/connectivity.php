<?php

if (!trait_exists('connectivity')) {
    trait connectivity {
        public function normalizar_frame_connectivity($frame_connectivity, $joint_coordinates_normalized) {
            // Número de los nodos y coordenadas normalizadas
            // Ver joints.py
            // coln = 0+n_tokens_joint_coordinates
            // colx = 4+n_tokens_joint_coordinates
            // coly = 5+n_tokens_joint_coordinates
            // colz = 6+n_tokens_joint_coordinates
        
            //global $tokens_joint_coordinates_normalized, $tokens_frame_connectivity_normalized;
        
            $coln = array_search('Norm_Joint', $this->tokens_joint_coordinates_normalized); 
            $colx = array_search('Orig_XorR', $this->tokens_joint_coordinates_normalized);
            $coly = array_search('Orig_Y', $this->tokens_joint_coordinates_normalized);
            $colz = array_search('Orig_Z', $this->tokens_joint_coordinates_normalized);
        
            // Se extrae la primera columna de joint_coordinates_normalized
            // Esta lista permite localizar fácilmente la fila de el nudo correspondiente
            // echo  'normalizar_frame_connectivity. TODO: lista[0]->?';
            $n_joint_coordinates = [];
            foreach ($joint_coordinates_normalized as $lista) {
                $n_joint_coordinates[] = $lista[0];
            }
        
            // Se añaden varias columnas:
            // Primero el id de la columna
            if (!in_array("Norm_JointI", $this->tokens_frame_connectivity_normalized)) {
                array_push($this->tokens_frame_connectivity_normalized, 'Norm_JointI', 'Norm_JointJ', 'JointI_Orig_XorR', 
                           'JointI_Orig_Y', 'JointI_Orig_Z', 'JointJ_Orig_XorR', 'JointJ_Orig_Y', 
                           'JointJ_Orig_Z', 'Norm_Lx', 'Norm_Ly', 'Norm_Lz', 'Norm_L', 'Norm_IxJ');
            }
        
            // Y ahora se añaden los valores de cada columna  
            foreach ($frame_connectivity as &$frame) {
                $nodoi = $frame[1];  // Este es el "nombre" del nudo, no su numero de orden en joint_coordinates
                $nodoj = $frame[2];
            
                // Esta es la posición del Joint en la lista de Joints
                $nodoI = array_search($nodoi, $n_joint_coordinates);
                $nodoJ = array_search($nodoj, $n_joint_coordinates);
            
                $newnodoi = $joint_coordinates_normalized[$nodoI][$coln];
                $newnodoj = $joint_coordinates_normalized[$nodoJ][$coln];
                $norm_IxJ = 0;  // Indicador de si se ha reorientado la barra     
                if ($newnodoi > $newnodoj) {
                    $norm_IxJ = 1;  // Se ha reorientado la barra
                    $nodoi = $frame[2];
                    $nodoj = $frame[1];
                    $nodoI = array_search($nodoi, $n_joint_coordinates);
                    $nodoJ = array_search($nodoj, $n_joint_coordinates);
                }
                $newnodoi = $joint_coordinates_normalized[$nodoI][$coln];
                $newnodoj = $joint_coordinates_normalized[$nodoJ][$coln];
            
                $xi = $joint_coordinates_normalized[$nodoI][$colx];
                $yi = $joint_coordinates_normalized[$nodoI][$coly];
                $zi = $joint_coordinates_normalized[$nodoI][$colz];
            
                $xj = $joint_coordinates_normalized[$nodoJ][$colx];
                $yj = $joint_coordinates_normalized[$nodoJ][$coly];
                $zj = $joint_coordinates_normalized[$nodoJ][$colz];
            
                array_push($frame, $newnodoi, $newnodoj, $xi, $yi, $zi, $xj, $yj, $zj);
            
                $lx = $xj - $xi;
                $ly = $yj - $yi;
                $lz = $zj - $zi;
                $l = sqrt($lx * $lx + $ly * $ly + $lz * $lz);
            
                array_push($frame, $lx, $ly, $lz, $l, $norm_IxJ);
            }
            //print_r($this->tokens_frame_connectivity_normalized);
            //$this->printL($frame_connectivity,"frame_connectivity");
            //exit(1);
        
            // echo  "normalizar_frame_connectivity.TODO: 5->n_tokens_frame_connectivity?";
            return $this->ordenar_lista_lista_coli_colj_colk($frame_connectivity, 4, 5, 5);
        }

        // Método para añadir las propiedades de la sección a frame_connectivity
        public function add_section_frame_connectivity_normalized(&$frame_connectivity, $frame_section_assignments, $section_properties) {
            //global $tokens_frame_connectivity_normalized, $tokens_section_properties_normalized;

            $this->printL($frame_connectivity,'-----------$frame_connectivity');
            $this->printL($frame_section_assignments,'-----------$frame_section_assignments');
            $this->printL($section_properties,'-----------$frame_section_properties');
            // Se extrae la primera columna de frame_connectivity
            // Esta lista permite localizar fácilmente la fila de la barra correspondiente
            $n_frame_connectivity = [];
            foreach ($frame_connectivity as $lista) {
                $n_frame_connectivity[] = $lista[0];
            }

            // Se extrae la primera columna de section_properties
            // Esta lista permite localizar fácilmente la fila de la seccion correspondiente
            $n_section_properties = [];
            foreach ($section_properties as $lista) {
                $n_section_properties[] = $lista[0];
            }
            //print_object('---------------------$n_section_properties');
            //print_object($n_section_properties);

            $col_SectionName = array_search('SectionName', $this->tokens_section_properties_normalized);
            $col_Material = array_search('Material', $this->tokens_section_properties_normalized);
            $col_Area = array_search('Norm_Area', $this->tokens_section_properties_normalized);
            $col_I33 = array_search('Norm_I33', $this->tokens_section_properties_normalized);
            $col_AMod = array_search('AMod', $this->tokens_section_properties_normalized);
            $col_I3Mod = array_search('I3Mod', $this->tokens_section_properties_normalized);
            // print_object('-----------------$this->tokens_frame_connectivity_normalized');
            //print_object($this->tokens_frame_connectivity_normalized);
            // Se añaden varias columnas:
            // Primero el id de la columna
            if (!in_array("Norm_Joint", $this->tokens_frame_connectivity_normalized)) {
                array_push($this->tokens_frame_connectivity_normalized, 'SectionName0', 'SectionName1', 'Material', 'Area', 'I33', 'AMod', 'I3Mod');
            }
            //print_object('-----------------$this->tokens_frame_connectivity_normalized');
 //print_object($this->tokens_frame_connectivity_normalized);

            //print_object('-----------------$frame_connectivity');
            //print_object($frame_connectivity);
            //$this->printL($frame_connectivity, '----add_section_frame_connectivity_normalized-------------$frame_connectivity');
            // Y ahora se añaden los valores de cada columna    
            foreach ($frame_section_assignments as $frame) {
                $n_frame = intval($frame[0]);
                $SectionName = $frame[1];
                $n_frame = array_search((string)$n_frame, $n_frame_connectivity);
            
                $SectionIndex = array_search($SectionName, $n_section_properties);
                $I_Section_Properties = $section_properties[$SectionIndex];
            
                array_push($frame_connectivity[$n_frame], $SectionName, $I_Section_Properties[$col_SectionName], 
                           $I_Section_Properties[$col_Material], floatval($I_Section_Properties[$col_Area]), 
                           floatval($I_Section_Properties[$col_I33]), floatval($I_Section_Properties[$col_AMod]), 
                           floatval($I_Section_Properties[$col_I3Mod]));
            }

            //print_object('-----------------$frame_connectivity');
            //print_object($frame_connectivity);
            //$this->printL($frame_connectivity, '----add_section_frame_connectivity_normalized-------------$frame_connectivity-AMPLIADA');

        }
    }
}
