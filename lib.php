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
 * Code for handling mass enrolment from a cvs file
 *
 * File         lib.php
 * Encoding     UTF-8
 *
 * @package     local_mass_enroll
 *
 * @copyright   1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Quick fix for Moodle 2.9
 *
 * @param settings_navigation $navigation
 * @param course_context      $context
 * @return void
 */
function local_mass_enroll_extend_settings_navigation(settings_navigation $navigation, $context) {
    local_mass_enroll_extends_settings_navigation($navigation, $context);
}
/**
 * Hook to insert a link in settings navigation menu block
 *
 * @param settings_navigation $navigation
 * @param course_context      $context
 * @return void
 */
function local_mass_enroll_extends_settings_navigation(settings_navigation $navigation, $context) {
    global $CFG;
    // If not in a course context, then leave.
    if ($context == null || $context->contextlevel != CONTEXT_COURSE) {
        return;
    }

    // Front page has a 'frontpagesettings' node, other courses will have 'courseadmin' node.
    if (null == ($courseadminnode = $navigation->get('courseadmin'))) {
        // Keeps us off the front page.
        return;
    }
    if (null == ($useradminnode = $courseadminnode->get('users'))) {
        return;
    }

    $config = get_config('local_mass_enroll');
    if ((bool)$config->enablemassenrol) {
        if (has_capability('local/mass_enroll:enrol', $context)) {
            $url = new moodle_url($CFG->wwwroot . '/local/mass_enroll/massenrol.php', ['id' => $context->instanceid]);
            $useradminnode->add(get_string('mass_enroll', 'local_mass_enroll'), $url,
                    navigation_node::TYPE_SETTING, null, 'massenrols', new pix_icon('i/admin', ''));
        }
    }
    if ((bool)$config->enablemassunenrol) {
        if (has_capability('local/mass_enroll:unenrol', $context)) {
            $url = new moodle_url($CFG->wwwroot . '/local/mass_enroll/massunenrol.php', ['id' => $context->instanceid]);
            $useradminnode->add(get_string('mass_unenroll', 'local_mass_enroll'), $url,
                    navigation_node::TYPE_SETTING, null, 'massunenrols', new pix_icon('i/admin', ''));
        }
    }
}

/**
 * process the mass enrolment
 *
 * @param csv_import_reader $cir  an import reader created by caller
 * @param stdClass $course  a course record from table mdl_course
 * @param stdClass $context  course context instance
 * @param stdClass $data    data from a moodleform
 * @return string  log of operations
 */
