<?php defined('BILLINGMASTER') or die ?>
<!DOCTYPE html>
<html lang="ru-ru" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
<title><?php echo $title;?></title>
<meta name="description" content="<?php echo $meta_desc;?>" />
<meta name="keywords" content="<?php echo $meta_keys;?>" />
<?php echo $page['in_head'];?>
<?php if(isset($comments) && $comments == 1) {
    if(!empty($params['params']['commenthead'])) echo $params['params']['commenthead'];
}?>
</head>

<body id="page">
    <?=System::renderContent($page['content']);?>
    <?php if(isset($page['custom_code'])) echo $page['custom_code'];
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php');?>

    <?=$page['in_body']?>
</body>
</html>