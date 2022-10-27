<?php
include __DIR__ . "/../includes/ws_load.php";

/**
 * If user is not logged in the execution die
 */
if(wp_get_session_token() === ''){
    return 'KO';
}

/**
 * If user is logged in the execution start
 */
if(in_array($_FILES["upfile"]['type'], $files_type_admitted)) {
    $source = $_FILES["upfile"]["tmp_name"];
    $destination = $_FILES["upfile"]["name"];
    if(move_uploaded_file($source, $dir.$destination)) echo "OK";
    else echo "KO";
}
