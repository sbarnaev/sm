<?php defined('BILLINGMASTER') or die;


class courseController {
    
    
    // ГЛАВНАЯ СТРАНИЦА КУРСОВ И КАТЕГОРИЙ
    public function actionIndex ()
    {
        $setting = System::getSetting();
        $is_page = 'courses_index';
        $cat_name = false;
        
        $extension = System::CheckExtensension('courses', 1);
        if (!$extension) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;
        }

        $params = unserialize(base64_decode(Course::getCourseSetting()));
    
        $use_css = 1;
        $user_id = intval(User::isAuth());
        
        // Если в URL нет параметров для категории
        if (!isset($_GET['category'])) {
            $title = $params['params']['title'];
            $meta_desc = $params['params']['desc'];
            $meta_keys = $params['params']['keys'];
            $h1 = $params['params']['h1'];

            $cats = Course::getCourseCatFromList(1); // Получить категории курсов
            $courses = Course::getCourseList(1, 0); // Получить курсы без категории
        } else {
            $alias = htmlentities($_GET['category']);
            $cat = Course::getCatDataByAlias($alias); // Получить данные категории по алиасу

            if ($cat) {
                $title = $cat['title'];
                $meta_desc = $cat['meta_desc'];
                $meta_keys = $cat['meta_keys'];
                $cat_name = $cat['name'];

                $courses = Course::getCourseList(1, $cat['cat_id']); // Получить курсы данной категории
                $cats = false;
            } else {
                require_once (ROOT . '/template/'.$setting['template'].'/404.php');
                exit;
            }
        }
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/course/index.php');
        return true;
    }
    
    
    
    // СТРАНИЦА МОИ КУРСЫ
    public function actionMycourses()
    {
        $setting = System::getSetting();
        $is_page = 'courses';
        $cat_name = false;
        $title = 'Доступные онлайн курсы';
        $meta_desc = '';
        $meta_keys = '';
        
        $extension = System::CheckExtensension($is_page, 1);
        if (!$extension) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;
        }
        
        // Проверка авторизации
        $user_id = intval(User::checkLogged());
    
        $use_css = 1;
        $user_id = User::isAuth();
        if ($user_id) {
            $user = User::getUserById($user_id);
        }
        
        $duration = null;
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/course/mycourses.php');
        
        return true;
    }
    
    
    
    // СТРАНИЦА КУРСА и СПИСКА УРОКОВ
    public function actionCourse($alias)
    {
        $alias = htmlentities($alias);
        $setting = System::getSetting();
        $is_page = 'lessons_list';
        $ext = 'courses';
        $time = time();
        $complete = false;
        $map_items = false;
        $open = false;
        $use_css = 1;
        $block = null;
        $extension = System::CheckExtensension($ext, 1);
        if (!$extension) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;
        }
        
        $params = unserialize(base64_decode(Course::getCourseSetting()));
        $course = Course::getCourseDataByAlias($alias); // данные курса по алиасу

        if ($course) {
            $title = $course['title'];
            $meta_desc = $course['meta_desc'];
            $meta_keys = $course['meta_keys'];
            $user_planes = false;
            $user_groups = false;
            $access = false;
			
			if($course['start_date'] > $time) {
				require_once (ROOT . '/template/'.$setting['template'].'/views/course/no_access_course.php');
				return true;
			}
            
            $user_id = intval(User::isAuth());
            if ($user_id) {
                $user_groups = User::getGroupByUser($user_id); // группы пользователя
                $membership = System::CheckExtensension('membership', 1); // подписки пользователя

                if ($membership) {
                    $user_planes = Member::getPlanesByUser($user_id);
                }

                if ($course['auto_train'] == 1) { // если курс автотренинг, то считаем толкьо пройденные уроки
                    $status_less = 1;
                } else { // иначе считаем все открытые уроки
                    $status_less = 0;
                }

                $map_items = Course::getCompleteLessonsUser($user_id, $course['course_id'], $status_less);  // карта прохождения уроков (0 - все уроки, 1 - только пройденные)
                $map_items_progress = Course::getCompleteLessonsUser($user_id, $course['course_id'], 1);
                $count_map_items = Course::countUniqCompeteLessons ($course['course_id'], $user_id, $status_less);
            }
            
        
            // В зависимости от того есть ли в курсе блоки или в уроках указаны блоки?????
            
            $lesson_list = Course::getLessonsList(1, $course['course_id'], $course['sort_less']);

            if ($course['auto_train'] == 1) {
                require_once (ROOT . '/template/'.$setting['template'].'/views/course/course_auto.php');
            } else {
                require_once (ROOT . '/template/'.$setting['template'].'/views/course/course.php');
            }
            
            return true;
        } else {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;
        }
    }
    
    
    
    // СТРАНИЦА УРОКА
    public function actionLesson($category, $lesson)
    {
        $setting = System::getSetting();
        $is_page = 'lesson_page';
        $ext = 'courses';
        $jquery_head = 1;
        $access = isset($_SESSION['admin_token']) ? true : false;
        $complete = false; // Пройден урок или нет.
        $extension = System::CheckExtensension($ext, 1);
        
        if (!$extension) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;
        }
        
        $params = unserialize(base64_decode(Course::getCourseSetting()));
        if (!empty($params['params']['commenthead'])) {
            $comments = 1;
        }
        
        $category = htmlentities($category);
        $lesson = htmlentities($lesson);
        $date = time();
        
        $use_css = 1;
        $plane_arr = false;
        $group_arr = false;
        //$cat_name = false;
        
        // Получить данные курса по алиасу
        $course = Course::getCourseDataByAlias($category);
        if (!$course) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;
        }
        
        $user = intval(User::isAuth());
        if ($user) {
            // Получить группы пользователя
            $group_arr = User::getGroupByUser($user);
            
            // Проверка доступа к уроку ч.1
            if ($course['auto_train'] == 1) {
                $status_less = 1; // если курс автотренинг, то считаем толкьо пройденные уроки
                $count_map_items = Course::countUniqCompeteLessons ($course['course_id'], $user, $status_less);
            }
            
            $membership = System::CheckExtensension('membership', 1);
            if ($membership) {
                $plane_arr = Member::getPlaneListByUser($user);
            }
            
            // Получить карту прохождения уроков
            $map_items = Course::getCompleteLessonsUser($user, $course['course_id'], 0); // нужно добавить 0 чтобы выбрать уроки со статусом на проверке
            $map_items_progress = Course::getCompleteLessonsUser($user, $course['course_id'], 1);
        }
        
        // Получить данные урока по алиасу и ID курса
        $lesson = Course::getLessonDataByAlias($lesson, $course['course_id']);
        if (!$lesson) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;
        }
        
        // Проверка доступа к уроку ч.2
        $next = 0;
        $open = true;
        if (isset($count_map_items)) {
            $next = $count_map_items + 1;
            $open = $lesson['sort'] <= $next ? true : false;
        }
        

        // Проверка доступа к уроку
        if ($lesson['access_type'] == 1 && $lesson['groups'] != null) { // если доступ по группам
            $groups_less = unserialize($lesson['groups']);
            foreach($groups_less as $group_id) {
                if ($group_arr && in_array($group_id, $group_arr) && $open) {
                    $access = true;
                    $timing_group = $group_id; // Вот здесь может быть баг, если групп несколько
                }
            }
        } elseif ($lesson['access_type'] == 2) { // если по плану подписки
            if ($lesson['access'] != null) {
                $access_less = unserialize($lesson['access']);
                foreach($access_less as $subs_id) {
                    if ($plane_arr && in_array($subs_id, $plane_arr) && $open) {
                        $access = true;
                        $timing_plane = $subs_id; // Вот здесь может быть баг, если подпсиок несколько
                    }
                }
            }
        } elseif ($lesson['access_type'] == 9) { // если ИЗ настроек курса
            if ($course['access_type'] == 1) {
                // Если по группам
                if ($course['groups'] != null) {
                    $groups_less = unserialize($course['groups']);
                    foreach($groups_less as $group_id) {
                        if ($group_arr && in_array($group_id, $group_arr) && $open) {
                            $access = true;
                            $timing_group = $group_id; // Вот здесь может быть баг, если групп несколько
                        }
                    }
                }
            } elseif ($course['access_type'] == 2) {
                // Если по подпискам
                if ($course['access'] != null) {
                    $access_less = unserialize($course['access']);
                    foreach($access_less as $subs_id) {
                        if ($plane_arr) {
                            if (in_array($subs_id, $plane_arr) && $open) {
                                $access = true;
                                $timing_plane = $subs_id; // Вот здесь может быть баг, если подпсиок несколько
                            }
                        }
                    }
                }
            } else {
                $access = true;
            }
        } elseif ($open) { // если досутп свободен
            $access = true;
        }
        

        // ЗАДАНИЕ
        if (isset($_POST['complete']) && $access == true) {
            $answer = isset($_POST['answer']) && !empty($_POST['answer']) ? $_POST['answer'] : null;
            // Обработка задания
            
            $attachments = isset($_FILES['lesson_attach']) && !empty($_FILES['lesson_attach']) ?
                Course::attachUpload($lesson['lesson_id'], $user, Course::USER_TYPE_USER) : '';
            
            $process = Course::ProcessTask($user, $course['course_id'], $lesson['lesson_id'], $lesson['task_type'], $lesson['task_time'], $answer, $date, $course['curators'], $attachments);
            if ($process) {
                header("Location: ".$setting['script_url']."/courses/". $course['alias']."/".$lesson['alias']."#complete");
            }
        }

        $title = $lesson['title'];
        $meta_desc = $lesson['meta_desc'];
        $meta_keys = $lesson['meta_keys'];
        
        // Список ответов к уроку
        $messages = $user ? Course::getDialogList($user, $lesson['lesson_id']) : false;
        
        // Задержка доступа по расписанию
        $open_time = 0;
        $multiplier = 1;
        if ($lesson['timing'] != 0) {
            switch($lesson['timing_period']) {
                case "hour":
                    $multiplier = 3600;
                    break;
                
                case "day":
                    $multiplier = 86400;
                    break;
                
                case "week":
                    $multiplier = 604800;
                    break;
                
                case "month":
                    $multiplier = 2592000;
                    break;
            }
            
            $timing = $lesson['timing'] * $multiplier; // кол-во секунд для задержки
            
            // если у тренинга есть дата старта
            if ($course['start_date'] != null) {
                $open_time = $course['start_date'] + $timing;
            } else {
                // если по дате покупки доступа
                
				$date_user = 0;
				if ($user) {
					if (isset($timing_group)) { // Получить дату прсивоения группы этому юзеру
						$date_user = Course::getPaidUserDate($user, $timing_group, false);
					} elseif (isset($timing_plane)) { // Доступ по подписке
						$date_user = Course::getPaidUserDate($user, false, $timing_plane);
					} else {
						$date_user = 0;
					}
				}
                
                $open_time = $date_user + $timing;
            }
        }

        if ($course['auto_train'] == 1) {
            $next = 1;
            if (isset($map_items) && $map_items == true) {
                $i = 0;
                foreach($map_items as $m_item) {
                    $less[$i++] = Course::getSortLessByID($m_item['lesson_id']);
                }
                
                $next = max($less) + 1; // Номер следующего открытого урока
            }
    
            $open = $lesson['sort'] <= $next ? true : false;
             
            // Для автотренинга
            
            // Доступ по расписанию
            $open = $date > $open_time ? true : false;
            
            if ($access && $open) {
                if ($user && CallPassword::notAccessUser($user)) {
                    require_once (ROOT . '/extensions/callpassword/views/course/no_access.php');
                } else {
                    require_once (ROOT . '/template/'.$setting['template'].'/views/course/lesson.php');
                }
            } else {
                require_once (ROOT . '/template/'.$setting['template'].'/views/course/no_access.php');
            }
            
        } else { // Для обычного тренинга
            // Доступ по расписанию
            $access = $access == true && $date > $open_time ? true : false;
            
            if ($access) {
                if ($user && CallPassword::notAccessUser($user)) {
                    require_once (ROOT . '/extensions/callpassword/views/course/no_access.php');
                } else {
                    require_once (ROOT . '/template/'.$setting['template'].'/views/course/lesson.php');
                }
            } else {
                require_once (ROOT . '/template/'.$setting['template'].'/views/course/no_access.php');
            }
        }
        
        return true;
    }
    
    
    // КАБИНЕТ КУРАТОРА
    public function actionCurator()
    {
        $setting = System::getSetting();
        $userId = intval(User::checkLogged());
        $extension = System::CheckExtensension('courses', 1);
        if ($extension) {// Проверка авторизации
            // Данные юзера
            $user = User::getUserById($userId);
            
            if ($user['is_curator'] != 1) {
                require_once (ROOT . '/template/'.$setting['template'].'/404.php');
                exit;
            }
        }
        
        
        $title = 'Кабинет куратора курсов';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = 'lk';
        $course_id = isset($_SESSION['course_id']) ? $_SESSION['course_id'] : null;
        $lesson_id = isset($_SESSION['lesson_id']) ? $_SESSION['lesson_id'] : null;
        $is_pagination = false;
        $show_items = 100;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $status = 0;
        
        if (isset($_POST['filter'])) {
            if (isset($_POST['course_id'])) {
                $_SESSION['course_id'] = intval($_POST['course_id']);
            }
            
            if (isset($_POST['lesson_id'])) {
                $_SESSION['lesson_id'] = intval($_POST['lesson_id']);
            }
    
            $redirect_url = '/lk/answers' . (isset($_GET['get']) ? '?get=all' : '');
            header("Location: $redirect_url");
            exit;
        } elseif (isset($_POST['reset'])) {
            unset($_SESSION['course_id']);
            unset($_SESSION['lesson_id']);
            
            $redirect_url = '/lk/answers' . (isset($_GET['get']) ? '?get=all' : '');
            header("Location: $redirect_url");
            exit;
        }
        
        if (isset($_POST['accept']) && !empty($_POST['lesson_id']) && !empty($_POST['user_id'])) {
            $lesson_id = intval($_POST['lesson_id']);
            $user_id = intval($_POST['user_id']);
            $accept = Course::UpdateDialogAnswersStatus($lesson_id, $user_id);
            
            if ($accept) {
                header("Location: /lk/answers");
            }
        }
    
        if (isset($_GET['get']) && $_GET['get'] == 'all') {
            $status = 1;
            $is_pagination = true;
            $show_items = $setting['show_items'];
            $total_answers = Course::getTotalAnswers($status, $course_id, $lesson_id);
            $pagination = new Pagination($total_answers, $page, $setting['show_items']);
        }
        
        $answer_list = Course::getAnswerList($status, $page, $show_items, $is_pagination, $course_id, $lesson_id);
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/users/curator.php');
        
        return true;
    }
    
    
    
    
    // ОТВЕТ НА ВОПРОС
    public function actionAnswer($id)
    {
        $setting = System::getSetting();
        $extension = System::CheckExtensension('courses', 1);

        // Проверка авторизации
        $curator_id = intval(User::checkLogged());

        if ($extension) {
            // Данные юзера
            $user = User::getUserById($curator_id);
            if ($user['is_curator'] != 1) {
                require_once (ROOT . '/template/'.$setting['template'].'/404.php');
                exit;
            }
        }

        $title = 'Кабинет куратора курсов';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = 'lk';

        $user_id = isset($_GET['user']) ? intval($_GET['user']) : null;
        $lesson_id = isset($_GET['lesson']) ? intval($_GET['lesson']) : null;
        if (!$user_id || !$lesson_id) {
            exit('неверные параметры URL');
        }

        // Ответ куратора
        if (isset($_POST['post_message']) || isset($_POST['success'])) {
            $user_data = User::getUserById($user_id);
            $email = $user_data['email'];
            $name = $user_data['user_name'];

            $message = isset($_POST['message']) ? $_POST['message'] : '';
            $success = isset($_POST['success']) ?  1 : 0;

            $less_data = Course::getLessonDataByID($lesson_id);
            
            $attachments = isset($_FILES['lesson_attach']) && !empty($_FILES['lesson_attach']) ?
                Course::attachUpload($less_data['lesson_id'], $curator_id, Course::USER_TYPE_CURATOR) : '';


            if (!$message && $success) {
                $upd = Course::UpdateLessonComplete($user_id, $lesson_id, 1, time());
                $upd = Course::UpdateDialogAnswersStatus($lesson_id, $user_id);
                header("Location: ".$setting['script_url']."/lk/answers/");
            } elseif ($message) {
                // Записать ответ
                $act = Course::AddAnswertoDialog($id, $_SESSION['user'], $message, $success, $lesson_id, $user_id, $less_data['course_id'], $attachments);
                if ($act) {
                    // Выставить статус 1 для диалогов
                    $upd = Course::UpdateDialogAnswersStatus($lesson_id, $user_id);
                    $course_data = Course::getCourseByID($less_data['course_id']);
                    $send = Email::SendUserNotifAboutTaskAnswer($email, $name, $course_data['alias'], $less_data['alias']);
                }
                
                header("Location: {$setting['script_url']}{$_SERVER['REQUEST_URI']}");
                exit;
            }
        }

        $dialog_list = Course::getDialogList($user_id, $lesson_id );
        $lesson_data = Course::getLessonDataByID($lesson_id);
        $course = Course::getCourseByID($lesson_data['course_id']);
        $less_status = Course::getStatusCompleteLesson($user_id, $lesson_id);

        require_once (ROOT . '/template/'.$setting['template'].'/views/users/answer_view.php');
    }
    
    
    
    
    // УДАЛИТЬ ДИАЛОГ 
    public function actionDeldialog($user_id, $lesson)
    {
        $setting = System::getSetting();
        $extension = System::CheckExtensension('courses', 1);
        if ($extension) {
            // Проверка авторизации
            $userId = intval(User::checkLogged());
            // Данные юзера
            $user = User::getUserById($userId);
            
            if ($user['is_curator'] != 1) {
                require_once (ROOT . '/template/'.$setting['template'].'/404.php');
                exit('error4ik');
            }
        }
        
        $user_id = intval($user_id);
        $lesson = intval($lesson);
        
        $del = Course::delDialog($user_id, $lesson);
        if ($del) {
            header("Location: ".$setting['script_url']."/lk/answers?success");
        }
    }
    
    
    // УДАЛИТЬ СООБЩЕНИЕ В ДИАЛОГЕ
    public function actionDelmessage($id)
    {
        $setting = System::getSetting();
        $extension = System::CheckExtensension('courses', 1);
        if ($extension) {
            // Проверка авторизации
            $userId = intval(User::checkLogged());
            // Данные юзера
            $user = User::getUserById($userId);
            
            if ($user['is_curator'] != 1) {
                require_once (ROOT . '/template/'.$setting['template'].'/404.php');
                exit('error4ik');
            }
        }
        
        $id = intval($id);
        $del = Course::delDialogMessage($id);
        
        if ($del) {
            header("Location: ".$setting['script_url']."/lk/answers?success");
        }
    }
    
    // ПОЛУЧИТЬ СПИСОК УРОКОВ ДЛЯ ФИЛЬТРА
    public function actionLessonsListFilter()
    {
       if (!empty($_POST['course_id'])) {
            $course_id = intval($_POST['course_id']);
            $lesson_list = $course_id ? Course::getLessonsList(2, $course_id) : null;
            $list = [];
            
            if ($lesson_list) {
                foreach($lesson_list as $lesson) {
                    $list[] = array($lesson['lesson_id']=>$lesson['name']);
                }
            }
            
            echo json_encode($list, true);
            exit;
        }
    }
}