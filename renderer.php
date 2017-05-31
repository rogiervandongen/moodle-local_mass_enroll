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
 * Renderer implementation for local_mass_enroll
 *
 * File         renderer.php
 * Encoding     UTF-8
 *
 * @package     local_mass_enroll
 *
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * local_mass_enroll_renderer
 *
 * @package     local_mass_enroll
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_mass_enroll_renderer extends \plugin_renderer_base {

    /**
     * return content for mass enrolment page.
     */
    public function page_mass_enrol() {
        global $CFG, $USER;
        require_once($CFG->libdir . '/csvlib.class.php');
        require_once($CFG->dirroot . '/local/mass_enroll/mass_enroll_form.php');
        require_once($CFG->dirroot . '/local/mass_enroll/lib.php');
        $course = $this->page->course;
        $context = $this->page->context;

        $mform = new mass_enroll_form(new moodle_url($CFG->wwwroot . '/local/mass_enroll/mass_enroll.php'), array(
            'course' => $course,
            'context' => $context
        ));

        $currenttab = 'mass_enroll';
        $out = '';
        $strinscriptions = get_string('mass_enroll', 'local_mass_enroll');
        if ($mform->is_cancelled()) {
            redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
        } else if ($data = $mform->get_data(false)) {

            $content = $mform->get_file_content('attachment');

            $iid = csv_import_reader::get_new_iid('uploaduser');
            $cir = new csv_import_reader($iid, 'uploaduser');
            $readcount = $cir->load_csv_content($content, $data->encoding, $data->delimiter_name);
            unset($content);

            $returnurl = $this->page->url;
            if ($readcount === false) {
                print_error('csvloaderror', '', $returnurl);
            } else if ($readcount == 0) {
                print_error('csvemptyfile', 'error', $returnurl);
            }

            $result = mass_enroll($cir, $course, $context, $data);

            $cir->close();
            $cir->cleanup(false); // Only currently uploaded CSV file.

            if ($data->mailreport) {
                $a = new stdClass();
                $a->course = $course->fullname;
                $a->report = $result;
                email_to_user($USER, $USER, get_string('mail_enrolment_subject', 'local_mass_enroll', $CFG->wwwroot),
                        get_string('mail_enrolment', 'local_mass_enroll', $a));
                $result .= "\n" . get_string('email_sent', 'local_mass_enroll', $USER->email);
            }

            $out .= $this->header();
            $out .= $this->get_tabs($context, $currenttab, array('id' => $course->id));
            $out .= $this->heading($strinscriptions);
            $out .= $this->box(nl2br($result), 'center');
            $out .= $this->continue_button($this->page->url); // Back to this page.
            $out .= $this->footer($course);
            return $out;
        }

        $out .= $this->header();
        $out .= $this->get_tabs($context, $currenttab, array('id' => $course->id));
        $out .= $this->heading_with_help($strinscriptions, 'mass_enroll', 'local_mass_enroll',
                'icon', get_string('mass_enroll', 'local_mass_enroll'));
        $out .= $this->box(get_string('mass_enroll_info', 'local_mass_enroll'), 'center');
        $out .= $mform->render();
        $out .= $this->footer($course);

        return $out;
    }

    /**
     * return content for mass unenrolment page.
     */
    public function page_mass_unenrol() {
        global $CFG, $USER;
        require_once($CFG->libdir . '/csvlib.class.php');
        require_once($CFG->dirroot . '/local/mass_enroll/mass_unenroll_form.php');
        require_once($CFG->dirroot . '/local/mass_enroll/lib.php');
        $course = $this->page->course;
        $context = $this->page->context;

        $mform = new mass_unenroll_form(new moodle_url($CFG->wwwroot . '/local/mass_enroll/mass_unenroll.php'), array(
            'course' => $course,
            'context' => $context
        ));

        $currenttab = 'mass_unenroll';
        $out = '';
        $strinscriptions = get_string('mass_unenroll', 'local_mass_enroll');
        if ($mform->is_cancelled()) {
            redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
        } else if ($data = $mform->get_data(false)) {

            $content = $mform->get_file_content('attachment');

            $iid = csv_import_reader::get_new_iid('uploaduser');
            $cir = new csv_import_reader($iid, 'uploaduser');
            $readcount = $cir->load_csv_content($content, $data->encoding, $data->delimiter_name);
            unset($content);

            $returnurl = $this->page->url;
            if ($readcount === false) {
                print_error('csvloaderror', '', $returnurl);
            } else if ($readcount == 0) {
                print_error('csvemptyfile', 'error', $returnurl);
            }

            $result = mass_unenroll($cir, $course, $context, $data);

            $cir->close();
            $cir->cleanup(false); // Only currently uploaded CSV file.

            if ($data->mailreport) {
                $a = new stdClass();
                $a->course = $course->fullname;
                $a->report = $result;
                email_to_user($USER, $USER, get_string('mail_unenrolment_subject', 'local_mass_enroll', $CFG->wwwroot),
                        get_string('mail_unenrolment', 'local_mass_enroll', $a));
                $result .= "\n" . get_string('email_sent', 'local_mass_enroll', $USER->email);
            }

            $out .= $this->header();
            $out .= $this->get_tabs($context, $currenttab, array('id' => $course->id));
            $out .= $this->heading($strinscriptions);
            $out .= $this->box(nl2br($result), 'center');
            $out .= $this->continue_button($this->page->url); // Back to this page.
            $out .= $this->footer($course);
            return $out;
        }

        $out .= $this->header();
        $out .= $this->get_tabs($context, $currenttab, array('id' => $course->id));
        $out .= $this->heading_with_help($strinscriptions, 'mass_unenroll', 'local_mass_enroll',
                'icon', get_string('mass_unenroll', 'local_mass_enroll'));
        $out .= $this->box(get_string('mass_unenroll_info', 'local_mass_enroll'), 'center');
        $out .= $mform->render();
        $out .= $this->footer($course);

        return $out;
    }

    /**
     * Get tabs
     *
     * @param \context $context
     * @param string $selected
     * @param array $params page parameters
     * @return string
     */
    protected function get_tabs($context, $selected, $params = array()) {
        global $CFG;
        $tabs = array();

        if (has_capability('local/mass_enroll:enrol', $context)) {
            $enrol = new \moodle_url($CFG->wwwroot . '/local/mass_enroll/mass_enroll.php', $params);
            $tabs[] = new \tabobject('mass_enroll', $enrol, get_string('mass_enroll', 'local_mass_enroll'));
        }

        if (has_capability('local/mass_enroll:unenrol', $context)) {
            $unenrol = new \moodle_url($CFG->wwwroot . '/local/mass_enroll/mass_unenroll.php', $params);
            $tabs[] = new \tabobject('mass_unenroll', $unenrol, get_string('mass_unenroll', 'local_mass_enroll'));
        }

        return '<div class="groupdisplay">' . $this->tabtree($tabs, $selected) . '</div>';
    }

}
