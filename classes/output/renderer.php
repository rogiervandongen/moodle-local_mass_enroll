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
 * @copyright   2015 onwards R.J. van Dongen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mass_enroll\output;

/**
 * local_mass_enroll_renderer
 *
 * @package     local_mass_enroll
 *
 * @copyright   R v Dongen
 * @author      R.J. van Dongen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Get tabs
     *
     * @param \context $context
     * @param string $selected
     * @param array $params page parameters
     * @return string
     */
    public function get_tabs($context, $selected, $params = []) {
        global $CFG;
        $tabs = [];

        if (has_capability('local/mass_enroll:enrol', $context)) {
            $enrol = new \moodle_url($CFG->wwwroot . '/local/mass_enroll/massenrol.php', $params);
            $tabs[] = new \tabobject('massenrol', $enrol, get_string('mass_enroll', 'local_mass_enroll'));
        }

        if (has_capability('local/mass_enroll:unenrol', $context)) {
            $unenrol = new \moodle_url($CFG->wwwroot . '/local/mass_enroll/massunenrol.php', $params);
            $tabs[] = new \tabobject('massunenrol', $unenrol, get_string('mass_unenroll', 'local_mass_enroll'));
        }

        return '<div class="groupdisplay">' . $this->tabtree($tabs, $selected) . '</div>';
    }

}
