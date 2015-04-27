<?php


//$input_array = [1, 1, 3, 5, 6, 8, 10, 11, 10, 9, 8, 9, 10, 11, 7];
//$input_array = [0,1,2,3,4,5,6,7,8,9,10];

//$output_array = findConsecutiveRuns($input_array);

//var_dump($output_array);

function findConsecutiveRuns($input) {
  if (empty($input)) {
    return null;
  }
  
  $in_run = false;
  $run_pos = null;
  $run_starts = array();
  for ($i = 0; $i < sizeof($input) - 1; $i++) {
    $current = $input[$i];
    $next = $input[$i+1];
    
    // We need to be careful as numerical 0 can evaluate to null in php
    if (($current == 0 || $current != null) && ($next == 0 || $next != null)) {
      if (!$in_run && abs($current - $next) == 1) {
        $in_run = true;
        if ($current - $next == 1) {
          $run_pos = true;
        } else {
          $run_pos = false;
        }
        $run_starts[] = $i;
      } elseif ($in_run) {
        if ($run_pos) {
          if ($current - $next == -1) {
            $run_pos = false;
            $run_starts[] = $i;
          } elseif (abs($current - $next) != 1) {
            $in_run = false;
            $run_pos = null;
          }

        } else {
         if ($current - $next == 1) {
            $run_pos = true;
            $run_starts[] = $i;
          } elseif (abs($current - $next) != 1) {
            $in_run = false;
            $run_pos = null;
          }
        }
      } // end of in_run
    } // end of current/next not null
  } // end of for loop
  
  if (!empty($run_starts)) {
    return $run_starts;
  }
  
  return null;
}