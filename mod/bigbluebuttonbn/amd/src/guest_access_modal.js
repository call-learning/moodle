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
 * Javascript module for importing presets.
 *
 * @module      mod_bigbluebuttonbn/guest_access_modal
 * @copyright   2021 Blindside Networks Inc
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalFactory from 'core/modal_factory';
import Templates from 'core/templates';
import {get_string as getString} from 'core/str';

const selectors = {
    showGuestAccessButton: '[data-action="show-guest-access"]',
};

/**
 * Intialise the object
 *
 * @param {object} templateInfo
 * Initialize module
 */
export const init = (templateInfo) => {
    const showGuestAccessButton = document.querySelector(selectors.showGuestAccessButton);

    showGuestAccessButton.addEventListener('click', event => {
        event.preventDefault();
        ModalFactory.create({
            title: getString('guestaccess_title', 'mod_bigbluebuttonbn'),
            body: Templates.render('mod_bigbluebuttonbn/guest_access_info', templateInfo),
            }
        ).done((modal) => modal.show());
    });
};
