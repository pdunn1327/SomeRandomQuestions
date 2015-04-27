<?php
/* Unit Tests for Question 2
 * Tests the findConsecutiveRuns() function
 *
 * @author Patrick Dunn <pdunn1327@gmail.com>
 * (c) 2015 Patrick Dunn
 */
require_once 'Question2.php';

class Question2Test extends \PHPUnit_Framework_TestCase {
  
  /*
   * Tests the situation where the input is a null value
   */
  public function testNullInput() {
    $expected_output = null;
    
    $input = null;
    $this->assertEquals($expected_output, findConsecutiveRuns($input));
  }
  
  /*
   * Tests the situation where the input is an empty array
   */
  public function testEmptyArrayInput() {
    $expected_output = null;
    
    $input = array();
    $this->assertEquals($expected_output, findConsecutiveRuns($input));
  }
  
  /*
   * Tests multiple valid inputs and a base case where the code is invalid
   */
  public function testValidRuns() {
    $expected_output = [3,6,7,10];
    $input = [1, 1, 3, 5, 6, 8, 10, 11, 10, 9, 8, 9, 10, 11, 7];
    $this->assertEquals($expected_output, findConsecutiveRuns($input));
    
    $expected_output = [0];
    $input = [0,1,2,3,4,5,6,7,8,9,10];
    $this->assertEquals($expected_output, findConsecutiveRuns($input));
    
    $expected_output = [0];
    $input = [-1,-2,-3,-4,-5,-6,-7,-8,-9,-10];
    $this->assertEquals($expected_output, findConsecutiveRuns($input));
    
    $expected_output = [0,1,2,3,4,5,6];
    $input = [1,2,1,2,1,2,1,2];
    $this->assertEquals($expected_output, findConsecutiveRuns($input));
    
    //TODO: if have enough time, create a random run generator
  }
  
  /*
   * Tests the situations where the codes are slightly off by one 
   * or have extraneous characters, as if someone had mistyped them
   */
  public function testNoValidRuns() {
    $expected_output = null;
    
    $input = [1,3,5,7,9,11,-2,-4,-6,-8];
    $this->assertEquals($expected_output, findConsecutiveRuns($input));
    
    $input = [0,0,0,0,0,0,0];
    $this->assertEquals($expected_output, findConsecutiveRuns($input));
    
    // next create and test sets of non-consecutive numbers
    for ($i = 0; $i < 10; $i++) {
      $input = array();
      $input[] = rand(0, getrandmax());
      for ($j = 1; $j < 99; $j++) {
        $distance = rand(2, getrandmax());
        if ((rand(1, 2) % 2 == 0)) {
          $input[] = $input[j-1] + $distance;
        } else {
          $input[] = $input[j-1] - $distance;
        }
      }
      
      $this->assertEquals($expected_output, findConsecutiveRuns($input));
    }
    
  }
}