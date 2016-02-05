<?php

    namespace thebuggenie\core\entities\common;

    /**
     * Timeable item class
     *
     * @package thebuggenie
     * @subpackage core
     */
    class Timeable
    {

        /**
         * The time units
         *
         * @var array
         */
        public static $units = array('months', 'weeks', 'days', 'hours', 'minutes');

        /**
         * Get time units with points.
         *
         * @return array
         */
        public static function getUnitsWithPoints()
        {
            $units = static::$units;
            $units[] = 'points';

            return $units;
        }

        /**
         * Get time units with points filled with 0.
         *
         * @return array
         */
        public static function getZeroedUnitsWithPoints()
        {
            return array_fill_keys(self::getUnitsWithPoints(), 0);
        }

        /**
         * Get time units without.
         *
         * @param array $without
         *
         * @return array
         */
        public static function getUnitsWithout(array $without)
        {
            return array_diff(static::$units, $without);
        }

        /**
         * Formats hours and minutes
         *
         * @param $hours
         * @param $minutes
         *
         * @return integer|string
         */
        public static function formatHoursAndMinutes($hours, $minutes)
        {
            if (!$hours && !$minutes) return 0;
            if (!$minutes) return $hours;
            if (strlen($minutes) == 1) $minutes = '0' . $minutes;

            return $hours . ':' . $minutes;
        }

        /**
         * Formats log time
         *
         * @param      $log
         * @param      $previous_value
         * @param      $current_value
         * @param bool $append_minutes
         * @param bool $subtract_hours
         *
         * @return string
         */
        public static function formatTimeableLog($time, $previous_value, $current_value, $append_minutes = false, $subtract_hours = false)
        {
            if (! $append_minutes && ! $subtract_hours) return $time;

            $old_time = unserialize($previous_value);
            $new_time = unserialize($current_value);

            if ($append_minutes)
            {
                $old_time['hours'] += (int) floor($old_time['minutes'] / 60);
                $new_time['hours'] += (int) floor($new_time['minutes'] / 60);
            }
            if ($subtract_hours)
            {
                $old_time['minutes'] = $old_time['minutes'] % 60;
                $new_time['minutes'] = $new_time['minutes'] % 60;
            }

            return \thebuggenie\core\entities\Issue::getFormattedTime($old_time) . ' &rArr; ' . \thebuggenie\core\entities\Issue::getFormattedTime($new_time);
        }

    }