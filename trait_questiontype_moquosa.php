<?php

if (!trait_exists('trait_questiontype_moquosa')) {
    trait trait_questiontype_moquosa {
        /***********************************************************************
         * FUNCIONES PROPIAS DE MOQUOSA
         * function save_question_moquosa($question)
         *     El fichero subido por el profesor en edit_moquosa_form.php a la
         *     tabla mdl_files con  
         *             filearea=draft 
         *             component=user
         *     Se guarda para poder usarlo posteriormente
         *             filearea=draft-> moquosa_prof 
         *             component=user-> question_moquosa
         *     Previamente se han eliminado los ficheros que se han subido antes
         *     Esto solo tiene sentido si se esta re-editando la pregunta y se 
         *     modifica el fichero que ha subido el profesor 
         **************************************************************************/
        public function save_question_moquosa($question) {
            global $DB, $CFG, $COURSE;

            // depuracion       
            //$functions = $this->wobinichTamino();

		   
         
            $context = context_course::instance($COURSE->id);
            $contextid = $context->id;
            $component = 'qtype_moquosa';
            $filearea = 'moquosa_prof';
            $itemid = $question->id; 

        
            // Eliminar archivos existentes antes de guardar los nuevos
            // Obtener el almacenamiento de archivos
            $fs = get_file_storage();
        
            debugging('questiontype.php->save_question_moquosa(borra ficheros anteriores anteriores)->$fs: '.print_r($fs, true), DEBUG_DEVELOPER);
            $existingfiles = $fs->get_area_files($contextid,
                                                 $component,
                                                 $filearea,
                                                 $itemid,
                                                 'filename',
                                                 false);
        
            debugging('questiontype.php->save_question_moquosa(borra anteriores)->$existingfiles: '.print_r($existingfiles, true), DEBUG_DEVELOPER);
            foreach ($existingfiles as $existingfile) {
                $existingfile->delete();
            }
        
        
            // Guardar archivos desde el área de borrador
            $draftitemid = file_get_submitted_draft_itemid($filearea); 
            debugging("----------------------- draftitemid->".print_r($draftitemid,true)."; ".
                      " contextid->".print_r($contextid,true)."; ".
                      " component->".print_r($component,true)."; ".
                      " filearea->".print_r($filearea,true)."; ".
                      " itemid->".print_r($itemid,true), DEBUG_DEVELOPER);
            file_prepare_draft_area(   $draftitemid,
                                       $contextid,
                                       $component, 
                                       $filearea, 
                                       $itemid,
                                       array('subdirs' => 0, 'maxfiles' => 1));
            file_save_draft_area_files($draftitemid,
                                       $contextid,
                                       $component,
                                       $filearea,
                                       $itemid,
                                       array('subdirs' => 0, 'maxfiles' => 1));
      
            return $question;
        }

        public function process_response(array $response) {
            global $USER;

            //$functions = $this->wobinichTamino();
            //debugging("Current function: questiontype.php" . $functions['current'], DEBUG_DEVELOPER);
            //debugging("Calling function: questiontype.php" . $functions['calling'], DEBUG_DEVELOPER);
            return parent::process_response($response);
        }
    }
}
