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
 * Bulk unenrolment form
 *
 * File         mass_enroll.php
 * Encoding     UTF-8
 *
 * @package     local_mass_enroll
 *
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Bulk unenrolment form
 *
 * @package     local_mass_enroll
 *
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mass_unenroll_form extends moodleform {

    /**
     * Form definition
     */
    public function definition() {
        $mform = & $this->_form;
        $course = $this->_customdata['course'];
        $config = get_config('local_mass_enroll');

        $mform->addElement('header', 'general', ''); // Fill in the data depending on page params.
        // Later using set_data.
        $mform->addElement('filepicker', 'attachment', get_string('location', 'enrol_flatfile'));

        $mform->addRule('attachment', null, 'required');

        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'tool_uploaduser'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }

        $choices = \core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploaduser'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        $ids = array(
            'idnumber' => get_string('idnumber', 'local_mass_enroll'),
            'username' => get_string('username', 'local_mass_enroll'),
            'email' => get_string('email')
        );
        $mform->addElement('select', 'firstcolumn', get_string('firstcolumn', 'local_mass_enroll'), $ids);
        $mform->setDefault('firstcolumn', 'idnumber');

        $mform->addElement('selectyesno', 'mailreport', get_string('mailreport', 'local_mass_enroll'));
        $mform->setDefault('mailreport', (int)$config->mailreportdefault);

        // Buttons.
        $this->add_action_buttons(true, get_string('unenroll', 'local_mass_enroll'));

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);
    }

    /**
     * Form data validation
     *
     * @param \stdClass $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }

}
