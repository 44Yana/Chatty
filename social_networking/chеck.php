<?php
echo "<h1>PHP работи!</h1>";
echo "DocumentRoot: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Текущ файл: " . __FILE__ . "<br>";

$path = "C:/Program Files/Xampp/htdocs/";
if(is_dir($path)) {
    echo " htdocs директорията съществува<br>";
    $files = scandir($path);
    echo "Файлове в htdocs: " . implode(", ", $files) . "<br>";
} else {
    echo " htdocs директорията НЕ съществува<br>";
}
?>