<?php 

// Создание папок
$folders = array(
array('/payments/wayforpay')
);


// Перемещение файлов имя => куда
$files = array(
array('/fail.php', 'payments/wayforpay/fail.php'),
array('/form.php', 'payments/wayforpay/form.php'),
array('/index.html', 'payments/wayforpay/index.html'),
array('/params.php', 'payments/wayforpay/params.php'),
array('/result.php', 'payments/wayforpay/result.php'),
array('/success.php', 'payments/wayforpay/success.php'),
array('/wayforpay.php', 'payments/wayforpay/wayforpay.php'),
array('/wayforpay.png', 'payments/wayforpay/wayforpay.png')
);

// Для установки в БД
$name = 'wayforpay';
$title = 'Wayforpay';
$enable = 0;
$type = 'system';
$desc = '<p>Оплата через сервис Wayforpay.</p>';
$params = '';
?>