<?php

if (!trait_exists('chk_all')) {
    trait chk_all {


        function chk_all($archivoSOL, $archivoRES, $f, $EscL = '1', $EscF = '1', $pythonSemanal = '0') {
            $calificacion = 0;

            
            
            list($unidadesF_SOL, $unidadesL_SOL) = self::obtener_unidades($archivoSOL);
            list($unidadesF_RES, $unidadesL_RES) = self::obtener_unidades($archivoRES);
            
            if ($unidadesF_RES === 0 || $unidadesL_RES === 0) {
                $f->write("Error al leer su solución. ");
                $f->write("Compruebe que es un fichero  \$2k\n");
                return 0;
            }
            // Output formatting lines replaced by fwrite
            $f->write("--------------------------------------------------------------\n");
            $f->write("CHEQUEO GEOMETRÍA\n");
            $f->write("--------------------------------------------------------------\n");
            $patron = '/Joint=.*Xor/';

            $joint_coordinates_SOL = self::buscar_patron_SAP_tokens($archivoSOL, $patron, $this->tokens_joint_coordinates);
            $joint_coordinates_normalized_SOL = self::normalizar_joint_coordinates($joint_coordinates_SOL, $unidadesL_SOL, $EscL);

            //print_object($joint_coordinates_normalized_SOL,"joint_coordinates_normalized_SOL");
            $joint_coordinates_RES = self::buscar_patron_SAP_tokens($archivoRES, $patron, $this->tokens_joint_coordinates);
            $joint_coordinates_normalized_RES = self::normalizar_joint_coordinates($joint_coordinates_RES, $unidadesL_RES);

            if (!$this->chk_joint_coordinates($joint_coordinates_normalized_SOL, $joint_coordinates_normalized_RES, $f)) {
                return $calificacion;
            }
            $calificacion++;
    
            // Check topology
            $f->write("--------------------------------------------------------------\n");
            $f->write("CHEQUEO TOPOLOGÍA\n");
            $f->write("--------------------------------------------------------------\n");
            $patron = '/Frame=.*JointI/';
            $frame_connectivity_SOL = self::buscar_patron_SAP_tokens($archivoSOL, $patron, $this->tokens_frame_connectivity);
            $frame_connectivity_RES = self::buscar_patron_SAP_tokens($archivoRES, $patron, $this->tokens_frame_connectivity);

            $frame_connectivity_normalized_SOL = self::normalizar_frame_connectivity($frame_connectivity_SOL, $joint_coordinates_normalized_SOL);
            $frame_connectivity_normalized_RES = self::normalizar_frame_connectivity($frame_connectivity_RES, $joint_coordinates_normalized_RES);

            if (!$this->chk_frame_connectivity($frame_connectivity_normalized_SOL, $frame_connectivity_normalized_RES, $f)) {
                return $calificacion;
            }
            $calificacion++;

            // Check sections
            $f->write("--------------------------------------------------------------\n");
            $f->write("CHEQUEO SECCIONES\n");
            $f->write("--------------------------------------------------------------\n");
            $patron = '/SectionName=.*Material/';

            $section_properties_SOL = self::buscar_patron_SAP_tokens($archivoSOL, $patron, $this->tokens_section_properties);
            $section_properties_RES = self::buscar_patron_SAP_tokens($archivoRES, $patron, $this->tokens_section_properties);

            $section_properties_normalized_SOL = self::normalizar_section_properties($section_properties_SOL, $unidadesL_SOL);
            $section_properties_normalized_RES = self::normalizar_section_properties($section_properties_RES, $unidadesL_RES);

            $patron = '/Frame=.*AnalSect=/';
            $frame_section_assignments_SOL = self::buscar_patron_SAP_tokens($archivoSOL, $patron, $this->tokens_frame_section_assignments);
            $frame_section_assignments_RES = self::buscar_patron_SAP_tokens($archivoRES, $patron, $this->tokens_frame_section_assignments);

            $this->add_section_frame_connectivity_normalized($frame_connectivity_normalized_SOL, $frame_section_assignments_SOL, $section_properties_normalized_SOL);
            $this->add_section_frame_connectivity_normalized($frame_connectivity_normalized_RES, $frame_section_assignments_RES, $section_properties_normalized_RES);

            if (!$this->chk_frame_section_properties($frame_connectivity_normalized_SOL, $frame_connectivity_normalized_RES, $f)) {
                return $calificacion;
            }
            $calificacion++;

            // Check DOF
            $f->write("--------------------------------------------------------------\n");
            $f->write("CHEQUEO DOF\n");
            $f->write("--------------------------------------------------------------\n");
            $patron = '/(UX=Y|UX=N)/';

            $DOF_SOL = $this->buscar_patron_SAP_tokens($archivoSOL, $patron, $this->tokens_DOF);
            $DOF_RES = $this->buscar_patron_SAP_tokens($archivoRES, $patron, $this->tokens_DOF);

            if (!$this->chk_DOF($DOF_SOL, $DOF_RES, $f)) {
                return $calificacion;
            }
            $calificacion++;

            // Check restraints
            $f->write("--------------------------------------------------------------\n");
            $f->write("CHEQUEO APOYOS\n");
            $f->write("--------------------------------------------------------------\n");
            $patron = '/Joint.*(U1=N|U1=Y)/';

            $restraint_assignments_SOL = $this->buscar_patron_SAP_tokens($archivoSOL, $patron, $this->tokens_restraint_assignments);
            $restraint_assignments_RES = $this->buscar_patron_SAP_tokens($archivoRES, $patron, $this->tokens_restraint_assignments);

            $restraint_assignments_normalized_SOL = $this->normalizar_restraint_assignments($restraint_assignments_SOL, $joint_coordinates_normalized_SOL);
            $restraint_assignments_normalized_RES = $this->normalizar_restraint_assignments($restraint_assignments_RES, $joint_coordinates_normalized_RES);

            if (!$this->chk_restraint_assignments($restraint_assignments_normalized_SOL, $restraint_assignments_normalized_RES, $DOF_SOL, $f)) {
                return $calificacion;
            }
            $calificacion++;

            $pre_f=new moquosaF();
            $out_f=new moquosaF();
            // Check loads on joints
            $pre_f->write("--------------------------------------------------------------\n");
            $pre_f->write("CHEQUEO CARGAS EN NUDOS\n");
            $pre_f->write("--------------------------------------------------------------\n");
            $patron = '/Joint.*LoadPat/';

            $joint_loads_force_SOL = $this->buscar_patron_SAP_tokens($archivoSOL, $patron, $this->tokens_joint_loads_force);
            $joint_loads_force_RES = $this->buscar_patron_SAP_tokens($archivoRES, $patron, $this->tokens_joint_loads_force);

            $joint_loads_force_normalized_SOL = $this->normalizar_joint_loads_force($joint_loads_force_SOL, $joint_coordinates_normalized_SOL, $unidadesL_SOL, $unidadesF_SOL, $EscL, $EscF);
            $joint_loads_force_normalized_RES = $this->normalizar_joint_loads_force($joint_loads_force_RES, $joint_coordinates_normalized_RES, $unidadesL_RES, $unidadesF_RES);

            $chk = $this->chk_joint_loads($joint_loads_force_normalized_SOL, $joint_loads_force_normalized_RES, $DOF_SOL, $out_f);
            if ($chk == 0) {
                $f->add($pre_f->val);
                $f->add($out_f->val);
                return $calificacion;
            } elseif ($chk != -1) {  // -1 if there are no loads
                $f->add($pre_f->val);
                $f->add($out_f->val);
                $calificacion++;
            }
            
            $pre_f->val='';
            $out_f->val='';
            // Check distributed loads on frames
            //-------------------------------------------------------------------------------
            $pre_f->write( "----------------------------------------------------------------\n");
            $pre_f->write( "CHEQUEO CARGAS EN BARRAS\n");
            $pre_f->write( "----------------------------------------------------------------\n");
            $patron = '/Frame.*LoadPat.*Type=Force.*FOverLA/';

            $frame_loads_distributed_SOL = $this->buscar_patron_SAP_tokens($archivoSOL, $patron, $this->tokens_frame_loads_distributed);
            $frame_loads_distributed_RES = $this->buscar_patron_SAP_tokens($archivoRES, $patron, $this->tokens_frame_loads_distributed);

            $this->printL($frame_loads_distributed_SOL, "frame_loads_distributed_SOL");
            $this->printL($frame_connectivity_normalized_SOL, "frame_connectivity_normalized_SOL");
 
            $frame_loads_distributed_normalized_SOL = $this->normalizar_frame_loads_distributed(
                $frame_loads_distributed_SOL, $frame_connectivity_normalized_SOL,
                $unidadesL_SOL, $unidadesF_SOL, $EscL, $EscF, $pythonSemanal
            );

            $frame_loads_distributed_normalized_RES = $this->normalizar_frame_loads_distributed(
                $frame_loads_distributed_RES, $frame_connectivity_normalized_RES,
                $unidadesL_RES, $unidadesF_RES, 1, 1, $pythonSemanal
            );

            $this->printL($frame_loads_distributed_normalized_SOL, "frame_loads_distributed_SOL");
            $this->printL($frame_loads_distributed_normalized_RES, "frame_loads_distributed_RES");

            $chk = $this->chk_frame_loads_distributed(
                $frame_loads_distributed_normalized_SOL,
                $frame_loads_distributed_normalized_RES,
                $this->tokens_frame_loads_distributed_normalized,
                [$this,'obtener_resultante_frame_loads_distributed'],  // Assuming this is a function name
                $DOF_SOL, $out_f
            );

            if ($chk == 0) {
                $f->add($pre_f->val);
                $f->add($out_f->val);
                return $calificacion;
            } elseif ($chk != -1) {  // -1 si no hay cargas
                $f->add($pre_f->val);
                $f->add($out_f->val);
                $calificacion += 1;
            }

            $pre_f->val='';
            $out_f->val='';
            //-------------------------------------------------------------------------------
            $pre_f->write( "----------------------------------------------------------------\n");
            $pre_f->write( "CHEQUEO CARGAS PUNTUALES EN BARRAS\n");
            $pre_f->write( "----------------------------------------------------------------\n");

            $patron = '/Frame.*LoadPat.*Type=Force.*Force/';

            $frame_loads_point_SOL = $this->buscar_patron_SAP_tokens($archivoSOL, $patron, $this->tokens_frame_loads_point);
            $frame_loads_point_RES = $this->buscar_patron_SAP_tokens($archivoRES, $patron, $this->tokens_frame_loads_point);

            $this->printL($frame_loads_point_SOL, "frame_loads_point_SOL");

            $frame_loads_point_normalized_SOL = $this->normalizar_frame_loads_point(
                $frame_loads_point_SOL, $frame_connectivity_normalized_SOL,
                $unidadesL_SOL, $unidadesF_SOL, $EscL, $EscF
            );

            $frame_loads_point_normalized_RES = $this->normalizar_frame_loads_point(
                $frame_loads_point_RES, $frame_connectivity_normalized_RES,
                $unidadesL_RES, $unidadesF_RES, 1, 1
            );

            $this->printL($frame_loads_point_normalized_SOL, "frame_loads_point_normalized_SOL");

            $chk = $this->chk_frame_loads_distributed(
                $frame_loads_point_normalized_SOL,
                $frame_loads_point_normalized_RES,
                $this->tokens_frame_loads_point_normalized,
                [$this,'obtener_resultante_frame_loads_point'],  // Assuming this is a function name
                $DOF_SOL, $out_f
            );

            if ($chk == 0) {
                $f->add($pre_f->val);
                $f->add($out_f->val);
                return $calificacion;
            } elseif ($chk != -1) {  // -1 si no hay cargas
                $f->add($pre_f->val);
                $f->add($out_f->val);
                $calificacion += 1;
            }


            $pre_f->val='';
            $out_f->val='';
            //-------------------------------------------------------------------------------
            $pre_f->write( "----------------------------------------------------------------\n");
            $pre_f->write( "CHEQUEO MOMENTOS PUNTUALES EN BARRAS\n");
            $pre_f->write( "----------------------------------------------------------------\n");

            $patron = '/Frame.*LoadPat.*Type=Moment.*Moment/';

            $frame_moments_point_SOL = $this->buscar_patron_SAP_tokens($archivoSOL, $patron, $this->tokens_frame_moments_point);
            $frame_moments_point_RES = $this->buscar_patron_SAP_tokens($archivoRES, $patron, $this->tokens_frame_moments_point);

            $this->printL($frame_moments_point_SOL, "frame_moments_point_SOL---------------------------");


            $frame_moments_point_normalized_SOL = $this->normalizar_frame_loads_point(
                $frame_moments_point_SOL, $frame_connectivity_normalized_SOL,
                $unidadesL_SOL, $unidadesF_SOL, $EscL, $EscF, 'Moment'
            );

            $frame_moments_point_normalized_RES = $this->normalizar_frame_loads_point(
                $frame_moments_point_RES, $frame_connectivity_normalized_RES,
                $unidadesL_RES, $unidadesF_RES, 1, 1, 'Moment'
            );

            $this->printL($frame_moments_point_normalized_SOL, "frame_moments_point_normalized_SOL-------------------------");

            $chk = $this->chk_frame_loads_distributed(
                $frame_moments_point_normalized_SOL,
                $frame_moments_point_normalized_RES,
                $this->tokens_frame_moments_point_normalized,
                [$this,'obtener_resultante_frame_loads_point'],  // Assuming this is a function name
                $DOF_SOL, $out_f, "momentos"
            );

            if ($chk == 0) {
                $f->add($pre_f->val);
                $f->add($out_f->val);
                return $calificacion;
            } elseif ($chk != -1) {  // -1 si no hay cargas
                $f->add($pre_f->val);
                $f->add($out_f->val);
                $calificacion += 1;
            }
            

            //-------------------------------------------------------------------------------
            return $calificacion;
 
        }
    }
}
