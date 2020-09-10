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
 * Redis Cache Store - Settings
 *
 * @package   cachestore_redis
 * @copyright 2013 Adam Durana
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(
    new admin_setting_configtext(
        'cachestore_redis/test_server',
        get_string('test_server', 'cachestore_redis'),
        get_string('test_server_desc', 'cachestore_redis'),
        '',
        PARAM_TEXT,
        16
    )
);
// Check if we can use the php-redis sentinel implementation.
if (core\local\redis_helper::is_redis_sentinel_version()) {
    $settings->add(
        new admin_setting_configcheckbox(
            'cachestore_redis/is_sentinel',
            get_string('test_is_sentinel', 'cachestore_redis'),
            get_string('test_is_sentinel_desc', 'cachestore_redis'),
            false
        )
    );
    $settings->add(
        new admin_setting_configtext(
            'cachestore_redis/sentinel_master',
            get_string('test_sentinel_master', 'cachestore_redis'),
            get_string('test_sentinel_master_desc', 'cachestore_redis'),
            \core\local\redis_helper::DEFAULT_SENTINEL_MASTER_NAME
        )
    );
}
$settings->add(
    new admin_setting_configpasswordunmask(
        'cachestore_redis/test_password',
        get_string('test_password', 'cachestore_redis'),
        get_string('test_password_desc', 'cachestore_redis'),
        ''
    )
);

if (class_exists('Redis')) { // Only if Redis is available.

    $options = array(Redis::SERIALIZER_PHP => get_string('serializer_php', 'cachestore_redis'));

    if (defined('Redis::SERIALIZER_IGBINARY')) {
        $options[Redis::SERIALIZER_IGBINARY] = get_string('serializer_igbinary', 'cachestore_redis');
    }

    $settings->add(new admin_setting_configselect(
            'cachestore_redis/test_serializer',
            get_string('test_serializer', 'cachestore_redis'),
            get_string('test_serializer_desc', 'cachestore_redis'),
            Redis::SERIALIZER_PHP,
            $options
        )
    );
}
