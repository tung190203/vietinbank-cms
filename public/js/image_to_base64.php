<?php
header('Access-Control-Allow-Origin: *');
// The file
// $image_path = 'https://web-1.vrplus.info/uploads/images/images-20231115T142949Z-001/chan_gio_quay/1.png';
if(!isset($_GET['image_path'])){
    exit();
}
// echo $_SERVER["HTTP_HOST"];
$image_path = str_replace($_SERVER["HTTP_HOST"].'/uploads','../uploads',$_GET['image_path']) ;
$url_explode = explode($_SERVER["HTTP_HOST"].'/uploads',$_GET['image_path']);
if(count($url_explode) == 1){
    $image_path = $_GET['image_path']; 
}else{
    $image_path = '../uploads'.$url_explode[1];
}

if(   !file_exists($image_path) ){
    echo '!file_exists';
    exit();
}
// exit();
// $image_name = 'pexels-paula-schmidt-353488-963486.jpg';
// $image_name = 'ong-pham-anh-tuan-pho-tgd-nhctvn-8-2008-6-2009--pho-tgd-vietinbank-7-2009-8-2014.png';
// $image_path = '../uploads/files/'.$image_name;

function image_resize($image_path,$new_width){
    list($width, $height) = getimagesize($image_path);
    if((float)$width > (float)$new_width){
        $new_width = (int)$new_width;
        $ratio = $width/$height;
        $new_height = (int)($new_width/$ratio);
        $image_mime = image_type_to_mime_type(exif_imagetype($image_path));
        $imge_return_function_name = str_replace("/","",$image_mime);
        $imagecreatefrom_x = str_replace("/","createfrom",$image_mime);
        $src = $imagecreatefrom_x($image_path);
        $imgResized = imagescale($src , $new_width, $new_height);
        imagedestroy($src);
        imagedestroy($imgResized);
        $resized_data = $imge_return_function_name ($imgResized);
    }else{
        $new_width      = $width;
        $new_height     = $height;
        $resized_data   = file_get_contents($image_path);
    }
   
    return [
        "origin"    => [],
        "resized"   => [
            "width"     => $new_width,
            "height"    => $new_height,
            "data"      => $resized_data
        ]  
    ];
}

if(isset($_GET['image_width'])){
    $new_width = $_GET['image_width'];
}else{
    $new_width = "300";
}
ob_start (); 
    $image_resize = image_resize($image_path,$new_width);
    echo $image_resize["resized"]['data'];
    $image_data = ob_get_contents ();
ob_end_clean (); 

$image = [
    "width"         => $image_resize["resized"]["width"],
    "height"        => $image_resize["resized"]["height"],
    "base64_code"   => base64_encode ($image_data),
];
echo json_encode($image);
 
?>

