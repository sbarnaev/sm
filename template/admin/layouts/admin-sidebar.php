<?php defined('BILLINGMASTER') or die;
$menu = [];
if (isset($acl['show_orders'])) {
    $menu['orders'] = [
        'title' => System::Lang('ORDERS'),
        'href' => '/admin/orders/',
        'icon_src' => '/template/admin/images/icons/money-bag.png',
        'sort' => 1,
    ];
}

if (isset($acl['show_products'])) {
    $menu['products'] = [
        'title' => System::Lang('INFOPRODUCTS'),
        'href' => '',
        'icon_src' => '/template/admin/images/icons/briefcase.png',
        'sort' => 2,
        'items' => [
            [
                'title' => 'Все продукты',
                'href' => '/admin/products/',
                'sort' => 1,
            ],
            [
                'title' => System::Lang('SALES_LIST'),
                'href' => '/admin/sales/',
                'sort' => 2,
            ],
            [
                'title' => 'Отзывы',
                'href' => '/admin/reviews/',
                'sort' => 3,
            ],
            [
                'title' => 'Рассрочка',
                'href' => '/admin/installment/',
                'sort' => 4,
            ],
        ],
    ];
}

if (isset($acl['show_courses'])) {
    $en_courses = System::CheckExtensension('courses', 1);
    if ($en_courses) {
        $menu['courses'] = [
            'title' => System::Lang('ONLINE_COURSES'),
            'href' => '/admin/courses/',
            'icon_src' => '/template/admin/images/icons/online-course.png',
            'sort' => 3,
        ];
    }

    $en_training = System::CheckExtensension('training', 1);
    if ($en_training) {
        $menu['trainings'] = [
            'title' => 'Тренинги 2.0',
            'href' => '/admin/training/',
            'icon_src' => '/template/admin/images/icons/trening-2-0.svg',
            'sort' => 4,
        ];
    }
}

if (isset($acl['show_users'])) {
    $menu['users'] = [
        'title' => System::Lang('USERS'),
        'href' => '/admin/users/',
        'icon_src' => '/template/admin/images/icons/user.png',
        'sort' => 5,
    ];
}

$menu['conditions'] = [
    'title' => System::Lang('CONDITIONS'),
    'href' => '/admin/conditions/',
    'icon_src' => '/template/admin/images/icons/term.png',
    'sort' => 6
];

if (isset($acl['show_member']) && System::CheckExtensension('membership', 1)) {
    $menu['members'] = [
        'title' => System::Lang('MEMBERSHIP'),
        'href' => '/admin/membersubs/',
        'icon_src' => '/template/admin/images/icons/member.png',
        'sort' => 7,
    ];
}

$partnership = System::CheckExtensension('partnership', 1);
if (isset($acl['show_aff']) && $partnership) {
    $menu['aff'] = [
        'title' => System::Lang('PARTNERSHIP'),
        'href' => '',
        'icon_src' => '/template/admin/images/icons/handshake.png',
        'sort' => 8,
        'items' => [
            [
                'title' => System::Lang('PARTNERS_PAY'),
                'href' => '/admin/aff/',
                'sort' => 1,
            ], [
                'title' => System::Lang('AUTHORS_PAY'),
                'href' => '/admin/authors/',
                'sort' => 2,
            ]
        ]
    ];
}

$menu['site'] = [
    'title' => System::Lang('SITE'),
    'href' => '',
    'icon_src' => '/template/admin/images/icons/laptop.png',
    'sort' => 9,
    'items' => []
];

$blog = System::CheckExtensension('blog', 1);
if ($blog && isset($acl['show_blog'])) {
    $menu['site']['items'][] = [
        'title' => System::Lang('ARTICLES'),
        'href' => '/admin/blog/',
        'sort' => 1,
    ];
}

$menu['site']['items'][] = [
    'title' => System::Lang('WIDGETS'),
    'href' => '/admin/widgets/',
    'sort' => 2,
];

if (isset($acl['show_feedback'])) {
    $menu['site']['items'][] = [
        'title' => System::Lang('FEEDBACK'),
        'href' => '/admin/feedback/',
        'sort' => 3,
    ];
}

if (isset($acl['show_pages'])) {
    $menu['site']['items'][] = [
        'title' => System::Lang('STAT_PAGE'),
        'href' => '/admin/statpages/',
        'sort' => 4,
    ];
}

if (isset($acl['show_rdr'])) {
    $menu['site']['items'][] = [
        'title' => System::Lang('REDIRECTS'),
        'href' => '/admin/redirect/',
        'sort' => 5,
    ];
}

$menu['site']['items'][] = [
    'title' => System::Lang('MENU_ITEMS'),
    'href' => '/admin/menuitems/',
    'sort' => 6,
];

