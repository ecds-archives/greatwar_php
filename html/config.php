<?php
/* Some minimal configuration for all wwi php pages */

// root dir/url where the website is
$basedir = "/home/httpd/html/rebecca/wwiweb";
$basehref = "http://reagan.library.emory.edu/rebecca/wwiweb";

print "<link rel='stylesheet' type='text/css' href='$basehref/wwi.css'>";

// add basedir to the include path (for header/footer files and lib directory)
set_include_path(get_include_path() . ":" . $basedir . ":" . "$basedir/lib");

?>