<?php
/* Question 3
 *
 *
 *
 *
 */

// Variables
$street_number = '555';
$street_name = 'CALIFORNIA';
$street_suffix = 'ST';
$unit = null;
$block = null;
$lot = null;

// URLS
$url_sf_base = 'http://dbiweb.sfgov.org/';
$url_search = 'dbipts/?page=address';
if (!empty($street_number) && !empty($street_name) && !empty($street_suffix)) {
  $url_search .= '&StreetNumber=' . $street_number . '&StreetName=' . $street_name . '&StreetSuffix=' . $street_suffix;
  // TODO: need at add unit?   '&Unit=' . $unit;
} elseif (!empty($block) && !empty($lot)) {
  $url_search .= '&Block=' . $block . '&Lot=' . $lot;
} else {
  // big problem
  echo '{"message":"ERROR: Improper inputs. Must have Block & Lot or Street information."}';
  exit();
}

// let's initialize the curl request we're going to be using
$curl = curl_init();

/*

<a href="default.aspx?page=AddressData2&ShowPanel=EID" id="InfoReq1_lnkElectrical">

// curl_setopt($curl, CURLOPT_REFERRER, 'http://blah.blah.blah/')

// CURLOPT_REFERER

*/

// Set the curl options
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // returns HTML as a variable
//curl_setopt($curl, CURLOPT_REFERER, $url_sf_base . 'dbipts/Default2.aspx?page=addressquery');
curl_setopt($curl, CURLOPT_URL, $url_sf_base . $url_search);

//var_dump($url_sf_base . $url_search);

$result = curl_exec($curl);

curl_close($curl);

var_dump($result);

/*
cURL Functions
curl_close — Close a cURL session
curl_copy_handle — Copy a cURL handle along with all of its preferences
curl_errno — Return the last error number
curl_error — Return a string containing the last error for the current session
curl_escape — URL encodes the given string
curl_exec — Perform a cURL session
curl_file_create — Create a CURLFile object
curl_getinfo — Get information regarding a specific transfer
curl_init — Initialize a cURL session
curl_multi_add_handle — Add a normal cURL handle to a cURL multi handle
curl_multi_close — Close a set of cURL handles
curl_multi_exec — Run the sub-connections of the current cURL handle
curl_multi_getcontent — Return the content of a cURL handle if CURLOPT_RETURNTRANSFER is set
curl_multi_info_read — Get information about the current transfers
curl_multi_init — Returns a new cURL multi handle
curl_multi_remove_handle — Remove a multi handle from a set of cURL handles
curl_multi_select — Wait for activity on any curl_multi connection
curl_multi_setopt — Set an option for the cURL multi handle
curl_multi_strerror — Return string describing error code
curl_pause — Pause and unpause a connection
curl_reset — Reset all options of a libcurl session handle
curl_setopt_array — Set multiple options for a cURL transfer
curl_setopt — Set an option for a cURL transfer
curl_share_close — Close a cURL share handle
curl_share_init — Initialize a cURL share handle
curl_share_setopt — Set an option for a cURL share handle.
curl_strerror — Return string describing the given error code
curl_unescape — Decodes the given URL encoded string
curl_version — Gets cURL version information

*/