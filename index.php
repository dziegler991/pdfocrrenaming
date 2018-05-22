<!DOCTYPE html>
<html lang="en" >

<head>
 <meta charset="UTF-8">
  <title>PDF OCR Renaming Tool</title>
  <link rel="icon" href="favicon.ico" type="image/x-icon"> 
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">  
  <link rel="stylesheet" href="css/style.css">
</head>

<h1>PDF Letter Renaming <span>For Thank You letters</span></h1>
<form action="" size:  method="post" enctype="multipart/form-data">
<div class="custom-file-upload">
<input type="file" accept=".pdf" position=relative  name="file[]"       multiple />
</div>
<br><br><br>
<input type="text" name="prefix" placeholder="Enter PDF Prefix"> 
<br><br><br>
<input type="submit" name="upload" value="Rename & Zip"/>

<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script  src="js/index.js"></script>
</body>
</html>

<?php

error_reporting(-1);
ini_set('display_errors', 'On');

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if (!empty($_POST['upload'])){
    $delnames = [];
    $zipname = "uploads/zip/".generateRandomString();
    $zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE); 
    foreach ($_FILES['file']['tmp_name'] as $file) {
        $directory = "/var/www/html/uploads/pdf/";
        $filecount = 0;
        exec("mogrify -format png -density 150 -depth 8 -strip -background white -alpha off $file");
        exec("tesseract -l eng {$file}.png $file");
        $arr = [];
        $contents = file("{$file}.txt");
        $contents1 = implode($contents);
        $targetdirectory = "/var/www/html/uploads/pdf/"; 
        preg_match_all("/^\S.*$/m", $contents1, $arr);
        $arrName = $arr[0][2] ;
        $name = str_replace(" ", " ", $arr[0][2]);
        $prefix = ($_POST['prefix']);
        rename($file, "{$targetdirectory}/{$prefix} {$name}.pdf");
        $zip->addFile("{$targetdirectory}/{$prefix} {$name}.pdf", "{$prefix} {$name}.pdf");
       // array_map('unlink', glob("{$targetdirectory}/{$prefix}_{$name}.pdf"));
        $delnames[] = "{$targetdirectory}/{$prefix} {$name}.pdf";
        echo "<br></br>";
         
    } 
    $zip->close(); 
    foreach ($delnames as $name) {
        unlink($name);
    }
    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename='files.zip'");
    header('Content-Length: ' . filesize($zipname));
    header("Location: $zipname");
}

?>
