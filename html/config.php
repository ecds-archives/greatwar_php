<?php

/* Configuration settings for entire site */

// root directory and url where the website resides
$basedir = "/home/httpd/html/rebecca/wwiweb";
$base_url = "http://reagan.library.emory.edu/rebecca/wwiweb/";

// add basedir to the php include path (for header/footer files and lib directory)
set_include_path(get_include_path() . ":" . $basedir . ":" . "$basedir/lib");

//shorthand for link to main css file
$csslink = "<link rel='stylesheet' type='text/css' href='$base_url/wwi.css'>";

/* tamino settings common to all pages
   Note: all pages use same database, but there are three different collections
 */
$tamino_server = "vip.library.emory.edu";
$tamino_db = "WW1";

?>