function mass_enroll($cir, $course, $context, $data) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/group/lib.php');

    $result = '';
    $roleid = $data->roleassign;
    $useridfield = $data->firstcolumn;

    $enrollablecount = 0;
    $createdgroupscount = 0;
    $createdgroupingscount = 0;
    $createdgroups = '';
    $createdgroupings = '';

    $checknonmanualenrolments = get_config('local_mass_enroll', 'checknonmanualenrolments');
    $role = $DB->get_record('role', ['id' => $roleid]);

    $result .= get_string('im:using_role', 'local_mass_enroll', $role->name) . "\n";

    $plugin = enrol_get_plugin('manual');
    // Moodle 2.x enrolment and role assignment are different.
    // Assure course has manual enrolment plugin instance we are going to use.
    // Only one instance is allowed; see enrol/manual/lib.php get_new_instance().
    $instance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
    if (empty($instance)) {
        // Only add an enrol instance to the course if non-existent.
        $enrolid = $plugin->add_instance($course);
        $instance = $DB->get_record('enrol', ['id' => $enrolid]);
    }

    // Init csv import helper.
    $cir->init();
    while ($fields = $cir->next()) {
        $a = new stdClass();

        if (empty($fields)) {
            continue;
        }

        // First column = id Moodle (idnumber,username or email).
        // Get rid on eventual double quotes unfortunately not done by Moodle CSV importer.
        $fields[0] = str_replace('"', '', trim($fields[0]));

        if (!$user = $DB->get_record('user', [$useridfield => $fields[0]])) {
            $result .= get_string('im:user_unknown', 'local_mass_enroll', $fields[0]) . "\n";
            continue;
        }
        // Already enroled?
        // We DO NOT support multiple roles in a course.
        if ($checknonmanualenrolments && user_has_role_assignment($user->id, $roleid, $context->id)) {
            $result .= get_string('im:already_in', 'local_mass_enroll', fullname($user));
        } else if ($DB->record_exists('user_enrolments', ['enrolid' => $instance->id, 'userid' => $user->id])) {
            $result .= get_string('im:already_in', 'local_mass_enroll', fullname($user));
        } else {
            // Take care of timestart/timeend in course settings.
            $timestart = time();
            // Remove time part from the timestamp and keep only the date part.
            $timestart = make_timestamp(date('Y', $timestart), date('m', $timestart), date('d', $timestart), 0, 0, 0);
            if ($instance->enrolperiod) {
                $timeend = $timestart + $instance->enrolperiod;
            } else {
                $timeend = 0;
            }
            // Enrol the user with this plugin instance (unfortunately return void, no more status).
            $plugin->enrol_user($instance, $user->id, $roleid, $timestart, $timeend);
            $result .= get_string('im:enrolled_ok', 'local_mass_enroll', fullname($user));
            $enrollablecount++;
        }

        if (empty($fields[1])) {
            $group = null;
        } else {
            $group = str_replace('"', '', trim($fields[1]));
        }
        // 2nd column?
        if (empty($group)) {
            $result .= "\n";
            continue; // No group for this one.
        }

        // Create group if needed.
        if (!($gid = mass_enroll_group_exists($group, $course->id))) {
            if ($data->creategroups) {
                if (!($gid = mass_enroll_add_group($group, $course->id))) {
                    $a->group = $group;
                    $a->courseid = $course->id;
                    $result .= get_string('im:error_addg', 'local_mass_enroll', $a) . "\n";
                    continue;
                }
                $createdgroupscount++;
                $createdgroups .= " $group";
            } else {
                $result .= get_string('im:error_g_unknown', 'local_mass_enroll', $group) . "\n";
                continue;
            }
        }

        // If groupings are enabled on the site (should be?).
        if (!($gpid = mass_enroll_grouping_exists($group, $course->id))) {
            if ($data->creategroupings) {
                if (!($gpid = mass_enroll_add_grouping($group, $course->id))) {
                    $a->group = $group;
                    $a->courseid = $course->id;
                    $result .= get_string('im:error_add_grp', 'local_mass_enroll', $a) . "\n";
                    continue;
                }
                $createdgroupingscount++;
                $createdgroupings .= " $group";
            }
        }
        // If grouping existed or has just been created.
        if ($gpid && !(mass_enroll_group_in_grouping($gid, $gpid))) {
            if (!(mass_enroll_add_group_grouping($gid, $gpid))) {
                $a->group = $group;
                $result .= get_string('im:error_add_g_grp', 'local_mass_enroll', $a) . "\n";
                continue;
            }
        }

        // Finally add to group if needed.
        if (!groups_is_member($gid, $user->id)) {
            $ok = groups_add_member($gid, $user->id);
            if ($ok) {
                $result .= get_string('im:and_added_g', 'local_mass_enroll', $group) . "\n";
            } else {
                $result .= get_string('im:error_adding_u_g', 'local_mass_enroll', $group) . "\n";
            }
        } else {
            $result .= get_string('im:already_in_g', 'local_mass_enroll', $group) . "\n";
        }
    }

    // Recap final.
    $result .= get_string('im:stats_i', 'local_mass_enroll', $enrollablecount) . "\n";
    $a->nb = $createdgroupscount;
    $a->what = $createdgroups;
    $result .= get_string('im:stats_g', 'local_mass_enroll', $a) . "\n";
    $a->nb = $createdgroupingscount;
    $a->what = $createdgroupings;
    $result .= get_string('im:stats_grp', 'local_mass_enroll', $a) . "\n";

    // Trigger event.
    $event = \local_mass_enroll\event\mass_enrolment_created::create([
        'objectid' => $course->id,
        'courseid' => $course->id,
        'context' => \context_course::instance($course->id),
        'other' => ['info' => get_string('mass_enroll', 'local_mass_enroll')],
    ]);
    $event->trigger();

    return $result;
}

