<?php

/* Configuration settings for entire site */

// root directory and url where the website resides
$basedir = "/home/httpd/html/rebecca/wwiweb";
$server = "reagan.library.emory.edu";
$base_path = "/rebecca/wwiweb";
$base_url = "http://$server/$base_path/";

// add basedir to the php include path (for header/footer files and lib directory)
set_include_path(get_include_path() . ":" . $basedir . ":" . "$basedir/lib");

//shorthand for link to main css file
$cssfile = "wwi.css";
$csslink = "<link rel='stylesheet' type='text/css' href='$base_url/$cssfile'>";

/* tamino settings common to all pages
   Note: all pages use same database, but there are three different collections
 */
$tamino_server = "vip.library.emory.edu";
$tamino_db = "WW1";

?>