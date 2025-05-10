<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');?>

<div class="main">
    <div class="top-wrap">
        <h1>Статистика</h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?=$setting['script_url'];?>/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li>Финансовая статистика</li>
    </ul>

    <div>
        <div class="row-line">
            <div class="col-1-1">
                <h4 class="course-list-item__name mb-20">Финансовая статистика</h4>

                <div class="tabs-statistics">
                    <div class="tabs-statistics-control">
                        <ul>
                            <li class="active" data-stat="common">Общая</li>
                            <li data-stat="membership">Мембершип</li>
                            <li data-stat="categories">По категориям</li>
                            <li data-stat="clients">База клиентов</li>
                            <li data-stat="installment">Рассрочка</li>
                        </ul>
                    </div>

                    <div class="admin_form" id="statistics">
                        <div class="overflow-container statistics-item active" id="common_statistics"></div>
                        <div class="overflow-container statistics-item" id="membership_statistics"></div>
                        <div class="overflow-container statistics-item" id="categories_statistics"></div>
                        <div class="overflow-container statistics-item" id="clients_statistics"></div>
                        <div class="overflow-container statistics-item" id="installment_statistics"></div>
                        <div class="loading"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
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
</style>

<script>
  $(function() {
    let cur_stat = $('.tabs-statistics li.active').data('stat');
    stat_ajax(cur_stat);

    $('.tabs-statistics li').click(function() {
      let cur_stat = $(this).data('stat');
      if ($('#'+cur_stat+'_statistics').html() == '') {
        stat_ajax(cur_stat);
      }
    });
  });

  let stat_ajax = function (stat) {
    $('#statistics .loading').show();
    $.ajax({
      type: "POST",
      dataType: "html",
      data: {get_stat: stat},
      success: function (html) {
        if (html !== '') {
          $('#'+stat+'_statistics').html(html);
          $('#statistics .loading').hide();
        }
      }
    });
  };
</script>
</html>