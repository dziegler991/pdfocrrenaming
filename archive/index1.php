<html>
<!-- <style>
body {
    background-color: white;
    text-align: center;
    font-family: arial;
}
h1, h2 {
    color: black;
    font-family: arial;
    text-align: center;
}
fieldset { 
    display: block;
    width: 45%;
    font-family: arial;
    margin-left: 2px;
    margin-right: 2px;
    padding-top: 0.35em;
    padding-bottom: 0.625em;
    padding-left: 0.75em;
    padding-right: 0.75em;
    border: 2px groove (internal value);
    background-color: white;
}
input[type=text] {
    width: 30%;
    padding: 12px 12px;
    margin: 3px 0;
    box-sizing: border-box;
    font-family: arial;
}
input[type=text]:focus {
    border: 3px solid #555;
    font-family: arial;
}
input[type=button], input[type=submit], input[type=reset] {
    background-color: #7a8ba5;
    border: none;
    color: white;
    padding: 16px 32px;
    text-decoration: none;
    margin: 4px 2px;
    cursor: pointer;
    font-family: arial;
   }
.file-upload {
  white-space: nowrap;
}

.file-upload > input[type="file"] {
  opacity: 0;            /* Set this to 0.2 and background-color: red to see how it overlays */
  width: 10em;           /* Enough width to cover the button and the label */
}

.file-upload button {
  margin-left: -10.3em;  /* move the button left to overlay the transparent file input */
  padding: 1em 1.5em 1em 1.3em;
  color: #fff;
  font-family: arial;
  background: #7a8ba5;
  border-width: 0;
  transition: background-color 300ms ease-out;
}

.file-upload button:hover {  
  background-color: #0078a0;
}

</style> -->
<head>
<h1>Upload PDF for OCR</h1>
</head>
<body>
<h2>Select your PDF file(s)to be uploaded:</h2>
<br> <form action="" size:  method="post" enctype="multipart/form-data">
<!--<div class="file-upload">-->
  <input type="file" accept=".pdf" position=relative  name="file[]" multiple />
  <!--<button>Browse</button> 
  <br><br><label>No File</label>
<div> -->

<p><input type="text" name="prefix" placeholder="Enter a Prefix for PDF Files (ex. USTDA Smart Grid RTM -)"> </p>
<!--<div class="file-submit">--> 
  <input type="submit" name="upload" button value="Upload, Rename, and Zip" />
<!--</div>-->
</form>
</fieldset>
<!--<script src="/var/www/html/scripts/main.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> -->
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
