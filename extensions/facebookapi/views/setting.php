<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Настройки Facebook API</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки Facebook API</li>
    </ul>
    
    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Сохранено!</div>
    <?php endif?>
    
    <form action="" method="POST">
        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-tren.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки Facebook API</h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/extensions/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4 class="h4-border">Основное</h4>
                    <div class="width-100"><label>Статус:</label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($enable == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($enable == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                    </div>
                    <div class="width-100"><label>Pixel id</label>
                        <input type="text" name="facebook[params][pixel_id]" value="<?php echo $params['params']['pixel_id'];?>">
                    </div>
                    <div class="width-100"><label>Маркер доступа</label>
                        <input type="text" name="facebook[params][access_token_fb]" value="<?php echo $params['params']['access_token_fb'];?>">
                    </div>
                </div>
            </div>
    
            <h4 class="h4-border mt-20">Тестирование серверных событий</h4>
                <div class="col-1-2">
                    <label>Значение test_event_code</label>
                    <input id="tec" type="text" name="test_event_code">
                </div>
                <p><input class="button-green-border-rounding" id="myDiv" type="button" value="Отправить"></p>
        </div>
        <div class="reference-link">
            <a class="button-blue-rounding" href="#"><i class="icon-info"></i>Справка по расширению</a>
        </div>
   
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

<script type="text/javascript">
    
    var myDiv = document.getElementById("myDiv");
    var tec = document.querySelector('input[name=test_event_code]');
  
        myDiv.addEventListener("click", function(){
        if (tec.value !== '') {
            $.ajax({
                method: "POST",
                url: "/admin/facebooksetting/testbutton",
                data: {
                    "tec": tec.value,
                },
                success: function(data) {
                    console.log(data);
                    alert("Проверьте события на Facebook на вкладке тестирование");
                },
                error: function(er) {
                    console.log(er);
                }
            });
        } else {
            alert("Заполните тестовое событие");
        }
        });
</script>
</body>
</html>