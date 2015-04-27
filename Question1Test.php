<?php
/* Unit Tests for Question 1
 * Uses the findCodeDescriptions() function
 *
 * @author Patrick Dunn <pdunn1327@gmail.com>
 * (c) 2015 Patrick Dunn
 */
require_once 'Question1.php';

class Question1Test extends \PHPUnit_Framework_TestCase {
  
  /*
   * Tests the situation where the input is a null value
   */
  public function testNullInput() {
    $expected_output = json_encode(["codes" => []]);
    
    $input = null;
    $this->assertEquals($expected_output, findCodeDescriptions($input));
  }
  
  /*
   * Tests the situation where the input is an empty array
   */
  public function testEmptyArrayInput() {
    $expected_output = json_encode(["codes" => []]);
    
    $input = array();
    $this->assertEquals($expected_output, findCodeDescriptions($input));
  }
  
  /*
   * Tests multiple valid inputs and a base case where the code is invalid
   */
  public function testBaseCaseCodes() {
    $expected_output = json_encode(
      [
        "codes" => [
          ["code" => "R3-2", "description" => "General Residence Districts"],
          ["code" => "C1-6", "description" => "Commercial Districts"],
          ["code" => "M1-1", "description" => "Manufacturing Districts"],
          ["code" => "M1-1/R5", "description" => "Mixed Manufacturing & Residential Districts"],
          ["code" => "BPC", "description" => "Battery Park City"],
          ["code" => "PARK", "description" => "New York City Parks"],
          ["code" => "PARKNYS", "description" => "New York State Parks"],
          ["code" => "PARKUS", "description" => "United States Parks"],
          ["code" => "ZNA", "description" => "Zoning Not Applicable"],
          ["code" => "ZR 11-151", "description" => "Special Zoning District"],
          ["code" => "XXX", "description" => "Not found"]
        ]
      ]
    );
    
    $input = ['R3-2','C1-6','M1-1','M1-1/R5','BPC','PARK','PARKNYS','PARKUS','ZNA','ZR 11-151','XXX'];
    
    $this->assertEquals($expected_output, findCodeDescriptions($input));
  }
  
  /*
   * Tests the situations where the codes are slightly off by one 
   * or have extraneous characters, as if someone had mistyped them
   */
  public function testEdgeCaseCodes() {
    $expected_output = json_encode(
      [
        "codes" => [
          ["code" => "R0-1", "description" => "Not found"],
          ["code" => "R11H", "description" => "Not found"],
          ["code" => "C1-5", "description" => "Not found"],
          ["code" => "C8-5", "description" => "Not found"],
          ["code" => "M1-0", "description" => "Not found"],
          ["code" => "M4-3", "description" => "Not found"],
          ["code" => "M1-1/R4", "description" => "Not found"],
          ["code" => "M1-6/R10H", "description" => "Not found"],
          ["code" => "R5", "description" => "Not found"],
          ["code" => "M1-1/C1-4", "description" => "Not found"],
          ["code" => "M", "description" => "Not found"],
          ["code" => "C", "description" => "Not found"],
          ["code" => "R", "description" => "Not found"],
          ["code" => "PARKS", "description" => "Not found"],
        ]
      ]
    );
    
    $input = ['R0-1','R11H','C1-5','C8-5','M1-0','M4-3',
              'M1-1/R4','M1-6/R10H','R5','M1-1/C1-4','M','C','R','PARKS'];
    
    $this->assertEquals($expected_output, findCodeDescriptions($input));
  }
}