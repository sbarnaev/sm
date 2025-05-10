<?php defined('BILLINGMASTER') or die; ?>

<?php if(!isset($jquery_head)):?>
    <script src="/template/<?=$setting['template'];?>/js/jquery-2.1.3.min.js"></script>
<?php endif;?>

<script src="/template/<?=$setting['template'];?>/js/libs.js"></script>
<script src="/template/<?=$setting['template'];?>/js/scripts.js"></script>

<script>
  objectFitImages();
</script>

<?php // JavaScripts and others elements or counters
$is_page = isset($is_page) ? $is_page : null;
if($is_page == 'lk' || isset($js) || $setting['use_cart'] == 1):?>
    <script src="<?=$setting['script_url'];?>/template/<?=$setting['template'];?>/js/tabs.js"></script>
    <script>
    jQuery(document).ready(function(){
    	jQuery(".tabs").lightTabs();
    });
    </script>
<?php endif;?>

<script>
jQuery(function() {
    jQuery(window).scroll(function() {
        if(jQuery(this).scrollTop() > 300) {
            jQuery('#toTop').fadeIn();
        } else {
            jQuery('#toTop').fadeOut();
        }
    });
    jQuery('#toTop').click(function() {
        jQuery('body,html').animate({scrollTop:0},800);
    });
});
</script>

<?php if($setting['use_cart'] == 1):?>
    <div id="cart_win" style="display: none;"></div>

    <script>
        $(document).ready(function () {
        $(".add_to_cart").click(function () {
            var id = $(this).attr("data-id");
            $.post("/cart/add/"+id, {}, function (data) {
                $("#cart-count").html(data);
                $("#cart_win").html('Товар добавлен в корзину');
                //jQuery("#cart_win").fadeOut(2000);
                $('#cart_win').css("display", "block")
                setTimeout(function(){$('#cart_win').css("display", "none")},2000);
                //jQuery("#cart_win".css("display", "block");
            });
            return false;
        });
        });
    </script>
<?php endif; 

echo $setting['counters'];?>

<?php if(defined('BM_GALLERY')):
if ($is_page == 'gallery') {
    $params = unserialize(System::getExtensionSetting('gallery'));
    $style = $params['params']['style'];
} else {
    $style = constant('BM_GALLERY');
}?>

<script src="/template/<?=$setting['template'];?>/js/gallery.min.js"></script>
<link href="/template/<?=$setting['template'];?>/css/gallery.css" rel="stylesheet" type="text/css" />

<?php if($style == 'grid'){?>
    <script src="/template/<?=$setting['template'];?>/themes/tiles/theme-tilesgrid.js"></script>
<?php }?>

<?php if($style == 'columns' || $style == 'justified' ){?>
    <script src="/template/<?=$setting['template'];?>/themes/tiles/theme-tiles.js"></script>
<?php }?>

<?php if($style == 'slider'){?>
    <script src="/template/<?=$setting['template'];?>/themes/slider/theme-slider.js" type="text/javascript"></script>
<?php }?>

<?php if($style == 'carousel'){?>
    <script src="/template/<?=$setting['template'];?>/themes/carousel/theme-carousel.js" type="text/javascript"></script>
<?php }?>


