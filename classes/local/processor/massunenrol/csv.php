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
 * The csv processor.
 *
 * File         csv.php
 * Encoding     UTF-8
 *
 * @package     local_mass_enroll
 *
 * @copyright   2015 onwards R v Dongen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mass_enroll\local\processor\massunenrol;

use local_mass_enroll\local\processor\csvbase;

/**
 * The csv processor.
 *
 * @package     local_mass_enroll
 *
 * @copyright   2015 onwards R v Dongen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv extends csvbase {

    /**
     * @var array
     */
    protected $enrolmentinstances;

    /**
     * @var array
     */
    protected $options = [
        'encoding' => 'UTF-8',
        'delimitername' => 'semicolon',
        'enclosure' => '"',
        'extramethods' => [],
    ];

    /**
     * Initialize course
     */
    protected function initialise_course() {
        parent::initialise_course();
        $extraenrolplugins = [];
        foreach ($this->options->extramethods as $enrol) {
            $extraenrolplugins[$enrol] = enrol_get_plugin($enrol);
        }
        $this->enrolmentinstances = mass_enroll_find_instances($this->course->id, array_keys($extraenrolplugins));
    }

    /**
     * Execution of code after processing
     *
     * @return void
     */
    protected function after_processing_complete() {
        global $CFG, $USER;

        // Only do this when we're not proof processing.
        if ($this->testrun) {
            return;
        }

        // Trigger event.
        $event = \local_mass_enroll\event\mass_enrolment_created::create([
            'objectid' => $this->course->id,
            'courseid' => $this->course->id,
            'context' => $this->coursecontext,
            'other' => ['info' => get_string('mass_enroll', 'local_mass_enroll')],
        ]);
        $event->trigger();

        if ($this->mailreport) {
            $a = new \stdClass();
            $a->course = $this->course->fullname;
            $a->report = $this->compile_results();
            email_to_user($USER, \core_user::get_noreply_user(),
                    get_string('mail_unenrolment_subject', 'local_mass_enroll', $CFG->wwwroot),
                    get_string('mail_unenrolment', 'local_mass_enroll', $a));
        }
    }

    /**
     * Process user data
     *
     * @param stdClass $dataobject
     * @return bool
     */
    protected function process_user(&$dataobject): bool {
        global $DB;

        if (isset($dataobject->username)) {
            $uparams = ['username' => $dataobject->username];
        } else if ($dataobject->idnumber) {
            $uparams = ['idnumber' => $dataobject->idnumber];
        } else if (isset($dataobject->email)) {
            $uparams = ['email' => $dataobject->email];
        }

        if (!$user = $DB->get_record('user', $uparams)) {
            $dataobject->error = get_string('im:user_unknown', 'local_mass_enroll', reset(array_values($uparams)));
            return false;
        }

        $dataobject->userid = $user->id;
        $dataobject->userfullname = fullname($user);

        $info = '';

        // Now try to process the unenrolment.
        if ($DB->record_exists('user_enrolments', ['enrolid' => $this->enrolinstance->id, 'userid' => $user->id])) {
            // Unenrol the user with this plugin instance (unfortunately return void, no more status).
            $this->enrolplugin->unenrol_user($this->enrolinstance, $user->id);
            $info .= get_string('im:unenrolled_ok', 'local_mass_enroll', fullname($user));
        } else if ($instance = mass_enroll_find_enrolment($user, $this->enrolmentinstances)) {
            // Try to locate in other instances.
            $enrolplugin = $this->enrolmentinstances[$instance->enrol];
            $enrolplugin->unenrol_user($instance, $user->id);
            $info .= get_string('im:unenrolled_ok', 'local_mass_enroll', fullname($user));
        } else {
            // Weird, user not enrolled.
            $info .= get_string('im:not_in', 'local_mass_enroll', fullname($user));
        }

        $dataobject->info = $info;

        return true;
    }

    /**
     * Compile execution results into readable format.
     *
     * @return string
     */
    protected function compile_results() {
        $result = '';

        $result .= '<table>';
        $result .= '<tr>';
        $result .= '<td>'.get_string('identifier', 'local_mass_enroll').'</td>';
        $result .= '<td>'.get_string('userid', 'local_mass_enroll').'</td>';
        $result .= '<td>'.get_string('userfullname', 'local_mass_enroll').'</td>';
        $result .= '<td>'.get_string('error', 'local_mass_enroll').'</td>';
        $result .= '<td>'.get_string('info', 'local_mass_enroll').'</td>';
        $result .= '</tr>';
        foreach ($this->results as $dataobj) {
            $result .= '<tr>';
            $result .= '<td>'.($dataobj->username ?? $dataobj->idnumber ?? $dataobj->email ?? '').'</td>';
            $result .= '<td>'.($dataobj->userid ?? '').'</td>';
            $result .= '<td>'.($dataobj->userfullname ?? '').'</td>';
            $result .= '<td>'.($dataobj->error ?? '').'</td>';
            $result .= '<td>'.($dataobj->info ?? '').'</td>';
            $result .= '</tr>';
        }
        $result .= '</table>';

        return $result;
    }

}
