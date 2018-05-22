<?php

$directory = "/var/www/html/uploads/pdf/";
$filecount = 0;

exec('mogrify -path /var/www/html/uploads/images -format tiff -density 300 -depth 8 -strip -background white -alpha off /var/www/html/uploads/pdf/*.pdf');

exec('for f in /var/www/html/uploads/images/*.tiff;do tesseract -l eng "$f" /var/www/html/uploads/text/"$(basename "$f" .tiff)";done');

echo '<form name="submit" method="post" action="readtext.php">';
 echo 'Read Text: <input type="submit" name="readtext" value="Read Text">';
 echo '</form>'; 

array_map('unlink', glob("/var/www/html/uploads/images/*.tiff"));
?>
