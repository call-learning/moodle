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
 * Redis helpers common to sessions and cache management
 *
 * @package    core
 * @copyright  2020 Laurent David <laurent@call-learning.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\local;

use core\session\exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Redis helper based session handler.
 *
 * @package    core
 * @copyright  2020 Laurent David <laurent@call-learning.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class redis_helper {

    /**
     * Default value for the master name.
     */
    const DEFAULT_SENTINEL_MASTER_NAME = 'mymaster';

    /**
     * Check if the Redis php extension has the RedisSentinel implementation
     *
     * @return bool
     */
    public static function is_redis_sentinel_version() {
        $version = phpversion('Redis');
        if (!$version || version_compare($version, '5.2') <= 0
            || !class_exists('RedisSentinel')
        ) {
            return false;
        }
        return true;
    }

    /**
     * Get the host/port for the master from sentinel
     *
     * @param string $host sentinel host
     * @param int $port sentinel port
     * @param string $mastername master name
     * @return array|bool
     * @throws exception
     */
    public static function get_redis_master_from_sentinel($host, $port, $mastername) {
        // TODO: support password auth for sentinel.
        $sentinel = new \RedisSentinel($host, $port);
        $masterinfo = $sentinel->getMasterAddrByName($mastername);
        if ($masterinfo && count($masterinfo) == 2) {
            return $masterinfo;
        } else {
            throw new exception('generalexceptionmessage',
                'error', '', null, 'Redis sentinel did not answer correctly.');
        }
    }
}
