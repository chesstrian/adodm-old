<?php

/**
 * Function to compare dates.
 *
 * @param $stdate
 * First date, should be asigned for 'date()'.
 * @param $nddate
 * Second date, should be asigned for 'date()'.
 *
 * @return
 * Function will return a negative number if $stdate
 * is less than $nddate, it will return 0 if $stdate
 * is equal to $nddate, and it will return a positive
 * number if $stdate is greater than $nddate.
 */
function compareDate($stdate, $nddate) {
  $first = strtotime($stdate);
  $second = strtotime($nddate);

  return $first - $second;
}
?>
