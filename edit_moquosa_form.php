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
 * Defines the editing form for the moquosa question type.
 *
 * @package    qtype
 * @subpackage moquosa
 * @copyright  2024 José Ignacio Hernando García, Miguel Hernando Padilla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * MOodle QUestion fOr SAp2000, moquosa, question editing form definition.
 *
 * @copyright  2024 José Ignacio Hernando García, Miguel Hernando Padilla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//include_once 'trait_depuracion_moquosa.php'; //añadido en ay.sh
//$GLOBALS["NOMBRE"] = "AUTO_moquosa"; //añadido en ay.sh
include_once 'trait_edit_moquosa_form.php'; //añadido en ay.sh
class qtype_moquosa_edit_form extends question_edit_form {

    use trait_edit_moquosa_form; //añadido en ay.sh

    //use trait_depuracion_moquosa; //añadido en ay.sh

    protected function definition_inner($mform) {

        $this->add_filepicker($mform); //añadido en ay.sh

        //$functions = $this->wobinichTamino(); //añadido en ay.sh

        $mform->addElement('text', 'escl', get_string('escL','qtype_moquosa'), 'maxlength="1023" size="50" ');
		$mform->setDefault('escl', '1');
        
        $mform->addElement('text', 'escf', get_string('escF','qtype_moquosa'), 'maxlength="1023" size="50" ');
		$mform->setDefault('escf', '1');
	

        $mform->addElement('static', 'answersinstruct',
                           get_string('correctanswers', 'qtype_moquosa'),
                           get_string('filloutoneanswer', 'qtype_moquosa'));
        $mform->closeHeaderBefore('answersinstruct');
        
        $mi_fraction_options=question_bank::fraction_options();
        $mi_fraction_options[0.0] = '0%';
        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_moquosa', '{no}'),
                                     $mi_fraction_options);

        $this->add_interactive_settings();
    }

    protected function data_preprocessing($question) {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_hints($question);

        return $question;
    }

    public function validation($data, $files) {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $answercount = 0;
        $maxgrade = false;
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer !== '') {
                $answercount++;
                if ($data['fraction'][$key] == 1) {
                    $maxgrade = true;
                }
            } else if ($data['fraction'][$key] != 0 ||
                       !html_is_blank($data['feedback'][$key]['text'])) {
                $errors["answer[$key]"] = get_string('answermustbegiven', 'qtype_moquosa');
                $answercount++;
            }
        }
        if ($answercount==0) {
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_moquosa', 1);
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }
        return $errors;
    }

    public function qtype() {

        //$functions = $this->wobinichTamino(); //añadido en ay.sh
        return 'moquosa';
    }
}





