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
            $useradminnode->add(
                get_string('mass_enroll', 'local_mass_enroll'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'massenrols',
                new pix_icon('i/admin', '')
            );
        }
    }
    if ((bool)$config->enablemassunenrol) {
        if (has_capability('local/mass_enroll:unenrol', $context)) {
            $url = new moodle_url($CFG->wwwroot . '/local/mass_enroll/massunenrol.php', ['id' => $context->instanceid]);
            $useradminnode->add(
                get_string('mass_unenroll', 'local_mass_enroll'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'massunenrols',
                new pix_icon('i/admin', '')
            );
        }
    }
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
    [$insql, $params] = $DB->get_in_or_equal($extramethods, SQL_PARAMS_NAMED, 'enrol', true);
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
        $result[$instance->enrol] = get_string('pluginname', 'enrol_' . $instance->enrol);
    }
    return $result;
}
