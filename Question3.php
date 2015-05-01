<?php
/* Question 3
 * This script makes a stream of curl requests, both GET and POST HTTP requests, to the pages at the San Francisco Department of Building Inspections.
 * It first navigates to the main search page and then initiates a search by Block and Lot, although it could be reconfigured to use the Street info as well.
 * Next it proceeds through the redirects and then navigates to the specific Electrical Permit information, before attempting to show all records and then
 * scrape them for the data, returning the rows from the dataset as a JSON object.
 * 
 * @author Patrick Dunn <pdunn1327@gmail.com>
 * (C) 2015 Patrick Dunn
 */

// Variables
$street_number = '555';
$street_name = 'CALIFORNIA';
$street_suffix = 'ST';
$unit = 0;
$block = "0259"; // currently these two (block and lot) are the indentifiers used. We could extend this script to use command line parameters
$lot = "026";    // for now, though, I just want to use these variables to keep things simple

// URL data that we'll reuse later
$url_sf_base = 'http://dbiweb.sfgov.org/';
$url_search_page = 'dbipts/default.aspx?page=AddressQuery';

// this isn't safe, but the HTML we receive back isn't always well formed
// and PHP doesn't like the POST parameter names that use $
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

// let's initialize the curl request we're going to be using
$curl = curl_init();

// Let's do a POST request to land on the initial search page and initialize our cookies and session data

$post_search_array = [
  "InfoReq1$cmdSearch" => "Search",
  "InfoReq1$txtBlock" => $block,
  "InfoReq1$txtLot" => $lot,
];

curl_setopt($curl, CURLOPT_URL, $url_sf_base . $url_search_page);
curl_setopt($curl, CURLOPT_POST, true); // set it to POST
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_search_array)); // set post fields
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // return the response as a string instead of to stdout
curl_setopt($curl, CURLOPT_COOKIEFILE, '/tmp/sitecompli.txt'); // set cookie file to given file
curl_setopt($curl, CURLOPT_COOKIEJAR, '/tmp/sitecompli.txt'); // set same file as cookie jar
$result = curl_exec($curl);

// Simulate making a search. Currently this uses just the Block and Lot information. It could do a search based on the other data but
//  the San Francisco site wants the search terms to either be the Street Data or the Block and Lot terms

curl_setopt($curl, CURLOPT_URL, 'http://dbiweb.sfgov.org/dbipts/?page=address&StreetNumber=&StreetName=&StreetSuffix=&Unit=&Block=' . $block . '&Lot=' . $lot);
curl_setopt($curl, CURLOPT_HTTPGET, true);
$result = curl_exec($curl);

// View the interstitial that would have been automatically redirected and parse for the link to the different permit data pages

$split_html = explode('"', $result);

$url_new = substr(rawurldecode($split_html[1]), 1); // decode and then remove leading slash "/"
curl_setopt($curl, CURLOPT_URL, $url_sf_base . $url_new);
$result = curl_exec($curl);

// We're almost there. Now we want to collect the link for the Electrical data and then use it to get to the Electrical permit data

$dom = new DOMDocument;
$dom->loadHTML($result);
$a_electrical_link = $dom->getElementByID('InfoReq1_lnkElectrical');

$url_electrical = $a_electrical_link->getAttribute('href');

$url_electrical_permits = $url_sf_base . 'dbipts/' . $url_electrical;

curl_setopt($curl, CURLOPT_URL, $url_electrical_permits);
$result = curl_exec($curl);

/* I'm going to leave this code here for now, but it definitely is having problems.

 I know this is an ASP.NET page and it's doing a Post Back to acces the rest of the data. I know that I need to have the five items filled out, 
 the __EVENTTARGET, __EVENTARGUMENT, __EVENTVALIDATION, __VIEWSTATE, and __VIEWSTATEGENERATOR. I was trying to get them working before attempting
 to scrape those values from the page. Currently it will not work even with the hardcoded values, I simply get the original page again as opposed
 to seeing all of the records on one screen. I know there are some PHP libraries out there that I could make use of to make the Post Back call, 
 but that doesn't really feel like it is in the spirit of the coding test to do so.  
 */
