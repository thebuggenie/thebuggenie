<?php

if (! function_exists('str_replace_nth')) {
  /**
   * Replace nth occurrence of the search string with the replacement string.
   *
   * $nth is a zero based index.
   *
   * @param $search
   * @param $replace
   * @param $subject
   * @param $nth
   *
   * @return mixed
   */
  function str_replace_nth($search, $replace, $subject, $nth) {
    $found = preg_match_all('/' . preg_quote($search) . '/', $subject, $matches, PREG_OFFSET_CAPTURE);

    if (false !== $found && $found > $nth) {
      return substr_replace($subject, $replace, $matches[0][ $nth ][1], strlen($search));
    }

    return $subject;
  }
}
