<?php
/* Question 2
 * This algorithm detects sequential runs, both positive and negative
 * For example, [1,2,3] is a run that starts at index 0 in a positive direction
 * and [2,1,0] is a run that starts at index 0 but in a negative direction
 * If we had an input like [2,3,2,9,8,9] then we'd have a number of different runs
 * starting at indexes 0,1,3,4, with them being positive, negative, negative, positive in directions respectively
 * 
 * @author Patrick Dunn <pdunn1327@gmail.com>
 * (C) 2015 Patrick Dunn
 */

// I've kept here the initial test values I used when coding, but I commented them out for the purposes of the unit tests

//$input_array = [1, 1, 3, 5, 6, 8, 10, 11, 10, 9, 8, 9, 10, 11, 7];
//$input_array = [0,1,2,3,4,5,6,7,8,9,10];

//$output_array = findConsecutiveRuns($input_array);

//var_dump($output_array);

function findConsecutiveRuns($input) {
  if (empty($input)) {
    return null;
  }
  
  // some variables to keep track of our status
  $in_run = false;
  $run_pos = null;
  $run_starts = array();
  for ($i = 0; $i < sizeof($input) - 1; $i++) {
    $current = $input[$i]; // look at where we are now
    $next = $input[$i+1];  // as well as where we will be in the next index
    
    // We need to be careful as numerical 0 can evaluate to null in php
    if (($current == 0 || $current != null) && ($next == 0 || $next != null)) {
      // is there a difference of 1? were we not in a run before?
      if (!$in_run && abs($current - $next) == 1) {
        $in_run = true; // we know we're in the middle of a run
        if ($current - $next == 1) {
          $run_pos = true; // we're traveling in a positive direction
        } else {
          $run_pos = false; // we're traveling in a negative direction
        }
        $run_starts[] = $i; // log where we just started this new run
      } elseif ($in_run) { // but if we were in a run, let's do some more evaluation
        if ($run_pos) { // were we traveling in a positive direction?
          if ($current - $next == -1) { // if we're now going negatively
            $run_pos = false; // then make note of that
            $run_starts[] = $i; // and add the current index as the start of a new run
          } elseif (abs($current - $next) != 1) { // or are we suddenly traveling by more than 1?
            $in_run = false; // then update our tracking variables
            $run_pos = null;
          }

        } else { // or were we traveling in a run in a negative direction?
         if ($current - $next == 1) { // so we were going negative but now we just turned around
            $run_pos = true; // so make note of the direction
            $run_starts[] = $i; // and then log where this new run started
          } elseif (abs($current - $next) != 1) { // or perhaps once again we are jumping around by more than 1
            $in_run = false; // so update our tracking variables
            $run_pos = null;
          }
        }
      } // end of in_run
    } // end of current/next not null
  } // end of for loop
  
  // as long as we have something, then return it
  if (!empty($run_starts)) {
    return $run_starts;
  }
  
  // otherwise, simply return a null value
  return null;
}