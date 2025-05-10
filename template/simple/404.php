<?php defined('BILLINGMASTER') or die;
header("HTTP/1.0 404 Not Found");
$setting = System::getSetting();?>
<html>
<head>
<title><?=System::Lang('ERROR_404');?></title>
<style>
body {background:#333}
h1 {color:#ffb122; font:bold 7em Arial; text-align:center; margin:1em 0 0.5em 0; transform: rotate(-10grad)}
h2 {color:#fff; font:2em Arial; text-align:center}
p {text-align: center; font:1.3em Arial}
a {color:#fff;}
</style>
</head>
<body>
<h1><?=System::Lang('ERROR_404_ONLY');?></h1>
<h2><?=System::Lang('PAGE_NOT_FOUND');?></h2>
<p><a href="<?php echo $setting['script_url'];?>"><?=System::Lang('GO_TO_MAIN');?></a></p>
</body>
</html>
<?php exit();?>