<?php
/* Question 1
 * Takes in an array as input of possible Zoning Codes for NYC.
 * It will attempt to find matches and if so will display what the description for the code is.
 * Otherwise, it will return "Not found"
 * 
 * @author Patrick Dunn <pdunn1327@gmail.com>
 * (C) 2015 Patrick Dunn
 */

/*
$input = ['R7A', 'R8A', 'C4-4A', 'M3-2', 'R8B', 'C1-6A', 'R7B', 'R8X', 'C1-7A', 'PARK',
  'C1-9A', 'R6', 'C1-7', 'C2-6', 'R10', 'C4-5', 'C6-3X', 'C1-6', 'M1-3/R9', 'C6-2M', 'C6-4M',
  'M2-4', 'M1-5/R7X'
  ];

$result = findCodeDescriptions($input);
var_dump($result);
*/

function findCodeDescriptions($input_array) {
  $codes = array();
  if (!empty($input_array)) {
    foreach ($input_array AS $item) {
      // handle the easy cases first
      switch ($item) {
        case 'BPC':
          $codes[] = ["code" => $item, "description" => "Battery Park City"];
          break;
        case 'PARK':
          $codes[] = ["code" => $item, "description" => "New York City Parks"];
          break;
        case 'PARKNYS':
          $codes[] = ["code" => $item, "description" => "New York State Parks"];
          break;
        case 'PARKUS':
          $codes[] = ["code" => $item, "description" => "United States Parks"];
          break;
        case 'ZNA':
          $codes[] = ["code" => $item, "description" => "Zoning Not Applicable"];
          break;
        case 'ZR 11-151':
          $codes[] = ["code" => $item, "description" => "Special Zoning District"];
          break;
        default:
          // now let's look at the more complicated cases. begin assuming we will not find a match
          $description = 'Not found';
          if (!empty($item)) {
            // look at the starting character as that can easily determine what will come next
            $starting_char = substr($item, 0, 1);
            if (in_array($starting_char, ['R', 'C', 'M'])) {
              $pattern = null;
              $is_mixed = null;
              switch ($starting_char) {
                case 'R': // if it's residential...
                  $pattern = '/^(R([1-9]|10)-([1-9]|10)|R([1-9]|10)[A-H])$/';
                  break;
                case 'C': // or is it commercial?
                  $pattern = '/^(C[2-7]-([1-9]|10)|C1-([6-9]|10)|C8-[1-4])$/';
                  break;
                case 'M': // or is it one of the Manufacturing or mixed zones?
                  if (strpos($item, 'R') === false) {
                    // not mixed
                    $is_mixed = false;
                    $pattern = '/^(M[1-2]-([1-9]|10)|M3-[1-2])$/';
                  } else {
                    // mixed
                    $is_mixed = true;
                    $pattern = '/^M1-[1-6]\/R([5-9]|10)$/';
                  }
                  break;
              }
              
              // now that we've determined which pattern to use for these more complicated patterns, let's test it out
              $match_result = preg_match($pattern, $item);
              // did we find a match? was it definitely just one match? if so, fill in the description
              if ($match_result !== false && $match_result == 1) {
                switch ($starting_char) {
                  case 'R':
                    $description = 'General Residence Districts';
                    break;
                  case 'C':
                    $description = 'Commercial Districts';
                    break;
                  case 'M':
                    if ($is_mixed) {
                      $description = 'Mixed Manufacturing & Residential Districts';
                    } else {
                      $description = 'Manufacturing Districts';
                    }
                    break;
                }
              }
            }
          }

          // place the description into the array that we'll encode as a JSON later
          $codes[] = ["code" => $item, "description" => $description];
          break;
      }
    }
  }
  
  return json_encode(array('codes' => $codes));
}

