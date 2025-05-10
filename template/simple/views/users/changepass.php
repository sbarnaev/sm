<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    <div id="content">
        <div class="layout" id="lk">
            <div class="content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min';?>">
                <div class="login-userbox">
                    <h1 class="cource-head"><?=System::Lang('CHANGE_PASSWORD');?></h1>
                <?php if(isset($_GET['success'])) echo '<div class="success_message">Успешно</div>';?>
                <form action="" method="POST">
                <div class="form-line"><label><?=System::Lang('NEW_PASSWORD');?></label><div class="form-line-input"><input type="text" name="pass"></div></div>
                <div class="form-line-submit"><input class="btn-yellow-fz-16 text-uppercase font-bold button" type="submit" name="changepass" value="<?php echo System::Lang('SAVE');?>"></div>
                </form>
                </div>
            </div>
            
            <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
        </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>