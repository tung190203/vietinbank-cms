<?php
header('Access-Control-Allow-Origin: *');
// The file
// $image_path = 'https://web-1.vrplus.info/uploads/images/images-20231115T142949Z-001/chan_gio_quay/1.png';
if(!isset($_GET['image_path'])){
    exit();
}
if(isset($_GET['image_width'])){
    $new_width = $_GET['image_width'];
}else{
    $new_width = 300;
}

$image_path = $_GET['image_path'];
header('Content-type: image/jpeg');
// imagejpeg(imagecreatefrompng('ntbinh.png'));
imagejpeg(imagecreatefrompng($image_path));