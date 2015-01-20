<?php

    namespace thebuggenie\core\helpers;

    /**
     * Text diff class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Text diff class
     *
     * @package thebuggenie
     * @subpackage main
     */
    class TextDiff
    {

        function arrayDiff($old, $new)
        {
            $biggestMatch = 0;
            foreach ($old as $oldInd => $oldVal)
            {
                $newInds = array_keys($new, $oldVal);
                foreach ($newInds as $newInd)
                {
                    $matches[$oldInd][$newInd] = isset($matches[$oldInd - 1][$newInd - 1]) ? $matches[$oldInd - 1][$newInd - 1] + 1 : 1;
                    if ($matches[$oldInd][$newInd] > $biggestMatch)
                    {
                        $biggestMatch = $matches[$oldInd][$newInd];
                        $oldMax = $oldInd + 1 - $biggestMatch;
                        $newMax = $newInd + 1 - $biggestMatch;
                    }
                }
            }

            if ($biggestMatch === 0)
            {
                return array(array('-' => $old, '+' => $new));
            }

            return array_merge(
                    $this->arrayDiff(array_slice($old, 0, $oldMax), array_slice($new, 0, $newMax)),
                    array_slice($new, $newMax, $biggestMatch),
                    $this->arrayDiff(array_slice($old, $oldMax + $biggestMatch), array_slice($new, $newMax + $biggestMatch)));
        }

        function split($delimiters, $str)
        {
            return $delimiters ? preg_split("~(?<=[" . $delimiters . "])~", $str) : str_split($str);
        }

        function merge($array)
        {
            return implode("", $array);
        }

        function stringDiff($old, $new, $delimiters = " .\n")
        {
            $diff = $this->arrayDiff($this->split($delimiters, $old), $this->split($delimiters, $new));
            $newDiff = array();
            $newKey = 0;
            foreach ($diff as $key => $val)
            {
                if (is_array($val))
                {
                    if (isset($newDiff[$newKey]))
                    {
                        $newKey++;
                    }

                    $newDiff[$newKey]['+'] = $this->merge($val['+']);
                    $newDiff[$newKey]['-'] = $this->merge($val['-']);
                    $newKey++;
                }
                else
                {
                    if (!isset($newDiff[$newKey]))
                    {
                        $newDiff[$newKey] = "";
                    }

                    $newDiff[$newKey] .= $val;
                }
            }

            return $newDiff;
        }

        function sequentialChanges($diff)
        {
            $changes = array();
            $index = 0;
            foreach ($diff as $val)
            {
                if (is_array($val))
                {
                    if ($val['-'])
                    {
                        $changes[] = array("type" => "-", "val" => $val['-'], "pos" => $index);
                    }
                    if ($val['+'])
                    {
                        $changes[] = array("type" => "+", "val" => $val['+'], "pos" => $index);
                        $index += mb_strlen($val['+']);
                    }
                }
                else
                {
                    $index += mb_strlen($val);
                }
            }
            return $changes;
        }

        function renderDiff($diff)
        {
            $str = "";
            foreach ($diff as $val)
            {
                if (is_array($val))
                {
                    $del = $val['-'] !== array() ? "<del>" . $val['-'] . "</del>" : '';
                    $ins = $val['+'] !== array() ? "<ins>" . $val['+'] . "</ins>" : '';
                    $str .= $del . $ins;
                }
                else
                {
                    $str .= $val;
                }
            }
            return $str;
        }

        function renderChanges($changes, $str = "")
        {
            foreach ($changes as $change)
            {
                if ($change['type'] === "+")
                {
                    $str = substr_replace($str, $change['val'], $change['pos'], 0);
                }
                if ($change['type'] === "-")
                {
                    $str = substr_replace($str, "", $change['pos'], mb_strlen($change['val']));
                }
            }
            return $str;
        }

    }