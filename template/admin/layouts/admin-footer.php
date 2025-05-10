<?php defined('BILLINGMASTER') or die; ?>
<div class="footer">
<p><?php //echo System::Lang('COPYRIGHT');?></p>
</div>
<script type="text/javascript">
	setTimeout(function(){$('.admin_message').fadeOut('fast')},5000); 
</script>

<script src="/template/admin/js/jquery-ui-1.12.1.min.js"></script>
<script src="/template/admin/js/jquery.ui.touch-punch-0.2.3.min.js"></script>
<script>
  $(function() {
    let $course_list = $('.course-list').length > 0 ? $('.course-list') : $('.cource-list');
    let sort_upd_url = $course_list.children('input[name="sort_upd_url"]').val();

    if ($course_list.length > 0 && typeof(sort_upd_url) != 'undefined' && $course_list.find('input[name="sort[]"]').length > 1) {
      $course_list.sortable({
        cursor: "move",
        handle: ".button-drag",
        stop: function() {
          $.ajax({
            url: sort_upd_url,
            method: 'post',
            dataType: 'json',
            data: $course_list.find('input[name="sort[]"]').serialize(),
            success: function(resp) {
              if(!resp.status) {
                alert('Произошла ошибка при сохранении данных, обратитесь к разработчику')
                console.log(resp.error);
              }
            },
            error: function(err) {
              alert("Произошла ошибка при сохранении данных, обратитесь к разработчику");
              console.log(err);
            }
          });
          $('.numbering').each(function(i) {
            $(this).val(i + 1);
          });
        }
      });
    }
  });
</script>

<?php if (System::isAvailblNewVrsn() && isset($acl['update_sm'])):
    if(isset($_SESSION['status']) && $_SESSION['status'] == 'noupdate') $str = '<div class="site-noupdate"><a target="_blank" href="https://lk.school-master.ru/buy/19">Вышла новая версия School-master '.$_SESSION['actual_ver'].' Продлить доступ к обновлениям</a></div>';
    else $str = '<div class="site-update"><a href="javascript:void(0)">Вышла новая версия School-master '.$_SESSION['actual_ver'].' Обновитесь</a></div>';?>
    <script>
      $(document).ready(function(){
        let block = '<?=$str;?>';
        if ($('div.main').length > 0) {
          $('div.main .top-wrap').after(block);
        }
        $('.site-update').on('click', function() {
          $.ajax({
            url: '/admin/cmsupdate',
            method: 'post',
            dataType: 'html',
            data: {token: '<?php echo $_SESSION['admin_token'];?>'},
            success: function(html) {
              if(html) {
                $('.site-update').replaceWith(html);
              }
            },
            error: function(err) {
              alert("Произошла ошибка при обновлении, обратитесь к разработчику");
              console.log(err);
            }
          });
        });
      });
    </script>
<?php endif;?>

<?php if(isset($product['product_id']) && isset($product['type_id'])):?>
<script>
  $(function() {
    $('a[data-prod_httpnotice_id]').click(function() {
      let notice_id = $(this).data('prod_httpnotice_id');

      $.ajax({
        url: "/admin/products/edithttpnotice/" + notice_id + "?prod_id",
        type: "GET",
        dataType: "html",
        data: {prod_id: "<?=$product['product_id'];?>", prod_type: "<?=$product['type_id'];?>"},
        success: function (html) {
          if (html !== '') {
            $("#prod_httpnotice_edit").html(html);
            UIkit.modal("#prod_httpnotice_edit").show();
          }
        }
      });
    });
  });
</script>
<?php endif;?>

<!-- Start of Omnidesk Widget script {literal}-->
<script>
!function(e,o){!window.omni?window.omni=[]:'';window.omni.push(o);o.g_config={widget_id:"13510-fjppr1kr"}; o.email_widget=o.email_widget||{};var w=o.email_widget;w.readyQueue=[];o.config=function(e){ this.g_config.user=e};w.ready=function(e){this.readyQueue.push(e)};var r=e.getElementsByTagName("script")[0];c=e.createElement("script");c.type="text/javascript",c.async=!0;c.src="https://omnidesk.ru/bundles/acmesite/js/cwidget0.2.min.js";r.parentNode.insertBefore(c,r)}(document,[]);
</script>
<!-- End of Omnidesk Widget script {/literal}-->


<?php //echo round((microtime(true) - START),3);?>