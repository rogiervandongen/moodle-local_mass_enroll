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
 * The csv processor base.
 *
 * File         csvbase.php
 * Encoding     UTF-8
 *
 * @package     local_mass_enroll
 *
 * @copyright   2015 onwards R v Dongen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mass_enroll\local\processor;

defined('MOODLE_INTERNAL') || die('NO_ACCESS');

global $CFG;
require_once($CFG->dirroot . '/lib/csvlib.class.php');
require_once($CFG->dirroot . '/local/mass_enroll/lib.php');
require_once($CFG->dirroot . '/group/lib.php');

/**
 * The csv processor base.
 *
 * @package     local_mass_enroll
 *
 * @copyright   2015 onwards R v Dongen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class csvbase {

    /**
     * @var string
     */
    protected $file;

    /**
     * @var bool
     */
    protected $fileisfile;

    /**
     * @var int
     */
    protected $importid = 0;

    /**
     * @var \csv_import_reader
     */
    protected $importer = null;

    /**
     * @var array
     */
    protected $mappings;

    /**
     * @var bool
     */
    protected $testrun = false;

    /**
     * @var bool
     */
    protected $skipfirstrow = false;

    /**
     * @var stdClass
     */
    protected $course;
    /**
     * @var \context_course
     */
    protected $coursecontext;
    /**
     * @var \enrol_plugin
     */
    protected $enrolplugin;
    /**
     * @var stdClass
     */
    protected $enrolinstance;
    /**
     * @var array
     */
    protected $errors;
    /**
     * @var array
     */
    protected $messages;
    /**
     * @var array
     */
    protected $results;
    /**
     * @var bool
     */
    protected $mailreport = false;

    /**
     * Set column mappings.
     *
     * @param array $mappings
     * @return $this
     */
    public function set_mappings($mappings) {
        $this->mappings = $mappings;
        return $this;
    }

    /**
     * Set course
     *
     * @param stdClass $course
     * @return $this
     */
    public function set_course($course) {
        $this->course = $course;
        $this->coursecontext = \context_course::instance($course->id);
        return $this;
    }

    /**
     * Are we test running?
     *
     * @param bool $testrun
     * @return $this
     */
    public function set_testrun($testrun) {
        $this->testrun = $testrun;
        return $this;
    }

    /**
     * Are we sending a mail report?
     *
     * @param bool $mailreport
     * @return $this
     */
    public function set_mailreport($mailreport) {
        $this->mailreport = $mailreport;
        return $this;
    }

    /**
     * Create new instance
     *
     * @param mixed $fileorcontent
     * @param boolean $isfile
     * @param array $options
     * @param boolean $testrun
     */
    public function __construct($fileorcontent, $isfile = false, array $options = [], $testrun = false) {
        $this->file = $fileorcontent;
        $this->fileisfile = $isfile;
        $this->testrun = $testrun;
        $this->merge_options($options);
    }

    /**
     * Merge options
     *
     * @param array $options
     */
    protected function merge_options(array $options) {
        foreach ($options as $k => $v) {
            if (isset($this->options[$k])) {
                $this->options[$k] = $v;
            }
        }
    }

    /**
     * Map row data
     *
     * @param stdClass $row
     * @return stdClass
     */
    protected function map_row($row) {
        $dataobj = [];
        foreach ($row as $index => $value) {
            if (isset($this->mappings[$index])) {
                $field = $this->mappings[$index];
                $dataobj[$field] = $value;
            }
        }
        return (object)$dataobj;
    }

    /**
     * Initialize course
     */
    protected function initialise_course() {
        global $DB;
        $this->coursegroups = $DB->get_records_sql_menu('SELECT name, id FROM {groups} WHERE courseid = ?',
                [$this->course->id]);
        $this->coursegroupings = $DB->get_records_sql_menu('SELECT name, id FROM {groupings} WHERE courseid = ?',
                [$this->course->id]);
        $this->enrolplugin = enrol_get_plugin('manual');
        $this->enrolinstance = $DB->get_record('enrol', ['courseid' => $this->course->id, 'enrol' => 'manual']);
    }

    /**
     * Process CSV.
     *
     * @return void
     */
    public function process() {
        global $DB;
        // Initialise course data.
        $this->initialise_course();
        $this->errors = [];
        $this->messages = [];
        $this->results = [];

        // Initialise import reader.
        $this->importid = \csv_import_reader::get_new_iid('massenrol');
        $this->importer = new \csv_import_reader($this->importid, 'massenrol');

        // Load contents.
        if ($this->fileisfile) {
            $text = file_get_contents($this->file);
        } else {
            $text = $this->file;
        }

        // Let CSV importer read.
        $validator = null;
        if (!$this->importer->load_csv_content($text, $this->options['encoding'],
                $this->options['delimitername'], $validator, $this->options['enclosure'])) {
            $this->fail(get_string('invalidimportfile', 'tool_lpimportcsv'));
            $this->fail($this->importer->get_error());
            $this->importer->cleanup();
            return;
        }

        if (!$this->importer->init()) {
            $this->fail(get_string('invalidimportfile', 'tool_lpimportcsv'));
            $this->importer->cleanup();
            return;
        }

        if ($this->testrun) {
            // Test run: we'll start a transaction we shall never commit.
            $DB->start_delegated_transaction();
        }

        $i = 0;
        while ($row = $this->importer->next()) {
            if ($this->skipfirstrow && $i === 0) {
                continue;
            }
            if (empty($row)) {
                continue;
            }

            $dataobj = $this->map_row($row);
            $this->process_user($dataobj);

            $this->results[] = $dataobj;
        }

        $this->importer->close();
        $this->importer->cleanup(false); // Only currently uploaded CSV file.

        $this->after_processing_complete();
    }

    /**
     * Code to execute after processing is complete
     */
    abstract protected function after_processing_complete();

    /**
     * Process user data
     *
     * @param stdClass $dataobject
     * @return bool
     */
    abstract protected function process_user(&$dataobject): bool;

}