$new_post_string = '__EVENTTARGET=InfoReq1%24btnEidShowAll&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwUKLTE3MTc5MzE3Mg9kFgICAw9kFgICAQ9kFgJmD2QWDgIBDw8WAh4EVGV4dAURNTU1IENBTElGT1JOSUEgU1RkZAIDDw8WAh8ABQowMjU5IC8gMDI2ZGQCDQ8PFgIfAAUzKEVsZWN0cmljYWwgcGVybWl0cyBtYXRjaGluZyB0aGUgc2VsZWN0ZWQgYWRkcmVzcy4pZGQCDw8PFgIeB1Zpc2libGVnZBYCAgEPPCsACwEADxYIHghEYXRhS2V5cxYAHgtfIUl0ZW1Db3VudAIHHglQYWdlQ291bnQCSh4VXyFEYXRhU291cmNlSXRlbUNvdW50AoIEZBYCZg9kFg4CAg9kFhBmD2QWAmYPDxYEHwAFDUVXMjAxNTA0MjM0MjEeC05hdmlnYXRlVXJsBTpkZWZhdWx0LmFzcHg%2FcGFnZT1FSURfUGVybWl0RGV0YWlscyZQZXJtaXRObz1FVzIwMTUwNDIzNDIxZGQCAQ8PFgIfAAUEMDI1OWRkAgIPDxYCHwAFAzAyNmRkAgMPDxYCHwAFAzU1NWRkAgQPZBYCAgEPZBYCZg8VAgpDQUxJRk9STklBAlNUZAIFDw8WAh8ABQEwZGQCBg8PFgIfAAUFRklMRURkZAIHDw8WAh8ABQk0LzIzLzIwMTVkZAIDD2QWEGYPZBYCZg8PFgQfAAUNRVcyMDE1MDQyMzQyMR8GBTpkZWZhdWx0LmFzcHg%2FcGFnZT1FSURfUGVybWl0RGV0YWlscyZQZXJtaXRObz1FVzIwMTUwNDIzNDIxZGQCAQ8PFgIfAAUEMDI1OWRkAgIPDxYCHwAFAzAyNmRkAgMPDxYCHwAFAzU1NWRkAgQPZBYCAgEPZBYCZg8VAgpDQUxJRk9STklBAlNUZAIFDw8WAh8ABQEwZGQCBg8PFgIfAAUGSVNTVUVEZGQCBw8PFgIfAAUJNC8yMy8yMDE1ZGQCBA9kFhBmD2QWAmYPDxYEHwAFDUVXMjAxMTA3MjY4MDEfBgU6ZGVmYXVsdC5hc3B4P3BhZ2U9RUlEX1Blcm1pdERldGFpbHMmUGVybWl0Tm89RVcyMDExMDcyNjgwMWRkAgEPDxYCHwAFBDAyNTlkZAICDw8WAh8ABQMwMjZkZAIDDw8WAh8ABQM1NTVkZAIED2QWAgIBD2QWAmYPFQIKQ0FMSUZPUk5JQQJTVGQCBQ8PFgIfAAUBMGRkAgYPDxYCHwAFB0VYUElSRURkZAIHDw8WAh8ABQk0LzIzLzIwMTVkZAIFD2QWEGYPZBYCZg8PFgQfAAUNRVcyMDEyMDgyNDkwMR8GBTpkZWZhdWx0LmFzcHg%2FcGFnZT1FSURfUGVybWl0RGV0YWlscyZQZXJtaXRObz1FVzIwMTIwODI0OTAxZGQCAQ8PFgIfAAUEMDI1OWRkAgIPDxYCHwAFAzAyNmRkAgMPDxYCHwAFAzU1NWRkAgQPZBYCAgEPZBYCZg8VAgpDQUxJRk9STklBAlNUZAIFDw8WAh8ABQEwZGQCBg8PFgIfAAUHRVhQSVJFRGRkAgcPDxYCHwAFCTQvMjMvMjAxNWRkAgYPZBYQZg9kFgJmDw8WBB8ABQ1FVzIwMTEwMzE2MDAxHwYFOmRlZmF1bHQuYXNweD9wYWdlPUVJRF9QZXJtaXREZXRhaWxzJlBlcm1pdE5vPUVXMjAxMTAzMTYwMDFkZAIBDw8WAh8ABQQwMjU5ZGQCAg8PFgIfAAUDMDI2ZGQCAw8PFgIfAAUDNTU1ZGQCBA9kFgICAQ9kFgJmDxUCCkNBTElGT1JOSUECU1RkAgUPDxYCHwAFATBkZAIGDw8WAh8ABQhDT01QTEVURWRkAgcPDxYCHwAFCTQvMjEvMjAxNWRkAgcPZBYQZg9kFgJmDw8WBB8ABQ1FVzIwMTAxMTMwNDY0HwYFOmRlZmF1bHQuYXNweD9wYWdlPUVJRF9QZXJtaXREZXRhaWxzJlBlcm1pdE5vPUVXMjAxMDExMzA0NjRkZAIBDw8WAh8ABQQwMjU5ZGQCAg8PFgIfAAUDMDI2ZGQCAw8PFgIfAAUDNTU1ZGQCBA9kFgICAQ9kFgJmDxUCCkNBTElGT1JOSUECU1RkAgUPDxYCHwAFATBkZAIGDw8WAh8ABQdFWFBJUkVEZGQCBw8PFgIfAAUJNC8yMS8yMDE1ZGQCCA9kFhBmD2QWAmYPDxYEHwAFDUVXMjAxNTA0MDI4MjIfBgU6ZGVmYXVsdC5hc3B4P3BhZ2U9RUlEX1Blcm1pdERldGFpbHMmUGVybWl0Tm89RVcyMDE1MDQwMjgyMmRkAgEPDxYCHwAFBDAyNTlkZAICDw8WAh8ABQMwMjZkZAIDDw8WAh8ABQM1NTVkZAIED2QWAgIBD2QWAmYPFQIKQ0FMSUZPUk5JQQJTVGQCBQ8PFgIfAAUBMGRkAgYPDxYCHwAFBUZJTEVEZGQCBw8PFgIfAAUINC8yLzIwMTVkZAIRD2QWAgIBDzwrAAsAZAITD2QWAgIBDzwrAAsAZAIVD2QWAgIBDzwrAAsAZGTJG%2BG4ORsvQ9DhHIPTUzJS2v2tNg%3D%3D&__VIEWSTATEGENERATOR=EBE5C146&__EVENTVALIDATION=%2FwEWDAKYy%2FA2AprRxi4CmtG6LgKa0b4uAprRsi4CmtG2LgKa0aouAprRri4CmtGiLgKa0aYuApvRwi4C64jc6QskQh%2FxIOo%2FtBDQHTcVsQ9z06NL8g%3D%3D';\
curl_setopt($curl, CURLOPT_URL, $url_electrical_permits);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $new_post_string);
$result = curl_exec($curl);

