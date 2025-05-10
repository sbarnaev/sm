<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');?>

<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
        require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    <div id="content">
        <div class="layout" id="courses">
            <ul class="breadcrumbs">
                <li><a href="/">Главная</a></li>
                <li><a href="/courses">Онлайн курсы</a></li>
                <li><a href="/courses/<?=$course['alias'];?>"><?=$course['name'];?></a></li>
                <li><?=$lesson['name'];?></li>
            </ul>
            
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <h1><?=$course['name'];?> : <?=$lesson['name'];?></h1>
                    <h3>К сожалению у вас пока нет доступа к этому уроку</h3>
                    <p>Для получения доступа <a href="/lk/#cp_confirm" target="_blank">подвтердите свой телефон</a></p>
                </div>

                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>
<?php exit;?>