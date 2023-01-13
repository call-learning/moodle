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

namespace mod_bigbluebuttonbn\task;

use core\task\adhoc_task;
use core\task\manager;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\config;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use mod_bigbluebuttonbn\meeting;

/**
 * Refresh cached information for this specific meeting by calling the relevant get_meeting_info
 *
 * For this to work ok, we need to make sure that adhoc task are running on a low
 * latency mode: https://docs.moodle.org/401/en/Cron#Low_latency_adhoc_tasks
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 Laurent David, Blindside Networks Inc, <laurent at call-learning dot fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class refresh_meeting_info extends adhoc_task {
    /**
     * Run the migration task.
     */
    public function execute() {
        $data = $this->get_custom_data();
        $instance = instance::get_from_instanceid($data->instanceid);
        if (!empty($instance)) {
            if (isset($data->groupid)) {
                $instance->set_group_id($data->groupid);
            }
            mtrace('Getting latest meeting information for this instance: ' . $instance->get_cm()->name);
            // Get the current meeting info or cached version.
            $meeting = new meeting($instance);
            $meetingrunningbefore = $meeting->is_running();
            bigbluebutton_proxy::refresh_meeting_info_cache($instance->get_meeting_id());
            $meetingcurrentlyrunning = $meeting->is_running();
            if ($meetingrunningbefore && !$meetingcurrentlyrunning) {
                mtrace('Meeting for instance ' . $instance->get_cm()->name . ' has ended. Stop checking.');
            } else {
                $this->reschedule_self($data);
            }
        }
    }

    /**
     * Allow the task to reschedule itself if only this current active task exist in the queue.
     *
     * @param mixed $customdata
     * @return void
     */
    protected function reschedule_self($customdata) {
        global $DB;
        $record = manager::record_from_adhoc_task($this);
        $params = [$record->classname, $record->component, $record->customdata];
        $sql = 'classname = ? AND component = ? AND ' .
            $DB->sql_compare_text('customdata', \core_text::strlen($record->customdata) + 1) . ' = ?';
        if ($record->userid) {
            $params[] = $record->userid;
            $sql .= " AND userid = ? ";
        }
        $count = $DB->count_records_select('task_adhoc', $sql, $params);
        if ($count === 1) {
            // Then reschedule.
            $task = new self();
            $task->set_custom_data($customdata);
            $now = time();
            $pollinterval = intval(config::get('poll_interval'));
            $task->set_next_run_time($now + $pollinterval);
            manager::queue_adhoc_task($task);
        }
    }

    /**
     * Get the name of the task for use in the interface.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('taskname:meeting_info_refresh', 'mod_bigbluebuttonbn');
    }
}
