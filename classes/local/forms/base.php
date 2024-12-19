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
 * Bulk enrolment form
 *
 * File         mass_enroll.php
 * Encoding     UTF-8
 *
 * @package     local_mass_enroll
 *
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mass_enroll\local\forms;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once($CFG->dirroot . '/local/mass_enroll/lib.php');

/**
 * Bulk enrolment form
 *
 * @package     local_mass_enroll
 *
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base extends \moodleform {

    /**
     * @var \stdClass
     */
    protected $config;

    /**
     * Add CSV delimiter selector element
     */
    protected function add_delimiter_element() {
        $choices = \csv_import_reader::get_delimiter_list();
        $this->_form->addElement('select', 'delimitername', get_string('csvdelimiter', 'tool_uploaduser'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $this->_form->setDefault('delimitername', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $this->_form->setDefault('delimitername', 'semicolon');
        } else {
            $this->_form->setDefault('delimitername', 'comma');
        }
    }

    /**
     * Add CSV encoding selector element
     */
    protected function add_encoding_element() {
        $choices = \core_text::get_encodings();
        $this->_form->addElement('select', 'encoding', get_string('encoding', 'tool_uploaduser'), $choices);
        $this->_form->setDefault('encoding', 'UTF-8');
    }

    /**
     * Add CSV encoding selector element
     */
    protected function add_enclosure_element() {
        $this->_form->addElement('text', 'enclosure', get_string('enclosure', 'local_mass_enroll'));
        $this->_form->setType('enclosure', PARAM_TEXT);
        $this->_form->setDefault('enclosure', '"');
    }

    /**
     * Add role selector element
     */
    protected function add_roles_element() {
        $context = $this->_customdata['context'];
        $roles = get_assignable_roles($context);
        $this->_form->addElement('select', 'roleassign', get_string('roleassign', 'local_mass_enroll'), $roles);
        $this->_form->setDefault('roleassign', $this->config->defaultrole);
    }

    /**
     * Add element to select enrolment methods.
     */
    protected function add_enrolment_methods_element() {
        $methods = local_mass_enroll_get_course_enrolment_methods($this->_customdata['course']->id);
        if (!empty($methods) && $this->config->enableextraunenrolmentplugins) {
            $this->_form->addElement('advcheckbox', 'addextraenrolments',
                    get_string('enableextraunenrolmentplugins', 'local_mass_enroll'));
            $this->_form->setDefault('addextraenrolments', 0);
            $this->_form->addHelpButton('addextraenrolments', 'enableextraunenrolmentplugins', 'local_mass_enroll');

            $emel = $this->_form->addElement('select', 'extramethods', get_string('allowedunenrolmentmethods', 'local_mass_enroll'),
                    $methods, ['size' => min(10, max(1, count($methods)))]);
            $emel->setMultiple(true);
            $this->_form->setDefault('extramethods', explode(',', $this->config->allowedunenrolmentmethods));
            $this->_form->addHelpButton('extramethods', 'allowedunenrolmentmethods', 'local_mass_enroll');
            $this->_form->hideIf('extramethods', 'addextraenrolments', 'eq', 0);
        }
    }

    /**
     * Add column mapping elements
     */
    protected function add_mapping_elements() {
        $fields = [
            '#' => get_string('none'),
            'username' => get_string('username'),
            'idnumber' => get_string('idnumber'),
            'email' => get_string('email'),
            'group' => get_string('group', 'group'),
            'grouping' => get_string('grouping', 'group'),
            'role' => get_string('role'),
        ];
        $defaults = [
            0 => 'idnumber',
            1 => 'group',
            2 => '#',
            3 => '#',
            4 => '#',
        ];
        // We'll allow for N mappings.
        $n = count($fields) - 3; // 3 due to # and 2 username fields.
        for ($i = 0; $i < $n; $i++) {
            $mappingname = "columns[$i]";
            $label = get_string('mapping:column', 'local_mass_enroll', $i + 1);
            $this->_form->addElement('select', $mappingname, $label, $fields);
            $this->_form->setDefault($mappingname, $defaults[$i]);
        }
    }

}
