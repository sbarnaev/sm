<?php defined('BILLINGMASTER') or die;


class adminCourseController extends AdminBase {


    // СПИСОК УРОКОВ
    public function actionLessons()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $course = 0;
        $status = 2;
		$now = time();

        $params = unserialize(base64_decode(Course::getCourseSetting()));

        if(isset($_POST['copy']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            $lesson_id = intval($_POST['lesson_id']);
            $copy = Course::copyLesson($lesson_id);
            if($copy) header("Location: /admin/lessons?success");
        }

        if(isset($_GET['reset'])) {
            unset($_SESSION['filter_course']);
        }

        if(isset($_GET['course'])) {
            $course = intval($_GET['course']);
            $_SESSION['filter_course'] = $course;
        }

        if(isset($_POST['filter']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            $course = $_POST['course'];
            $_SESSION['filter_course'] = $course;
            $status = $_POST['status'];

        }

        if(isset($_SESSION['filter_course']) && $_SESSION['filter_course'] > 0) $course = intval($_SESSION['filter_course']);
        else header("Location: /admin/courses");

        $lesson_list = Course::getLessonsList($status, $course);

        require_once (ROOT . '/template/admin/views/course/lessons.php');
    }


    // СТАТИСТИКА ПО КУРСУ
    public function actionStat($course_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            header("Location: /admin");
        }

        $name = $_SESSION['admin_name'];
        $params = unserialize(base64_decode(Course::getCourseSetting()));

        if (isset($_GET['reset'])) {
            if (isset($_SESSION['filter'])) {
                unset($_SESSION['filter']);
                header("Location: /admin/courses/stat/$course_id");
            }
        }

        $groups = false;
        $planes = false;
        $course = Course::getCourseByID($course_id);

        if ($course['access_type'] == 1 && !empty($course['groups'])) {
            $groups = implode(",", unserialize($course['groups'])); // получаем группы через запятую для SQL запроса
        }

        if ($course['access_type'] == 2 && !empty($course['access'])) {
            $planes = implode(",", unserialize($course['access'])); // получаем планы подписок через запятую для SQL запроса
        }

        $now = time();
        $start = $now - 2592000; // 30 дней назад
        $finish = $now; // сегодня

        if (isset($_POST['filter']) || isset($_SESSION['filter'])) {
            if (isset($_POST['filter'])) {
                $_SESSION['filter']['start'] = $_POST['start'];
                $_SESSION['filter']['finish'] = $_POST['finish'];
            }

            $start = $_SESSION['filter']['start'] ? strtotime($_SESSION['filter']['start']) : null;
            $finish = $_SESSION['filter']['finish'] ? strtotime($_SESSION['filter']['finish']) : null;
        }

        $lesson_list = Course::getLessonsList(2, $course_id);

        require_once (ROOT . '/template/admin/views/course/stat_course.php');
    }


    // СТАТИСТКА ПО УРОКУ
    public function actionStatless($course_id, $lesson_id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];

        $params = unserialize(base64_decode(Course::getCourseSetting()));
        

        $now = time();
        $start = $now - 2592000; // 30 дней назад
        $finish = $now; // сегодня

        if(isset($_POST['filter']) || isset($_SESSION['filter'])){

            if(!empty($_POST['start']) || isset($_SESSION['filter']['start'])) {

                isset($_POST['start']) ? $start = strtotime($_POST['start']) : $start = strtotime($_SESSION['filter']['start']);
                if(isset($_POST['start'])) $_SESSION['filter']['start'] = $_POST['start'];
            }

            if(!empty($_POST['finish']) || isset($_SESSION['filter']['finish'])) {

                isset($_POST['finish']) ? $finish = strtotime($_POST['finish']) : $finish = strtotime($_SESSION['filter']['finish']);
                if(isset($_POST['finish']))$_SESSION['filter']['finish'] = $_POST['finish'];
            }

        }

        $groups = false;
        $planes = false;
        $course = Course::getCourseByID($course_id);

        if($course['access_type'] == 1) $groups = implode(",", unserialize($course['groups'])); // получаем группы через запятую для SQL запроса
        if($course['access_type'] == 2) $planes = implode(",", unserialize($course['access'])); // получаем планы подписок через запятую для SQL запроса

        if(isset($_GET['type'])) $status = intval($_GET['type']);

        $lesson = Course::getLessonDataByID($lesson_id);
        $users = Course::getLessonStat($lesson_id, $status, $start, $finish, $groups, $planes);

