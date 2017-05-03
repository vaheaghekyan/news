<?php
$files = glob($_SERVER["DOCUMENT_ROOT"]."/uploads/temp/*"); // get all file names
foreach($files as $file)
{ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}