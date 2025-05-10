<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>

    
    <div id="content">
        <div class="layout" id="blog">
            <ul class="breadcrumbs">
                <li><a href="/">Главная</a></li>
                <li><a href="/blog">Блог</a></li>
                <li>Нет доступа</li>
            </ul>
            <div class="content-wrap">

                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <h1>К сожалению у вас пока нет доступа к этой странице</h1>
                    <?php if(!$user_id){?>
                    <p>Возможно вы просто не авторизованы. <a href="#modal-login" data-uk-modal="{center:true}">Войти на сайт</a></p>
                    <?php } else {?>
                    <p></p>
                    <?php }?>
					
                    
                </div>
            <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>

            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>