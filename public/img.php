<?php
define("NVCMS_DIR", $_SERVER['DOCUMENT_ROOT'] . trim(dirname(" " . $_SERVER['SCRIPT_NAME'])));
define("NVCMS_URL", "http://" . $_SERVER['HTTP_HOST'] . trim(dirname(" " . $_SERVER['SCRIPT_NAME'])));
define("DIR_UPLOADS", NVCMS_DIR . "/uploads");

if (isset($_GET['pic'])) {
    $pic = isset($_GET["pic"]) ? $_GET["pic"] : "";
    if (isset($_GET["encode"]) && $_GET["encode"] == 1 && $pic != "") {
        $pic = base64_decode($_GET['pic']);
    }
    if ($pic == "") {
        $pic = "nopic.jpg";
    }

    $w = isset($_GET["w"]) ? $_GET["w"] : 148;
    $h = isset($_GET["h"]) ? $_GET["h"] : 101;
    $img = new img(DIR_UPLOADS . "/" . $pic, $w, $h);
    $img->resize($w, $h, false);
    $img->show();
}

function read_file($fid)
{
    $file = @fopen($fid, "rb");
    if ($file) {
        while (!feof($file)) {
            print(fread($file, 1024 * 8));
            flush();
            if (connection_status() != 0) {
                @fclose($file);
                die();
            }
        }
        @fclose($file);
    }
}

class img
{
    var $sourceFile = "";
    var $image = '';
    var $temp = '';
    var $thumbs = "";
    var $image_type = "jpg";

    function __construct($sourceFile, $width = 100, $height = 100)
    {
        //begin tuanta added
        $this->sourceFile = $sourceFile;
        if (!file_exists($sourceFile)) {
            $this->sourceFile = DIR_UPLOADS . "/nopic.jpg";
        }
        $tmp_thumbs_name = md5($this->sourceFile) . "_" . basename($this->sourceFile);
        $this->thumbs = "tn_" . $width . "x" . $height . "_" . $tmp_thumbs_name;
        if (file_exists(DIR_UPLOADS . "/thumbs/" . $this->thumbs)) {
            $this->sourceFile = DIR_UPLOADS . "/thumbs/" . $this->thumbs;
            header("location:" . NVCMS_URL . "/uploads/thumbs/" . $this->thumbs);
            //read_file($this->sourceFile);
            exit();
        }
        //end tuanta added
        if (file_exists($this->sourceFile)) {
            if (strtolower(substr($this->sourceFile, -3)) == "jpg" || strtolower(substr($this->sourceFile,
                    -4)) == "jpeg") {
                $this->image = imagecreatefromjpeg($this->sourceFile);
                $this->image_type = "jpg";
            } else {
                if (strtolower(substr($this->sourceFile, -3)) == "gif") {
                    $this->image = imagecreatefromgif($this->sourceFile);
                    $this->image_type = "gif";
                } else {
                    if (strtolower(substr($this->sourceFile, -3)) == "png") {
                        $this->image = imagecreatefrompng($this->sourceFile);
                        $this->image_type = "png";
                    }
                }
            }
        } else {
            $this->errorHandler();

        }
        return;
    }

    function resize($width = 100, $height = 100, $aspectradio = true)
    {
        //begin tuanta added
        if (file_exists(DIR_UPLOADS . "/thumbs/" . $this->thumbs)) {
            return;
        }
        //end tuanta added
        $o_wd = imagesx($this->image);
        $o_ht = imagesy($this->image);
        if (isset($aspectradio) && $aspectradio) {
            $w = round($o_wd * $height / $o_ht);
            $h = round($o_ht * $width / $o_wd);
            if (($height - $h) < ($width - $w)) {
                $width =& $w;
            } else {
                $height =& $h;
            }
        }
        if ($o_wd * $height >= $o_ht * $width) {
            $i = 1;
        } else {
            $i = 2;
        }
        $o_x = $o_y = 0;
        if ($i == 1) {
            $newheight = $height;
            $newwidth = round($o_wd * $newheight / $o_ht);
            $o_x = round(($newwidth - $width) / 2);
        } else {
            $newwidth = $width;
            $newheight = round($o_ht * $newwidth / $o_wd);
            $o_y = round(($newheight - $height) / 2);
        }
        $this->temp = imageCreateTrueColor($width, $height);
        imageCopyResampled($this->temp, $this->image, 0, 0, $o_x, $o_y, $newwidth, $newheight, $o_wd, $o_ht);
        $this->sync();
        //begin tuanta added
        $this->store(DIR_UPLOADS . "/thumbs/" . $this->thumbs);
        //end tuanta added
        return;
    }

    function sync()
    {
        $this->image =& $this->temp;
        unset($this->temp);
        $this->temp = '';
        return;
    }

    function show()
    {
        $this->_sendHeader();
        imagejpeg($this->image, null, 100);
        imagedestroy($this->image);
        return;
    }

    function _sendHeader()
    {
        if ($this->image_type == "jpg") {
            header('Content-Type: image/jpeg');
        } elseif ($this->image_type == "gif") {
            header('Content-Type: image/gif');
        } elseif ($this->image_type == "png") {
            header('Content-Type: image/png');
        }
    }

    function errorHandler()
    {
        echo "error";
        exit();
    }

    function store($file)
    {
        if ($this->image_type == "jpg") {
            imagejpeg($this->image, $file, 100);
        } elseif ($this->image_type == "gif") {
            imagegif($this->image, $file);
        } elseif ($this->image_type == "png") {
            imagepng($this->image, $file);
        }
        return;
    }

    function watermark($pngImage, $left = 0, $top = 0)
    {
        ImageAlphaBlending($this->image, true);
        $layer = ImageCreateFromPNG($pngImage);
        $logoW = ImageSX($layer);
        $logoH = ImageSY($layer);
        ImageCopy($this->image, $layer, $left, $top, 0, 0, $logoW, $logoH);
    }
}

?>
