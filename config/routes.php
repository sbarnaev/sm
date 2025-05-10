<?php defined('BILLINGMASTER') or die;

return array (
       
    // Админпанель
    'admin' => 'adminProduct/index',
    'admin/backup' => 'adminSetting/backup',
    'admin/cronjobs' => 'adminSetting/cronjobs',
    
    'admin/emailog/edit/([0-9]+)' => 'adminStat/emailogview/$1',
    'admin/emailog' => 'adminStat/emailog',
    
    'admin/actionlog/view/([0-9]+)' => 'adminStat/actionlogview/$1',
    'admin/actionlog' => 'adminStat/actionlog',
    
    'admin/smslog' => 'adminStat/smslog',
	'admin/paylog/view/([0-9]+)' => 'adminStat/paylogview/$1',
    'admin/paylog' => 'adminStat/paylog',

    // Условия 
    'admin/conditions/del/([0-9]+)' => 'adminConditions/del/$1',
    'admin/conditions/edit/([0-9]+)' => 'adminConditions/edit/$1',
    'admin/conditions/add' => 'adminConditions/add',
    'admin/conditions' => 'adminConditions/index',
    
    // Галерея
    'admin/gallery/editcat/([0-9]+)' => 'adminGallery/editcat/$1',
    'admin/gallery/del/([0-9]+)' => 'adminGallery/delimg/$1',
    'admin/gallery/delcat/([0-9]+)' => 'adminGallery/deltcat/$1',
    'admin/gallery/edit/([0-9]+)' => 'adminGallery/editimg/$1',
    'admin/gallery/addcat' => 'adminGallery/addcat',
    'admin/gallery/cats' => 'adminGallery/cats',
    'admin/gallery/add' => 'adminGallery/addimg',
    'admin/gallery/page-([0-9]+)' => 'adminGallery/index/$1',
    'admin/gallery' => 'adminGallery/index',
    'admin/gallerysettings' => 'adminGallery/settings',
    
    // Меню
    'admin/menuitems/del/([0-9]+)' => 'adminSetting/delmenuitem/$1',
    'admin/menuitems/edit/([0-9]+)' => 'adminSetting/editmenuitem/$1',
    'admin/menuitems/add' => 'adminSetting/addmenuitem',
    'admin/menuitems' => 'adminSetting/menuitems',

    
    // ЮЗЕРЫ
    
    'admin/users/export' => 'adminUsers/export',
    'admin/users/import' => 'adminUsers/import',
    
    'admin/users/del/([0-9]+)' => 'adminUsers/delete/$1',
    'admin/users/edit/([0-9]+)' => 'adminUsers/edit/$1',
    'admin/users/delcompletelesson/([0-9]+)' => 'adminUsers/delCompleteLesson/$1',
    'admin/users/create' => 'adminUsers/create',
    'admin/users/resetpass' => 'adminUsers/resetPass',
    'admin/users' => 'adminUsers/index',
    
    'admin/usergroups/del/([0-9]+)' => 'adminUsers/delgroup/$1',
    'admin/usergroups/edit/([0-9]+)' => 'adminUsers/editgroup/$1',
    'admin/usergroups/add' => 'adminUsers/addgroup',
    'admin/usergroups' => 'adminUsers/group',


    // РАССЫЛКА
    'admin/responder/autoletters/([0-9]+)/del/([0-9]+)' => 'adminResponder/delautoletter/$1/$2', // изменить письмо автосерии
    'admin/responder/autoletters/([0-9]+)/edit/([0-9]+)' => 'adminResponder/editautoletter/$1/$2', // изменить письмо автосерии
    'admin/responder/autoletters/([0-9]+)/add' => 'adminResponder/addautoletter/$1', // добавить письмо в серию
    'admin/responder/autoletters/([0-9]+)' => 'adminResponder/autoletters/$1', // список писем автосерии

    'admin/responder/del/([0-9]+)' => 'adminResponder/delete/$1',
    'admin/responder/edit/([0-9]+)' => 'adminResponder/edit/$1',
    'admin/responder/add_delivery' => 'adminResponder/add', // создать рассылку
    'admin/responder/test' => 'adminResponder/test', // тест

    'admin/responder/([a-z]+)' => 'adminResponder/index/$1', // Список рассылок

    'admin/subsforms/([0-9]+)' => 'adminResponder/form/$1', // создание формы подписки
    'admin/respondersetting' => 'adminResponder/setting', // настройки

    'admin/subscribers/import' => 'adminResponder/import', // Импорт подписчиков
    'admin/subscribers/add' => 'adminResponder/addsubscriber', // добавить подписчика вручную

    'admin/subscribers/bad/([0-9]+)' => 'adminResponder/badsubscribers/$1', // Список некорректных емайлов по рассылке
    'admin/subscribers/del/([0-9]+)' => 'adminResponder/delsubs/$1',
    'admin/subscribers' => 'adminResponder/subscribers', // список подписчиков


    // БЛОГ
    'admin/blog/del/([0-9]+)' => 'adminBlog/delete/$1',
    'admin/blog/edit/([0-9]+)' => 'adminBlog/editpost/$1',
    'admin/blog/add' => 'adminBlog/addpost',
    'admin/blog' => 'adminBlog/index',

    'admin/rubrics/del/([0-9]+)' => 'adminBlog/delrubric/$1',
    'admin/rubrics/edit/([0-9]+)' => 'adminBlog/editrubric/$1',
    'admin/rubrics/add' => 'adminBlog/addrubric',
    'admin/rubrics' => 'adminBlog/rubrics',

    'admin/blogsetting' => 'adminBlog/settings',

    // FEEDBACK
    'admin/feedback/delform/([0-9]+)' => 'adminSite/delfeedbackform/$1',
    'admin/feedback/editform/([0-9]+)' => 'adminSite/editfeedbackform/$1',
    'admin/feedback/addform' => 'adminSite/addfeedbackform',
    'admin/feedback/forms' => 'adminSite/feedbackforms',

    'admin/feedback/del/([0-9]+)' => 'adminSite/delfeedback/$1',
    'admin/feedback/view/([0-9]+)' => 'adminSite/viewmessage/$1',
    'admin/feedback' => 'adminSite/feedback',

    // СТАТИЧНЫЕ СТРАНИЦЫ
    'admin/statpages/del/([0-9]+)' => 'adminSite/delpage/$1',
    'admin/statpages/edit/([0-9]+)' => 'adminSite/editpage/$1',
    'admin/statpages/add' => 'adminSite/addpage',
    'admin/statpages' => 'adminSite/pages',


    // РЕДИРЕКТЫ
    'admin/redirect/add' => 'adminRedirect/add',
    'admin/redirect/edit/([0-9]+)' => 'adminRedirect/edit/$1',
    'admin/redirect/del/([0-9]+)' => 'adminRedirect/del/$1',
    'admin/redirect/editcat/([0-9]+)' => 'adminRedirect/editcat/$1',
    'admin/redirect/delcat/([0-9]+)' => 'adminRedirect/delcat/$1',
    'admin/redirect/addcat' => 'adminRedirect/addcat',
    'admin/redirect/cats' => 'adminRedirect/cats',
    'admin/redirect' => 'adminRedirect/index',

    // Интересы для блога
    'admin/segments/delurl/([0-9]+)/([0-9]+)' => 'adminBlog/delurl/$1/$2',
    'admin/segments/delete/([0-9]+)' => 'adminBlog/delsegment/$1',
    'admin/segments/edit/([0-9]+)' => 'adminBlog/editsegment/$1',
    'admin/segments/add' => 'adminBlog/addsegment',
    'admin/segments' => 'adminBlog/segments',

    // ЗАКАЗЫ
    'admin/orders/edit/([0-9]+)' => 'adminOrders/edit/$1',
    'admin/orders/del/([0-9]+)' => 'adminOrders/del/$1',
    'admin/orders/delpartner' => 'adminOrders/delpartner',
    'admin/orders/confirm/([0-9]+)' => 'adminOrders/confirm/$1',
    'admin/orders/add' => 'adminOrders/add',
    'admin/orders/addproduct/([0-9]+)' => 'adminOrders/addProduct/$1',
    'admin/orders' => 'adminOrders/index',


    // ПРОДУКТЫ
    'admin/products/del/([0-9]+)' => 'adminProduct/delproduct/$1',
    'admin/products/edit/([0-9]+)' => 'adminProduct/editproduct/$1',
    'admin/products/copy/([0-9]+)' => 'adminProduct/copyproduct/$1',
    'admin/products/reset/([0-9]+)' => 'adminProduct/reset/$1',
    'admin/products/add' => 'adminProduct/addproduct',
    'admin/products/updatesort' => 'adminProduct/updsortproducts',
    'admin/products' => 'adminProduct/product',
    'admin/products/addhttpnotice' => 'adminProduct/addHttpNotice',
    'admin/products/edithttpnotice/([0-9]+)' => 'adminProduct/editHttpNotice/$1',
    'admin/products/delhttpnotice/([0-9]+)' => 'adminProduct/delHttpNotice/$1',
    'admin/products/addreminder' => 'adminProduct/addReminder',
    'admin/products/editreminder/([0-9]+)' => 'adminProduct/editReminder/$1',

    'admin/category/del/([0-9]+)' => 'adminProduct/delcategory/$1',
    'admin/category/edit/([0-9]+)' => 'adminProduct/editcategory/$1',
    'admin/category/add' => 'adminProduct/addcategory',
    'admin/category' => 'adminProduct/category',
	
    'admin/installment/delnextorder/([0-9]+)/([0-9]+)' => 'adminProduct/delnextorder/$1/$2',
	'admin/installment/delahead/([0-9]+)/([0-9]+)' => 'adminProduct/delahead/$1/$2',
    'admin/installment/delmap/([0-9]+)' => 'adminProduct/delinstallmap/$1',
    
    'admin/installment/del/([0-9]+)' => 'adminProduct/delinstallment/$1',
    'admin/installment/edit/([0-9]+)' => 'adminProduct/editinstallment/$1',
    'admin/installment/map/del/([0-9]+)/([0-9]+)' => 'adminProduct/delpayinstall/$1/$2',
    'admin/installment/map/([0-9]+)' => 'adminProduct/viewinstall/$1',
    'admin/installment/map' => 'adminProduct/installmaps',
    'admin/installment/add' => 'adminProduct/addinstallment',
    'admin/installment' => 'adminProduct/installment',

    'admin/related/edit/([0-9]+)/([0-9]+)' => 'adminProduct/related/$1/$2',
	
	'admin/organizations/edit/([0-9]+)' => 'adminOrganization/edit/$1',
    'admin/organizations/add' => 'adminOrganization/add',
    'admin/organizations' => 'adminOrganization/index',

    // ОТЗЫВЫ
    'admin/reviews/dellabel/([0-9]+)' => 'adminProduct/dellabel/$1',
    'admin/reviews/editlabel/([0-9]+)' => 'adminProduct/editlabel/$1',
    'admin/reviews/addlabel' => 'adminProduct/addlabel',
    'admin/reviews/labels' => 'adminProduct/labels',

    'admin/reviews/delrev/([0-9]+)' => 'adminProduct/delreview/$1',
    'admin/reviews/del/([0-9]+)' => 'adminProduct/delreviewscat/$1',
    'admin/reviewscat/edit/([0-9]+)' => 'adminProduct/editreviewscat/$1',
    'admin/reviewscat/add' => 'adminProduct/addreviewscat',
    'admin/reviewscat' => 'adminProduct/reviewscats',
    'admin/reviews/edit/([0-9]+)' => 'adminProduct/editreview/$1',
    'admin/reviews' => 'adminProduct/reviews',

    // АКЦИИ / РАСПРОДАЖИ
    'admin/sales/del/([0-9]+)' => 'adminProduct/delsale/$1',
    'admin/sale/edit/([0-9]+)' => 'adminProduct/editsale/$1',
    'admin/sales/add' => 'adminProduct/addsale',
    'admin/sales/page' => 'adminProduct/tunepage',
    'admin/sales' => 'adminProduct/sales',


    // ОНЛАЙН КУРСЫ
    'admin/courses/edit/([0-9]+)' => 'adminCourse/edit/$1', // Редактировать курс
    'admin/courses/delete/([0-9]+)' => 'adminCourse/delcourse/$1', // Редактировать курс
    'admin/courses/statlessext/([0-9]+)/([0-9]+)' => 'adminCourse/statlessext/$1/$2',
    'admin/courses/statless/([0-9]+)/([0-9]+)' => 'adminCourse/statless/$1/$2',
    'admin/courses/stat/([0-9]+)' => 'adminCourse/stat/$1',
    'admin/courses/add' => 'adminCourse/add', // Добавить курс
    'admin/courses/updatesort' => 'adminCourse/updsort', //Обновить порядок курсов (sort)
    'admin/courses' => 'adminCourse/index', // Список курсов

    'admin/courses/profs/edit/([0-9]+)' => 'adminCourse/editprof/$1', // Редактировать профессию
    'admin/courses/delprof/([0-9]+)' => 'adminCourse/delprof/$1', // Удалить профессию
    'admin/courses/addprof' => 'adminCourse/addprof', // Добавить профессию
    'admin/courses/profs' => 'adminCourse/profs', // Список профессий

    'admin/courses/cats/edit/([0-9]+)' => 'adminCourse/editcat/$1', // Редактировать категорию
    'admin/courses/delcat/([0-9]+)' => 'adminCourse/delcat/$1', // Удалить категорию
    'admin/courses/addcat' => 'adminCourse/addcat', // Добавить категорию курса
    'admin/courses/cats' => 'adminCourse/cats', // Список категорий

    'admin/lessons/del/([0-9]+)' => 'adminCourse/dellesson/$1',
    'admin/lessons/edit/([0-9]+)' => 'adminCourse/editlesson/$1',
    'admin/lessons/add' => 'adminCourse/addlesson',
    'admin/lessons' => 'adminCourse/lessons', // Список уроков
    'admin/lessons/delattach' => 'adminCourse/delLessonAttach',
    'admin/lessons/updatesort' => 'adminCourse/updSortLessons', //Обновить порядок уроков (sort)
    'admin/lessons/listfilter' => 'adminCourse/lessonsListFilter', //Получить список уроков для фильтра

    'admin/answers/deldialog/([0-9]+)/([0-9]+)' => 'adminCourse/deldialog/$1/$2',
    'admin/answers/delmess/([0-9]+)' => 'adminCourse/delmessage/$1', // удалить сообщение в диалоге
    'admin/answers/([0-9]+)' => 'adminCourse/answerview/$1',
    'admin/answers' => 'adminCourse/answer', // Список заданий на проверку
    'admin/coursesetting' => 'adminCourse/setting', // настройки

    // МАТЕРИАЛЫ ДЛЯ КУРСОВ
    'admin/dopmat/edit/([0-9]+)' => 'adminCourse/dopmatedit/$1',
    'admin/dopmat/del/([0-9]+)' => 'adminCourse/dopmatdel/$1',
    'admin/dopmat/delcat/([0-9]+)' => 'adminCourse/deldopmatcat/$1',
    'admin/dopmat/add' => 'adminCourse/dopmatadd',
    'admin/dopmat/addcat' => 'adminCourse/dopmataddcat',
    'admin/dopmat/cat' => 'adminCourse/dopmatcat',
    'admin/dopmat' => 'adminCourse/dopmat',


    // MEMBERSHIP
    'admin/membersubs/delete/([0-9]+)' => 'adminMembership/delsubs/$1', // Удалить подписку
    'admin/membersubs/edit/([0-9]+)' => 'adminMembership/editsubs/$1', // Редактировать подписку
    'admin/membersubs/add' => 'adminMembership/addsubs', // Добавить подписку

    'admin/membersubs' => 'adminMembership/index',

    'admin/memberlevels/add' => 'adminMembership/addlevel',
    'admin/memberlevels' => 'adminMembership/levels',

    'admin/memberusers/edit/([0-9]+)' => 'adminMembership/edituser/$1',
    'admin/memberusers/delete/([0-9]+)' => 'adminMembership/delmember/$1',
    'admin/memberusers/add' => 'adminMembership/addmember',
	
	'admin/memberlog' => 'adminMembership/log',

    'admin/memberusers' => 'adminMembership/users',
    'admin/membersetting' => 'adminMembership/settings',
    'admin/memberusers/export' => 'adminMembership/export',

    // РАСШИРЕНИЕ ExpertSender
    'admin/expertsendersetting' => 'extensions/expertsender/adminExpertSender/settings',

    // РАСШИРЕНИЕ GetFunnels
    'admin/getfunnelssetting' => 'extensions/getfunnels/adminGetFunnels/settings',

    // РАСШИРЕНИЕ TELEGRAM
    'admin/telegramsetting' => 'extensions/telegram/adminTelegram/settings',
    'admin/telegramsetting/setwebhook' => 'extensions/telegram/adminTelegram/setWebhook',
    'admin/telegramsetting/delwebhook' => 'extensions/telegram/adminTelegram/delWebhook',
    'admin/telegramsetting/memberslist' => 'extensions/telegram/adminTelegram/membersList',
    'admin/telegramsetting/delmember/([0-9]+)' => 'extensions/telegram/adminTelegram/delMember/$1',
    'admin/telegramsetting/delstowaways' => 'extensions/telegram/adminTelegram/delStowaways',
    'admin/telegramsetting/log' => 'extensions/telegram/adminTelegram/log',

    // РАСШИРЕНИЕ CALLPASSWORD
    'admin/callpasswordsetting' => 'extensions/callpassword/adminCallPassword/settings',

    // РАСШИРЕНИЕ AUTOPILOT
    'admin/autopilotsetting' => 'extensions/autopilot/adminAutopilot/settings',

    // ВИДЖЕТЫ
    'admin/widgets/del/([0-9]+)' => 'adminSite/delwidget/$1',
    'admin/widgets/edit/([0-9]+)' => 'adminSite/editwidget/$1',
    'admin/widgets/add' => 'adminSite/addwidget',
    'admin/widgets' => 'adminSite/widgets',

    // ПАРТНЁРКА
    'admin/aff/userstat/([0-9]+)' => 'adminAff/userstat/$1',
    'admin/aff/paystat' => 'adminAff/paystat',
    'admin/aff/top' => 'adminAff/top',
    'admin/affsetting' => 'adminAff/partnership',
    'admin/aff' => 'adminAff/index',

    // АВТОРЫ
    'admin/authors/userstat/([0-9]+)' => 'adminAff/authorstat/$1',
    'admin/authors/paystat' => 'adminAff/authorpaystat',
    'admin/authors' => 'adminAff/authors',

    'admin/paysettings/del/([0-9]+)' => 'adminSetting/deletepayments/$1',
    'admin/paysettings/([0-9]+)' => 'adminSetting/editpayments/$1',
    'admin/paysettings' => 'adminSetting/payments',
    'admin/settings' => 'adminSetting/settings',
    'admin/extensions/all' => 'adminSetting/allextensions',
    'admin/extensions' => 'adminSetting/extensions',
    'admin/cmsupdate' => 'adminSetting/CMSUpdate',

    // СТАТИСТИКА
    'admin/stat/product' => 'adminStat/productstat',
    'admin/stat/channels' => 'adminStat/channelstat',
    'admin/stat' => 'adminStat/index',
	
	'admin/extstat' => 'adminStat/extstat',

    'admin/channels/group/del/([0-9]+)' => 'adminStat/delgroup/$1',
    'admin/channels/group/edit/([0-9]+)' => 'adminStat/editgroup/$1',

    'admin/channels/del/([0-9]+)' => 'adminStat/delchannel/$1',
    'admin/channels/edit/([0-9]+)' => 'adminStat/editchannel/$1',
    'admin/channels/addgroup' => 'adminStat/addgroup',
    'admin/channels/add' => 'adminStat/addchannels',
    'admin/channels' => 'adminStat/channels',
    'admin/channels/group' => 'adminStat/groupchannels',

    'admin/logout' => 'adminProduct/logout',

    'admin/delimg/([0-9]+)' => 'adminSetting/delimg/$1', // Удаление обложек

    'admin/test' => 'adminSetting/test',
	'admin/config' => 'adminSetting/config',

    'admin/permissions/del/([0-9]+)' => 'adminSetting/delpermissions/$1',
    'admin/permissions/edit/([0-9]+)' => 'adminSetting/editpermissions/$1',
    'admin/permissions/add' => 'adminSetting/addpermissions',
    'admin/permissions' => 'adminSetting/permissions',

    'admin/deliverysettings/del/([0-9]+)' => 'adminSetting/deletedeliverymethod/$1',
    'admin/deliverysettings/edit/([0-9]+)' => 'adminSetting/editdeliverymethod/$1',
    'admin/deliverysettings/add' => 'adminSetting/adddeliverymethod',
    'admin/deliverysettings' => 'adminSetting/deliveryset',

    ///////////////
    // FRONT - END ---------------------------------
    //////////////\\

    'ext/([0-9]+)/([0-9]+)' => 'aff/extland/$1/$2',
    'buy/([0-9]+)' => 'order/buy/$1',
    'pay/([0-9]+)' => 'order/pay/$1', // оплата заказа
    'related/([0-9]+)' => 'order/related/$1', // оплата заказа
    'cancelpay/([0-9]+)' => 'order/cancelpay/$1', // оплата заказа
    'offer/([0-9]+)' => 'order/offer/$1', // апселлы 
    'confirmcustom' => 'order/confirm',
    'download/([0-9]+)'=> 'attachments/download/$1', // Страница для скачивания продукта из заказа
    'delivery/([0-9]+)' => 'order/delivery/$1',
    'delivery/confirm/([0-9]+)' => 'order/confirmdelivery/$1', // Подтверждение доставки
    'feedback' => 'site/feedback', // обратная связь
    'sale' => 'site/sales', // страница акции красная цена
    'api' => 'order/api',
    'order-info/([0-9]+)' => 'order/orderInfo/$1',

    // РАСШИРЕНИЕ TELEGRAM
    'telegram/getupdates' => 'extensions/telegram/telegram/getUpdates',
    'telegram/savedata' => 'extensions/telegram/telegram/saveData',
    'telegram/checkbindinguser' => 'extensions/telegram/telegram/checkBindingUser',

    // РАСШИРЕНИЕ CALLPASSWORD
    'callpassword/confirmphone' => 'extensions/callpassword/callPassword/confirmPhone',

    // РАСШИРЕНИЕ AUTOPILOT
    'autopilot/vkauth' => 'extensions/autopilot/autopilot/vkauth',
    'autopilot/api' => 'extensions/autopilot/autopilot/api',


    'sitemap.xml' => 'site/sitemap',

    // Gallery
    'gallery/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)' => 'gallery/subcats/$1/$2',
    'gallery/([a-zA-Z0-9_-]+)' => 'gallery/cats/$1', // список избражений категории
    'gallery' => 'gallery/index', // список категорий
    
    // Отзывы
    'reviews/add' => 'catalog/addreview',
    'reviews' => 'catalog/reviews',

    // ДЛЯ КОРЗИНЫ
    'cart/del/([0-9]+)' => 'cart/del/$1',
    'cart/add/([0-9]+)' => 'cart/add/$1',
    'cart' => 'cart/index',
    
    
    // СТАТИЧНЫЕ СТРАНИЦЫ
    'page/([a-zA-Z0-9_-]+)' => 'site/page/$1',
    'politika' => 'site/politika',
    'oferta' => 'site/oferta',


    // РАССЫЛКА
    'responder/confirm/([0-9]+)' => 'responder/confirm/$1', // подтверждение подписки
    'responder/subscribe/([0-9]+)' => 'responder/subscribe/$1', // подписка
    'responder/unsubscribe/([a-zA-Z0-9_-]+)' => 'responder/unsubscribe/$1', // отписка
    'responder/unsubclick/([a-zA-Z0-9_-]+)' => 'responder/unsubclick/$1', // отписка в 1 клик


    // БЛОГ
    'blog/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)' => 'blog/post/$1/$2',
    'blog/([a-zA-Z0-9_-]+)' => 'blog/rubric/$1',
    'blog' => 'blog/index',


    // КАТАЛОГ
    'catalog' => 'catalog/catalog',
    'catalog/([a-zA-Z0-9_-]+)' => 'catalog/landing/$1',

    // ОНЛАЙН КУРСЫ
    'courses/lessons/listfilter' => 'course/lessonslistfilter', //Получить список уроков для фильтра
    'courses/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)' => 'course/lesson/$1/$2',
    'courses/([a-zA-Z0-9_-]+)' => 'course/course/$1',
    'courses' => 'course/index',
    
    // ЛИЧНЫЙ КАБИНЕТ
    'lk/registration' => 'user/registration', // Регистрация нового пользователя
    'lk/registration/([a-zA-Z0-9_-]+)' => 'user/registrationConfirm/$1', // Подтверждение регистрации
    'lk/logout' => 'user/logout',
    'lk/orders' => 'cabinet/orders',
    'lk/changepass' => 'cabinet/changepass', // Сменить пароль

    'lk/answers/deldialog/([0-9]+)/([0-9]+)' => 'course/deldialog/$1/$2', // Удалить весь диалог
    'lk/answers/delmess/([0-9]+)' => 'course/delmessage/$1', // Удалить сообщение в диалоге
    'lk/answers/([0-9]+)' => 'course/answer/$1', // Кабинет куратора - ответ на вопрос
    'lk/answers' => 'course/curator', // Кабинет куратора
    'lk/mycourses' => 'course/mycourses',
    'lk/membership' => 'cabinet/membership',

    'lk' => 'cabinet/index',
    'login' => 'user/login',
    'forgot' => 'user/forgot',
    'lostpass' => 'user/changepass',
    
    
    // Рассрочка
    'installment/rules/([0-9]+)' => 'order/rulesinstallment/$1',
    'installahead/([0-9]+)' => 'order/ahead/$1',
	'installament/ahead/([0-9]+)' => 'order/aheadorder/$1',
    'installment/vote' => 'order/voteinstallment',
    'installment' => 'order/installment',
    
    
    // ПАРТНЁРСКИЕ РЕДИРЕКТЫ
    'pr/([0-9]+)' => 'aff/redirect/$1',
    
    
    // РЕДИРЕКТЫ 
    'rdr/([0-9]+)' => 'redirect/go/$1',
    
    // ПАРТНЁРКА
    'aff' => 'aff/affdesc', // Описание партнёрки публичное
    'aff/reg' => 'aff/affreg', // Регистрация партнёра
    'aff/confirm' => 'aff/confirm', // Подтверждение емейла партнёра
    'lk/aff' => 'aff/aff', // стартовая страница партнёрки в кабинете
    'lk/author' => 'aff/author',
    
    '' => 'site/index', // Главная страница
    'upload-image' => 'attachments/uploadImage',
    'upload-avatar' => 'attachments/uploadAvatar',
 );