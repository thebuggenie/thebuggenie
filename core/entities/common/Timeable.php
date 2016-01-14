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

    }