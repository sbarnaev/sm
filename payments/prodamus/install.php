<?php 

// Создание папок
$folders = array(
array('/payments/prodamus')
);


// Перемещение файлов имя => куда
$files = array(
array('/fail.php', 'payments/prodamus/fail.php'),
array('/form.php', 'payments/prodamus/form.php'),
array('/index.html', 'payments/prodamus/index.html'),
array('/params.php', 'payments/prodamus/params.php'),
array('/result.php', 'payments/prodamus/result.php'),
array('/success.php', 'payments/prodamus/success.php'),
array('/prodamus.png', 'payments/prodamus/prodamus.png'),
array('/Hmac.php', 'payments/prodamus/Hmac.php')
);

// Для установки в БД
$name = 'prodamus';
$title = 'Prodamus';
$enable = 0;
$type = 'system';
$desc = '<p>Оплата через сервис Prodamus. Принимаются банковский карты, Яндекс.Деньги, Webmoney, QIWI.</p>';
$params = '';
?>