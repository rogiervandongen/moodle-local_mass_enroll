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

/**
 * Bulk enrolment form
 *
 * @package     local_mass_enroll
 *
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class massenrol extends base {

    /**
     * Form definition
     */
    public function definition() {
        $mform = & $this->_form;
        $course = $this->_customdata['course'];
        $this->config = get_config('local_mass_enroll');

        $mform->addElement('header', 'general', ''); // Fill in the data depending on page params.
        // Later using set_data.
        $mform->addElement('filepicker', 'attachment', get_string('location', 'enrol_flatfile'));

        $mform->addRule('attachment', null, 'required');

        $this->add_delimiter_element();
        $this->add_encoding_element();
        $this->add_enclosure_element();

        $mform->addElement('header', 'mappings', get_string('mappings', 'local_mass_enroll'));
        $this->add_mapping_elements();

        $mform->addElement('header', 'other', get_string('other', 'local_mass_enroll'));
        $this->add_roles_element();

        $mform->addElement('selectyesno', 'creategroups', get_string('creategroups', 'local_mass_enroll'));
        $mform->setDefault('creategroups', 1);

        $mform->addElement('selectyesno', 'creategroupings', get_string('creategroupings', 'local_mass_enroll'));
        $mform->setDefault('creategroupings', 1);

        $mform->addElement('selectyesno', 'mailreport', get_string('mailreport', 'local_mass_enroll'));
        $mform->setDefault('mailreport', (int)$this->config->mailreportdefault);

        // Buttons.
        $this->add_action_buttons(true, get_string('enroll', 'local_mass_enroll'));

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);

        $this->_form->setExpanded('mappings', true, true);
        $this->_form->setExpanded('other', true, true);
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

    /**
     * Process upload
     *
     * @return boolean
     */
    public function process() {
        $data = $this->get_data();

        // If we have no data, we didn't pass validation.
        if (empty($data)) {
            return false;
        }

        $mappings = $this->get_mappings($data);
        $options = [
            'encoding' => $data->encoding,
            'delimitername' => $data->delimitername,
            'enclosure' => $data->enclosure ?? '"',
            'defaultrole' => $data->roleassign,
            'creategroups' => (bool)$data->creategroups,
            'creategroupings' => (bool)$data->creategroupings,
        ];
        $content = $this->get_file_content('attachment');

        $csvprocessor = new \local_mass_enroll\local\processor\massenrol\csv($content, false, $options);
        $csvprocessor->set_mappings($mappings);
        $csvprocessor->set_course($this->_customdata['course']);
        $csvprocessor->set_mailreport((bool)$data->mailreport);

        $result = $csvprocessor->process();

        return $result;
    }

    /**
     * Get column mappings
     *
     * @param stdClass $data
     * @return array
     */
    protected function get_mappings($data) {
        $mappings = [];
        foreach ($data->columns as $index => $mapping) {
            if ($mapping !== '#') {
                $mappings[$index] = $mapping;
            }
        }
        return $mappings;
    }

}
