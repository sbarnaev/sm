<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body class="invert-page" id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php')
    ?>
    
    
    <div id="content">
        <div class="layout" id="landing">
            <ul class="breadcrumbs">
                <li><a href="/"><?=System::Lang('MAIN');?></a></li>
                <li><a href="/reviews"><?=System::Lang('REVIEWS');?></a></li>
                <li> <?=System::Lang('ADD_REVIEW');?> </li>
            </ul>
            <div class="content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                <div class="maincol-inner">
                <?php if(isset($_GET['success']) && isset($_SESSION['review'])) echo '<p>'.$reviews_tune['after_text'].'</div>';
                elseif(isset($_GET['fail'])) echo '<p >Ошибка <br />'.$_GET['fail'].'</p>';
                else {?>
                
                <h1><?=System::Lang('ADD_NEW_REVIEW');?></h1>
                <form action="" method="POST" enctype="multipart/form-data">
<style>
.myrange {-webkit-appearance: none;width: 200px;height: 15px;border-radius: 5px;   background: #d3d3d3;outline: none;opacity: 0.7;-webkit-transition: .2s;
transition: opacity .2s}
.myrange::-webkit-slider-thumb {-webkit-appearance: none;appearance: none;width: 25px;height: 25px;border-radius: 50%; background: #4CAF50;cursor: pointer}
.myrange::-moz-range-thumb {width: 25px;height: 25px;border-radius: 50%;background: #4CAF50;cursor: pointer}
#demo {font-weight:bold; font-size:1.5em; color:#359a39}
</style>
                    <div class="userbox">
                        <p class="userbox-max-width"><label for="name"><?=System::Lang('YOUR_NAME');?> </label> <input type="text" name="name" required="required" id="name"></p>
                        <?php if($reviews_tune['email'] == 1):?>
                        <p class="userbox-max-width"><label for="email"><?=System::Lang('YOUR_EMAIL');?> </label> <input type="email" name="email" <?php if($reviews_tune['email'] == 2) echo ' required="required"';?> id="email"></p>
                        <?php endif;?>
                        
                        <?php if($reviews_tune['site_url'] == 1):?>
                        <p class="userbox-max-width"><label for="site_url"><?=System::Lang('YOUR_SITE');?> </label> <input type="text" name="site_url" <?php if($reviews_tune['site_url'] == 2) echo ' required="required"';?> id="site_url"></p>
                        <?php endif;?>
                        
                        <?php if($reviews_tune['vk_url'] == 1):?>
                        <p class="userbox-max-width"><label for="vk_url"><?=System::Lang('VK_LINK');?> </label> <input type="text" name="vk_url" <?php if($reviews_tune['vk_url'] == 2) echo ' required="required"';?> id="vk_url"></p>
                        <?php endif;?>
                        
                        <?php if($reviews_tune['fb_url'] == 1):?>
                        <p class="userbox-max-width"><label for="fb_url"><?=System::Lang('FB_LINK');?> </label> <input type="text" name="fb_url" <?php if($reviews_tune['fb_url'] == 2) echo ' required="required"';?> id="fb_url"></p>
                        <?php endif;?>
                        
                        
                        <?php if($reviews_tune['rate'] == 1):?>
                        <p><label><?=System::Lang('YOUR_ASSESSMENT');?> <span id="demo"></span></label><input type="range" min="0" max="5" value="1" name="range" class="myrange" id="myRange"></p>
                        <script>
                        var slider = document.getElementById("myRange");
                        var output = document.getElementById("demo");
                        output.innerHTML = slider.value; // Display the default slider value
                        
                        // Update the current slider value (each time you drag the slider handle)
                        slider.oninput = function() {
                            output.innerHTML = this.value;
                        }
                        </script>
                        <?php endif;?>

                        <?php if($reviews_tune['photo'] > 0 ):?>
                        <p><label for="photo"><?=System::Lang('ADD_PHOTO');?> </label> <input type="file" name="photo" <?php if($reviews_tune['photo'] == 2) echo ' required="required"';?> id="photo"></p>
                        <?php endif;?>
                        
                        <p class="textarea-big"><label for="email"><?=System::Lang('YOUR_REVIEW');?> </label><script>document.write(window.atob("PHRleHRhcmVhIG5hbWU9InJldmlldyIgY29scz0iNjAiIHJvd3M9IjkiIHJlcXVpcmVkPSJyZXF1aXJlZCI+PC90ZXh0YXJlYT4="));</script>
                        <input type="hidden" name="time" value="<?php echo time();?>"></p>
                        
                        <p><label> </label> <input type="submit" class="button btn-blue" value="Отправить" name="addreview"></p>
                        
                    </div>
                </form>
                <?php } ?>
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