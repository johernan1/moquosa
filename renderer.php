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
 *  MOodle COurse on SAp2000, moquosa, question renderer class.
 *
 * @package    qtype
 * @subpackage moquosa
 * @copyright  2024 José Ignacion Hernando García, Miguel Hernando Padilla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for structure moquosa questions.
 *
 * @copyright  2024 José Ignacio Hernando García, Miguel Hernando Padilla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//include_once 'trait_depuracion_moquosa.php'; //añadido en ay.sh
//$GLOBALS["NOMBRE"] = "AUTO_moquosa"; //añadido en ay.sh
//include_once 'trait_renderer_moquosa.php'; //añadido en ay.sh
class qtype_moquosa_renderer extends qtype_renderer {

    //use trait_renderer_moquosa; //añadido en ay.sh

    //use trait_depuracion_moquosa; //añadido en ay.sh
    public function formulation_and_controls(question_attempt $qa,
                                             question_display_options $options) {

        ////$functions = $this->wobinichTamino(); //añadido en ay.sh

        $question = $qa->get_question();
        $currentanswer = $qa->get_last_qt_var('answer');
        $inputname = $qa->get_qt_field_name('answer');
        $inputattributes = array(
            'type' => 'text',
            'name' => $inputname,
            'value' => $currentanswer,
            'id' => $inputname,
            'size' => 80,
        );

        if ($options->readonly) {
            $inputattributes['readonly'] = 'readonly';
        }

        $feedbackimg = '';
        if ($options->correctness) {
            $answer = $question->get_matching_answer(
                array('answer' => $currentanswer));
            if ($answer) {
                $fraction = $answer->fraction;
            } else {
                $fraction = 0;
            }
            $inputattributes['class'] = $this->feedback_class($fraction);
            $feedbackimg = $this->feedback_image($fraction);
        }

        $questiontext = $question->format_questiontext($qa);
        $placeholder = false;
        if (preg_match('/_____+/', $questiontext, $matches)) {
            $placeholder = $matches[0];
            $inputattributes['size'] = round(strlen($placeholder) * 1.1);
        }
        $input = html_writer::empty_tag('input', $inputattributes) .
               $feedbackimg;

        if ($placeholder) {
            $inputinplace = html_writer::tag('label', get_string('answer'),
                                             array('for' => $inputattributes['id'], 
                                                   'class' => 'accesshide'));
            $inputinplace .= $input;
            $questiontext = substr_replace($questiontext, $inputinplace,
                                           strpos($questiontext, $placeholder), strlen($placeholder));
        }

        $result = html_writer::tag('div', $questiontext, 
                                   array('class' => 'qtext'));

        if (!$placeholder) {
            $result .= html_writer::start_tag('div',
                                              array('class' => 'ablock'));
            $result .= html_writer::tag('label', 
                                        get_string('answer', 'qtype_moquosa',
                                                   html_writer::tag('span', 
                                                                    $input, array('class' => 'answer'))),
                                        array('for' => $inputattributes['id']));
            $result .= html_writer::end_tag('div');
        }

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                                                 $question->get_validation_error(array('answer' => 
                                                                                       $currentanswer)),
                                                 array('class' => 'validationerror'));
        }

        /**
         * Se determina si se dibuja la solucion:
         * roles "teacher","editingteacher" o user "admin" 
         */
        global $DB, $COURSE, $USER, $CFG;
        $course_id=$COURSE->id;

	$context = context_course::instance($course_id)
        $roles = get_user_roles($context, $USER->id, true);
        $drawSol = 0;
        foreach($roles as $r) {
            if($r->shortname == "editingteacher" ||
               $r->shortname == "teacher") {
                $drawSol = 1;  //1:    Dibuja la solucion
                //0: No dibuja la solucion    
            }
        }
        if($USER->username == "admin") { $drawSol=1;};	
	
        // Se determina si activan los events y se dibujan los botones
        $events=1;
        if ($options->readonly) {
            $events=0;
        }


        return $result;
    }


    public function specific_feedback(question_attempt $qa) {

        ////$functions = $this->wobinichTamino(); //añadido en ay.sh
        $question = $qa->get_question();

        $answer = $question->get_matching_answer(array('answer' => $qa->get_last_qt_var('answer')));
        if (!$answer || !$answer->feedback) {
            return '';
        }

        return $question->format_text($answer->feedback, 
                                      $answer->feedbackformat,
                                      $qa, 'question', 'answerfeedback', $answer->id);
    }

    public function correct_response(question_attempt $qa) {

        ////$functions = $this->wobinichTamino(); //añadido en ay.sh
        $myoutput =get_string('correctansweris', 'qtype_moquosa',"");
        // MODIFICADO en ay.sh $result .=$this->moquosa($qa, 1 , "correct_response", 0);
        return $myoutput;
     
    }

}




