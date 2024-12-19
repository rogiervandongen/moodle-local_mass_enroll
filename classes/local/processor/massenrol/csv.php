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

namespace local_mass_enroll\local\processor\massenrol;

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
    protected $options = [
        'encoding' => 'UTF-8',
        'delimitername' => 'semicolon',
        'enclosure' => '"',
        'defaultrole' => 0,
        'creategroups' => true,
        'creategroupings' => true,
    ];

    /**
     * @var array
     */
    protected $assignableroles;
    /**
     * @var array
     */
    protected $groups;
    /**
     * @var array
     */
    protected $groupings;
    /**
     * @var array
     */
    protected $coursegroups;
    /**
     * @var array
     */
    protected $coursegroupings;

    /**
     * Set course
     *
     * @param stdClass $course
     * @return $this
     */
    public function set_course($course) {
        parent::set_course($course);
        $this->assignableroles = get_assignable_roles($this->coursecontext);
        return $this;
    }

    /**
     * Initialize course
     */
    protected function initialise_course() {
        global $DB;
        parent::initialise_course();
        if (empty($this->enrolinstance)) {
            // Only add an enrol instance to the course if non-existent.
            $enrolid = $this->plugin->add_instance($this->course);
            $this->instance = $DB->get_record('enrol', ['id' => $enrolid]);
        }
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
        $event = \local_mass_enroll\event\mass_unenrolment_created::create([
                'objectid' => $this->course->id,
                'courseid' => $this->course->id,
                'context' => $this->coursecontext,
                'other' => ['info' => get_string('mass_unenroll', 'local_mass_enroll')],
            ]);
        $event->trigger();

        if ($this->mailreport) {
            $a = new \stdClass();
            $a->course = $this->course->fullname;
            $a->report = $this->compile_results();
            email_to_user($USER, \core_user::get_noreply_user(),
                    get_string('mail_enrolment_subject', 'local_mass_enroll', $CFG->wwwroot),
                    get_string('mail_enrolment', 'local_mass_enroll', $a));
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
        } else if (isset($dataobject->idnumber)) {
            $uparams = ['idnumber' => $dataobject->idnumber];
        } else if (isset($dataobject->email)) {
            $uparams = ['email' => $dataobject->email];
        }

        if (!$user = $DB->get_record('user', $uparams, '*', IGNORE_MULTIPLE)) {
            $dataobject->error = get_string('im:user_unknown', 'local_mass_enroll', reset(array_values($uparams)));
            return false;
        }

        $dataobject->userid = $user->id;
        $dataobject->userfullname = fullname($user);

        // Set role.
        if (!isset($dataobject->role)) {
            $dataobject->role = $this->options['defaultrole'];
        }

        // Is this an assignable role?
        if (!isset($this->assignableroles[$dataobject->role])) {
            $dataobject->error = get_string('im:nonassignablerole', 'local_mass_enroll', $dataobject->role);
            return false;
        }

        // General info for this user object.
        $info = '';

        // Already enroled?
        // We DO NOT support multiple roles in a course.
        $checknonmanualenrolments = (bool)get_config('local_mass_enroll', 'checknonmanualenrolments');

        if ($checknonmanualenrolments && user_has_role_assignment($user->id, $dataobject->role, $this->coursecontext->id)) {
            $info .= get_string('im:already_in', 'local_mass_enroll', fullname($user));
        } else if ($DB->record_exists('user_enrolments', ['enrolid' => $this->enrolinstance->id, 'userid' => $user->id])) {
            $info .= get_string('im:already_in', 'local_mass_enroll', fullname($user));
        } else {
            // Take care of timestart/timeend in course settings.
            $timestart = time();
            // Remove time part from the timestamp and keep only the date part.
            $timestart = make_timestamp(date('Y', $timestart), date('m', $timestart), date('d', $timestart), 0, 0, 0);
            if ($this->enrolinstance->enrolperiod) {
                $timeend = $timestart + $this->enrolinstance->enrolperiod;
            } else {
                $timeend = 0;
            }
            // Enrol the user with this plugin instance (unfortunately return void, no more status).
            $this->enrolplugin->enrol_user($this->enrolinstance, $user->id, $dataobject->role, $timestart, $timeend);
            $info .= get_string('im:enrolled_ok', 'local_mass_enroll', fullname($user));
        }

        // Group processing.
        $group = $dataobject->group ?? null;
        if (!empty($group)) {
            // See if this grouping exists.
            if (isset($this->coursegroups[$dataobject->group])) {
                $dataobject->groupid = $this->coursegroups[$dataobject->group];
            } else {
                if ($this->options['creategroups']) {
                    if (!($gpid = mass_enroll_add_group($dataobject->group, $this->course->id))) {
                        $a = (object)[
                            'group' => $dataobject->group,
                            'courseid' => $this->course->id,
                        ];
                        $this->errors[] = get_string('im:error_addg', 'local_mass_enroll', $a);
                    } else {
                        $dataobject->groupid = $gpid;
                        $this->coursegroups[$dataobject->group] = $gpid;
                    }
                }
            }
        }

        // Grouping processing.
        $grouping = $dataobject->grouping ?? null;
        if (!empty($grouping)) {
            // See if this grouping exists.
            if (isset($this->coursegroupings[$dataobject->grouping])) {
                $dataobject->groupingid = $this->coursegroupings[$dataobject->grouping];
            } else {
                if ($this->options['creategroupings']) {
                    if (!($gpid = mass_enroll_add_grouping($dataobject->grouping, $this->course->id))) {
                        $a = (object) [
                            'grouping' => $dataobject->grouping,
                            'courseid' => $this->course->id,
                        ];
                        $this->errors[] = get_string('im:error_add_grp', 'local_mass_enroll', $a);
                    } else {
                        $dataobject->groupingid = $gpid;
                        $this->coursegroupings[$dataobject->grouping] = $gpid;
                    }
                }
            }
        }

        // If grouping existed or has just been created.
        if (!empty($dataobject->groupid) && !empty($dataobject->groupingid)) {
            if (!mass_enroll_group_in_grouping($dataobject->groupid, $dataobject->groupingid)) {
                if (!mass_enroll_add_group_grouping($dataobject->groupid, $dataobject->groupingid)) {
                    $a = (object) [
                        'group' => $dataobject->group,
                        'grouping' => $dataobject->grouping,
                        'courseid' => $this->course->id,
                    ];
                    $this->errors[] = get_string('im:error_add_g_grp', 'local_mass_enroll', $a);
                }
            }

        }

        if (!empty($dataobject->groupid)) {
            // Finally add to group if needed.
            if (!groups_is_member($dataobject->groupid, $user->id)) {
                $a = (object)[
                    'group' => $dataobject->group,
                    'grouping' => $dataobject->grouping,
                ];
                $ok = groups_add_member($dataobject->groupid, $user->id);
                if ($ok) {
                    $info .= get_string('im:and_added_g', 'local_mass_enroll', $a);
                } else {
                    $info .= get_string('im:error_adding_u_g', 'local_mass_enroll', $a);
                }
            }
        } else {
            $info .= get_string('im:already_in_g', 'local_mass_enroll', $a);
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
        $result .= '<td>'.get_string('role', 'local_mass_enroll').'</td>';
        $result .= '<td>'.get_string('group').'</td>';
        $result .= '<td>'.get_string('grouping', 'group').'</td>';
        $result .= '<td>'.get_string('error', 'local_mass_enroll').'</td>';
        $result .= '<td>'.get_string('info', 'local_mass_enroll').'</td>';
        $result .= '</tr>';
        foreach ($this->results as $dataobj) {
            $result .= '<tr>';
            $result .= '<td>'.($dataobj->username ?? $dataobj->idnumber ?? $dataobj->email ?? '').'</td>';
            $result .= '<td>'.($dataobj->userid ?? '').'</td>';
            $result .= '<td>'.($dataobj->userfullname ?? '').'</td>';
            $result .= '<td>'.($dataobj->role ?? '').'</td>';
            $result .= '<td>'.($dataobj->group ?? '').'</td>';
            $result .= '<td>'.($dataobj->grouping ?? '').'</td>';
            $result .= '<td>'.($dataobj->error ?? '').'</td>';
            $result .= '<td>'.($dataobj->info ?? '').'</td>';
            $result .= '</tr>';
        }
        $result .= '</table>';

        return $result;
    }

}
