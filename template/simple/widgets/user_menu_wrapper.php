<?php defined('BILLINGMASTER') or die;

if($widget_arr):
    foreach($widget_arr as $widget):
        $suffix = $widget['suffix'];
        $template = $setting['template'];
        $widget_params = unserialize($widget['params']);
        if($widget['private'] != 1 || $is_auth):?>
            <section class="widget<?=$suffix;?>">
                <?php if($widget['show_header'] || $widget['show_subheader'] || $widget['show_right_button']):?>
                    <div class="widget-top<?=$widget['position'] != 'slider' ? ' layout' : '';?>">
                        <div class="widget-header-top">
                            <?php if($widget['show_header']):?>
                                <div class="widget-header widget-header-box">
                                    <h2 class="h1 main-page__h1 widget-header"><?=$widget['header'];?></h2>
                                </div>
                            <?php endif;?>

                            <?php if($widget['show_right_button']):?>
                                <div class="z-1 widget-right-button-box">
                                    <a class="btn-yellow btn-orange widget-right-button" href="<?=$widget['right_button_link'];?>"><?=$widget['right_button_name'];?></a>
                                </div>
                            <?php endif;?>
                        </div>

                        <?php if($widget['show_subheader']):?>
                            <div class="widget-subheader-box">
                                <h2 class="widget-subheader"><?=$widget['subheader'];?></h2>
                            </div>
                        <?php endif;?>
                    </div>
                <?php endif;?>

                <?php require (ROOT . "/template/$template/widgets/{$widget['widget_type']}.php");?>
            </section>
        <?endif;
    endforeach;
endif;?>