<?php defined('BILLINGMASTER') or die;

$template = $setting['template'];
$pos2big_headers = ['aftertext', 'aftertext2', 'bottom', 'footer'];

if ($widget_arr):
    foreach ($widget_arr as $widget):
        $suffix = $widget['suffix'];
        $widget_params = unserialize($widget['params']);

        if ($widget['show_for_course'] != null && isset($course['course_id'])) {
            $show = unserialize(base64_decode($widget['show_for_course']));
            if (!in_array($course['course_id'], $show)) continue;
        }
        
        
        if ($widget['show_for_training'] != null && isset($training['training_id'])) {
            $show_training = json_decode($widget['show_for_training'], true);
            if (!in_array($training['training_id'], $show_training)) {
                continue;
            }
        }

        if (!$widget['private'] || $is_auth):?>
            <section class="widget<?=$suffix;?>">
                <?php if($widget['show_header'] || $widget['show_subheader'] || $widget['show_right_button']):?>
                    <div class="widget-header-top">
                        <?php if($widget['show_header']):?>
                            <div class="widget-header-box">
                                <?php if (in_array($widget['position'], $pos2big_headers)):?>
                                    <h2 class="h2-widget-header"><?=$widget['header'];?></h2>
                                <?php else:?>
                                    <h3 class="widget-header"><?=$widget['header'];?></h3>
                                <?php endif;?>
                            </div>
                        <?php endif;?>
                    </div>

                <?php if ($widget['show_subheader'] || $widget['show_right_button']):?>
                <div class="widget-with-button-box">
                    <?php if($widget['show_subheader']):?>
                        <div class="widget-subheader-box">
                            <?php if (in_array($widget['position'], $pos2big_headers)):?>
                                <h2 class="widget-subheader"><?=$widget['subheader'];?></h2>
                            <?php else:?>
                                <h4 class="widget-subheader"><?=$widget['subheader'];?></h4>
                            <?php endif;?>
                        </div>
                    <?php endif;?>

                        <?php if($widget['show_right_button']):?>
                        <div class="z-1 widget-right-button-box">
                            <a class="btn-yellow btn-orange widget-right-button" href="<?=$widget['right_button_link'];?>"><?=$widget['right_button_name'];?></a>
                        </div>
                        <?php endif;?>
                </div>
                        <?php endif;?>
                <?php endif;?>
                <div class="widget-inner">
                <?php require(ROOT . "/template/$template/widgets/{$widget['widget_type']}.php");?>
                </div>
            </section>
        <?php endif;
    endforeach;
endif;?>