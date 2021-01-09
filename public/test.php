<?php
$static_file = 'd:\\test.html';
define('HTML_STATIC_FILE', $static_file);
if(!defined('HTML_STATIC_FILE')){
    echo 'nfo';
}else{

   // echo 'yecs'.HTML_STATIC_FILE;
    $b = HTML_STATIC_FILE;
    echo $b;
}
