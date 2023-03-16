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
namespace mod_bigbluebuttonbn;

use mod_bigbluebuttonbn\local\extension\action_url_mutate;
use mod_bigbluebuttonbn\local\extension\mod_form_manager;
use mod_bigbluebuttonbn\local\extension\mod_instance_helper;
use stdClass;

/**
 * Generic subplugin management helper
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class extension {
    /**
     * Plugin name for extension
     */
    const BBB_EXTENSION_PLUGIN_NAME = 'bbbext';

    /**
     * Get form managers for this module and extensions
     *
     * @param \MoodleQuickForm $mform we have to provide this parameter as it is a private member of the form
     * and we might modify the form content.
     * @param stdClass|null $instancedata
     * @return array
     */
    public static function get_mod_form_managers(\MoodleQuickForm $mform, ?stdClass $instancedata): array {
        $allformclasses = self::get_class_implementing(mod_form_manager::class);
        return array_map(function($fm) use ($mform, $instancedata) {
            return new $fm($mform, $instancedata);
        }, $allformclasses);
    }

    /**
     * Invoke a subplugin hook that implies a mutation of its parameters
     *
     * @param string $action
     * @param array $data
     * @param array $metadata
     * @return void
     */
    public static function mutate_action_url(string $action = '', array &$data = [], array &$metadata = []) {
        $allmutationclass = self::get_class_implementing(action_url_mutate::class);
        foreach ($allmutationclass as $mutationclass) {
            $mutationclass::execute($action, $data, $metadata);
        }
    }

    /**
     * Add instance processing
     * @param stdClass $data data to persist
     * @return void
     */
    public static function add_instance(\stdClass $data): void {
        $allsettingsclass = self::get_class_implementing(mod_instance_helper::class);
        $formmanagersclasses = array_unique($allsettingsclass);
        foreach ($formmanagersclasses as $fmclass) {
            $fmclass::add_instance($data);
        }
    }

    /**
     * Update instance processing
     * @param stdClass $data data to persist
     * @return void
     */
    public static function update_instance(\stdClass $data): void {
        $allsettingsclass = self::get_class_implementing(mod_instance_helper::class);
        $formmanagersclasses = array_unique($allsettingsclass);
        foreach ($formmanagersclasses as $fmclass) {
            $fmclass::update_instance($data);
        }
    }

    /**
     * Delete instance processing
     *
     * @param int $id instance id
     * @return void
     */
    public static function delete_instance(int $id): void {
        $allsettingsclass = self::get_class_implementing(mod_instance_helper::class);
        $formmanagersclasses = array_unique($allsettingsclass);
        foreach ($formmanagersclasses as $fmclass) {
            $fmclass::delete_instance($id);
        }
    }

    /**
     * Get all classes named on the base of this classname and implementing this class
     *
     * @param string $classname
     * @return array
     */
    protected static function get_class_implementing(string $classname) {
        $classbasename = (new \ReflectionClass($classname))->getShortName();
        $allsubs = \core_plugin_manager::instance()->get_plugins_of_type(self::BBB_EXTENSION_PLUGIN_NAME);
        $extensionclasses = [];
        foreach ($allsubs as $sub) {
            if ($sub->is_enabled()) {
                $targetclassname = "\\bbbext_{$sub->name}\\bigbluebuttonbn\\$classbasename";
                if (class_exists($targetclassname) && is_subclass_of($targetclassname, $classname)
                ) {
                    $extensionclasses[] = $targetclassname;
                }
            }
        }
        $extensionclasses = array_unique($extensionclasses);
        return $extensionclasses;
    }
}
