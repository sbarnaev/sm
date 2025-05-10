<?php defined('BILLINGMASTER') or die;
$user_id = intval(User::isAuth());
if($user_id) $user = User::getUserById($user_id);
$menu_items = System::getMenuItems(1);
$current_url = htmlentities(substr($_SERVER['REQUEST_URI'], 1));
$en_trainings = System::CheckExtensension('training', 1);
$courses_enable = System::CheckExtensension('courses', 1);
?>

<nav class="topmenu">
    <div class="uk-offcanvas" id="offcanvas-1">
        <div class="uk-offcanvas-bar uk-offcanvas-bar-flip">
            <a onclick="UIkit.offcanvas.hide();" class="tm-offcanvas-close">
                <span class="icon-close"></span>
            </a>

            <ul class="main-menu">
                <?php if($menu_items):
                    foreach($menu_items as $item):
                        $blank = $item['new_window'] == 1 ? ' target="_blank"' : '';
                        if ($item['visible'] == 0) {
                            continue;
                        };?>

                        <li>
                            <?php if($item['type'] == 'main'):?>
                                <a href="/"<?=$blank;?> title="<?=$item['title'];?>"<?php if($current_url == '') echo ' class="current"';?>>
                                    <?=$item['name'];?>
                                </a>
                            <?php elseif($item['type'] == 'custom'):?>
                                <a href="<?=$item['link'];?>"<?=$blank;?> title="<?=$item['title'];?>"<?php if(!empty($current_url) && strpos($item['link'], $current_url)) echo ' class="current"';?>>
                                    <?=$item['name'];?>
                                </a>
                            <?php else:?>
                                <a href="/<?=$item['link'];?>"<?=$blank;?> title="<?=$item['title'];?>"<?php if($item['link'] == $current_url) echo ' class=" current"';?>>
                                    <?=$item['name'];?>
                                </a>
                            <?php endif;

                            $sub_items = System::getMenuItems(1, $item['item_id']);
                            $sub_blank = '';
                                if($sub_items):?>
                                    <ul class="submenu">
                                        <?php foreach($sub_items as $sub):
                                            if ($sub['new_window'] == 1) {
                                                $sub_blank = ' target="_blank"';
                                            };
                                            if ($sub['visible'] == 0) {
                                                continue;
                                            };?>
                                        <li>
                                            <?php if($sub['type'] == 'main'):?>
                                                <a href="/"<?=$sub_blank;?> title="<?=$sub['title'];?>">
                                                    <?=$sub['name'];?>
                                                </a>
                                            <?php elseif($sub['type'] == 'custom'):?>
                                                <a href="<?=$sub['link'];?>"<?=$sub_blank;?> title="<?=$sub['title'];?>">
                                                    <?=$sub['name'];?>
                                                </a>
                                            <?php else:?>
                                                <a href="/<?=$sub['link'];?>"<?=$sub_blank;?> title="<?=$sub['title'];?>">
                                                    <?=$sub['name'];?>
                                                </a>
                                            <?php endif;?>
                                        </li>
                                    <?php endforeach;?>
                                </ul>
                            <?php endif;?>
                        </li>
                    <?php endforeach;
                endif;?>
            </ul>
        </div>
    </div>

    <?php if(!$is_auth):?>
        <ul class="logout-block">
            <li class="button-login">
                <a class="btn-blue-border-2" href="#modal-login" data-uk-modal="{center:true}"><?=System::Lang('LOGIN');?></a>
            </li>
        </ul>

    <?php else:?>
        <div class="block-login">
            <div class="block-login__click">
                <img id="avatar-top" src="<?=User::getAvatarUrl($user, $setting);?>" />
                <span class="icon-angle-up"></span>
            </div>

            <ul class="block-login__list">
                <?php $setting_main = System::getSettingMainpage();
                $user_menu = json_decode($setting_main['user_menu'], 1);
                
                // TODO требуется переименовать названия меню
                if($user['is_curator'] == 1):
                    if($en_trainings && isset($user_menu['curators2']) && $user_menu['curators2'] == 1):?>
                        <li><a href="/lk/<?='curator'?>"><?=$user_menu['curators2_title'];?></a></li>
                    <?php endif;

                    if($courses_enable && isset($user_menu['curators']) && $user_menu['curators'] == 1):?>
                        <li><a href="/lk/<?='answers'?>"><?=$user_menu['curators_title'];?></a></li>
                    <?php endif;?>
                <?php endif;

                if($en_trainings && isset($user_menu['mytraining2']) && $user_menu['mytraining2'] == 1):?>
                    <li><a href="/lk/mytrainings"><?=$user_menu['mytraining2_title'];?></a></li>
                <?php endif;

                if($courses_enable && isset($user_menu['mytraining']) && $user_menu['mytraining'] == 1):?>
                    <li><a href="/lk/mycourses"><?=$user_menu['mytraining_title'];?></a></li>
                <?php endif;?>
                
                <?php if($user['is_partner'] == 1 && isset($user_menu['partners']) && $user_menu['partners'] == 1):?>
                <li><a href="/lk/aff"><?=$user_menu['partners_title'];?></a></li>
                <?php endif;?>
                
                <?php if($user['is_author'] == 1 && isset($user_menu['authors']) && $user_menu['authors'] == 1):?>
                <li><a href="/lk/author"><?=$user_menu['authors_title'];?></a></li>
                <?php endif;?>

                <?php if(isset($user_menu['myorders']) && $user_menu['myorders'] == 1):?>
                <li><a href="/lk/orders"><?=$user_menu['myorders_title'];?></a></li>
                <?php endif;?>
                
                <?php if(isset($user_menu['forum']) && $user_menu['forum'] == 1):?>
                <li><a href="/forum"><?=$user_menu['forum_title'];?></a></li>
                <?php endif;?>
                
                <?php if(isset($user_menu['myprofile']) && $user_menu['myprofile'] == 1):?>
                <li><a href="/lk"><?=$user_menu['myprofile_title'];?></a></li>
                <?php endif;?>
                
                <?php if(isset($user_menu['custom1']) && $user_menu['custom1'] == 1):?>
                <li><a href="<?=$user_menu['custom1_url'];?>"><?=$user_menu['custom1_title'];?></a></li>
                <?php endif;?>
                
                <li><a href="/lk/logout"><?=System::Lang('QUIT');?></a></li>
            </ul>
        </div>
    <?php endif;?>

    <a data-uk-offcanvas href="#offcanvas-1" class="open-menu">
        <span></span><span></span><span></span>
    </a>
</nav>