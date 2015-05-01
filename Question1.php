<?php
/* Question 1
 * 
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
          $description = 'Not found';
          if (!empty($item)) {
            $starting_char = substr($item, 0, 1);
            if (in_array($starting_char, ['R', 'C', 'M'])) {
              $pattern = null;
              $is_mixed = null;
              switch ($starting_char) {
                case 'R':
                  $pattern = '/^(R([1-9]|10)-([1-9]|10)|R([1-9]|10)[A-H])$/';
                  break;
                case 'C':
                  $pattern = '/^(C[2-7]-([1-9]|10)|C1-([6-9]|10)|C8-[1-4])$/';
                  break;
                case 'M':
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
              
              $match_result = preg_match($pattern, $item);
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

          $codes[] = ["code" => $item, "description" => $description];
          break;
      }
    }
  }
  
  return json_encode(array('codes' => $codes));
}

