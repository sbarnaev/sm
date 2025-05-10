<?php defined('BILLINGMASTER') or die;
$en_trainings = System::CheckExtensension('training', 1);
$en_courses = System::CheckExtensension('courses', 1);

$aff = System::CheckExtensension('partnership', 1);
if($aff) $aff_params = unserialize(System::getExtensionSetting('partnership'));

$user_id = intval(User::isAuth());
$user = $user_id ? User::getUserById($user_id) : null;
$avatar_enable = isset($widget_params['params']['allow_avatar']) &&  $widget_params['params']['allow_avatar'] == 1 ? true : false ?>

<?php if($user && isset($_SESSION['name'])):?>
    <div class="client-menu <?=$widget_params['params']['orient'] == 'gorizontal' ? 'gorizontal' : 'vertical';?>">
        <div class="client-menu__row">
            <div class="client-menu__left">
                <img id="avatar" src="<?=User::getAvatarUrl($user, $setting);?>"/>
                <?php if ($avatar_enable):?>
                    <form method="post">
                        <input type="file" name="image" class="image input-avatar" data-browse="Загрузить фото">
                    </form>
                <?php endif;?>
            </div>

            <div class="client-menu__right">
                <div class="client-menu__top-line">
                    <h4 class="client-menu__title"><?=$_SESSION['name'];?></h4>
                    <div class="client-menu__info">
                        <?php if(isset($widget_params['params']['editprofile']) && $widget_params['params']['editprofile'] == 1):?>
                            <a class="client-menu__edit" href="/lk">
                                <span class="client-menu__icon icon-pencil"></span>
                                <span class="client-menu__text-link"><?=$widget_params['params']['editprofile_title'];?></span>
                            </a>
                        <?php endif;
                        
                        if(isset($widget_params['params']['editpass']) && $widget_params['params']['editpass'] == 1):?>
                            <a href="/lk/changepass"><?=$widget_params['params']['editpass_title'];?></a>
                        <?php endif;

                        if($user['is_partner'] == 1):
                            if(isset($widget_params['params']['partners']) && $widget_params['params']['partners'] == 1):?>
                                <a href="/lk/aff"><?php if(isset($aff_params['params']['title'])) echo $aff_params['params']['title']; else echo $widget_params['params']['partners_title'];?></a>
                            <?php endif;?>
                        <?php endif;

                        if($user['is_author'] == 1):
                            if(isset($widget_params['params']['authors']) && $widget_params['params']['authors'] == 1):?>
                                <a href="/lk/author"><?=$widget_params['params']['authors_title'];?></a>
                            <?php endif;?>
                        <?php endif;

                        if($user['is_curator'] == 1):?>
                            <?php if($en_trainings && isset($widget_params['params']['curators2']) && $widget_params['params']['curators2'] == 1):?>
                                <a href="/lk/<?='curator'?>"><?=$widget_params['params']['curators2_title'];?></a>
                            <?php endif;

                            if($en_courses && isset($widget_params['params']['curators']) && $widget_params['params']['curators'] == 1):?>
                                <a href="/lk/<?='answers'?>"><?=$widget_params['params']['curators_title'];?></a>
                            <?php endif;
                        endif;

                        if(isset($widget_params['params']['custom_link']) && $widget_params['params']['custom_link']):?>
                            <a href="<?=$widget_params['params']['custom_link_url'];?>"><?=$widget_params['params']['custom_link_title'];?></a>
                        <?php endif;?>
                    </div>
                </div>

                <div class="client-menu__bottom-line">
                    <?php if($en_trainings && Training::getCountTrainings()):
                        if(isset($widget_params['params']['mytraining2']) && $widget_params['params']['mytraining2'] == 1):?>
                            <a href="/lk/mytrainings">
                                <span class="sprite-user sprite-user-briefcase"></span>
                                <span><?=$widget_params['params']['mytraining2_title'];?></span>
                            </a>
                        <?php endif;
                    endif;

                    if($en_courses && Course::getCourseListFromSitemap()):
                        if(isset($widget_params['params']['mytraining']) && $widget_params['params']['mytraining'] == 1):?>
                            <a href="/lk/mycourses">
                                <span class="sprite-user sprite-user-briefcase"></span>
                                <span><?=$widget_params['params']['mytraining_title'];?></span>
                            </a>
                        <?php endif;
                    endif;

                    if($widget_params['params']['myorders'] == 1):?>
                        <a href="/lk/orders">
                            <span class="sprite-user sprite-user-money-bag"></span>
                            <span><?=isset($widget_params['params']['myorders_title']) ? $widget_params['params']['myorders_title'] : 'Покупки';?></span>
                        </a>
                    <?php endif;

                    $has_members = Member::getMemberList($user['email']);
                    if($has_members && isset($widget_params['params']['mymembership']) && $widget_params['params']['mymembership'] == 1):?>
                        <a href="/lk/membership">
                            <span class="sprite-user sprite-user-user"></span>
                            <span><?=isset($widget_params['params']['mymembership_title']) ? $widget_params['params']['mymembership_title'] : 'Подписки';?></span>
                        </a>
                    <?php endif;
                    
                    if(isset($widget_params['params']['forum']) && $widget_params['params']['forum'] == 1):?>
                        <a href="/forum">
                            <span class="sprite-user sprite-user-forum"></span>
                            <span><?=$widget_params['params']['forum_title'];?></span>
                        </a>
                    <?php endif;
                    
                    if(isset($widget_params['params']['forum_topics']) && $widget_params['params']['forum_topics'] == 1):?>
                        <a href="/forum/mytopics">
                            <span class="sprite-user sprite-user-forum"></span>
                            <span><?=$widget_params['params']['forum_topics_title'];?></span>
                        </a>
                    <?php endif;
					
					if($_SERVER['HTTP_HOST'] == 'lk.school-master.ru'):?>
                        <a href="<?php echo $setting['script_url'];?>/lk/mylicense"><span class="sprite-user sprite-user-money-bag"></span><span><?=System::Lang('LICENSES');?></span></a>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
    <?php if ($avatar_enable):?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
        
        <div id="modal-avatar" class="uk-modal">
            <div class="uk-modal-dialog">
                    <a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close">
                        <span class="icon-close"></span>
                    </a>
                    <div class="uk-modal-body">
                        <div class="img-container">
                                    <img id="image" src="">
                                    <div class="preview"></div>
                        </div>
                    </div>
                    <div class="uk-modal-footer">
                        <button type="button" class="btn-green" id="crop"><?=System::Lang('UPLOAD');?></button>
                    </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {

        var $modal = UIkit.modal('#modal-avatar');
        var image = document.getElementById('image');
        var cropper;

        $("body").on("change", ".image", function(e){
            var files = e.target.files;
            var done = function (url) {
            image.src = url;
            $modal.show();
            };
            var reader;
            var file;
            var url;

            if (files && files.length > 0) {
            file = files[0];

            if (URL) {
                done(URL.createObjectURL(file));
            } else if (FileReader) {
                reader = new FileReader();
                reader.onload = function (e) {
                done(reader.result);
                };
                reader.readAsDataURL(file);
            }
            }
            
            
        });


        $modal.on('show.uk.modal', function () {
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
            });
        }).on('hide.uk.modal', function () {
        cropper.destroy();
        cropper = null;
        });

        $("#crop").click(function(){
            canvas = cropper.getCroppedCanvas({
                width: 160,
                height: 160,
                minWidth: 256,
                minHeight: 256,
                maxWidth: 1080,
                maxHeight: 1080,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            canvas.toBlob(function(blob) {
                url = URL.createObjectURL(blob);
                var reader = new FileReader();
                reader.readAsDataURL(blob); 
                reader.onloadend = function() {
                    var base64data = reader.result;  
                    
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "<?=$setting['script_url']?>/upload-avatar?token=<?=isset($_SESSION['user_token']) ? $_SESSION['user_token'] : '';?>",
                        data: {image: base64data},
                        success: function(data){
                            $modal.hide();
                            document.getElementById("avatar").src = data;
                            document.getElementById("avatar-top").src = data;
                        }
                    });
                }
            });
        })
        }, false);

        </script>
    <?php endif;?>
<?php endif;?>