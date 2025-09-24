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
 * Question type class for the moquosa answer question type.
 *
 * @package    qtype
 * @subpackage moquosa
 * @copyright  2024 José Ignacio Hernando García, Miguel Hernando Padilla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/moquosa/question.php');


/**
 * The moquosa answer question type.
 *
 * @copyright  2024 José Ignacio Hernando García, Miguel Hernando Padilla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//include_once 'trait_depuracion_moquosa.php'; //añadido en ay.sh
//$GLOBALS["NOMBRE"] = "AUTO_moquosa"; //añadido en ay.sh
include_once 'trait_questiontype_moquosa.php'; //añadido en ay.sh
class qtype_moquosa extends question_type {

    use trait_questiontype_moquosa; //añadido en ay.sh

    //use trait_depuracion_moquosa; //añadido en ay.sh
    public function extra_question_fields() {

        ////$functions = $this->wobinichTamino(); //añadido en ay.sh
        return array ('question_moquosa', 'answers', 'escl', 'escf');
        //return array('question_moquosa', 'answers', 'topology',
        //            'graphofinternalforces', 'width', 'mifeedback', 
        //             'internalforceserror', 'abscissaerror', 'beam', 'sign');
    }

    public function questionid_column_name() {

        ////$functions = $this->wobinichTamino(); //añadido en ay.sh
        return 'question';
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {

        ////$functions = $this->wobinichTamino(); //añadido en ay.sh
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {

        ////$functions = $this->wobinichTamino(); //añadido en ay.sh
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    public function save_question_options($question) {

        $this->save_question_moquosa($question); //añadido en ay.sh

        ////$functions = $this->wobinichTamino(); //añadido en ay.sh
        global $DB;
        $result = new stdClass();

        $context = $question->context;

        $oldanswers = $DB->get_records('question_answers',
                                       array('question' => $question->id), 'id ASC');

        $answers = array();
        $maxfraction = -1;

        // Insert all the new answers
        foreach ($question->answer as $key => $answerdata) {
            // Check for, and ignore, completely blank answer from the form.
            if (trim($answerdata) == '' && $question->fraction[$key] == 0 &&
                html_is_blank($question->feedback[$key]['text'])) {
                continue;
            }

            // Update an existing answer if possible.
            $answer = array_shift($oldanswers);
            if (!$answer) {
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = '';
                $answer->feedback = '';
                $answer->id = $DB->insert_record('question_answers', $answer);
            }

            $answer->answer   = trim($answerdata);
            $answer->fraction = $question->fraction[$key];
            $answer->feedback = $this->import_or_save_files(
                $question->feedback[$key], $context, 'question', 
                'answerfeedback', $answer->id);
            $answer->feedbackformat = $question->feedback[$key]['format'];
            $DB->update_record('question_answers', $answer);

            $answers[] = $answer->id;
            if ($question->fraction[$key] > $maxfraction) {
                $maxfraction = $question->fraction[$key];
            }
        }

        $question->answers = implode(',', $answers);
        $parentresult = parent::save_question_options($question);
        if ($parentresult !== null) {
            // Parent function returns null if all is OK
            return $parentresult;
        }

        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach ($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 
                                   'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', 
                                array('id' => $oldanswer->id));
        }

        $this->save_hints($question);

        // Perform sanity checks on fractional grades.
        if ($maxfraction != 1) {
            $result->noticeyesno = get_string('fractionsnomax',
                                              'question', $maxfraction * 100);
            return $result;
        }
    }

    protected function initialise_question_instance(
	    question_definition $question, $questiondata) {

        ////$functions = $this->wobinichTamino(); //añadido en ay.sh
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_question_answers($question, $questiondata);
    }

    public function get_random_guess_score($questiondata) {

        ////$functions = $this->wobinichTamino(); //añadido en ay.sh
        foreach ($questiondata->options->answers as $aid => $answer) {
            if ('*' == trim($answer->answer)) {
                return $answer->fraction;
            }
        }
        return 0;
    }

    public function get_possible_responses($questiondata) {

        ////$functions = $this->wobinichTamino(); //añadido en ay.sh
        $responses = array();

        $starfound = false;
        foreach ($questiondata->options->answers as $aid => $answer) {
            $responses[$aid] = new question_possible_response($answer->answer,
                                                              $answer->fraction);
            if ($answer->answer === '*') {
                $starfound = true;
            }
        }

        if (!$starfound) {
            $responses[0] = new question_possible_response(
                get_string('didnotmatchanyanswer', 'question'), 0);
        }

        $responses[null] = question_possible_response::no_response();

        return array($questiondata->id => $responses);
    }
}





