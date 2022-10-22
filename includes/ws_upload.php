<?php
require __DIR__ . "/../../../../wp-load.php";
$upload_dir   = wp_upload_dir();
$source = $_FILES["upfile"]["tmp_name"];
$destination = $_FILES["upfile"]["name"];
move_uploaded_file($source, $upload_dir['basedir']."/word/".$destination);
echo "OK";