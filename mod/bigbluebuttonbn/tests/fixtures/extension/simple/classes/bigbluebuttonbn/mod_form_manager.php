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
namespace bbbext_simple\bigbluebuttonbn;

/**
 * Completion raise hand twice computation class
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class mod_form_manager extends \mod_bigbluebuttonbn\local\extension\mod_form_manager {

    /**
     * Add completion rule to the form.
     *
     * @return void
     */
    public function add_completion_rule() {
        // TODO: Implement add_completion_rule() method.
    }

    /**
     * Get completion elements for the form
     *
     * @return array
     */
    public function get_completion_elements_names(): array {
        return [];
    }

    /**
     * Get all added element names
     *
     * @return array
     */
    public function get_elements_names(): array {
        return [];
    }
}
