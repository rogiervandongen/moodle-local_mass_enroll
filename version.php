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
 * Version information
 *
 * File         version.php
 * Encoding     UTF-8
 *
 * @package     local_mass_enroll
 *
 * @copyright   1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
$plugin->version   = 2015092402;            // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires  = 2014051200;            // Moodle 2.7 onwards.
$plugin->component = 'local_mass_enroll';   // Full name of the plugin (used for diagnostics).
$plugin->maturity  = MATURITY_STABLE;       // Required for registering to Moodle's database of plugins.
$plugin->release   = '2.7.0 (Build 2015092402)';// Required for registering to Moodle's database of plugins.