// Now that we have the record data, pull the DOM and parse through it

$dom = new DOMDocument;
$dom->loadHTML($result);
$data_table = $dom->getElementByID('InfoReq1_dgEID'); // get just the data table
$rows = $data_table->getElementsByTagName('tr'); // and then get specifically the rows therein

// Now let's loop through the rows and pull out the data we want from the table data elements
$count = 0;
$json_array = array();
foreach ($rows AS $row) {
  if ($count >= 1) { // skip first line (the table header)
    
    // choose the individual table data elements
    $elements = $row->getElementsByTagName('td');
    $line_array = array();
    foreach ($elements AS $element) {
      // store them in an array for now
      $line_array[] = trim($element->textContent);
    }
    
    // The rows we want to process will have exactly 8 elements
    if (sizeof($line_array) == 8) {
      $json_array[] = [
        "Permit #" => $line_array[0],
        "Block" => $line_array[1],
        "Lot" => $line_array[2],
        "Street #" => $line_array[3],
        "Street Name" => $line_array[4],
        "Unit" => $line_array[5],
        "Current Stage" => $line_array[6],
        "Stage Date" => $line_array[7]
      ];
    } else { // no more rows to process
      break;
    }
  }
  $count++;
}

// close the cURL object
curl_close($curl);

// now encode the array as a json object
$json = json_encode($json_array);

// and finally, output to stdout
echo $json;
