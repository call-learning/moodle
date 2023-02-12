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
 * CLI script to retrieve some stats from Moodle and activities
 *
 * This is technically just a thin wrapper for {@link get_config()} and
 * {@link set_config()} functions.
 *
 * @package     core
 * @subpackage  cli
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
global $CFG;
require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');

global $DB;

cli_writeln("* Question types statistics");

$questiontypes = $DB->get_records_sql('SELECT q.qtype as id, COUNT(*) AS qcount FROM {question} q GROUP BY q.qtype');
cli_writeln(join(',', ['Question type', 'Question count']));
foreach($questiontypes as $qt) {
    cli_writeln(join(',', [
        $qt->id,
        $qt->qcount,
    ]));
}
exit();
$moduletypes = $DB->get_records_sql('SELECT mod.name as id, cm.module FROM {course_modules} cm 
LEFT JOIN {modules} mod ON mod.id = cm.module 
GROUP BY cm.module, mod.name');

$courseids = $DB->get_fieldset_select('course', 'id', '1=1');

$modulestats = array_fill_keys(array_map(
        function($mtype) {
            return $mtype->id;
        }, $moduletypes)
, null);
cli_writeln("* Course statistics");
cli_writeln("\t => Total courses: " . count($courseids));
cli_writeln("\t => Total visible courses:" . $DB->count_records('course', ['visible' => '1']));
cli_writeln("* Module statistics");
cli_writeln("\t => Total modules types: " . count($moduletypes));
cli_writeln("\t => Total modules:" . $DB->count_records('course_modules'));
cli_writeln("\t => Total visible modules:" . $DB->count_records('course_modules', ['visible' => '1']));

foreach ($moduletypes as $moduleid => $mtype) {
    $modulestats[$moduleid] = (object) [
        'modtype' => '',
        'configupdated' => 0,
        'filesupdated'=> 0,
        'instancecount' => 0,
        'fileareas' => []
    ];
    $modulestats[$moduleid]->modtype = $moduleid;
    $modulestats[$moduleid]->fileareas = $DB->get_fieldset_sql(
        'SELECT f.filearea FROM {files} f WHERE f.component = :component GROUP BY f.component, f.filearea',
            ['component' => "mod_{$moduleid}"]
    );
    foreach ($courseids as $cid) {
        $modinfo = course_modinfo::instance($cid);
        $cms = $modinfo->get_instances_of($moduleid);
        foreach ($cms as $cm) {
            $cm->is_visible_on_course_page();
            $modulestats[$moduleid]->instancecount ++;
            $mod = $DB->get_record($cm->modname, array('id' => $cm->instance), '*', MUST_EXIST);

            $component = 'mod_' . $cm->modname;

            // Check changes in the module configuration.
            if (isset($mod->timemodified)) {
                if ($modulestats[$moduleid]->configupdated < $mod->timemodified) {
                    $modulestats[$moduleid]->configupdated = $mod->timemodified;
                }
            }
            if (!empty($modulestats[$moduleid]->fileareas)) {
                $fs = get_file_storage();
                $files =
                    $fs->get_area_files($cm->context->id, $component, array_values($modulestats[$moduleid]->fileareas), false,
                        "filearea, timemodified DESC", false);
                foreach ($files as $file) {
                    if ($modulestats[$moduleid]->filesupdated < $file->get_timemodified()) {
                        $modulestats[$moduleid]->filesupdated = $file->get_timemodified();
                    }
                }
            }
        }
    }
    cli_writeln('Processing...'. $moduleid);
}


cli_writeln(join(',', ['Module Name', 'Module shortname', 'Count', 'Last updated', 'Config updated','Files updated']));
foreach($modulestats as $mstat) {
    $moduleinfo = [
        get_string("modulename", $mstat->modtype),
        $mstat->modtype,
        $mstat->instancecount,
        userdate(max($mstat->configupdated, $mstat->filesupdated), get_string('strftimedatefullshort')),
        userdate($mstat->configupdated, get_string('strftimedatefullshort')),
        userdate($mstat->filesupdated, get_string('strftimedatefullshort'))
    ];

    cli_writeln(
        join(',',$moduleinfo)
    );
}
