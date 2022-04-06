<?php


//в корне сайта создал файл с содержимым 
3
2
1


$fr = @ fopen ('users.txt', 'r');

if(!$fr) {
    echo "Ошибка! Невозможно открыть файл.<ВК>";
    exit;
}

fscanf($fr, "%d", $a); // a = 7
fscanf($fr, "%d", $b); // b = 8
fscanf($fr, "%d", $c); // c = 9

// закрыть файл ( обязательно )
fclose($fr);

echo '<br>';
echo $a; // выйдет 7
echo '<br>';
echo $b; // выйдет 8
echo '<br>';
echo $c; // выйдет 9



?>

 
