<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');?>

<div class="main">
    <div class="top-wrap">
        <h1>Сводная статистика по тренингу</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги 2.0</a></li>
        <li>Статистика по тренингу</li>
    </ul>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>

    <div class="traning-top">
        <div class="admin_top-inner">
            <div>
                <img src="/extensions/training/web/admin/images/icons/training-2.svg" alt="">
            </div>

            <div>
                <span>Статистика по тренингу</span>
                <h3 class="mb-0 mt-0"><span class="traning-name-text"><?=$training['name'];?></span></h3>
            </div>
        </div>

        <ul class="nav_button">
            <li class="nav_button__last"><a class="button red-link" href="/admin/training/">Закрыть</a></li>
        </ul>
    </div>

    <div class="tabs tabs-training-statistics">
        <ul class="overflow-container tabs-ul">
            <li class="active" data-stat_type="common">Общая</li>
            <li data-stat_type="users">Пользователи</li>
            <li data-stat_type="lessons">Прохождение уроков</li>
            <li data-stat_type="curators">Кураторы</li>
            <li data-stat_type="certificates">Сертификаты</li>
        </ul>

        <div class="admin_form" id="statistics">
            <div class="statistics-item active" id="common_statistics"></div>
            <div class="statistics-item" id="users_statistics"></div>
            <div class="statistics-item" id="lessons_statistics"></div>
            <div class="statistics-item" id="curators_statistics"></div>
            <div class="statistics-item" id="certificates_statistics"></div>
            <div class="loading"></div>
        </div>
    </div>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
<style>
    #statistics {
        min-height: 600px;
        position: relative;
    }
    #statistics .statistics-item {
        margin-top: 0;
    }
    #statistics .green-text {
        font-weight: bold;
        text-align: right;
    }
    #statistics .loading {
        display: none;
        width: 128px;
        height: 128px;
        position: absolute;
        left: 50%;
        top: 50%;
        background: url('/template/admin/images/spinner2.gif') no-repeat 50% 50%;
        margin: -64px 0 0 -64px;
    }
    .admin_result {
        padding: 20px 0;
    }
    .admin_result table th, .admin_result table td {
        padding-left: 0;
    }
    .progress {
        width : 120px;
        height: 5px;
        border-radius: 10px;
        background: #D8DAE7;
    }
    .list-users-progress.progress{
        width: 92px;
    }
    .text-right .progress {
        margin-left: auto;
    }
    .progress .completed_line {
        height: 100%;
        border-radius: 10px;
        max-width: 100%;
        background: linear-gradient(90deg, #5DCE59 4.34%, rgba(93, 206, 89, 0.67) 53.17%, #5DCE59 100%);
    }
    .number-people{
        display: block;
        margin-top: 5px;
    }
</style>
<script>
  $(function() {
    let stat_type = $('.tabs-training-statistics li.active').data('stat_type');
    if ($('#'+stat_type+'_statistics').find('form').length == 0) {
      stat_ajax(stat_type);
    }

    $('.tabs-training-statistics li').click(function() {
      stat_type = $(this).data('stat_type');
      if ($('#'+stat_type+'_statistics').find('form').length == 0) {
        stat_ajax(stat_type);
      }
    });
  });

  let stat_ajax = function (stat_type) {
    $('.tabs-training-statistics .loading').show();
    $.ajax({
      type: "POST",
      dataType: "html",
      data: {stat_type: stat_type},
      success: function (html) {
        if (html !== '') {
          $('#'+stat_type+'_statistics').html(html);
          $('#statistics .loading').hide();
          dependent_blocks();
        }
      }
    });
  };
</script>
</html>