<?php defined('BILLINGMASTER') or die;
$is_auth = User::isAuth();
$all_widgets = Widgets::getWidgets($is_page, $is_auth);
$sidebar = Widgets::RenderWidget($all_widgets, 'sidebar');

header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("X-Frame-Options:sameorigin");
header("X-Permitted-Cross-Domain-Policies: none");
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header("X-Content-Type-Options: nosniff");
?>
<!DOCTYPE html>
<html lang="ru-ru" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php if(isset($noindex)):?>
        <meta name="robots" content="noindex, nofollow"/>
    <?php endif;?>
    <link href="<?=$setting['script_url'];?>/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <meta name="description" content="<?=$meta_desc;?>" />
    <meta name="keywords" content="<?=$meta_keys;?>" />
    <?php if(isset($use_css) && $use_css == 1):?>
        <link rel="stylesheet" href="<?=$setting['script_url'];?>/template/<?=$setting['template'];?>/css/normalize.css" type="text/css" />
        <link rel="stylesheet" href="<?=$setting['script_url'];?>/template/<?=$setting['template'];?>/css/style.css" type="text/css" />
        <link rel="stylesheet" href="<?=$setting['script_url'];?>/template/<?=$setting['template'];?>/css/mobile.css" type="text/css" />
    <?php endif; ?>

    <?php if(isset($jquery_head)):?>
        <script src="<?=$setting['script_url'];?>/template/<?=$setting['template']?>/js/jquery-3.4.1.min.js"></script>
        <script src="https://widget.cloudpayments.ru/bundles/cloudpayments"></script>
    <?php endif;?>

    <title><?=$title;?></title>
    <?php if(isset($no_tmpl) && $no_tmpl == 1):
        echo $in_head;
    endif;?>

    <?php if(isset($comments) && $comments == 1 && !empty($params['params']['commenthead'])) {
        echo $params['params']['commenthead'];
    }
    
    if(!empty($setting['counters_head'])) {
        echo $setting['counters_head'];
    }?>
	
	<?php $main_settings = System::getSettingMainpage();
	if(isset($main_settings['sidebar'])){?>
	<style>
			<?php if($main_settings['sidebar'] == 'left') echo '.sidebar {order:-1}';
			if($main_settings['sidebar'] == 'right') echo '.sidebar {order:2}';?>
	</style>		
	<?php }?>

    <meta property="og:type" content="article">
    <meta property="og:title" content="<?=$title;?>"/>
    <meta property="og:description" content="<?=$meta_desc;?>" />
    <meta property="og:image" content="<?php echo $setting['logotype'];?>" />
    <meta property="og:image:type" content="image/png" />
	
	<?php if(!empty($main_settings['custom_css'])):?>
    <style><?=$main_settings['custom_css'];?></style>
    <?php endif;?>
    <?php $fb_api = System::CheckExtensension('facebookapi', 1);
    if($fb_api):
        echo Facebook::getPixelCode();
    endif;?>
</head>
