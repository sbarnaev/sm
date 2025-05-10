<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?=System::Lang('WIDGETS_LIST');?></h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a>
            <a href="/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><?=System::Lang('WIDGETS_LIST');?></li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap">
                <a class="button-red-rounding" href="#ModalWidgets" data-uk-modal="{center:true}"><?=System::Lang('ADD_WIDGET');?></a>
            </li>
        </ul>
    </div>

    <div class="admin_form admin_form--margin-top">
        <div class="overflow-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th class="text-left"><?=System::Lang('TITLE');?></th>
                        <th class="text-left"><?=System::Lang('TYPE');?></th>
                        <th class="td-last"></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if($widgets):
                    foreach($widgets as $widget):?>
                        <tr<?php if($widget['status'] == 0) echo ' class="off"';?>>
                            <td><?=$widget['widget_id'];?></td>
                            <td class="text-left">
                                <a href="/admin/widgets/edit/<?=$widget['widget_id'];?>"><?=$widget['widget_title'];?></a>
                            </td>

                            <td class="text-left">
                                <?=$widget['widget_type'];?>
                            </td>

                            <td class="td-last">
                                <a class="link-delete" onclick="return confirm('<?=System::Lang('YOU_SHURE');?>?')" href="/admin/widgets/del/<?=$widget['widget_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="<?=System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

<div id="ModalWidgets" class="uk-modal">
    <div class="uk-modal-dialog">
        <div class="userbox modal-userbox">
            <a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close"><span class="icon-close"></span></a>
            <div><h3 class="modal-head">Выберите тип виджета</h3>
                <div class="row-line">
                    <div class="col-1-2">
                        <p><a href="/admin/widgets/add?type=html"><?=System::Lang('SIMPLE_HTML');?></a></p>
                        <p><a href="/admin/widgets/add?type=client_menu"><?=System::Lang('CLIENT_MENU');?></a></p>
                        <p><a href="/admin/widgets/add?type=reviews"><?=System::Lang('REVIEWS_LIST');?></a></p>
                        <p><a href="/admin/widgets/add?type=feedback"><?=System::Lang('FEEDBACK_FORM');?></a></p>
                        <p><a href="/admin/widgets/add?type=training"><?=System::Lang('TRAININGS');?></a></p>
                    </div>

                    <div class="col-1-2">
                        <?php $polls = System::CheckExtensension('polls', 1);
                        if($polls):?>
                            <p><a href="/admin/widgets/add?type=polls"><?=System::Lang('POLLS');?></a></p>
                        <?php endif;?>
						
						<?php $blog = System::CheckExtensension('blog', 1);
                        if($blog):?>
                            <p><a href="/admin/widgets/add?type=rubrics"><?=System::Lang('RUBRICS');?></a></p>
                            <p><a href="/admin/widgets/add?type=lastposts"><?=System::Lang('LAST_POSTS');?></a></p>
                        <?php endif;?>

                        <?php $gallery = System::CheckExtensension('gallery', 1);
                        if($gallery):?>
                            <p><a href="/admin/widgets/add?type=gallery"><?=System::Lang('GALLERY');?></a></p>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>