if (System::CheckExtensension('polls', 1) && isset($acl['show_products'])) {
    $menu['site']['items'][] = [
        'title' => 'Опросы',
        'href' => '/admin/polls/',
        'sort' => 7,
    ];
}

$responder = System::CheckExtensension('responder', 1);
if ($responder && isset($acl['show_responder'])) {
    $menu['responder'] = [
        'title' => System::Lang('EMAIL_DELIVERY'),
        'href' => '',
        'icon_src' => '/template/admin/images/icons/letter.png',
        'sort' => 10,
        'items' => [
            [
                'title' => System::Lang('MASS_MAIL'),
                'href' => '/admin/responder/mass/',
                'sort' => 1,
            ], [
                'title' => System::Lang('AUTORESPONDERS'),
                'href' => '/admin/responder/auto/',
                'sort' => 2,
            ], [
                'title' => 'Список подписчиков',
                'href' => '/admin/subscribers/',
                'sort' => 3,
            ]
        ]
    ];
}

if (System::CheckExtensension('gallery', 1) && isset($acl['show_gallery'])) {
    $menu['gallery'] = [
        'title' => 'Галерея',
        'href' => '',
        'icon_src' => '/template/admin/images/icons/gallery.png',
        'sort' => 11,
        'items' => [
            [
                'title' => 'Изображения',
                'href' => '/admin/gallery/',
                'sort' => 1,
            ], [
                'title' => 'Категории',
                'href' => '/admin/gallery/cats/',
                'sort' => 2,
            ]
        ]
    ];
}


if (System::CheckExtensension('forum2', 1) && isset($acl['show_forum'])) {
    $menu['forum'] = [
        'title' => System::Lang('FORUM'),
        'href' => '',
        'icon_src' => '/template/admin/images/icons/forum.png',
        'sort' => 12,
        'items' => [
            [
                'title' => System::Lang('FORUM'),
                'href' => '/admin/forum/',
                'sort' => 1,
            ], [
                'title' => System::Lang('FORUM_CATS'),
                'href' => '/admin/forum/cats/',
                'sort' => 2,
            ], [
                'title' => 'Модераторы',
                'href' => '/admin/forum/moderators/',
                'sort' => 3,
            ]
        ]
    ];
}

$menu['settings'] = [
    'title' => System::Lang('SETTINGS'),
    'href' => '',
    'icon_src' => '/template/admin/images/icons/settings.png',
    'sort' => 13,
    'items' => [],
];

if (isset($acl['show_main_tunes'])) {
    $menu['settings']['items'] = [
        [
            'title' => System::Lang('MAIN_SETTINGS'),
            'href' => '/admin/settings/',
            'sort' => 1,
        ], [
            'title' => 'Внешний вид',
            'href' => '/admin/settings/?cat=vid',
            'sort' => 2,
        ], [
            'title' => 'Письма',
            'href' => '/admin/settings/?cat=letters',
            'sort' => 3,
        ]
    ];
}

if (isset($acl['show_ext_tunes'])) {
    $menu['settings']['items'][] = [
        'title' => System::Lang('EXTENSIONS'),
        'href' => '/admin/extensions/',
        'sort' => 4,
    ];
}

if (isset($acl['show_payment_tunes'])) {
    $menu['settings']['items'][] = [
        'title' => System::Lang('PAY_MODULES'),
        'href' => '/admin/paysettings/',
        'sort' => 5,
    ];
    $menu['settings']['items'][] = [
        'title' => System::Lang('DELIVERY_VARIANTS'),
        'href' => '/admin/deliverysettings/',
        'sort' => 6,
    ];
}

if (isset($acl['show_perms'])) {
    $menu['settings']['items'][] = [
        'title' => System::Lang('USER_PERMISS'),
        'href' => '/admin/permissions/',
        'sort' => 7,
    ];
}

if (isset($acl['show_backups'])) {
    $menu['settings']['items'][] = [
        'title' => System::Lang('BACKUP'),
        'href' => '/admin/backup/',
        'sort' => 8,
    ];
}

if (isset($acl['show_main_tunes'])) {
    $menu['settings']['items'][] = [
        'title' => 'Задания CRON',
        'href' => '/admin/cronjobs/',
        'sort' => 9,
    ];
}

if (isset($acl['show_stat']) || isset($acl['show_channel'] )) {
    $menu['statistics'] = [
        'title' => System::Lang('STATISTICS'),
        'href' => '',
        'icon_src' => '/template/admin/images/icons/bar-chart.png',
        'sort' => 13,
        'items' => [],
    ];
}

if (isset($acl['show_stat'])) {
    $menu['statistics']['items'][] = [
        'title' => System::Lang('REPORT'),
        'href' => '/admin/stat/',
        'sort' => 1,
    ];
    $menu['statistics']['items'][] = [
        'title' => System::Lang('FINSTAT'),
        'href' => '/admin/extstat/',
        'sort' => 2,
    ];
}

