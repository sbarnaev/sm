<?php defined('BILLINGMASTER') or die;
if (isset($external)) {
    $ch = curl_init($external);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $html = curl_exec($ch);
    curl_close($ch);

    echo $html;
} else {
    if ($setting_main['main_page_tmpl'] == 1) {
        require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');?>
        <body id="page">
            <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
            require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>

            <div id="content">
                <div class="layout">
                    <div class="content-wrap">
                        <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
							<?php $contentbody = Widgets::RenderWidget($all_widgets, 'contentbody');
							$widget_arr = $contentbody;
							if($contentbody): ?>
								<div class="contentbody">
									<?php require(ROOT . '/template/'.$setting['template'].'/widgets/widget_wrapper.php');?>
								</div>
							<?php endif; ?>
							<?php echo System::renderContent($setting_main['main_page_text']);?>
						</div>
                        <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
                    </div>

                    <?php $aftertext = Widgets::RenderWidget($all_widgets, 'aftertext');
                    $widget_arr = $aftertext;
                    if($aftertext): ?>
                        <div class="aftertext">
                            <?php require(ROOT . '/template/'.$setting['template'].'/widgets/widget_wrapper.php');?>
                        </div>
                    <?php endif; ?>

                    <?php $aftertext2 = Widgets::RenderWidget($all_widgets, 'aftertext2');
                    $widget_arr = $aftertext2;
                    if($aftertext2):?>
                        <div class="aftertext">
                            <?php require(ROOT . '/template/'.$setting['template'].'/widgets/widget_wrapper.php');?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
		
		    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
		    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php');
		} else {
			$use_css = 0;
			$no_tmpl = 1;
			require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');        
			echo '<body id="page">'.$setting_main['main_page_text'];
			echo $in_bottom;
		}
	}?>
</body>
</html>