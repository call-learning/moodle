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
 * Redis Sentinel based session handler.
 *
 * @package    core
 * @copyright  2020 Laurent David <laurent@call-learning.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\session;

defined('MOODLE_INTERNAL') || die();

/**
 * Redis Sentinel based session handler.
 *
 * This is based on the Redis handler. We use the sentinel to get the information about the current master and
 * do exactly the same as the redis handler for the rest
 *
 * @package    core
 * @copyright  2016 Russell Smith
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class redis_sentinel extends redis {
    /** @var string $sentinelhost save_path string */
    protected $sentinelhost = '127.0.0.1';
    /** @var int $port The port to connect to */
    protected $sentinelport = 26379;
    /** @var string $mastername the name of the master */
    protected $master_name;

    /**
     * Create new instance of handler.
     */
    public function __construct() {
        global $CFG;
        parent::__construct(); // We setup the usual variables for REDIS.

        $this->master_name = \core\local\redis_helper::DEFAULT_SENTINEL_MASTER_NAME;
        if (isset($CFG->session_redis_sentinel_host)) {
            $this->sentinelhost = $CFG->session_redis_sentinel_host;
        }

        if (isset($CFG->session_redis_sentinel_port)) {
            $this->sentinelport = (int) $CFG->session_redis_sentinel_port;
        }

        if (isset($CFG->session_redis_master_name)) {
            $this->master_name = $CFG->session_redis_master_name;
        }

        // TODO: for now there is no redis sentinel auth parameter in the RedisSentinel
        // function (see redis_sentinel.c in phpredis extension).
    }

    /**
     * Start the session.
     *
     * @return bool success
     */
    public function start() {
        $result = parent::start();
        return $result;
    }

    /**
     * Init session handler.
     */
    public function init() {
        // We need the first version that implements Sentinel.
        if (!\core\local\redis_helper::is_redis_sentinel_version()) {
            throw new exception('sessionhandlerproblem', 'error', '', null,
                'redis extension version must be at least 5.2 for Redis Sentinel');
        }
        try {
            list($this->host, $this->port) =
                \core\local\redis_helper::get_redis_master_from_sentinel($this->sentinelhost, $this->sentinelport,
                    $this->master_name);
        } catch (\Exception $e) {
            throw new exception('sessionhandlerproblem',
                'error', '', null, 'redis sentinel did not answer:' . $e->getMessage());

        }
        parent::init();
    }
}