/**
 * process the mass unenrolment
 *
 * @param csv_import_reader $cir  an import reader created by caller
 * @param stdClass $course  a course record from table mdl_course
 * @param stdClass $context  course context instance
 * @param stdClass $data    data from a moodleform
 * @return string  log of operations
 */
function mass_unenroll($cir, $course, $context, $data) {
    global $DB;
    $result = '';

    $useridfield = $data->firstcolumn;
    $unenrollablecount = 0;

    $manualenrolplugin = enrol_get_plugin('manual');
    $extraenrolplugins = [];
    foreach ($data->extramethods as $enrol) {
        $extraenrolplugins[$enrol] = enrol_get_plugin($enrol);
    }
    $extrainstances = mass_enroll_find_instances($course->id, array_keys($extraenrolplugins));

    // Moodle 2.x enrolment and role assignment are different.
    // Assure course has manual enrolment plugin instance we are going to use.
    // Only one instance is allowed; see enrol/manual/lib.php get_new_instance().
    $manualinstance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
    if (empty($manualinstance)) {
        // Only add an enrol instance to the course if non-existent.
        $enrolid = $manualenrolplugin->add_instance($course);
        $manualinstance = $DB->get_record('enrol', ['id' => $enrolid]);
    }

    // Init csv import helper.
    $cir->init();
    while ($fields = $cir->next()) {
        if (empty($fields)) {
            continue;
        }

        // First column = id Moodle (idnumber,username or email).
        // Get rid on eventual double quotes unfortunately not done by Moodle CSV importer.
        $fields[0] = str_replace('"', '', trim($fields[0]));

        if (!$user = $DB->get_record('user', [$useridfield => $fields[0]])) {
            $result .= get_string('im:user_unknown', 'local_mass_enroll', $fields[0]) . "\n";
            continue;
        }
        // Already enroled?
        if ($DB->record_exists('user_enrolments', ['enrolid' => $manualinstance->id, 'userid' => $user->id])) {
            // Unenrol the user with this plugin instance (unfortunately return void, no more status).
            $manualenrolplugin->unenrol_user($manualinstance, $user->id);
            $result .= get_string('im:unenrolled_ok', 'local_mass_enroll', fullname($user)) . "\n";
            $unenrollablecount++;
        } else if ($instance = mass_enroll_find_enrolment($user, $extrainstances)) {
            // Try to locate in other instances.
            $enrolplugin = $extraenrolplugins[$instance->enrol];
            $enrolplugin->unenrol_user($instance, $user->id);
            $result .= get_string('im:unenrolled_ok', 'local_mass_enroll', fullname($user)) . "\n";
            $unenrollablecount++;
        } else {
            // Weird, user not enrolled.
            $result .= get_string('im:not_in', 'local_mass_enroll', fullname($user)) . "\n";
        }
    }

    // Recap final.
    $result .= get_string('im:stats_ui', 'local_mass_enroll', $unenrollablecount) . "\n";

    // Trigger event.
    $event = \local_mass_enroll\event\mass_unenrolment_created::create([
        'objectid' => $course->id,
        'courseid' => $course->id,
        'context' => context_course::instance($course->id),
        'other' => ['info' => get_string('mass_unenroll', 'local_mass_enroll')],
    ]);
    $event->trigger();

    return $result;
}

/**
 * Find enrolment instances based on given array of enrolment methods
 *
 * @param int $courseid
 * @param array $extramethods extra enrolment plugin names
 * @return array
 */
function mass_enroll_find_instances($courseid, array $extramethods) {
    global $DB;
    $result = [];
    if (empty($extramethods)) {
        return $result;
    }
    list($insql, $params) = $DB->get_in_or_equal($extramethods, SQL_PARAMS_NAMED, 'enrol', true);
    $params['courseid'] = $courseid;
    return array_values($DB->get_records_sql('SELECT * FROM {enrol} WHERE courseid = :courseid AND enrol ' . $insql, $params));
}

