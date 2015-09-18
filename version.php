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
 * @copyright   2012 onwards Patrick Pollet {@link mailto:pp@patrickpollet.net
 * @copyright   2015 onwards Rogier van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
$plugin->version   = 2012101009;        // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires  = 2011120100;        // Moodle 2.2 onwards
$plugin->component = 'local_mass_enroll';       // Full name of the plugin (used for diagnostics)
$plugin->maturity = MATURITY_STABLE; // required for registering to Moodle's database of plugins
$plugin->release = '2.2 (Build 20121001)';  // required for registering to Moodle's database of plugins
