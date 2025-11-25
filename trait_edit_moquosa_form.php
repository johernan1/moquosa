<?php

if (!trait_exists('trait_edit_moquosa_form')) {
    trait trait_edit_moquosa_form {
        /***********************************************************************
         * FUNCIONES PROPIAS DE MOQUOSA
         * function add_filepicker
         *     unicamente despliega un filepicker EN LA VENTANA DEL PROFESOR. 
         *     Cuando se usa sube el fichero a la 
         *     tabla mdl_files con  
         *             filearea=draft 
         *             component=user
         *     Este fichero hay que guardarlo posteriormente 
         *             filearea=draft-> moquosa_prof 
         *             component=user-> question_moquosa
         *     Pero esto se hace en questiontype.php->save_question_moquosa()
         **********************************************************************/

        /**
         * Add the filepicker to the form.
         * @param object $mform the form being built.
         */
        protected function add_filepicker($mform) {
            //Depuracion   
            //$functions = $this->wobinichTamino();
            global $CFG, $COURSE;
    
            $context = context_course::instance($COURSE->id);
            $contextid = $context->id;
            $questionid = $this->question->id;

            // Añade un selector de archivos.
            $filearea = "moquosa_prof";
            $itemid = $questionid; // El ID de la pregunta
        
            // Prepara el área de archivos y obtén el draftitemid
            $draftitemid = file_get_submitted_draft_itemid($filearea);
            file_prepare_draft_area($draftitemid,
                                    $contextid,
                                    'qtype_moquosa',
                                    $filearea,
                                    $itemid,
                                    array('subdirs' => 0,
                                          'maxfiles' => 1));
            // Añade el selector de archivos
            $mform->addElement('filepicker',
                               $filearea,
                               get_string('file'),
                               null,
                               array('maxbytes' => $CFG->maxbytes,
                                     // 'maxbytes' => 10485760, // 10 MB
                                     'maxfiles' => 1,
                                     'accepted_types' => ['*'],
                                     'draftitemid' => $draftitemid));
            // Regla para hacer el campo obligatorio.
            $mform->addRule($filearea,
                            null,
                            'required',
                            null,
                            'client');
            // Establece el tipo de parámetro
            $mform->setType($filearea, PARAM_FILE);
        
            // Establece el draftitemid para el filepicker
            $mform->setDefault($filearea, $draftitemid);
        }
    }
}