/**
 * Find user enrolment instance for a specific combination of user/enrolment.
 *
 * @param stdClass $user
 * @param array $instances
 * @return stdClass|null instance or null if not located.
 */
function mass_enroll_find_enrolment($user, array $instances) {
    global $DB;
    foreach ($instances as $instance) {
        if ($DB->get_record('user_enrolments', ['enrolid' => $instance->id, 'userid' => $user->id])) {
            return $instance;
        }
    }
    return null;
}

/**
 * Add a group
 *
 * @param string $newgroupname
 * @param int $courseid
 * @return int id   Moodle id of inserted record
 */
function mass_enroll_add_group($newgroupname, $courseid) {
    $newgroup = new stdClass();
    $newgroup->name = $newgroupname;
    $newgroup->courseid = $courseid;
    $newgroup->lang = current_language();
    return groups_create_group($newgroup);
}

/**
 * Add a grouping
 *
 * @param string $newgroupingname
 * @param int $courseid
 * @return int id Moodle id of inserted record
 */
function mass_enroll_add_grouping($newgroupingname, $courseid) {
    $newgrouping = new stdClass();
    $newgrouping->name = $newgroupingname;
    $newgrouping->courseid = $courseid;
    return groups_create_grouping($newgrouping);
}

/**
 * Check if a group exists
 *
 * @param string $name group name
 * @param int $courseid course
 * @return string or false
 */
function mass_enroll_group_exists($name, $courseid) {
    return groups_get_group_by_name($courseid, $name);
}

/**
 * Check if a grouping exists
 *
 * @param string $name group name
 * @param int $courseid course
 * @return string or false
 */
function mass_enroll_grouping_exists($name, $courseid) {
    return groups_get_grouping_by_name($courseid, $name);
}

/**
 * Get a group in a grouping
 *
 * @param int $gid group ID
 * @param int $gpid grouping ID
 * @return mixed a fieldset object containing the first matching record or false
 */
function mass_enroll_group_in_grouping($gid, $gpid) {
    global $DB;
    $conditions = ['groupingid' => $gpid, 'groupid' => $gid];
    return $DB->get_record('groupings_groups', $conditions, '*', IGNORE_MISSING);
}

/**
 * Add a grouping
 *
 * @param int $gid group ID
 * @param int $gpid grouping ID
 * @return bool|int true or new id
 * @throws dml_exception A DML specific exception is thrown for any errors.
 */
function mass_enroll_add_group_grouping($gid, $gpid) {
    global $DB;
    $new = new stdClass();
    $new->groupid = $gid;
    $new->groupingid = $gpid;
    $new->timeadded = time();
    return $DB->insert_record('groupings_groups', $new);
}

/**
 * Load list of enrolment methods (except manual, this is defaulted).
 *
 * return array list of enrolment methods.
 */
function local_mass_enroll_get_enrolment_methods() {
    global $CFG;
    require_once($CFG->dirroot . '/lib/enrollib.php');
    $list = enrol_get_plugins(false);
    $methods = [];
    foreach ($list as $instance) {
        $enrol = $instance->get_name();
        if ($enrol == 'manual') {
            continue; // This is a forced default.
        }
        $methods[$enrol] = get_string('pluginname', 'enrol_' . $enrol);
    }
    return $methods;
}

/**
 * Load list of course enrolment methods (except manual, this is defaulted).
 *
 * @param int $courseid
 * @return array list of enrolment methods.
 */
function local_mass_enroll_get_course_enrolment_methods($courseid) {
    $config = get_config('local_mass_enroll');
    if (empty($config->allowedunenrolmentmethods)) {
        return [];
    }
    $extraenrolplugins = explode(',', $config->allowedunenrolmentmethods);
    $instances = mass_enroll_find_instances($courseid, $extraenrolplugins);
    $result = [];
    foreach ($instances as $instance) {
        $result[$instance->enrol] = get_string('pluginname', 'enrol_'.$instance->enrol);
    }
    return $result;
}
