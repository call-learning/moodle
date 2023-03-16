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
namespace mod_bigbluebuttonbn\local\extension;

/**
 * Completion form and edition management
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
abstract class mod_form_manager {
    /**
     * @var \MoodleQuickForm $mform
     */
    protected $mform;

    /**
     * Constructor
     *
     * @param \MoodleQuickForm $mform the Moodle quick form belonging to this form
     */
    public function __construct(\MoodleQuickForm $mform) {
        $this->mform = $mform;
    }

    /**
     * Is the form element enabled
     *
     * @param array $currentdata
     * @return bool module name
     */
    public static function completion_rule_enabled(array $currentdata): bool {
        return false;
    }

    /**
     * Preprocess process data for completion
     *
     * @param array $defaultvalues
     * @param object $currentdata
     * @return void
     */
    public static function data_preprocessing(array &$defaultvalues, object $currentdata): void {

    }

    /**
     * Post process data for completion
     *
     * @param object $data
     * @return void
     */
    public static function data_postprocessing(object &$data): void {
        // Nothing to do here by default.
    }

    /**
     * Add additional form elements for this completion group (module editing form)
     *
     */
    abstract public function add_completion_rule();

    /**
     * Get completion element names
     *
     * @return array
     */
    abstract public function get_completion_elements_names(): array;

    /**
     * Get all element names.
     * This is potentially a superset of get_completion_elements_names.
     *
     * @return array
     */
    abstract public function get_elements_names(): array;

    /**
     * Form adjustments after setting data
     *
     * @return void
     */
    public function definition_after_data(): void {
        // Nothing to do here by default.
    }
}
