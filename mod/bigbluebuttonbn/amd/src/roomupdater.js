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
 * JS room updater.
 *
 * @module      mod_bigbluebuttonbn/roomupdater
 * @copyright   2021 Blindside Networks Inc
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Pending from 'core/pending';
import Templates from "core/templates";
import {exception as displayException} from 'core/notification';
import {getMeetingInfo} from './repository';

let timerReference = null;
let timerRunning = false;
let pollInterval = 0;

const resetValues = () => {
    timerRunning = false;
    timerReference = null;
    pollInterval = 0;
};

/**
 * Start the information poller.
 * @param {Number} interval interval in miliseconds between each poll action.
 */
export const start = (interval) => {
    resetValues();
    timerRunning = true;
    pollInterval = interval;
    poll();
};

/**
 * Stop the room updater.
 */
export const stop = () => {
    if (timerReference) {
        clearTimeout(timerReference);
    }
    resetValues();
};

/**
 * Start the information poller.
 */
const poll = () => {
    if (!timerRunning || !pollInterval) {
        // The poller has been stopped.
        return;
    }
    updateRoom()
        .then(() => {
            timerReference = setTimeout(() => poll(), pollInterval);
            return true;
        })
        .catch();
};

/**
 * Update the room information.
 *
 * @returns {Promise}
 */
export const updateRoom = () => {
    const bbbRoomViewElement = document.getElementById('bigbluebuttonbn-room-view');
    if (bbbRoomViewElement === null) {
        return Promise.resolve(false);
    }

    const bbbId = bbbRoomViewElement.dataset.bbbId;
    const groupId = bbbRoomViewElement.dataset.groupId;

    const pendingPromise = new Pending('mod_bigbluebuttonbn/roomupdater:updateRoom');

    return getMeetingInfo(bbbId, groupId)
        .then(data => {
            // Just make sure we have the right information for the template.
            data.haspresentations = !!(data.presentations && data.presentations.length);
            return Templates.renderForPromise('mod_bigbluebuttonbn/room_view', data);
        })
        .then(({html, js}) => Templates.replaceNode(bbbRoomViewElement, html, js))
        .then(() => pendingPromise.resolve())
        .catch(displayException);
};