        require_once (ROOT . '/template/admin/views/course/stat_lesson.php');
    }



    // СВОДНАЯ СТАТИСТИКА ПО УРОКУ
    public function actionStatlessext($course_id, $lesson_id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];

        $params = unserialize(base64_decode(Course::getCourseSetting()));
        

        $now = time();
        $start = $now - 2592000; // 30 дней назад
        $finish = $now; // сегодня

        if(isset($_POST['filter']) || isset($_SESSION['filter'])){

            if(!empty($_POST['start']) || isset($_SESSION['filter']['start'])) {

                isset($_POST['start']) ? $start = strtotime($_POST['start']) : $start = strtotime($_SESSION['filter']['start']);
                if(isset($_POST['start'])) $_SESSION['filter']['start'] = $_POST['start'];
            }

            if(!empty($_POST['finish']) || isset($_SESSION['filter']['finish'])) {

                isset($_POST['finish']) ? $finish = strtotime($_POST['finish']) : $finish = strtotime($_SESSION['filter']['finish']);
                if(isset($_POST['finish']))$_SESSION['filter']['finish'] = $_POST['finish'];
            }

        }

        $groups = false;
        $planes = false;
        $course = Course::getCourseByID($course_id);
        $lesson = Course::getLessonDataByID($lesson_id);

        if($course['access_type'] == 1) $groups = implode(",", unserialize($course['groups'])); // получаем группы через запятую для SQL запроса
        if($course['access_type'] == 2) $planes = implode(",", unserialize($course['access'])); // получаем планы подписок через запятую для SQL запроса

        $users = Course::getLessonStat($lesson_id, 100, $start, $finish, $groups, $planes);

        require_once (ROOT . '/template/admin/views/course/stat_lesson_ext.php');
    }



    // ДОБАВИТЬ УРОК
    public function actionAddlesson()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];

        $params = unserialize(base64_decode(Course::getCourseSetting()));
        
        $setting = System::getSetting();

        if(isset($_POST['addless']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            if(!isset($acl['change_courses'])){
                header("Location: /admin/courses");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $course_id = intval($_POST['course_id']);
            $img_alt = htmlentities($_POST['img_alt']);

            $type_access = intval($_POST['type_access']);
            if(!empty($_POST['groups'])) $groups = serialize($_POST['groups']);
            else $groups = null;
            if(!empty($_POST['accesses'])) $accesses = serialize($_POST['accesses']);
            else $accesses = null;
            $block_id = intval($_POST['block_id']);

            $sort = intval($_POST['sort']);
            $status = intval($_POST['status']);
            $allow_comments = intval($_POST['allow_comments']);
            $show_comments = intval($_POST['show_comments']);
            $show_hits_count = intval($_POST['show_hits_count']);

            if(!empty($_POST['start'])) $start = strtotime($_POST['start']);
            else $start = time();
            if(!empty($_POST['end'])) $end = strtotime($_POST['end']);
            else $end = $start + 330720000;

            if(empty($_POST['alias'])) {
                $alias = System::Translit($_POST['name']);
                if(System::searchDuplicateAlias($alias, 'course_lessons')) $alias = $alias.'-1';
            } else {
                $alias = $_POST['alias'];
                if(System::searchDuplicateAlias($alias, 'course_lessons', true)) $alias = $alias.'-1';
            }
            

            if(empty($_POST['title'])) $title = $name;
            else $title = $_POST['title'];

            if(isset($_POST['dopmat'])) $dopmat = serialize($_POST['dopmat']);
            else $dopmat = null;

            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);

            $desc = $_POST['desc'];
            $content = $_POST['content'];
            $video_urls = htmlentities($_POST['video_urls']);
			$audio_urls = htmlentities($_POST['audio_urls']);												 

            $task_type = intval($_POST['task_type']);
            $task_time = intval($_POST['task_time']);
            $task = $_POST['task'];

            $type_access_buy = intval($_POST['type_access_buy']);
            $product_access = intval($_POST['product_access_buy']);
            $link_access = htmlentities($_POST['link_access_buy']);
            $custom_code = $_POST['custom_code'];
            $duration = intval($_POST['duration']);

            if(isset($_FILES['cover'])){
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/lessons/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if(is_uploaded_file($tmp_name)){
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                }
            }

            $add = Course::AddLesson($name, $course_id, $img, $img_alt, $type_access, $groups, $accesses, $sort, $status, $allow_comments, $show_comments,
                $show_hits_count, $start, $end, $alias, $title, $meta_desc, $meta_keys, $desc, $content, $dopmat, $task_type, $task_time, $task,
                $type_access_buy, $product_access, $link_access, $custom_code, $video_urls, $audio_urls, $duration, $block_id );

            if($add) header("Location: ".$setting['script_url']."/admin/lessons?success");

        }

        require_once (ROOT . '/template/admin/views/course/add_lesson.php');
    }



    // ИЗМЕНИТЬ УРОК
    public function actionEditlesson($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            header("Location: /admin");
        }
        
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();
        $params = unserialize(base64_decode(Course::getCourseSetting()));

        if (isset($_POST['editless']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {

            if(!isset($acl['change_courses'])){
                header("Location: /admin/courses");
                exit();
            }
            
            $name = htmlentities($_POST['name']);
            $course_id = intval($_POST['course_id']);
            $block_id = intval($_POST['block_id']);
            $img_alt = htmlentities($_POST['img_alt']);

            $type_access = intval($_POST['type_access']);
            $groups = !empty($_POST['groups']) ? serialize($_POST['groups']) : null;
            $accesses = !empty($_POST['accesses']) ? serialize($_POST['accesses']) : null;

            $sort = intval($_POST['sort']);
            $status = intval($_POST['status']);
            $allow_comments = intval($_POST['allow_comments']);
            $show_comments = intval($_POST['show_comments']);
            $show_hits_count = intval($_POST['show_hits_count']);

            $task_type = intval($_POST['task_type']);
            $task_time = intval($_POST['task_time']);
            $task = $_POST['task'];

            $type_access_buy = intval($_POST['type_access_buy']);
            $product_access = intval($_POST['product_access_buy']);
            $link_access = htmlentities($_POST['link_access_buy']);
            $custom_code = $_POST['custom_code'];
            $custom_code_up = $_POST['custom_code_up'];

            $start = !empty($_POST['start']) ? strtotime($_POST['start']) : time();
            $end = !empty($_POST['end']) ? strtotime($_POST['end']) : $start + 330720000;
            
            if(empty($_POST['alias'])) {
                $alias = System::Translit($_POST['name']);
                if(System::searchDuplicateAlias($alias, 'course_lessons')) $alias = $alias.'-1';
            } else {
                $alias = $_POST['alias'];
                if(System::searchDuplicateAlias($alias, 'course_lessons', true)) $alias = $alias.'-1';
            }
            $title = empty($_POST['title']) ? $name : $_POST['title'];
          
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);

            $dopmat = isset($_POST['dopmat']) ? serialize($_POST['dopmat']) : null;
            $desc = $_POST['desc'];
            $content = $_POST['content'];
            $video_urls = htmlentities($_POST['video_urls']);
			$audio_urls = htmlentities($_POST['audio_urls']);												 
            $duration = intval($_POST['duration']);

            $timing = intval($_POST['timing']);
            $timing_period = htmlentities($_POST['timing_period']);

            if (isset($_FILES['cover']) && $_FILES["cover"]["size"] != 0) {
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/lessons/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if (is_uploaded_file($tmp_name)) {
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                }
            } else {
                $img = $_POST['current_img'];
            }
            
            if (isset($_FILES['attachments'])) {
                $attachments = !empty($_POST['current_attachments']) ? json_decode($_POST['current_attachments'], true) : array();
                
                foreach($_FILES["attachments"]["name"] as $key => $attach_name) {
                    $attach_name = System::getSecureString($attach_name);
                    
                    if ($_FILES["attachments"]["size"][$key] != 0) {
                        $tmp_name = $_FILES["attachments"]["tmp_name"][$key]; // Временное имя файла на сервере
                        
                        $folder = ROOT . "/load/lessons/$id/"; // папка для сохранения
                        if (!file_exists($folder)) {
                            mkdir($folder);
                        }
                        
                        $path = $folder . $attach_name; // Полный путь с именем файла
                        if (is_uploaded_file($tmp_name)) {
                            if (file_exists($path)) {
                                unlink($path);
                            }
                            
                            if (!in_array($attach_name, $attachments)) {
                                $attachments[] = $attach_name;
                            }
                            
                            move_uploaded_file($tmp_name, $path);
                        }
                    }
                }
                
                $attachments = !empty($attachments) ? json_encode($attachments) : $_POST['current_attachments'];
            } else {
                $attachments = '';
            }

            $edit = Course::EditLesson($id, $name, $course_id, $img, $img_alt, $type_access, $groups, $accesses, $sort, $status, $allow_comments,
                $show_comments, $show_hits_count, $start, $end, $alias, $title, $meta_desc, $meta_keys, $desc, $content,
                $dopmat, $task_type, $task_time, $task, $type_access_buy, $product_access, $link_access, $custom_code, $custom_code_up, $video_urls, $audio_urls, $block_id,
                $duration, $timing, $timing_period, $attachments);
            if ($edit) {
                header("Location: ".$setting['script_url']."/admin/lessons/edit/$id?success&filer_course=$course_id");
            }
        }

        $lesson = Course::getLessonDataByID($id);
        $course = Course::getCourseByID($lesson['course_id']);
        
        require_once (ROOT . '/template/admin/views/course/lesson_edit.php');
    }



    // УДАЛИТЬ УРОК
    public function actionDellesson($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_courses'])) {
            header("Location: /admin/courses");
            exit();
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();

        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Course::DelLesson($id);
            if($del) header("Location: ".$setting['script_url']."/admin/lessons?success");
            else header("Location: ".$setting['script_url']."/admin/lessons?fail");
        }
    }


    // УДАЛИТЬ ВЛОЖЕНИЕ У УРОКА
    public function actionDelLessonAttach()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_courses'])) {
            header("Location: /admin/courses");
            exit();
        }
        
        if (!empty($_POST) && !empty($_POST['lesson_id'])) {
            $lesson_id = intval($_POST['lesson_id']);
            if ($lesson_id && !empty($_POST['attachments'])) {
                $attachments = json_decode($_POST['attachments'], true);
                foreach ($attachments as $key => $attach_name) {
                    if ($attach_name == $_POST['attach_delete']) {
                        unset($attachments[$key]);
                        $path = ROOT . "/load/lessons/{$lesson_id}/{$attach_name}";
                        if(file_exists($path)) {
                            unlink($path);
                        }
                        break;
                    }
                }
                $attachments = !empty($attachments) ? json_encode($attachments) : '';
                $result = Course::UpdateLessonAttach($lesson_id, $attachments);
                echo json_encode(array('result' => $result, 'attachments' => $attachments));
            }
        }

    }


    // ОБНОВИТЬ ПОРЯДОК УРОКОВ (SORT)
    public function actionUpdSortLessons()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['change_courses'])) {
            header("Location: /admin/courses");
            exit();
        }
        
        if (!empty($_POST['sort'])) {
            $resp = array('status' => true, 'error' => '');
            foreach ($_POST['sort'] as $sort => $lesson_id) {
                $result = Course::UpdateSortLesson(intval($lesson_id), intval($sort)+1);
                if (!$result) {
                    $resp['status'] = false;
                    $resp['error'] = 'Не удалось сохранить sort для урока с id = ' . $lesson_id;
                    break;
                };
            }
            echo json_encode($resp);
        }
    }


    // ПОЛУЧИТЬ СПИСОК УРОКОВ ДЛЯ ФИЛЬТРА
    public function actionLessonsListfilter()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) {
            header("Location: /admin/courses");
            exit();
        }
        
        if (!empty($_POST['course_id'])) {
            $course_id = intval($_POST['course_id']);
            $lesson_list = $course_id ? Course::getLessonsList(2, $course_id) : null;
            $list = [];
            if($lesson_list) {
                foreach($lesson_list as $lesson) {
                    $list[] = array($lesson['lesson_id']=>$lesson['name']);
                }
            }
            echo json_encode($list, true);  
            exit;
        }
    }


    /**
     *  КУРСЫ
     */

    // СПИСОК КУРСОВ
    public function actionIndex()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];

        $params = unserialize(base64_decode(Course::getCourseSetting()));
        
        $cat_id = 0;
        if(isset($_POST['filter'])){
            $cat_id = intval($_POST['cat_id']);
            $_SESSION['filter_cat_id'] = $cat_id;
        } elseif(isset($_SESSION['cat_id'])) $cat_id = intval($_SESSION['cat_id']);

        if(isset($_POST['copy']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            $course_id = intval($_POST['course_id']);
            $copy = Course::copyCourse($course_id);
            if($copy) header("Location: /admin/courses?success");

        }
        
        if(isset($_POST['exportcsv']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            $course_id = intval($_POST['course_id']);
            $copy = Course::exportTrainingtoCSV($course_id);
            if($copy) header("Location: /admin/courses?success");

        }

        $course_list = Course::getCourseList(0, $cat_id);

        require_once (ROOT . '/template/admin/views/course/index.php');
    }



    // ДОБАВИТЬ НОВЫЙ КУРС
    public function actionAdd()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();

        $params = unserialize(base64_decode(Course::getCourseSetting()));
        

        if(isset($_POST['addcourse']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_courses'])){
                header("Location: /admin/courses");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $cat_id = intval($_POST['cat_id']);
            $prof_id = intval($_POST['prof_id']);
            $autotrain = intval($_POST['autotrain']);
            $img_alt = htmlentities($_POST['img_alt']);
            $free_lessons = intval($_POST['free_lessons']);

            $short_desc = htmlentities($_POST['short_desc']);
            $view_desc = base64_encode(serialize($_POST['view_desc']));

            $sort_less = intval($_POST['sort_less']);
            $status = intval($_POST['status']);
            if(empty($_POST['alias'])){
                $alias = System::Translit($_POST['name']);
                if(System::searchDuplicateAlias($alias, 'course')) $alias = $alias.'-1';
            } else {
                $alias = $_POST['alias'];
                if(System::searchDuplicateAlias($alias, 'course', true)) $alias = $alias.'-1';
            }

            if(empty($_POST['title'])){
                $title = $name;
            } else {
                $title = $_POST['title'];
            }
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            $desc = $_POST['desc'];

            $course_access = intval($_POST['access_type']);
            if(!empty($_POST['groups'])) $groups = serialize($_POST['groups']);
            else $groups = null;
            if(!empty($_POST['accesses'])) $access = serialize($_POST['accesses']);
            else $access = null;

            $lessons_count = intval($_POST['lessons_count']);
            $show_desc = intval($_POST['show_desc']);
            $show_progress = intval($_POST['show_progress']);
            $show_comments = intval($_POST['show_comments']);
            $show_hits = intval($_POST['show_hits']);
            $show_pupil = intval($_POST['show_pupil']);
            if(isset($_POST['show_begin'])) $show_begin = intval($_POST['show_begin']);
            else $show_begin = 0;
            $sertificate = intval($_POST['sertificate']);
            if(isset($_POST['curators'])) $curators = serialize($_POST['curators']);
            else $curators = null;

            $sort = intval($_POST['sort']);
            $show_in_main = intval($_POST['show_in_main']);

            $type_access = intval($_POST['type_access_buy']);
            $product_access = intval($_POST['product_access_buy']);
            $link_access = htmlentities($_POST['link_access_buy']);

            $is_free = intval($_POST['is_free']);
            $button_text = htmlentities($_POST['button_text']);

            if(!empty($_POST['start'])) $start = strtotime($_POST['start']);
            else $start = null;
            if(!empty($_POST['end'])) $end = strtotime($_POST['end']);
            elseif(!empty($_POST['start'])) $end = $start + 230720000; // 15 лет
            else $end = null;

            if(isset($_FILES['cover'])){
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/course/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if(is_uploaded_file($tmp_name)){
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                    $resize = System::imgResize($path, false, 350);
                }
            }

            $add = Course::AddCourse($name, $cat_id, $prof_id, $autotrain, $img, $img_alt, $sort, $sort_less, $status, $alias, $title, $meta_desc, $meta_keys,
                $desc, $lessons_count, $show_desc, $show_progress, $show_comments, $show_hits, $show_pupil, $sertificate, $start, $end, $curators,
                $type_access, $product_access, $link_access, $show_begin, $free_lessons, $show_in_main, $short_desc, $is_free, $button_text,
                $view_desc, $course_access, $groups, $access);
            if($add) header("Location: ".$setting['script_url']."/admin/courses?success");
        }

        require_once (ROOT . '/template/admin/views/course/add_course.php');
    }



    // РЕДАКТИРОВАТЬ КУРС
    public static function actionEdit($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];
        $id = intval($id);

        $params = unserialize(base64_decode(Course::getCourseSetting()));
        

        // Добавление блока
        if(isset($_POST['add_block']) && !empty($_POST['block_name']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            if(!isset($acl['change_courses'])){
                header("Location: /admin/courses");
                exit();
            }
            $name = htmlentities($_POST['block_name']);
            $sort = intval($_POST['sort']);
            $add = Course::addLessonBlock($id, $name, $sort);
            if($add) header("Location: /admin/courses/edit/$id");

        }



        // Изменение \ удаление блоков
        if(isset($_POST['del_block']) || isset($_POST['save_block'])){

            if(!isset($acl['change_courses'])){
                header("Location: /admin/courses");
                exit();
            }
            $block_id = intval($_POST['block_id']);
            $sort = intval($_POST['sort']);
            $block_name = htmlentities($_POST['block_name']);

            if(isset($_POST['del_block'])) {

                $del = Course::editLessonBlock($block_id, $sort, $block_name, 0);
                if($del) header("Location: /admin/courses/edit/$id?success");
                else header("Location: /admin/courses/edit/$id?fail");
            }

            if(isset($_POST['save_block'])) {

                $del = Course::editLessonBlock($block_id, $sort, $block_name, 1);
                if($del) header("Location: /admin/courses/edit/$id?success");
                else header("Location: /admin/courses/edit/$id?fail");
            }
        }




        if(isset($_POST['savecourse']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            if(!isset($acl['change_courses'])){
                header("Location: /admin/courses");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $cat_id = intval($_POST['cat_id']);
            $prof_id = intval($_POST['prof_id']);
            $autotrain = intval($_POST['autotrain']);
            $img_alt = htmlentities($_POST['img_alt']);
            $padding = htmlentities($_POST['padding']);

            $course_access = intval($_POST['access_type']);
            if(!empty($_POST['groups'])) $groups = serialize($_POST['groups']);
            else $groups = null;
            if(!empty($_POST['accesses'])) $access = serialize($_POST['accesses']);
            else $access = null;

            $short_desc = htmlentities($_POST['short_desc']);
            if($_POST['view_desc']['type'] != 0) $view_desc = base64_encode(serialize($_POST['view_desc']));
            else $view_desc = null;

            $sort = intval($_POST['sort_course']);
            $sort_less = intval($_POST['sort_less']);
            $status = intval($_POST['status']);
            if(empty($_POST['alias'])){
                $alias = System::Translit($_POST['name']);
                if(System::searchDuplicateAlias($alias, 'course')) $alias = $alias.'-1';
            } else {
                $alias = $_POST['alias'];
                if(System::searchDuplicateAlias($alias, 'course', true)) $alias = $alias.'-1';
            }

            if(empty($_POST['title'])){
                $title = $name;
            } else {
                $title = $_POST['title'];
            }
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            $desc = $_POST['desc'];
            if(isset($_POST['curators'])) $curators = serialize($_POST['curators']);
            else $curators = null;

            $lessons_count = intval($_POST['lessons_count']);
            $show_desc = intval($_POST['show_desc']);
            $show_progress = intval($_POST['show_progress']);
            $show_comments = intval($_POST['show_comments']);
            $show_hits = intval($_POST['show_hits']);
            $show_pupil = intval($_POST['show_pupil']);
            $show_begin = intval($_POST['show_begin']);
            $sertificate = intval($_POST['sertificate']);

            $type_access = intval($_POST['type_access_buy']);
            $product_access = intval($_POST['product_access_buy']);
            $link_access = htmlentities($_POST['link_access_buy']);
            $author_id = intval($_POST['author_id']);
            $free_lessons = intval($_POST['free_lessons']);

            $show_in_main = intval($_POST['show_in_main']);
            $is_free = intval($_POST['is_free']);
            $button_text = htmlentities($_POST['button_text']);

            if(!empty($_POST['start'])) $start = strtotime($_POST['start']);
            else $start = null;
            if(!empty($_POST['end'])) $end = strtotime($_POST['end']);
            elseif(!empty($_POST['start'])) $end = $start + 230720000; // 15 лет
            else $end = null;

            if(isset($_FILES['cover']) && $_FILES["cover"]["size"] != 0){
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/course/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if(is_uploaded_file($tmp_name)){
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                    $resize = System::imgResize($path, false, 350);
                }
            } else $img = $_POST['current_img'];

            $edit = Course::EditCourse($id, $name, $cat_id, $prof_id, $autotrain, $img, $img_alt, $sort, $sort_less, $status, $alias, $title, $meta_desc, $meta_keys,
                $desc, $lessons_count, $show_desc, $show_progress, $show_comments, $show_hits, $show_pupil, $sertificate, $start, $end, $curators,
                $type_access, $product_access, $link_access, $author_id, $padding, $show_begin, $free_lessons, $show_in_main, $short_desc, $is_free,
                $button_text, $view_desc, $course_access, $groups, $access);
            if($edit) header("Location: ".$setting['script_url']."/admin/courses/edit/$id?success");


        }

        $course = Course::getCourseByID($id);
        $lessons_blocks = Course::getBlocksFromCourse($id); // список блоков в курсе
        if($lessons_blocks) $sort_num = end($lessons_blocks); // последний порядочный номер блока

        $view_desc = unserialize(base64_decode($course['view_desc']));

        require_once (ROOT . '/template/admin/views/course/edit.php');
    }



    // УДАЛИТЬ КУРС
    public function actionDelcourse($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_courses'])){
            header("Location: /admin/courses");
            exit();
        }

        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];
        $id = intval($id);

        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Course::DelCourse($id);
            header("Location: ".$setting['script_url']."/admin/courses");
        }
    }



    // ОБНОВИТЬ ПОРЯДОК КУРСОВ (SORT)
    public function actionUpdSort() {
        if (!empty($_POST['sort'])) {
            $resp = array('status' => true, 'error' => '');
            foreach ($_POST['sort'] as $sort => $course_id) {
                $result = Course::UpdateSortCourse(intval($course_id), intval($sort)+1);
                if (!$result) {
                    $resp['status'] = false;
                    $resp['error'] = 'Не удалось сохранить sort для курса с id = ' . $course_id;
                    break;
                };
            }
            echo json_encode($resp);
        }
    }


    /**
     *  КАТЕГОРИИ КУРСОВ
     */


    // СПИСОК КАТЕГОРИЙ
    public static function actionCats()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];

        $params = unserialize(base64_decode(Course::getCourseSetting()));
        

        $cat_list = Course::getCourseCatFromList();

        require_once (ROOT . '/template/admin/views/course/cat_index.php');
    }


    // ДОБАВИТЬ КАТЕГОРИЮ КУРСА
    public function actionAddcat()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];

        $params = unserialize(base64_decode(Course::getCourseSetting()));
        

        if(isset($_POST['addcat']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            if(!isset($acl['change_courses'])){
                header("Location: /admin/courses");
                exit();
            }
            $name = htmlentities($_POST['name']);
            if(empty($_POST['alias'])) $alias = System::Translit($_POST['name']);
            else $alias = $_POST['alias'];

            if(empty($_POST['title'])) $title = $name;
            else $title = $_POST['title'];

            $img_alt = htmlentities($_POST['img_alt']);
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            $status = intval($_POST['status']);
            $desc = $_POST['cat_desc'];

            if(isset($_FILES['cover'])){
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/course/category/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if(is_uploaded_file($tmp_name)){
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                }
            }

            $add_cat = Course::AddCategory($name, $img, $img_alt, $alias, $title, $meta_desc, $meta_keys, $status, $desc);

            if($add_cat) header("Location: ".$setting['script_url']."/admin/courses/cats?success");


        }

        $cat_list = Course::getCourseCatFromList();

        require_once (ROOT . '/template/admin/views/course/add_cat.php');
    }


    // ИЗМЕНИТЬ КАТЕГОРИЮ
    public function actionEditcat($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();

        if(isset($_POST['editcat']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            if(!isset($acl['change_courses'])){
                header("Location: /admin/courses");
                exit();
            }
            $name = htmlentities($_POST['name']);
            if(empty($_POST['alias'])) $alias = System::Translit($_POST['name']);
            else $alias = $_POST['alias'];

            if(empty($_POST['title'])) $title = $name;
            else $title = $_POST['title'];

            $img_alt = htmlentities($_POST['img_alt']);
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            $status = intval($_POST['status']);
            $desc = $_POST['cat_desc'];

            if(isset($_FILES['cover']) && $_FILES["cover"]["size"] != 0){
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/course/category/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if(is_uploaded_file($tmp_name)){
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                }
            } else $img = $_POST['current_img'];

            $edit_cat = Course::EditCategory($id, $name, $img, $img_alt, $alias, $title, $meta_desc, $meta_keys, $status, $desc);
            if($edit_cat) header("Location: ".$setting['script_url']."/admin/courses/cats/edit/$id?success");
        }

        $category = Course::getCourseCatData($id);

        require_once (ROOT . '/template/admin/views/course/cat_edit.php');
    }



    // УДАЛИТЬ КАТЕГОРИЮ
    public function actionDelcat($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_courses'])) {
            header("Location: /admin/courses");
            exit();
        }
        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];
        $id = intval($id);

        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Course::DelCategory($id);
            if($del) header("Location: ".$setting['script_url']."/admin/courses/cats?success");
            else header("Location: ".$setting['script_url']."/admin/courses/cats?fail");
        }
    }



    /**
     *  ПРОФЕССИИ
     */

    // СПИСОК ПРОФЕССИЙ
    public function actionProfs()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];

        $prof_list = Course::getCourseProfList();

        require_once (ROOT . '/template/admin/views/course/prof_index.php');
    }



    // ДОБАВИТЬ ПРОФЕССИЮ
    public function actionAddprof()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();

        if(isset($_POST['addprof']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            if(!isset($acl['change_courses'])){
                header("Location: /admin");
                exit();
            }
            $name = htmlentities($_POST['name']);
            if(empty($_POST['alias'])) $alias = System::Translit($_POST['name']);
            else $alias = $_POST['alias'];

            if(empty($_POST['title'])) $title = $name;
            else $title = $_POST['title'];

            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            $prof_desc = $_POST['prof_desc'];

            $add = Course::AddProff($name, $alias, $title, $meta_desc, $meta_keys, $prof_desc );
            if($add) header("Location: ".$setting['script_url']."/admin/courses/profs?success");

        }

        require_once (ROOT . '/template/admin/views/course/add_prof.php');
    }


    public function actionEditprof($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];
        $id = intval($id);

        if(isset($_POST['editprof']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_courses'])){
                header("Location: /admin");
                exit();
            }
            $name = htmlentities($_POST['name']);
            if(empty($_POST['alias'])) $alias = System::Translit($_POST['name']);
            else $alias = $_POST['alias'];

            if(empty($_POST['title'])) $title = $name;
            else $title = $_POST['title'];

            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            $prof_desc = $_POST['prof_desc'];

            $edit = Course::EditProff($id, $name, $alias, $title, $meta_desc, $meta_keys, $prof_desc);
            if($edit) header("Location: ".$setting['script_url']."/admin/courses/profs/edit/$id");

        }

        $prof = Course::getCourseProfData($id);

        require_once (ROOT . '/template/admin/views/course/prof_edit.php');
    }


    // УДАЛИТЬ ПРОФЕССИЮ
    public function actionDelprof($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_courses'])) {
            header("Location: /admin/courses");
            exit();
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();

        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Course::DelProff($id);
            if($del) header("Location: ".$setting['script_url']."/admin/courses/profs?success");
            else header("Location: ".$setting['script_url']."/admin/courses/profs?fail");
        }
    }



    /**
     *   МАТЕРИАЛЫ И ЗАДАНИЯ
     */

    // СПИСОК с ДОП,МАТом
    public function actionDopmat()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];

        $params = unserialize(base64_decode(Course::getCourseSetting()));
        

        $dopmat_list = Course::getDopMatList();

        require_once (ROOT . '/template/admin/views/course/dopmat_index.php');
    }



    // ДОБАВИТЬ ДОП, МАТ
    public function actionDopmatadd()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];

        if(isset($_POST['adddop']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            if(!isset($acl['change_courses'])){
                header("Location: /admin");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $cat_id = intval($_POST['cat_id']);

            if(isset($_FILES['file']) && $_FILES["file"]["size"] != 0 && $_FILES['inputfile']['error'] == 0){
                $tmp_name = $_FILES["file"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["file"]["name"]; // Имя картинки при загрузке

                // Отделить расширение и добавить к имени произволный набор цифр (для уникальности)
                $img = time().$img;

                $folder = ROOT . '/load/dopmat/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if(is_uploaded_file($tmp_name)){
                    $move = move_uploaded_file($tmp_name, $path);
                }


                $add = Course::AddDopMatItem($name, $cat_id, $img);
                if($add) header("Location: ".$setting['script_url']."/admin/dopmat?success");
            } else header("Location: ".$setting['script_url']."/admin/dopmat?fail");

        }

        require_once (ROOT . '/template/admin/views/course/dopmat_add.php');
    }


    // Изменить ДОП,МАТ
    public function actionDopmatedit($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];
        $id = intval($id);

        if(isset($_POST['delete_file']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            $file = htmlentities($_POST['file']);

            unlink(ROOT . '/load/dopmat/'.$file);
            $del = Course::delFileInDopmat($id);
        }

        if(isset($_POST['editdop']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            if(!isset($acl['change_courses'])){
                header("Location: /admin");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $cat_id = intval($_POST['cat_id']);
            if(isset($_FILES['file']) && $_FILES["file"]["size"] != 0){
                $tmp_name = $_FILES["file"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["file"]["name"]; // Имя картинки при загрузке

                // Отделить расширение и добавить к имени произволный набор цифр (для уникальности)
                $img = explode(".", $img);
                $img = $img[0] .time().'.'.$img[1];

                $folder = ROOT . '/load/dopmat/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if(is_uploaded_file($tmp_name)){
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                }
            } else $img = $_POST['current_file'];

            $edit = Course::EditDopMat($id, $name, $cat_id, $img );

        }

        $dopmat = Course::getDopMatData($id);

        require_once (ROOT . '/template/admin/views/course/dopmat_edit.php');
    }


    // КАТЕГОРИИ ДОП,МАТА
    public function actionDopmatcat()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];

        if(isset($_POST['addcat']) && !empty($_POST['cat_name']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            if(!isset($acl['change_courses'])){
                header("Location: /admin");
                exit();
            }

            $name = htmlentities($_POST['cat_name']);

            $add_cat = Course::AddDopmatCat($name);
            if($add_cat) header("Location: ".$setting['script_url']."/admin/dopmat/cat?success");
        }

        $cat_list = Course::getDopmatCat();

        require_once (ROOT . '/template/admin/views/course/dopmat_cats.php');
    }


    // УДАЛИТЬ КАТЕГОРИЮ ДОП,МАТА
    public function actionDeldopmatcat($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_courses'])) {
            header("Location: /admin/courses");
            exit();
        }
        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];
        $id = intval($id);

        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Course::delDopmatCat($id);
            if($del) header("Location: ".$setting['script_url']."/admin/dopmat/cat?success");
            else header("Location: ".$setting['script_url']."/admin/dopmat/cat?fail");
        }

    }


    // УДАЛИТЬ ДОП МАТ
    public function actionDopmatdel($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_courses'])) {
            header("Location: /admin/courses");
            exit();
        }
        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];
        $id = intval($id);

        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Course::delDopmat($id);
            if($del) header("Location: ".$setting['script_url']."/admin/dopmat?success");
        }
    }



    /**
     *   ОТВЕТЫ НА ЗАДАНИЯ
     */

    // СПИСОК ОТВЕТОВ
    public function actionAnswer()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $params = unserialize(base64_decode(Course::getCourseSetting()));
        
        $course_id = isset($_SESSION['course_id']) ? $_SESSION['course_id'] : null;
        $lesson_id = isset($_SESSION['lesson_id']) ? $_SESSION['lesson_id'] : null;
    
        $is_pagination = true;
        $status = 1;
        if(isset($_GET['get']) && $_GET['get'] == 'check') {
            $status = 0;
            $is_pagination = false;
        }

        if(isset($_POST['filter'])) {
            if (isset($_POST['course_id'])) {
                $_SESSION['course_id'] = intval($_POST['course_id']);
            }
            if (isset($_POST['lesson_id'])) {
                $_SESSION['lesson_id'] = intval($_POST['lesson_id']);
            }
            
            $redirect_url = '/admin/answers' . (isset($_GET['get']) ? '?get=check' : '');
            header("Location: $redirect_url");
            exit();
        } elseif (isset($_POST['reset'])) {
            unset($_SESSION['course_id']);
            unset($_SESSION['lesson_id']);
    
            $redirect_url = '/admin/answers' . (isset($_GET['get']) ? '?get=check' : '');
            header("Location: $redirect_url");
        }
    
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $total = Course::getTotalAnswers($status, $course_id, $lesson_id);
        $pagination = new Pagination($total, $page, $setting['show_items']);
        
        $answer_list = Course::getAnswerList($status, $page, $setting['show_items'], $is_pagination, $course_id, $lesson_id);

        require_once (ROOT . '/template/admin/views/course/answers.php');
    }



    // ПРОСМОТР ОТВЕТА | ДИАЛОГА
    public static function actionAnswerview($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();

        $lesson_id = isset($_GET['lesson']) ? intval($_GET['lesson']) : null;
        $user_id = isset($_GET['user']) ? intval($_GET['user']) : null;
    
        if (!$user_id || !$lesson_id) {
            exit('неверные параметры URL');
        }
        
        // Ответ куратора
        if (isset($_POST['post_message'])) {
            if (!isset($acl['change_courses'])) {
                header("Location: /admin");
            }

            if (empty($_POST['message']) && !isset($_POST['success'])) {
                header("Location: {$setting['script_url']}/admin/answers/");
            } else {
                $message = isset($_POST['message']) ? $_POST['message'] : '';
                $success = isset($_POST['success']) ? 1 : 0;
                $email = htmlentities($_POST['user_email']);
                $name = htmlentities($_POST['user_name']);

                if (!$message && $success) {
                    $upd = Course::UpdateLessonComplete($user_id, $lesson_id, 1, time());
                    $upd = Course::UpdateDialogAnswersStatus($lesson_id, $user_id);
                    
                    header("Location: ".$setting['script_url']."/admin/answers/");
                } elseif ($message) {
                    // Записать ответ
                    $less_data = Course::getLessonDataByID($lesson_id);
                    
                    $attachments = isset($_FILES['lesson_attach']) && !empty($_FILES['lesson_attach']) ?
                        Course::attachUpload($lesson_id, $_SESSION['admin_user'], Course::USER_TYPE_ADMIN) : '';
                    
                    $act = Course::AddAnswertoDialog($id, $_SESSION['admin_user'], $message, $success, $lesson_id, $user_id, $less_data['course_id'], $attachments);
                    if ($act) {
                        // Выставить статус 1 для диалогов
                        $upd = Course::UpdateDialogAnswersStatus($lesson_id, $user_id);
                        $course_data = Course::getCourseByID($less_data['course_id']);
                        $send = Email::SendUserNotifAboutTaskAnswer($email, $name, $course_data['alias'], $less_data['alias']);
                    }
                    
                    header("Location: {$setting['script_url']}{$_SERVER['REQUEST_URI']}");
                }
            }
        }
        
        $dialog_list = Course::getDialogList($user_id, $lesson_id);
        $lesson = Course::getLessonDataByID($lesson_id, 0);

        require_once (ROOT . '/template/admin/views/course/answer_view.php');
    }




    // УДАЛИТЬ ВЕСЬ ДИАЛОГ в УРОКЕ
    public function actionDeldialog($user, $lesson)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_courses'])) {
            header("Location: /admin/courses");
            exit();
        }
        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];
        $user = intval($user);
        $lesson = intval($lesson);

        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Course::delDialog($user, $lesson);
            if($del) header("Location: ".$setting['script_url']."/admin/answers?success");
        }
    }


    // УДАЛИТЬ СООБЩЕНИЕ В ДИАЛОГЕ
    public function actionDelmessage($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_courses'])) {
            header("Location: /admin/courses");
            exit();
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();

        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Course::delDialogMessage($id);
            if($del) header("Location: ".$setting['script_url']."/admin/answers?success");
        }
    }


    // НАСТРОЙКИ ОНЛАЙН КУРСОВ
    public function actionSetting()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_courses'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();

        if(isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            if(!isset($acl['change_courses'])){
                header("Location: /admin");
                exit();
            }

            $params = base64_encode(serialize($_POST['course']));
            $status = intval($_POST['status']);

            $save = Course::SaveCourseSetting($params, $status);
        }

        $params = unserialize(base64_decode(Course::getCourseSetting()));
        $enable = Course::getCourseStatus();
        require_once (ROOT . '/template/admin/views/course/setting.php');
    }


}