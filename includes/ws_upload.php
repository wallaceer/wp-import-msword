<?php
include __DIR__ . "/../includes/ws_load.php";
if(in_array($_FILES["upfile"]['type'], $files_type_admitted)) {
    $source = $_FILES["upfile"]["tmp_name"];
    $destination = $_FILES["upfile"]["name"];
    if(move_uploaded_file($source, $dir.$destination)) echo "OK";
    else echo "KO";
}