<script>	
	jQuery(document).ready(function(){
		jQuery("#gallery").unitegallery({
            <?php if($style == 'columns'){?>
                gallery_theme:"tiles",
                tile_show_link_icon: true,
                <?php if(isset($params['params']['width'])):?>
                    tiles_col_width:<?=$params['params']['width'];?>,
                <?php endif;?>
                lightbox_textpanel_enable_description: true,
            <?php } ?>
            
            
            <?php if($style == 'justified'){?>
                gallery_theme:"tiles",
                tiles_type:"justified",
                tile_show_link_icon: true,
                <?php if(isset($params['params']['height'])):?>
                    tiles_justified_row_height:<?=$params['params']['height'];?>,
                <?php endif;?>
                lightbox_textpanel_enable_description: true,
            <?php } ?>
            
            <?php if($style == 'grid'){?>
                gallery_theme:"tilesgrid",
                tile_show_link_icon: true,
                lightbox_textpanel_enable_description: true,
                <?php if(isset($params['params']['width'])):?>
                    tile_width: <?=$params['params']['width']?>,
                    tile_height: <?=$params['params']['height'];?>,
                <?php endif;?>
                tile_enable_border:false,
                tile_shadow_color:"#CCCCCC",
                tile_shadow_blur:2,
                tile_shadow_spread:1,
                grid_num_rows:4,
            <?php } ?>

  <?php if($style == 'slider'){?>
      gallery_theme:"slider",
        slider_control_zoom: false,
        slider_enable_arrows: true,
        slider_enable_progress_indicator: false,
        gallery_images_preload_type:"visible",
        slider_link_newpage: true,
    <?php if(isset($params['params']['width'])):?>
      gallery_width: <?=$params['params']['width'];?>,
      gallery_height: <?=$params['params']['height'];?>,
    <?php endif;?>

    <?php if(!isset($params['params']['width'])):?>
      gallery_width: 1920,
        gallery_min_height: 250,
    <?php endif;?>
    <?php }?>
            
            <?php if($style == 'carousel'){?>
                gallery_theme: "carousel",
                <?php if(isset($params['params']['width'])):?>
                    tile_width: <?=$params['params']['width'];?>,
                    tile_height: <?=$params['params']['height'];?>,
                <?php endif;?>
                tile_show_link_icon: true,
            <?php }?>
		});
	});
</script>
<?php endif;?>

<?php
$settings = System::getSetting();
$countries_list = $settings['countries_list'];
if (!empty($countries_list)):?>
    <link href="/template/simple/css/intlTelInput-11.0.14.css" rel="stylesheet" />
    <script src="/template/simple/js/utils-11.0.14.js"></script>
    <script src="/template/simple/js/intlTelInput-11.0.14.js"></script>
    <script src="/template/simple/js/jquery.mask-1.14.11.js"></script>

    <script>
      let cntrs_list = <?=$countries_list;?>;
      let $phone_input = $("input[name='phone']");
      if ($phone_input.length == 1) {
        let iti = $phone_input.intlTelInput({
          initialCountry: cntrs_list.indexOf('ru') != -1 ? 'ru' : cntrs_list[0],
          preferredCountries: cntrs_list.indexOf('ru') != -1 ? ['ru'] : [cntrs_list[0]],
          separateDialCode: true,
          onlyCountries: cntrs_list
        });
      }

      $(document).ready(function() {
        if ($phone_input.length == 1) {
          if ($('.intl-tel-input .iti-flag').hasClass('ru')) {
            $phone_input.attr('placeholder', '912 333-33-33');
          }
          let mask = $phone_input.attr('placeholder').replace(/[0-9]/g, 0);
          $phone_input.mask(mask);

          $phone_input.on("countrychange", function(e, countryData) {
            if (countryData.iso2 == 'ru') {
              $phone_input.attr('placeholder', '912 333-33-33');
            }
            mask = $phone_input.attr('placeholder').replace(/[0-9]/g, 0);
            $phone_input.mask(mask).attr('maxlength', 13);
          });

          $phone_input.parents('form').submit(function() {
            if ($('.selected-flag .selected-dial-code').length > 0) {
              let phone_code = $(this).find('.selected-flag .selected-dial-code').text();
              $(this).append('<input type="hidden" name="phone_code" value="' + phone_code + '">');
            }

            let phone = $phone_input.val();
            let placeholder = $phone_input.attr('placeholder');
            if (typeof(placeholder) !== 'undefined' && phone.length !== placeholder.length) {
              $phone_input.addClass('error');
              return false;
            } else {
              $phone_input.removeClass('error');
            }
          });
        }
      });
    </script>
