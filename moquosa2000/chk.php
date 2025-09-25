<?php

if (!trait_exists('chk')) {
    trait chk {

        /**
         * Chequea las coordenadas de los nudos.
         *
         * @param array $joint_coordinates_normalized_SOL Lista de coordenadas normalizadas SOL.
         * @param array $joint_coordinates_normalized_RES Lista de coordenadas normalizadas RES.
         * @param resource $f Recurso de archivo para escritura.
         * @return int Resultado del chequeo (0 si hay error, 1 si está correcto).
         */
        public function chk_joint_coordinates($joint_coordinates_normalized_SOL, $joint_coordinates_normalized_RES, $f)
        {
            //--------------------------------------------------------------------------
            //--- Chequeo número de nudos. Exist si KO
            $ncoor_SOL = count($joint_coordinates_normalized_SOL);
            $ncoor_RES = count($joint_coordinates_normalized_RES);
            $textoKO = "[-EE-] El número de nudos de su modelo es $ncoor_RES. Debería ser $ncoor_SOL. KO";
            $textoOK = "[-II-] El número de nudos de su modelo es $ncoor_RES. OK";
            if (!$this->comparar_con_tolerancia_KOexit($ncoor_SOL, $ncoor_RES, 0, $f, $textoKO, $textoOK)) {
                return 0;
            }

            //---------------------------------------------------------------------------
            //--- Chequeo Lx, Ly, Lz. Exit si KO
            $Lx_SOL = $this->obtener_Lx_joint_coordinates_normalized($joint_coordinates_normalized_SOL);
            $Lx_RES = $this->obtener_Lx_joint_coordinates_normalized($joint_coordinates_normalized_RES);
        
            $Ly_SOL = $this->obtener_Ly_joint_coordinates_normalized($joint_coordinates_normalized_SOL);
            $Ly_RES = $this->obtener_Ly_joint_coordinates_normalized($joint_coordinates_normalized_RES);
        
            $Lz_SOL = $this->obtener_Lz_joint_coordinates_normalized($joint_coordinates_normalized_SOL);
            $Lz_RES = $this->obtener_Lz_joint_coordinates_normalized($joint_coordinates_normalized_RES);
        
            $textoKO = "[-EE-] La longitud de la proyección sobre el eje X de su modelo es $Lx_RES. Debería ser $Lx_SOL. KO";
            $textoOK = "[-II-] La longitud de la proyección sobre el eje X de su modelo es $Lx_RES. OK";
            if (!$this->comparar_con_tolerancia_KOexit($Lx_SOL, $Lx_RES, $Lx_SOL / 100, $f, $textoKO, $textoOK)) {
                return 0;
            }
        
            $textoKO = "[-EE-] La longitud de la proyección sobre el eje Y de su modelo es $Ly_RES. Debería ser $Ly_SOL. KO";
            $textoOK = "[-II-] La longitud de la proyección sobre el eje Y de su modelo es $Ly_RES. OK";
            if (!$this->comparar_con_tolerancia_KOexit($Ly_SOL, $Ly_RES, $Ly_SOL / 100, $f, $textoKO, $textoOK)) {
                return 0;
            }
        
            $textoKO = "[-EE-] La longitud de la proyección sobre el eje Z de su modelo es $Lz_RES. Debería ser $Lz_SOL. KO";
            $textoOK = "[-II-] La longitud de la proyección sobre el eje Z de su modelo es $Lz_RES. OK";
            if (!$this->comparar_con_tolerancia_KOexit($Lz_SOL, $Lz_RES, $Lz_SOL / 100, $f, $textoKO, $textoOK)) {
                return 0;
            }

            $col_x_norm = array_search('Norm_XorR', $this->tokens_joint_coordinates_normalized);
            $col_y_norm = array_search('Norm_Y', $this->tokens_joint_coordinates_normalized);
            $col_z_norm = array_search('Norm_Z', $this->tokens_joint_coordinates_normalized);
            $col_x_orig = array_search('Orig_XorR', $this->tokens_joint_coordinates_normalized);
            $col_y_orig = array_search('Orig_Y', $this->tokens_joint_coordinates_normalized);
            $col_z_orig = array_search('Orig_Z', $this->tokens_joint_coordinates_normalized);

            
            // print_object('$this->tokens_joint_coordinates_normalized');
            // print_object($this->tokens_joint_coordinates_normalized);
            // print_object('$col_x_norm');print_object($col_x_norm);
            // print_object('$col_y_norm');print_object($col_y_norm);
            // print_object('$col_x_orig');print_object($col_x_orig);
            // print_object('$col_y_orig');print_object($col_y_orig);
            
            //---------------------------------------------------------------------------
            //--- Chequeo de las coordenadas de los nudos. Como si hay un nudo mal 
            //--- mal definido varia el cdg, las coordenadas normalizadas varían, por
            //--- lo que solo se da un aviso y se interrumpe cuando el número de errores
            //--- supera maxKO  
            $KOx = 0;
            $KOy = 0;
            $KOz = 0;
            $maxKO = 5;
        
            foreach ($joint_coordinates_normalized_SOL as $index => $joint_coordinate_SOL) {
                $joint_coordinate_RES = $joint_coordinates_normalized_RES[$index];
            
                $x_SOL = $joint_coordinate_SOL[$col_x_norm];
                $x_RES = $joint_coordinate_RES[$col_x_norm];
                $x_RES_ORG = $joint_coordinate_RES[$col_x_orig];
                $y_SOL = $joint_coordinate_SOL[$col_y_norm];
                $y_RES = $joint_coordinate_RES[$col_y_norm];
                $y_RES_ORG = $joint_coordinate_RES[$col_y_orig];
                $z_SOL = $joint_coordinate_SOL[$col_z_norm];
                $z_RES = $joint_coordinate_RES[$col_z_norm];
                $z_RES_ORG = $joint_coordinate_RES[$col_z_orig];
            
                $textoKO = "[-WW-] La coordenada x={$x_RES_ORG} del nudo ({$x_RES_ORG}, {$y_RES_ORG}, {$z_RES_ORG}) de su modelo, probablemente sea incorrecta";
                if (!$this->comparar_con_tolerancia($x_SOL, $x_RES, $Lx_SOL / 100, $f, $textoKO, '')) {
                    $KOx++;
                }
          
                $textoKO = "[-WW-] La coordenada y={$y_RES_ORG} del nudo ({$x_RES_ORG}, {$y_RES_ORG}, {$z_RES_ORG}) de su modelo, probablemente sea incorrecta";
                if (!$this->comparar_con_tolerancia($y_SOL, $y_RES, $Ly_SOL / 100, $f, $textoKO, '')) {
                    $KOy++;
                }
          
                $textoKO = "[-WW-] La coordenada z={$z_RES_ORG} del nudo ({$x_RES_ORG}, {$y_RES_ORG}, {$z_RES_ORG}) de su modelo, probablemente sea incorrecta";
                if (!$this->comparar_con_tolerancia($z_SOL, $z_RES, $Lz_SOL / 100, $f, $textoKO, '')) {
                    $KOz++;
                }

                if ($KOx + $KOy + $KOz >= $maxKO) {
                    $f->write( "[-EE-] Superado el número máximo de errores admitidos. KO");
                    return 0;
                }
            }
        
            if ($KOx > 0) {
                $f->write( "[-WW-] {$KOx} coordenadas X de los nudos de su modelo puede ser incorrecta");
            } else {
                $f->write( "[-II-] Las coordenadas X de los nudos de su modelo son correctas");
            }
      
            if ($KOy > 0) {
                $f->write( "[-WW-] {$KOy} coordenadas Y de los nudos de su modelo puede ser incorrecta");
            } else {
                $f->write( "[-II-] Las coordenadas Y de los nudos de su modelo son correctas");
            }
       
            if ($KOz > 0) {
                $f->write( "[-WW-] {$KOz} coordenadas Z de los nudos de su modelo puede ser incorrecta");
            } else {
                $f->write( "[-II-] Las coordenadas Z de los nudos de su modelo son correctas");
            }

            return 1;
        }


    public function chk_frame_connectivity($frame_connectivity_normalized_SOL, $frame_connectivity_normalized_RES, $f) {
        //--------------------------------------------------------------------------
        //--- Chequeo número de barras. Exist si KO
        $nbar_SOL = count($frame_connectivity_normalized_SOL);
        $nbar_RES = count($frame_connectivity_normalized_RES);
        $textoKO = "[-EE-] El número de barras de su modelo es " . $nbar_RES . ". Debería ser " . $nbar_SOL . ". KO";
        $textoOK = "[-II-] El número de barras de su modelo es " . $nbar_RES . ". OK";
        
        if (!$this->comparar_con_tolerancia_KOexit($nbar_SOL, $nbar_RES, 0, $f, $textoKO, $textoOK)) {
            return 0;
        }

        // Asignación de índices para las columnas
        $col_JointI_x = array_search('JointI_Orig_XorR', $this->tokens_frame_connectivity_normalized);
        $col_JointI_y = array_search('JointI_Orig_Y', $this->tokens_frame_connectivity_normalized);
        $col_JointI_z = array_search('JointI_Orig_Z', $this->tokens_frame_connectivity_normalized);
        
        $col_JointJ_x = array_search('JointJ_Orig_XorR', $this->tokens_frame_connectivity_normalized);
        $col_JointJ_y = array_search('JointJ_Orig_Y', $this->tokens_frame_connectivity_normalized);
        $col_JointJ_z = array_search('JointJ_Orig_Z', $this->tokens_frame_connectivity_normalized);
        
        $col_Lx = array_search('Norm_Lx', $this->tokens_frame_connectivity_normalized);
        $col_Ly = array_search('Norm_Ly', $this->tokens_frame_connectivity_normalized);
        $col_Lz = array_search('Norm_Lz', $this->tokens_frame_connectivity_normalized);
        $col_L = array_search('Norm_L', $this->tokens_frame_connectivity_normalized);

        //--------------------------------------------------------------------------
        //--- Chequeo Lx, Ly, Lz.
        $KOLx = 0;
        $KOLy = 0;
        $KOLz = 0;
        $maxKO = 15;
        
        foreach ($frame_connectivity_normalized_SOL as $index => $frame_connectivity_SOL) {
            $frame_connectivity_RES = $frame_connectivity_normalized_RES[$index];
            
            $Lx_SOL = $frame_connectivity_SOL[$col_Lx];
            $Lx_RES = $frame_connectivity_RES[$col_Lx];
            $Ly_SOL = $frame_connectivity_SOL[$col_Ly];
            $Ly_RES = $frame_connectivity_RES[$col_Ly];
            $Lz_SOL = $frame_connectivity_SOL[$col_Lz];
            $Lz_RES = $frame_connectivity_RES[$col_Lz];
            $L_SOL = $frame_connectivity_SOL[$col_L];
            
            $xi = $frame_connectivity_RES[$col_JointI_x];
            $yi = $frame_connectivity_RES[$col_JointI_y];
            $zi = $frame_connectivity_RES[$col_JointI_z];
            
            $xj = $frame_connectivity_RES[$col_JointJ_x];
            $yj = $frame_connectivity_RES[$col_JointJ_y];
            $zj = $frame_connectivity_RES[$col_JointJ_z];

            // print_object("Lx=".$Lx_SOL.", ". $Lx_RES."Lz=".$Lz_SOL.", ". $Lz_RES."\n");
            $textoKO = "[-WW-] La longitud de la proyección sobre el eje x de la barra definida por los nudos (" . $xi . ", " . $yi . ", " . $zi . ") (" . $xj . ", " . $yj . ", " . $zj . "), probablemente deba ser " . $Lx_SOL;
            if (!$this->comparar_con_tolerancia($Lx_SOL, $Lx_RES, $Lx_SOL / 100, $f, $textoKO, '')) {
                $KOLx++;
            }
    
            $textoKO = "[-WW-] La longitud de la proyección sobre el eje y de la barra definida por los nudos (" . $xi . ", " . $yi . ", " . $zi . ") (" . $xj . ", " . $yj . ", " . $zj . "), probablemente deba ser " . $Ly_SOL;
            if (!$this->comparar_con_tolerancia($Ly_SOL, $Ly_RES, $Ly_SOL / 100, $f, $textoKO, '')) {
                $KOLy++;
            }
                
            $textoKO = "[-WW-] La longitud de la proyección sobre el eje z de la barra definida por los nudos (" . $xi . ", " . $yi . ", " . $zi . ") (" . $xj . ", " . $yj . ", " . $zj . "), probablemente deba ser " . $Lz_SOL;
            if (!$this->comparar_con_tolerancia($Lz_SOL, $Lz_RES, $Lz_SOL / 100, $f, $textoKO, '')) {
                $KOLz++;
            }
    
            if (($KOLx + $KOLy + $KOLz) >= $maxKO) {
                $f->write( "[-EE-] Superado el número máximo de errores admitidos. KO");
                return 0;
            }
        }

        if ($KOLx > 0) {
            $f->write( "[-WW-] " . $KOLx . " longitudes de la proyección sobre el eje X de las barras de su modelo puede ser incorrecta");
        } else {
            $f->write( "[-II-] La longitud de la proyección sobre el eje X de todas las barras de su modelo son correctas");
        }
    
        if ($KOLy > 0) {
            $f->write( "[-WW-] " . $KOLy . " longitudes de la proyección sobre el eje Y de las barras de su modelo puede ser incorrecta");
        } else {
            $f->write( "[-II-] La longitud de la proyección sobre el eje Y de todas las barras de su modelo son correctas");
        }
        
        if ($KOLz > 0) {
            $f->write( "[-WW-] " . $KOLz . " longitudes de la proyección sobre el eje Z de las barras de su modelo puede ser incorrecta");
        } else {
            $f->write( "[-II-] La longitud de la proyección sobre el eje Z de todas las barras de su modelo son correctas");
        }

        return 1;
    }



        public function chk_frame_section_properties($frame_connectivity_normalized_SOL, $frame_connectivity_normalized_RES, $f) {
            $this->printL($frame_connectivity_normalized_SOL,'$frame_connectivity_normalized_SOL-----------');
            $this->printL($frame_connectivity_normalized_RES,'$frame_connectivity_normalized_RES-----------');
            // Obtener los índices de las columnas necesarias
            $col_JointI_x = array_search('JointI_Orig_XorR', $this->tokens_frame_connectivity_normalized);
            $col_JointI_y = array_search('JointI_Orig_Y', $this->tokens_frame_connectivity_normalized);
            $col_JointI_z = array_search('JointI_Orig_Z', $this->tokens_frame_connectivity_normalized);

            $col_JointJ_x = array_search('JointJ_Orig_XorR', $this->tokens_frame_connectivity_normalized);
            $col_JointJ_y = array_search('JointJ_Orig_Y', $this->tokens_frame_connectivity_normalized);
            $col_JointJ_z = array_search('JointJ_Orig_Z', $this->tokens_frame_connectivity_normalized);

            $col_Area = array_search('Area', $this->tokens_frame_connectivity_normalized);
            $col_I33 = array_search('I33', $this->tokens_frame_connectivity_normalized);
            $col_AMod = array_search('AMod', $this->tokens_frame_connectivity_normalized);
            $col_I3Mod = array_search('I3Mod', $this->tokens_frame_connectivity_normalized);
            $col_SectionName0 = array_search('SectionName0', $this->tokens_frame_connectivity_normalized);

            $col_Material = array_search('Material', $this->tokens_frame_connectivity_normalized);

            // Chequeo Area, I.
            $maxKO = 8;
            $KO = 0;

            foreach ($frame_connectivity_normalized_SOL as $index => $frame_connectivity_SOL) {
                $frame_connectivity_RES = $frame_connectivity_normalized_RES[$index];

                $Area_SOL = $frame_connectivity_SOL[$col_Area];
                $Area_RES = $frame_connectivity_RES[$col_Area];
                $I33_SOL = $frame_connectivity_SOL[$col_I33];
                $I33_RES = $frame_connectivity_RES[$col_I33];
                $AMod_SOL = $frame_connectivity_SOL[$col_AMod];
                $AMod_RES = $frame_connectivity_RES[$col_AMod];
                $I3Mod_SOL = $frame_connectivity_SOL[$col_I3Mod];
                $I3Mod_RES = $frame_connectivity_RES[$col_I3Mod];
                $Material_SOL = $frame_connectivity_SOL[$col_Material];
                $Material_RES = $frame_connectivity_RES[$col_Material];
                $SectionName0_RES = $frame_connectivity_RES[$col_SectionName0];

                $xi = $frame_connectivity_RES[$col_JointI_x];
                $yi = $frame_connectivity_RES[$col_JointI_y];
                $zi = $frame_connectivity_RES[$col_JointI_z];

                $xj = $frame_connectivity_RES[$col_JointJ_x];
                $yj = $frame_connectivity_RES[$col_JointJ_y];
                $zj = $frame_connectivity_RES[$col_JointJ_z];

                $textoKO = "[-WW-] En la barra definida por los nudos ($xi, $yi, $zi) ($xj, $yj, $zj) cuya sección es '$SectionName0_RES', su área ($Area_RES), su 'set modifier' ($AMod_RES) o ambos son incorrectos. Su producto debe ser " . ($Area_SOL * $AMod_SOL);

                if (!$this->comparar_con_tolerancia($Area_SOL * $AMod_SOL, $Area_RES * $AMod_RES, $Area_SOL * $AMod_SOL / 100, $f, $textoKO, '')) {
                    //$this->printL($frame_connectivity_SOL,'---solucion---');
                    //$this->printL($frame_connectivity_RES,'---respuesta---');
                    // echo  "error en linea (respuesta)\n";
                    // echo  implode(' ', $frame_connectivity_RES) . "\n";
                    // echo  "Area_RES=$Area_RES\n";
                    // echo  "AMod_RES=$AMod_RES\n";
                    // echo  "solucion\n";
                    // echo  implode(' ', $frame_connectivity_SOL) . "\n";
                    // echo  "Area_SOL=$Area_SOL\n";
                    // echo  "AMod_SOL=$AMod_SOL\n";
                    //print_object($frame_connectivity_RES,'---respuesta---');
                    $KO++;
                }

                $textoKO = "[-WW-] En la barra definida por los nudos ($xi, $yi, $zi) ($xj, $yj, $zj) cuya sección es '$SectionName0_RES', su inercia($I33_RES), su 'set modifier' ($I3Mod_RES) o ambos son incorrectos. Su producto debe ser " . ($I33_SOL * $I3Mod_SOL);
                if (!$this->comparar_con_tolerancia($I33_SOL * $I3Mod_SOL, $I33_RES * $I3Mod_RES, $I33_SOL * $I3Mod_SOL / 100, $f, $textoKO, '')) {
                    $KO++;
                }

                if ($Material_SOL != $Material_RES) {
                    $f->write( "[-WW-] En la barra definida por los nudos ($xi, $yi, $zi) ($xj, $yj, $zj) cuya sección es '$SectionName0_RES', su material es ($Material_RES). Debería ser ($Material_SOL).");
                    $KO++;
                }

                if ($KO >= $maxKO) {
                    $f->write( "[-EE-] Superado el número máximo de errores admitidos. KO");
                    return 0;
                }
            }

            if ($KO > 0) {
                $f->write( "[-EE-] $KO propiedades de las secciones de su modelo son incorrectas. KO");
                return 0;
            }

            $f->write( "[-II-] Las propiedades de las secciones de su modelo son correctas. OK");
            return 1;
        }

        public function chk_DOF($DOF_SOL, $DOF_RES, $f) {
            $KO = 0;

            // Recorre los 6 primeros grados de libertad
            for ($i = 0; $i < 6; $i++) {
                if ($DOF_SOL[0][$i] != $DOF_RES[0][$i]) {
                    $KO++;
                    if ($DOF_SOL[0][$i] === 'Yes') {
                        $f->write( "[-WW-] El grado de libertad " . $this->tokens_DOF[$i] . " de su modelo está desactivado. Probablemente deba activarlo");
                    }
                    if ($DOF_SOL[0][$i] === 'No') {
                        $f->write( "[-WW-] El grado de libertad " . $this->tokens_DOF[$i] . " de su modelo está activado. Probablemente deba desactivarlo");
                    }
                }
            }

            if ($KO > 0) {
                $f->write( "[-WW-] Revise los grados de libertad activos de su modelo");
            } else {
                $f->write( "[-II-] Los grados de libertad activos de su modelo son correctos. OK");
            }

            return 1;
        }

        /**
         * Chequea las asignaciones de restricciones normalizadas en comparación con los valores esperados.
         *
         * @param array $restraint_assignments_normalized_SOL Asignaciones de restricciones normalizadas del modelo SOL.
         * @param array $restraint_assignments_normalized_RES Asignaciones de restricciones normalizadas del modelo RES.
         * @param array $DOF_SOL Grados de libertad del modelo SOL.
         * @param resource $f Recurso de archivo para escribir mensajes.
         * @return int 1 si todo está correcto, 0 si hay errores.
         */
        public function chk_restraint_assignments($restraint_assignments_normalized_SOL, $restraint_assignments_normalized_RES, $DOF_SOL, $f) {
            //--------------------------------------------------------------------------
            //--- Chequeo número de nudos coaccionados. Exist si KO
            $nrst_SOL = count($restraint_assignments_normalized_SOL);
            $nrst_RES = count($restraint_assignments_normalized_RES);
            $textoKO = "[-EE-] El número de apoyos de su modelo es " . $nrst_RES . ". Debería ser " . $nrst_SOL . ". KO";
            $textoOK = "[-II-] El número de apoyos de su modelo es " . $nrst_RES . ". OK";

            if (!$this->comparar_con_tolerancia_KOexit($nrst_SOL, $nrst_RES, 0, $f, $textoKO, $textoOK)) {
                return 0;
            }

            //--------------------------------------------------------------------------
            //--- Se chequea que los nudos coaccionados son los correctos
            $col_Norm_Joint = array_search('Norm_Joint', $this->tokens_restraint_assignments_normalized);
            $col_JointI_x = array_search('Orig_XorR', $this->tokens_restraint_assignments_normalized);
            $col_JointI_y = array_search('Orig_Y', $this->tokens_restraint_assignments_normalized);
            $col_JointI_z = array_search('Orig_Z', $this->tokens_restraint_assignments_normalized);

            $KO = 0;
            foreach ($restraint_assignments_normalized_SOL as $index => $r_a_SOL) {
                $r_a_RES = $restraint_assignments_normalized_RES[$index];

                $Norm_Joint_SOL = $r_a_SOL[$col_Norm_Joint];
                $Norm_Joint_RES = $r_a_RES[$col_Norm_Joint];
                $xi = $r_a_RES[$col_JointI_x];
                $yi = $r_a_RES[$col_JointI_y];
                $zi = $r_a_RES[$col_JointI_z];

                $textoKO = "[-WW-] El nudo de su modelo cuyas coordenadas son (" . $xi . ", " . $yi . ", " . $zi . ") tiene algún movimiento coaccionado. No debería tenerlo";
                if (!$this->comparar_con_tolerancia($Norm_Joint_SOL, $Norm_Joint_RES, 0, $f, $textoKO, '')) {
                    $KO++;
                }
            }

            if ($KO > 0) {
                $f->write( "[-EE-] " . $KO . " apoyos de su modelo no están en el nudo adecuado. KO");
                return 0;
            }

            //--------------------------------------------------------------------------
            //--- Se chequean las restricciones de los nudos coaccionados
            $KO = 0;
            foreach ($restraint_assignments_normalized_SOL as $index => $r_a_SOL) {
                $r_a_RES = $restraint_assignments_normalized_RES[$index];

                $xi = $r_a_RES[$col_JointI_x];
                $yi = $r_a_RES[$col_JointI_y];
                $zi = $r_a_RES[$col_JointI_z];

                for ($i = 0; $i < 6; $i++) {
                    if ($DOF_SOL[0][$i] == 'Yes') {
                        if ($r_a_SOL[$i + 1] != $r_a_RES[$i + 1]) {
                            $KO++;
                            if ($r_a_SOL[$i + 1] == 'Yes') {
                                $f->write( "[-WW-] El desplazamiento " . $this->tokens_restraint_assignments[$i + 1] . " del nudo de su modelo cuyas coordenadas son (" . $xi . ", " . $yi . ", " . $zi . ") NO está coaccionado. Debería estarlo");
                            } else {
                                $f->write( "[-WW-] El desplazamiento " . $this->tokens_restraint_assignments[$i + 1] . " del nudo de su modelo cuyas coordenadas son (" . $xi . ", " . $yi . ", " . $zi . ") está coaccionado. NO debería estarlo");
                            }
                        }
                    }
                }
            }

            if ($KO > 0) {
                $f->write( "[-EE-] " . $KO . " vínculos de su modelo son incorrectos. KO");
                return 0;
            }

            $f->write( "[-II-] Los apoyos de su modelo son correctos. OK");
            return 1;
        }


        //-------------------------------------------------------------------------------
        // Se comparan los patrones de carga LoadPat_SOL y  LoadPat_RES
        // En la función chk_joint_loads se elige los patrones que pueden compararse
        // Actualmente aquellos que son estáticamente equivalentes
        public function chk_joint_load($joint_loads_normalized_SOL, $joint_loads_normalized_RES, $LoadPat_SOL, $LoadPat_RES, $col_LoadPat, $f) {
            $col_F1 = array_search('Norm_F1', $this->tokens_joint_loads_force_normalized);
            $col_F2 = array_search('Norm_F2', $this->tokens_joint_loads_force_normalized);
            $col_F3 = array_search('Norm_F3', $this->tokens_joint_loads_force_normalized);

            $col_M1 = array_search('Norm_M1', $this->tokens_joint_loads_force_normalized);
            $col_M2 = array_search('Norm_M2', $this->tokens_joint_loads_force_normalized);
            $col_M3 = array_search('Norm_M3', $this->tokens_joint_loads_force_normalized);

            $col_x_orig = array_search('Orig_XorR', $this->tokens_joint_loads_force_normalized);
            $col_y_orig = array_search('Orig_Y', $this->tokens_joint_loads_force_normalized);
            $col_z_orig = array_search('Orig_Z', $this->tokens_joint_loads_force_normalized);

            $col_Norm_Joint = array_search('Norm_Joint', $this->tokens_joint_loads_force_normalized);

            // Se seleccionan las filas cuyos LoadPat sean los que se quieren comparar
            $joint_loads_normalized_SOL_LoadPat = array_filter($joint_loads_normalized_SOL, function($fila) use ($col_LoadPat, $LoadPat_SOL) {
                return $fila[$col_LoadPat] === $LoadPat_SOL;
            });
            $joint_loads_normalized_RES_LoadPat = array_filter($joint_loads_normalized_RES, function($fila) use ($col_LoadPat, $LoadPat_RES) {
                return $fila[$col_LoadPat] === $LoadPat_RES;
            });

            // Se comprueba el número de cargas del patrón de cargas
            $n_RES = count($joint_loads_normalized_RES_LoadPat);
            $n_SOL = count($joint_loads_normalized_SOL_LoadPat);
            $textoKO = "[-EE-]     El número de cargas del patrón de cargas " . $LoadPat_RES . " de su modelo es " . $n_RES . ". Debería ser " . $n_SOL . ". KO";
            if (!$this->comparar_con_tolerancia($n_SOL, $n_RES, 0, $f, $textoKO, '')) {
                return 1;
            }

            // Se recorren las filas comparando las acciones incluidas
            $KO = 0;
            foreach (array_map(null, $joint_loads_normalized_SOL_LoadPat, $joint_loads_normalized_RES_LoadPat) as [$I_joint_loads_normalized_SOL_LoadPat, $I_joint_loads_normalized_RES_LoadPat]) {
                $F_M_SOL = [
                    floatval($I_joint_loads_normalized_SOL_LoadPat[$col_F1]),
                    floatval($I_joint_loads_normalized_SOL_LoadPat[$col_F2]),
                    floatval($I_joint_loads_normalized_SOL_LoadPat[$col_F3]),
                    floatval($I_joint_loads_normalized_SOL_LoadPat[$col_M1]),
                    floatval($I_joint_loads_normalized_SOL_LoadPat[$col_M2]),
                    floatval($I_joint_loads_normalized_SOL_LoadPat[$col_M3]),
                    $I_joint_loads_normalized_SOL_LoadPat[$col_Norm_Joint]
                ];
                $F_M_RES = [
                    floatval($I_joint_loads_normalized_RES_LoadPat[$col_F1]),
                    floatval($I_joint_loads_normalized_RES_LoadPat[$col_F2]),
                    floatval($I_joint_loads_normalized_RES_LoadPat[$col_F3]),
                    floatval($I_joint_loads_normalized_RES_LoadPat[$col_M1]),
                    floatval($I_joint_loads_normalized_RES_LoadPat[$col_M2]),
                    floatval($I_joint_loads_normalized_RES_LoadPat[$col_M3]),
                    $I_joint_loads_normalized_RES_LoadPat[$col_Norm_Joint]
                ];

                $excMax = 0.05; // Excentricidad máxima en m (5cm)
                $tol = 1 / 100;
                $F_M_tol = [
                    $F_M_SOL[0] * $tol,
                    $F_M_SOL[1] * $tol,
                    $F_M_SOL[2] * $tol,
                    max(abs($F_M_SOL[3]), (abs($F_M_SOL[1]) + abs($F_M_SOL[2])) * $excMax) * $tol,
                    max(abs($F_M_SOL[4]), (abs($F_M_SOL[2]) + abs($F_M_SOL[0])) * $excMax) * $tol,
                    max(abs($F_M_SOL[5]), (abs($F_M_SOL[0]) + abs($F_M_SOL[1])) * $excMax) * $tol,
                    0
                ];

                if (!$this->comparar_listas_con_tolerancia2($F_M_SOL, $F_M_RES, $F_M_tol)) {
                    $KO++;
                    $xi = $I_joint_loads_normalized_RES_LoadPat[$col_x_orig];
                    $yi = $I_joint_loads_normalized_RES_LoadPat[$col_y_orig];
                    $zi = $I_joint_loads_normalized_RES_LoadPat[$col_z_orig];
                    $F_M_RES = array_slice($F_M_RES, 0, 5);
                    $f->write( "[-WW-]     Las componentes de la carga " . implode(", ", $F_M_RES) . " o el nudo sobre el cual la ha situado (" . $xi . ", " . $yi . ", " . $zi . ") son incorrectos\n");
                }
            }

            if ($KO === 0) {
                $f->write( "[-II-]    Las cargas del patron " . $LoadPat_RES . " son correctas. OK\n");
                return 0;
            } else {
                $f->write( "[-WW-]     " . $KO . " cargas sobre los nudos del patron de cargas " . $LoadPat_RES . " de su modelo son incorrectas (aunque la resultante de todas ellas queda dentro de los margenes de error)\n");
                return 1;
            }
        }

        //-------------------------------------------------------------------------------
        public function chk_joint_loads($joint_loads_normalized_SOL, $joint_loads_normalized_RES, $DOF_SOL, $f) {
            $KO_LP = 0;
            $col_LoadPat = array_search('LoadPat', $this->tokens_joint_loads_force_normalized);

            // Se obtienen dos listas:
            // una lista con la resultante de las distintas hipotesis del modelo
            // y otra con el nombre del LoadPat de cada una de las resultantes
            list($Resul_joint_loads_SOL, $LoadPat_joint_loads_SOL) = $this->obtener_resultante_joint_loads_forces($joint_loads_normalized_SOL);
            list($Resul_joint_loads_RES, $LoadPat_joint_loads_RES) = $this->obtener_resultante_joint_loads_forces($joint_loads_normalized_RES);

            $Resul_RES_chequeado = [];
            if (count($LoadPat_joint_loads_SOL) === 0) {
                return -1;
            }

            $f->write( "[-II-] Se comprueban " . count($LoadPat_joint_loads_SOL) . " patrones de carga. Las componentes de sus resultantes de fuerzas y momentos son (el nombre es orientativo):");

            // Se imprime la resultante de cada LoadPat
            foreach ($Resul_joint_loads_SOL as $index => $I_Resul) {
                //$f->write( "[-II-]    " . implode(", ", $I_Resul));
                //$f->write( " (" . $LoadPat_joint_loads_SOL[$index] . ")");
                $f->write( "[-II-]    " . implode(", ", $I_Resul). 
                           " (" . $LoadPat_joint_loads_SOL[$index] . ")");
                //$f->write( "\n");
            }

            foreach ($LoadPat_joint_loads_RES as $I_Resul) {
                $Resul_RES_chequeado[] = false;
            }

            // Para cada LoadPat de SOL se busca uno en RES cuya resultante sea igual
            foreach ($Resul_joint_loads_SOL as $index_SOL => $Resul_SOL) {
                $index_SOL_chequeado = false;
                foreach ($Resul_joint_loads_RES as $index_RES => $Resul_RES) {
                    $excMax = 0.05; // Maxima excentricidad en m (5cm)
                    $tol = 2.5 / 100;
                    $Resul_tol = [
                        $Resul_SOL[0] * $tol,
                        $Resul_SOL[1] * $tol,
                        $Resul_SOL[2] * $tol,
                        max(abs($Resul_SOL[3]), (abs($Resul_SOL[1]) + abs($Resul_SOL[2])) * $excMax) * $tol,
                        max(abs($Resul_SOL[4]), (abs($Resul_SOL[2]) + abs($Resul_SOL[0])) * $excMax) * $tol,
                        max(abs($Resul_SOL[5]), (abs($Resul_SOL[0]) + abs($Resul_SOL[1])) * $excMax) * $tol
                    ];

                    if ($this->comparar_listas_con_tolerancia2($Resul_SOL, $Resul_RES, $Resul_tol)) {
                        // Un patron de cargas de la respuesta tiene la misma resultante
                        // que uno de la solución.
                        // Se ajustan unos estados...
                        $Resul_RES_chequeado[$index_RES] = true;
                        $index_SOL_chequeado = true;
                        $LoadPat_SOL = $LoadPat_joint_loads_SOL[$index_SOL];
                        $LoadPat_RES = $LoadPat_joint_loads_RES[$index_RES];

                        // Se imprime un mensaje...
                        $f->write( "[-II-] Se comprueba el patron de cargas de su modelo " . $LoadPat_RES . ", cuya resultante es " . implode(", ", $Resul_RES) . "\n");
                    
                        // Y se comprueban las cargas del patron nudo a nudo
                        $KO_LP += $this->chk_joint_load($joint_loads_normalized_SOL, $joint_loads_normalized_RES, $LoadPat_SOL, $LoadPat_RES, $col_LoadPat, $f);
                        break;
                    }
                }
                if (!$index_SOL_chequeado) {
                    $f->write( "[-EE-] El patron de carga cuya resultante es " . implode(", ", $Resul_SOL) . ", no está definido en su modelo. KO\n");
                    $KO_LP++;
                }
            }

            foreach ($Resul_RES_chequeado as $i => $I_OK) {
                if (!$I_OK) {
                    $f->write( "[-WW-] El patron de carga " . $LoadPat_joint_loads_RES[$i] . " de su modelo, cuya resultante es " . implode(", ", $Resul_joint_loads_RES[$i]) . ", no se ha comprobado\n");
                }
            }

            if ($KO_LP > 0) {
                $f->write( "[-EE-] " . $KO_LP . " patrones de carga de su modelo son incorrectos\n");
                return 0;
            } else {
                $f->write( "[-II-] Los patrones de carga de su modelo son correctos.\n");
            }
            return 1;
        }




        public function chk_frame_loads_distributed($frame_loads_distributed_normalized_SOL,
                                                $frame_loads_distributed_normalized_RES,
                                                $tokens_frame_loads_distributed_normalized,
                                                $obtener_resultante_frame_loads_distributed, // Función pasada como parámetro
                                                $DOF_SOL, $f, $fuerzas="fuerzas") {
        
        $KO_LP = 0;
        $col_LoadPat = array_search('LoadPat', $tokens_frame_loads_distributed_normalized);

        // Se obtienen dos listas:
        // una lista con la resultante de las distintas hipotesis del modelo
        // y otra con el nombre del LoadPat de cada una de las resultantes
        list($Resul_frame_loads_distributed_SOL, $LoadPat_frame_loads_distributed_SOL) = $obtener_resultante_frame_loads_distributed($frame_loads_distributed_normalized_SOL, $tokens_frame_loads_distributed_normalized);
        //$this->printL($frame_loads_distributed_normalized_SOL,'$frame_loads_distributed_normalized_SOL');
        //print_r($tokens_frame_loads_distributed_normalized);
        //$this->printL($Resul_frame_loads_distributed_SOL,'$Resul_frame_loads_distributed_SOL');
        //exit(1);
        list($Resul_frame_loads_distributed_RES, $LoadPat_frame_loads_distributed_RES) = $obtener_resultante_frame_loads_distributed($frame_loads_distributed_normalized_RES, $tokens_frame_loads_distributed_normalized);

        if (count($LoadPat_frame_loads_distributed_SOL) == 0) {
            return -1;
        }

        $Resul_RES_chequeado = [];
        $f->write( "[-II-] Se comprueban " . count($LoadPat_frame_loads_distributed_SOL) . " patrones de carga. Las componentes de sus resultantes de ".$fuerzas." son (el nombre es orientativo):");

        // Se imprime la resultante de cada LoadPat
        foreach ($Resul_frame_loads_distributed_SOL as $I_Resul) {
            $LoadPat_SOL = current($LoadPat_frame_loads_distributed_SOL);
            $f->write( "[-II-]    " . implode(", ", $I_Resul) . " (" . $LoadPat_SOL . ")");
            next($LoadPat_frame_loads_distributed_SOL);
        }

        foreach ($LoadPat_frame_loads_distributed_RES as $I_Resul) {
            $Resul_RES_chequeado[] = false;
        }

        // Para cada LoadPat de SOL se busca uno en RES cuya resultante sea igual
        $index_SOL = 0;
        foreach ($Resul_frame_loads_distributed_SOL as $Resul_SOL) {
            $index_SOL_chequeado = false;
            $index_RES = 0;
            $excMax = 0.05; // Maxima excentricidad en m (5cm)
            $tol = 2.5 / 100;

            $Resul_tol = [
                $Resul_SOL[0] * $tol,
                $Resul_SOL[1] * $tol,
                $Resul_SOL[2] * $tol,
                max(abs($Resul_SOL[3]), (abs($Resul_SOL[1]) + abs($Resul_SOL[2])) * $excMax) * $tol,
                max(abs($Resul_SOL[4]), (abs($Resul_SOL[2]) + abs($Resul_SOL[0])) * $excMax) * $tol,
                max(abs($Resul_SOL[5]), (abs($Resul_SOL[0]) + abs($Resul_SOL[1])) * $excMax) * $tol
            ];

            foreach ($Resul_frame_loads_distributed_RES as $Resul_RES) {
                if ($this->comparar_listas_con_tolerancia2($Resul_SOL, $Resul_RES, $Resul_tol)) {
                    // Un patron de cargas de la respuesta tiene la misma resultante
                    // que uno de la solución.
                    $Resul_RES_chequeado[$index_RES] = true;
                    $index_SOL_chequeado = true;
                    $LoadPat_SOL = $LoadPat_frame_loads_distributed_SOL[$index_SOL];
                    $LoadPat_RES = $LoadPat_frame_loads_distributed_RES[$index_RES];

                    // Se imprime un mensaje...
                    $f->write( "[-II-] Se comprueba el patron de cargas de su modelo " . $LoadPat_RES . ", cuya resultante es " . implode(", ", $Resul_RES) . "\n");

                    // Y se comprueban las cargas del patron barra a barra
                    $KO_LP += $this->chk_frame_load_distributed($frame_loads_distributed_normalized_SOL,
                                                                $frame_loads_distributed_normalized_RES,
                                                                $tokens_frame_loads_distributed_normalized,
                                                                $LoadPat_SOL,
                                                                $LoadPat_RES,
                                                                $col_LoadPat, $f);
                    break;
                }
                $index_RES++;
            }

            if (!$index_SOL_chequeado) {
                $f->write( "[-EE-] El patron de carga cuya resultante es " . implode(", ", $Resul_SOL) . ", no está definido en su modelo. KO\n");
                $KO_LP++;
            }
            $index_SOL++;
        }

        $i = 0;
        foreach ($Resul_RES_chequeado as $I_OK) {
            if ($I_OK == false) {
                $I_Resul = $Resul_frame_loads_distributed_RES[$i];
                $f->write( "[-WW-] El patron de carga " . $LoadPat_frame_loads_distributed_RES[$i] . " de su modelo, cuya resultante es " . implode(", ", $I_Resul) . ", no se ha comprobado\n");
            }
            $i++;
        }

        if ($KO_LP > 0) {
            $f->write( "[-EE-] " . $KO_LP . " patrones de carga de su modelo son incorrectos\n");
            return 0;
        } else {
            $f->write( "[-II-] Los patrones de carga de su modelo son correctos.\n");
        }
        
        return 1;
    }


    // Se comparan los patrones de carga LoadPat_SOL y  LoadPat_RES
    // En la función chk_frame_loads_disturbed se elige los patrones que pueden compararse
    // Actualmente aquellos que son estáticamente equivalentes
    // La función se puede utilizar también para frame_loads_point y
    // frame_moments_point
    public function chk_frame_load_distributed($frame_loads_distributed_normalized_SOL,
                                               $frame_loads_distributed_normalized_RES,
                                               $tokens_frame_loads_distributed_normalized,
                                               $LoadPat_SOL,
                                               $LoadPat_RES,
                                               $col_LoadPat, $f) {

        $col_Norm_Frame = array_search('Norm_Frame', $tokens_frame_loads_distributed_normalized);
        $col_Norm_R = array_search('Norm_R', $tokens_frame_loads_distributed_normalized);
        $col_Norm_e = array_search('Norm_e', $tokens_frame_loads_distributed_normalized);

        $col_JointI_Orig_XorR = array_search('JointI_Orig_XorR', $tokens_frame_loads_distributed_normalized);
        $col_JointI_Orig_Y = array_search('JointI_Orig_Y', $tokens_frame_loads_distributed_normalized);
        $col_JointI_Orig_Z = array_search('JointI_Orig_Z', $tokens_frame_loads_distributed_normalized);
        $col_JointJ_Orig_XorR = array_search('JointJ_Orig_XorR', $tokens_frame_loads_distributed_normalized);
        $col_JointJ_Orig_Y = array_search('JointJ_Orig_Y', $tokens_frame_loads_distributed_normalized);
        $col_JointJ_Orig_Z = array_search('JointJ_Orig_Z', $tokens_frame_loads_distributed_normalized);

        $col_IxJ = array_search('Norm_IxJ', $tokens_frame_loads_distributed_normalized);
        $col_Dir = array_search('Dir', $tokens_frame_loads_distributed_normalized);

        // Se seleccionan las filas cuyos LoadPat sean los que se quieren comparar
        $frame_loads_distributed_normalized_SOL_LoadPat = array_filter($frame_loads_distributed_normalized_SOL, function($fila) use ($col_LoadPat, $LoadPat_SOL) {
            return $fila[$col_LoadPat] === $LoadPat_SOL;
        });

        $frame_loads_distributed_normalized_RES_LoadPat = array_filter($frame_loads_distributed_normalized_RES, function($fila) use ($col_LoadPat, $LoadPat_RES) {
            return $fila[$col_LoadPat] === $LoadPat_RES;
        });

        // Se comprueba el número de barras cargadas del patrón de cargas
        $frames_SOL = array_unique(array_column($frame_loads_distributed_normalized_SOL_LoadPat, $col_Norm_Frame));
        $frames_RES = array_unique(array_column($frame_loads_distributed_normalized_RES_LoadPat, $col_Norm_Frame));

        $n_SOL = count($frames_SOL);
        $n_RES = count($frames_RES);
        $textoKO = "[-EE-]     El número de barras cargadas del patrón de cargas " . $LoadPat_RES . " de su modelo es " . $n_RES . ". Debería ser " . $n_SOL . ". KO";

        if (!$this->comparar_con_tolerancia($n_SOL, $n_RES, 0, $f, $textoKO, '')) {
            return 1;
        }

        // Se recorren las barras comparando las acciones incluidas en cada una
        $KO = 0;
        $all_frames = array_unique(array_merge($frames_SOL, $frames_RES));

        foreach ($all_frames as $Iframe) {
            // Rx   Ry   Rz   ex   ey   ez
            $Resul = array(0.0, 0.0, 0.0, 0.0, 0.0, 0.0);

            foreach ($frame_loads_distributed_normalized_SOL_LoadPat as $I_load) {
                if ($I_load[$col_Norm_Frame] == $Iframe) {
                    $Dir = $I_load[$col_Dir];
                    $xi = $I_load[$col_JointI_Orig_XorR];
                    $yi = $I_load[$col_JointI_Orig_Y];
                    $zi = $I_load[$col_JointI_Orig_Z];
                    $xj = $I_load[$col_JointJ_Orig_XorR];
                    $yj = $I_load[$col_JointJ_Orig_Y];
                    $zj = $I_load[$col_JointJ_Orig_Z];
                    $IxJ = $I_load[$col_IxJ]; // Si 1 se ha reorientado la barra
                    $norm_R = $I_load[$col_Norm_R];
                    $norm_e = $I_load[$col_Norm_e];

                    if ($IxJ) {
                        $norm_e = 1 - $norm_e;
                    }

                    if (in_array($Dir, ['"GravProj"', 'Gravity', '"ZProj"', 'Z'])) {
                        $new_res = $Resul[2] + $norm_R;
                        if ($new_res != 0) {
                            $Resul[5] = ($Resul[2] * $Resul[5] + $norm_R * $norm_e) / $new_res;
                            $Resul[2] = $new_res;
                        } else {
                            $Resul[2] = 0;
                            $Resul[5] = 0;
                        }
                    }

                    if (in_array($Dir, ['"XProj"', 'X'])) {
                        $new_res = $Resul[0] + $norm_R;
                        if ($new_res != 0) {
                            $Resul[3] = ($Resul[0] * $Resul[3] + $norm_R * $norm_e) / $new_res;
                            $Resul[0] = $new_res;
                        } else {
                            $Resul[0] = 0;
                            $Resul[3] = 0;
                        }
                    }

                    if (in_array($Dir, ['"YProj"', 'Y'])) {
                        $new_res = $Resul[1] + $norm_R;
                        if ($new_res != 0) {
                            $Resul[4] = ($Resul[1] * $Resul[4] + $norm_R * $norm_e) / $new_res;
                            $Resul[1] = $new_res;
                        } else {
                            $Resul[1] = 0;
                            $Resul[4] = 0;
                        }
                    }
                }
            }

            $Resul_SOL = $Resul;

            // Rx   Ry   Rz   ex   ey   ez
            $Resul = array(0.0, 0.0, 0.0, 0.0, 0.0, 0.0);

            foreach ($frame_loads_distributed_normalized_RES_LoadPat as $I_load) {
                // echo  "-------I_load--------\n";
                // print_r($I_load);
                // echo  "-------Iframe--------\n";
                // print_r($Iframe);
                // echo  "-------I_load[col_Norm_Frame]--------\n";
                // print_r($I_load[$col_Norm_Frame]);
                if ($I_load[$col_Norm_Frame] == $Iframe) {
                    $Dir = $I_load[$col_Dir];
                    $norm_R = $I_load[$col_Norm_R];
                    $norm_e = $I_load[$col_Norm_e];
                    $xi = $I_load[$col_JointI_Orig_XorR];
                    $yi = $I_load[$col_JointI_Orig_Y];
                    $zi = $I_load[$col_JointI_Orig_Z];
                    $xj = $I_load[$col_JointJ_Orig_XorR];
                    $yj = $I_load[$col_JointJ_Orig_Y];
                    $zj = $I_load[$col_JointJ_Orig_Z];
                    $IxJ = $I_load[$col_IxJ]; // Si 1 se ha reorientado la barra

                    if ($IxJ) {
                        $norm_e = 1 - $norm_e;
                    }
                    //echo "dir=$dir, Resul[2]=$Resul[2], norm_R=$norm_R\n";
                    if (in_array($Dir, ['"GravProj"', 'Gravity', '"ZProj"', 'Z'])) {
                        $new_res = $Resul[2] + $norm_R;
                        if ($new_res != 0) {
                            $Resul[5] = ($Resul[2] * $Resul[5] + $norm_R * $norm_e) / $new_res;
                            $Resul[2] = $new_res;
                        } else {
                            $Resul[2] = 0;
                            $Resul[5] = 0;
                        }
                    }

                    if (in_array($Dir, ['"XProj"', 'X'])) {
                        $new_res = $Resul[0] + $norm_R;
                        if ($new_res != 0) {
                            $Resul[3] = ($Resul[0] * $Resul[3] + $norm_R * $norm_e) / $new_res;
                            $Resul[0] = $new_res;
                        } else {
                            $Resul[0] = 0;
                            $Resul[3] = 0;
                        }
                    }

                    if (in_array($Dir, ['"YProj"', 'Y'])) {
                        $new_res = $Resul[1] + $norm_R;
                        if ($new_res != 0) {
                            $Resul[4] = ($Resul[1] * $Resul[4] + $norm_R * $norm_e) / $new_res;
                            $Resul[1] = $new_res;
                        } else {
                            $Resul[1] = 0;
                            $Resul[4] = 0;
                        }
                    }
                }
            }

            $Resul_RES = $Resul;

            $excMax = 0.05; // Máxima excentricidad en m (5cm)
            $tol = 2.5 / 100;

            $Resul_tol = [
                $Resul_SOL[0] * $tol,
                $Resul_SOL[1] * $tol,
                $Resul_SOL[2] * $tol,
                max(abs($Resul_SOL[3]), (abs($Resul_SOL[1]) + abs($Resul_SOL[2])) * $excMax) * $tol,
                max(abs($Resul_SOL[4]), (abs($Resul_SOL[2]) + abs($Resul_SOL[0])) * $excMax) * $tol,
                max(abs($Resul_SOL[5]), (abs($Resul_SOL[0]) + abs($Resul_SOL[1])) * $excMax) * $tol
            ];
            // print_r($Resul_SOL);
            // print_r($Resul_RES);
            // print_r($Resul_tol);
            //echo "Salgo";
            //exit(1);
            if (!$this->comparar_listas_con_tolerancia2($Resul_SOL, $Resul_RES, $Resul_tol)) {
                $KO++;

                $f->write( "[-WW-]    En la barra definida por los nudos ($xi, $yi, $zi) ($xj, $yj, $zj) la resultante de las cargas o su posición son incorrectas para su hipótesis $LoadPat_RES\n");
                //$f->write( "[-WW-]    En la barra definida por los nudos ($xi, $yi, $zi) ($xj, $yj, $zj) la resultante de las cargas ( $Resul_RES[0], $Resul_RES[1], $Resul_RES[2]-> $Resul_SOL[0], $Resul_SOL[1], $Resul_SOL[2])o su posición son incorrectas para su hipótesis $LoadPat_RES\n");
            }
        }

        if ($KO == 0) {
            $f->write( "[-II-]    Las cargas distribuidas sobre las barras del patrón $LoadPat_RES son correctas. OK\n");
            return 0;
        } else {
            $f->write( "[-WW-]    $KO cargas sobre las barras del patrón de cargas $LoadPat_RES de su modelo son incorrectas (aunque la resultante de todas ellas queda dentro de los márgenes de error)\n");
            return 1;
        }
    }
        
    }
}
