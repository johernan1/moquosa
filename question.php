<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *  MOodle QUestion fOr SAp2000, moquosa, question definition class.
 *
 * @package    qtype
 * @subpackage moquosa
 * @copyright  2024 José Ignacio Hernando García, Miguel Hernando Padilla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Represents a structure moquosa question.
 *
 * @copyright  2024 José Ignacio Hernando García, Miguel Hernando Padilla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//include_once 'trait_depuracion_moquosa.php'; //añadido en ay.sh
//$GLOBALS["NOMBRE"] = "AUTO_moquosa"; //añadido en ay.sh

include_once 'moquosa2000/moquosa2000.php' ; // La clase con los métodos para evaluar 

class qtype_moquosa_question extends question_graded_by_strategy
    implements question_response_answer_comparer {

    //use trait_depuracion_moquosa; //añadido en ay.sh
    /** @var boolean whether answers should be graded case-sensitively. */
    public $beam;
    /** @var array of question_answer. */
    public $answers = array();

    public function __construct() {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        parent::__construct(new question_first_matching_answer_grading_strategy($this));
    }

    public function get_expected_data() {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        return array('answer' => PARAM_RAW_TRIMMED);
    }

    public function summarise_response(array $response) {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        if (isset($response['answer'])) {
            return $response['answer'];
        } else {
            return null;
        }
    }

    public function is_complete_response(array $response) {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        return array_key_exists('answer', $response) &&
            ($response['answer'] || $response['answer'] === '0');
    }

    public function get_validation_error(array $response) {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_moquosa');
    }

    public function is_same_response(array $prevresponse, array $newresponse) {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        return question_utils::arrays_same_at_key_missing_is_blank(
            $prevresponse, $newresponse, 'answer');
    }

    public function get_answers() {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        return $this->answers;
    }

    public function compare_response_with_answer(
        array $response, question_answer $answer) {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        if (!array_key_exists('answer', $response) || 
            is_null($response['answer'])) {
            return false;
        }
	
        //$nOK=self::php_chkIfos($response['answer']);
        //print_object("question.php: function compare_response_with_answer");
        //print_object($nOK);
        //print_object("RESPUESTA DEL ALUMNO");
        //print_object(substr($response['answer'],0,100));
        //print_object("FICHERO DEL PROFESOR");
        global $COURSE;
        $context = context_course::instance($COURSE->id);
        $contextid = $context->id;
        $component = 'qtype_moquosa';
        $filearea = 'moquosa_prof';
        $itemid = $this->id; // El ID de la pregunta o el ID del área de archivo

        
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid,
                                     $component,
                                     $filearea,
                                     $itemid,
                                     'filename',
                                     false);
        //debugging($functions['current'].'->$files: '.print_r($files, true), DEBUG_DEVELOPER);
        if (!empty($files)) {
            $file = reset($files); // Obtenemos el primer (y único) archivo.
            $content = $file->get_content();
            //$firstline = strtok($content, "\n"); // Obtener la primera línea del archivo.

            //debugging($functions['current'].'->$content: '.print_r($content, true),
            //          DEBUG_DEVELOPER);
            // Imprimir la primera línea del archivo.
            
            //debugging($functions['current'].'->$firstline: '.print_r($firstline, true),
            //          DEBUG_DEVELOPER);
            //echo '--------Copie el contenido de su fichero *.$2k en el espacio resresvado a la respuesta'."<br><br>";
            //echo "--------Primera linea del fichero del profesor=". $firstline."<br><br>";
            
            //$result .= html_writer::tag('p', $firstline);
        } else {
            // No se encontró ningún archivo.
            //$result .= html_writer::tag('p', get_string('nofile', 'qtype_moquosa'));
        }
        
        $miMoquosa2000 = new Moquosa2000();

        $archivoRES=$miMoquosa2000->prepo_entrada_alumno($response['answer']);
        //echo "archivoRes:";
        //echo $archivoRES;
        $f=new moquosaF();
        
        // Llamar a la función chk_all
        $EscL=$this->escl; //$response['escl']; topolog
        $EscF=$this->escf; //$response['escf'];
        //echo "EscL=$EscL";
        //echo "EscF=$EscF";
        $nOK=$miMoquosa2000->chk_all($content, $archivoRES, $f, (float)$EscL, (float)$EscF);
        
        // print_object($nOK);
        // print_object($response['answer']);
        
        //$nOK=self::chk_all($content, $archivoRES, $f, $EscL = '1', $EscF = '1', $pythonSemanal = '0');
        
        // print_object("++++++++++++++++++++++++++++++++++++++");
        // print_object("+++FIN DE chk_all+++++++++++++++++++++++++++++++++++");
        // print_object($f->val);
        // print_object('$nOK');
        // print_object($nOK);
        // print_object("++++++++++++++++++++++++++++++++++++++");
        // print_object("++++++++++++++++++++++++++++++++++++++");

        $answer->feedback=$answer->feedback.$f->val;
        $answer->feedback=$f->val;
        return self::compare_string_with_wildcard(
            //org: $response['answer'], $answer->answer, !$this->beam);
            $nOK, $answer->answer);
	        
    }

    public static function compare_string_with_wildcard($string, $pattern) {
        // Break the string on non-escaped asterisks.
        $bits = preg_split('/(?<!\\\\)\*/', $pattern);
        // Escape regexp special characters in the bits.
        $excapedbits = array();
        foreach ($bits as $bit) {
            $excapedbits[] = preg_quote(str_replace('\*', '*', $bit));
        }
        // Put it back together to make the regexp.
        $regexp = '|^' . implode('.*', $excapedbits) . '$|u';

        return preg_match($regexp, trim($string));
        
    }

    public function get_correct_response() {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        $response = parent::get_correct_response();
        if ($response) {
            $response['answer'] = $this->clean_response($response['answer']);
        }
        return $response;
    }

    public function clean_response($answer) {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        // Break the string on non-escaped asterisks.
        $bits = preg_split('/(?<!\\\\)\*/', $answer);

        // Unescape *s in the bits.
        $cleanbits = array();
        foreach ($bits as $bit) {
            $cleanbits[] = str_replace('\*', '*', $bit);
        }

        // Put it back together with spaces to look nice.
        return trim(implode(' ', $cleanbits));
    }

    public function check_file_access($qa, $options, $component, $filearea,
                                      $args, $forcedownload) {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        if ($component == 'question' && $filearea == 'answerfeedback') {
            $currentanswer = $qa->get_last_qt_var('answer');
            $answer = $qa->get_question()->get_matching_answer(array('answer' => $currentanswer));
            $answerid = reset($args); // itemid is answer id.
            return $options->feedback && $answer && $answerid == $answer->id;

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                                             $args, $forcedownload);
        }
    }


}