if (isset($acl['show_channel'])) {
    $menu['statistics']['items'][] = [
        'title' => System::Lang('CHANNELS'),
        'href' => '/admin/channels/',
        'sort' => 3,
    ];
}

if (isset($acl['show_stat'])) {
    $menu['statistics']['items'][] = [
        'title' => System::Lang('EMAIL_LOG'),
        'href' => '/admin/emailog/',
        'sort' => 4,
    ];
    $menu['statistics']['items'][] = [
        'title' => System::Lang('SMS_LOG'),
        'href' => '/admin/smslog/',
        'sort' => 5,
    ];
    $menu['statistics']['items'][] = [
        'title' => 'Логи транзакций',
        'href' => '/admin/paylog/',
        'sort' => 6,
    ];
    $menu['statistics']['items'][] = [
        'title' => 'Логи мембершипа',
        'href' => '/admin/memberlog/',
        'sort' => 7,
    ];
    $menu['statistics']['items'][] = [
        'title' => 'Логи действий',
        'href' => '/admin/actionlog/',
        'sort' => 8,
    ];
}

if (file_exists(__DIR__ . '/custom-menu.php')) {
    require_once (__DIR__ . '/custom-menu.php');
}

$menu = Helpers::arraySort($menu);

foreach ($menu as $key => $menu_item) {
    if (isset($menu_item['items']) && $menu_item['items']) {
        $menu[$key]['items'] = Helpers::arraySort($menu_item['items']);
    }
}?>

<a data-uk-offcanvas="{mode:'slide'}" href="#offcanvas-1" class="open-menu">
    <span></span><span></span><span></span>
</a>

<div class="sidebar uk-offcanvas" id="offcanvas-1">
    <div class="uk-offcanvas-bar">
        <a onclick="UIkit.offcanvas.hide();" class="tm-offcanvas-close"><span class="icon-close"></span></a>

        <div class="hello">
            <span class="logo">
                <a href="/admin"><?php if (!empty($setting['site_name'])) echo $setting['site_name'];?></a>
            </span>

            <div>
                <?php if (!empty($setting['cover'])):?>
                    <img class="avatar-img" src="/images/<?=$setting['cover'];?>" alt="" title="<?=System::Lang('HELLO');?> <?=$name; ?>! <?=System::Lang('EXELLENT_TODAY');?>">
                <?php endif;?>
            </div>
        </div>

        <nav id="menu" class="mainNav">
            <ul class="admin_nav accordion-nav">
                <?php foreach($menu as $menu_item):?>
                    <li<?php if($menu_item['href'] && strpos($_SERVER['REQUEST_URI'], $menu_item['href']) === 0) echo ' class="current"';?>>
                        <a href="<?=$menu_item['href'];?>"><span class="icon_img">
                                <img src="<?=$menu_item['icon_src'];?>" alt="">
                            </span><?=$menu_item['title'];?>
                        </a>

                        <?php if(isset($menu_item['items']) && $menu_item['items']):?>
                            <ul>
                                <?php foreach($menu_item['items'] as $sub_menu):?>
                                    <li<?php if($_SERVER['REQUEST_URI'] == $sub_menu['href']) echo ' class="current"';?>>
                                        <a href="<?=$sub_menu['href'];?>"><?=$sub_menu['title'];?></a>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        <?php endif;?>
                    </li>
                <?php endforeach;?>
            </ul>
        </nav>

        <p class="cms_version"><span class="cms_version-inner">School-Master v. <?=System::CurrVersion()?></span>
            <?php if (isset($_SESSION['status'])):?>
                <span class="cms_update">
                    <?php if (isset($_SESSION['status']['end'])) {
                        $update = '<span class="cms_version-title">Обновления:</span> <span class="version-status" style="color:#5DCE59"><i class="icon-version-yes"></i>Да</span> (до ' . date("d.m.y", $_SESSION['status']['end']) . ')';
                    }
                    if ($_SESSION['status'] == 'noupdate') {
                        $update = 'Обновления: <span style="color:#E04265"><i class="icon-lock"></i>Нет</span>&nbsp;&nbsp;<a style="color:#fff" target="_blank" href="https://lk.school-master.ru/buy/29">(продлить)</a>';
                    } elseif ($_SESSION['status'] == 'stop') {
                        $update = '<span class="version-status" style="color:#ce4163">Ваша лицензия недействительна</span><br /><a target="_blank" href="https://school-master.ru/support">Задать вопрос</a>';
                    }
                    echo isset($update) ? $update : '';?>
                </span>
            <?php endif;?>
        </p>
    </div>
</div>