<?php endif;?>

<script>var editors = [];</script>
<?php if($setting['editor'] == 1):?>
    <link rel="stylesheet" href="/lib/trumbowyg/dist/ui/trumbowyg.min.css">
    <script src="/lib/trumbowyg/dist/trumbowyg.min.js"></script>
    <script src="/lib/trumbowyg/dist/plugins/pasteembed/trumbowyg.pasteembed.js"></script>
    <script src="/lib/trumbowyg/dist/plugins/upload/trumbowyg.cleanpaste.js"></script>
    <script src="/lib/trumbowyg/dist/plugins/upload/trumbowyg.pasteimage.js"></script>
    <script src="/lib/trumbowyg/dist/langs/ru.js"></script>

    <script type="text/javascript">
      editor_transfiguration = function($el) {
        if ($el.length > 0) {
          $el.trumbowyg({
            btns: [
              ['strong', 'em', 'del'],
              ['link'],
              ['insertImage'],
              ['unorderedList', 'orderedList']
            ],
            autogrow: true,
            lang: 'ru',
            removeformatPasted: false
          });
        }
      };

      $(document).ready(editor_transfiguration($("textarea.editor")));
    </script>
<?php elseif($setting['editor'] == 2):?>
    <script src="/lib/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
      var editor_transfiguration = function (el) {
        let editor = CKEDITOR.replace(el, {
          uiColor: '#282f3a',
          toolbar: [
            ['Bold', 'Italic', 'Strike', '-', 'Link', '-', 'Image', '-', 'NumberedList', 'BulletedList']
          ],
          extraPlugins: 'stylesheetparser,image2,uploadimage,autogrow',
          contentsCss: '/template/<?=$setting['template'];?>/css/ckeditor.style.css',
          contentsJs: '/template/<?=$setting['template'];?>/css/ckeditor.style.css',
          stylesSet: [],
          uploadUrl: '<?=$setting['script_url']?>/upload-image?token=<?=isset($_SESSION['user_token']) ? $_SESSION['user_token'] : '';?>',
          height: 140,
          autoGrow_onStartup: true,
          autoGrow_minHeight: 140,
          autoGrow_maxHeight: 9999,
          autoGrow_bottomSpace: 20
        });
        editors.push(editor);
      };

      $(document).ready(function() {
        $("textarea.editor").each(function () {
          editor_transfiguration($(this).attr("name"));
        });
      });
    </script>
<?php endif;?>
<script src="/template/<?=$setting['template'];?>/js/editor-draft.js"></script>


<?php if(isset($_SESSION['user_token'])):?>
    <script type="text/javascript">
      $.ajaxSetup({
        headers: {
          'X-Csrf-Token': '<?=$_SESSION['user_token'];?>'
        }
      });

      $(document).ready(function() {
        $('form').each(function() {
            let method = $(this).attr('method');
            let action = $(this).attr('action');
            let script_url = '<?=$setting['script_url'];?>';
            if (method == 'POST' && (typeof(action) == 'undefined' || action == '' || action.indexOf('#') == 0 || action.indexOf('/') == 0 || action.indexOf(script_url)  == 0)) {
                $(this).append('<input type="hidden" name="token" value="<?=$_SESSION['user_token'];?>">');
            }
        });
      });
    </script>
<?php endif;?>

<script src="/lib/select2/js/select2.js"></script>
<link href="/lib/select2/css/select2.css" rel="stylesheet" type="text/css" />

<?php if(System::CheckExtensension('training', 1) && isset($training_filter_enabled) && $training_filter_enabled):?>
    <link rel="stylesheet" href="/extensions/training/web/frontend/style/style.css" type="text/css" />
    <script src="/extensions/training/web/frontend/js/main.js"></script>
    <script src="/extensions/training/views/frontend/filter/main.js"></script>
    <link href="/extensions/training/views/frontend/filter/style.css" rel="stylesheet" type="text/css" />
<?php endif;?>