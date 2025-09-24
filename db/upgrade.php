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
 * Upgrade library code for the moquosa question type.
 *
 * @package    qtype
 * @subpackage moquosa
 * @copyright  2013 Jose Ignacio Hernando García, Miguel Martínez Pañeda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


function xmldb_qtype_moquosa_upgrade($oldversion=0) {
global $DB;

$dbman = $DB->get_manager();

// Add the new field mynewfield.
if ($oldversion < 2013100806) {
// Define field mynewfield to be added to question_moquosa.
$table = new xmldb_table('question_moquosa');
$field = new xmldb_field('sign', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'beam');

// Conditionally launch add field mynewfield.
if (!$dbman->field_exists($table, $field)) {
$dbman->add_field($table, $field);
}

// moquosa savepoint reached.
upgrade_plugin_savepoint(true, 2013100806, 'qtype', 'moquosa');
}
return true;
}
