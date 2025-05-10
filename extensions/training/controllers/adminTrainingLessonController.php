<?php defined('BILLINGMASTER') or die;


class adminTrainingLessonController  extends AdminBase {

    protected $setting;
    protected $tr_settings;
    protected $admin_name;
    protected $user_type;

    public function __construct()
    {
        $this->setting = System::getSetting();
        $this->tr_settings = Training::getSettings();
        $this->admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : null;
    }


    /**
     * СПИСОК УРОКОВ
     * @param $training_id
     */
    public function actionLessons($training_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $lesson_list = TrainingLesson::getLessons2Training($training_id);
        require_once (ROOT . '/extensions/training/views/admin/lesson/index.php');
    }


    /**
     * ДОБАВИТЬ УРОК
     * @param $training_id
     */
    public function actionAddLesson($training_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            System::redirectUrl('/admin');
        }

        $training = Training::getTraining($training_id);
        if (!$training) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['addlesson']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $data = TrainingLesson::beforeSaveLessonData2Admin($_POST, $training_id);

            if (isset($_FILES['cover']) && $_FILES["cover"]["size"] != 0) {
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $data['img'] = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/training/lessons/'; // папка для сохранения
                $path = $folder . $data['img']; // Полный путь с именем файла

                if (is_uploaded_file($tmp_name)) {
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $data['img'] = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                    /// Ресайз картинки урока убрал в форму вывода там ресайзится копия от настороек и оригинал
                    // не затирается
                    //$resize = System::imgResize($path, 550, false);
                }
            } else {
                $data['img'] = null;
            }

            $lesson_id = TrainingLesson::addLesson($training_id, $data);
            if ($lesson_id) {
                $add_task = TrainingLesson::addTask($lesson_id); //task
                $add_test = $add_task ? TrainingTest::addTest($lesson_id) : false; //test

                System::redirectUrl("/admin/training/editlesson/$training_id/$lesson_id", $add_task && $add_test);
            }
        }

        require_once (ROOT . '/extensions/training/views/admin/lesson/add.php');
    }


    /**
     * ИЗМЕНИТЬ УРОК
     * @param $training_id
     * @param $lesson_id
     */
    public function actionEditLesson($training_id, $lesson_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            System::redirectUrl('/admin');
        }

        $training = Training::getTraining($training_id);
        $lesson = TrainingLesson::getLesson($lesson_id);

        if (!$training || !$lesson) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $task = TrainingLesson::getTask2Lesson($lesson_id);
        $test = TrainingLesson::getTest2Lesson($lesson_id);
        $questions = $test['test_id'] ? TrainingTest::getQuestionByTestId($test['test_id']) : null;
        $elements = TrainingLesson::getElements2Lesson($lesson_id, null);
        $by_button = $lesson['by_button'] ? json_decode($lesson['by_button'], true) : null;
    
        // добавить опцию к тестам
        if (isset($_POST['addoption']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $option_title = htmlentities($_POST['option_title']);
            $option_value = htmlentities($_POST['option_value']);
            $option_valid = intval($_POST['option_valid']);
            $option_points = intval($_POST['option_points']);
            $sort = intval($_POST['sort'] + 1);

            $add = Training::addOption($test['test_id'], $option_title, $option_value, $option_valid, $sort, $option_points);
            System::redirectUrl("/admin/training/editlesson/$training_id/$lesson_id", $add);
        }


        // Изменить урок и задание и тест
        if (isset($_POST['editless']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $data = TrainingLesson::beforeSaveLessonData2Admin($_POST, $training_id, $lesson_id);

            if (isset($_FILES['cover']) && $_FILES["cover"]["size"] != 0) {
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $data['img'] = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/training/lessons/'; // папка для сохранения
                $path = $folder . $data['img']; // Полный путь с именем файла

                if (is_uploaded_file($tmp_name)) {
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $data['img'] = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                    /// Ресайз картинки урока убрал в форму вывода там ресайзится копия от настороек и оригинал
                    // не затирается
                    //$resize = System::imgResize($path, $this->tr_settings['width_less_img'], false);
                }
            } else {
                $data['img'] = $_POST['current_img'];
            }

            $edit = TrainingLesson::editLesson($lesson_id, $data);

            if ($edit) {
                /*task*/
                $data = TrainingLesson::beforeSaveLessonTaskData2Admin($_POST['task']);
                $edit_task = TrainingLesson::updateTask($lesson_id, $data);

                /*test*/
                $data = TrainingLesson::beforeSaveLessonTestData2Admin($_POST);
                $edit_test = TrainingTest::updTest($lesson_id, $data);

                System::redirectUrl("/admin/training/editlesson/$training_id/$lesson_id", $edit_task && $edit_test);
            }
        }

        require_once (ROOT . '/extensions/training/views/admin/lesson/edit.php');
    }


    /**
     * СКОПИРОВАТЬ УРОК
     * @param $training_id
     * @param $lesson_id
     */
    public function actionCopyLesson($training_id, $lesson_id) {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            System::redirectUrl('/admin');
        }

        $lesson = TrainingLesson::getLesson($lesson_id);
        if (!$lesson) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $add_copy = TrainingLesson::copyLesson($training_id, $lesson);

        if ($add_copy) {
            System::redirectUrl("/admin/training/structure/$training_id", $add_copy);
        }
        
    }

      /**
     * СКОПИРОВАТЬ УРОК В ДРУГОЙ ТРЕНИНГ
     */
    public function actionCopyTransfer() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['newtraining'])) {
            $new_training_id = $_POST['newtraining'];
            $lesson_id = $_POST['transferlesson'];
            $lesson = TrainingLesson::getLesson($lesson_id);
            $add_copy = TrainingLesson::copyTransfer($new_training_id, $lesson);
            
            if ($add_copy) {
                System::redirectUrl("/admin/training/structure/$new_training_id", $add_copy);
            }
        }        
    }



    /**
     * УДАЛИТЬ УРОК
     * @param $training_id
     * @param $lesson_id
     */
    public function actionDelLesson($training_id, $lesson_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_courses'])) {
            System::redirectUrl('/admin');
        }

        $training_id = intval($training_id);
        $lesson_id = intval($lesson_id);

        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = TrainingLesson::DelLesson($lesson_id);
            System::redirectUrl("/admin/training/structure/$training_id", $del);
        }
    }



    /**------------------------------ЭЛЕМЕНТЫ УРОКА------------------------------*/


    /**
     * ДОБАВИТЬ ЭЛЕМЕНТ ДЛЯ УРОКА
     */
    public function actionAddElement() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['add_element']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $lesson_id = intval($_POST['lesson_id']);
            $element_type = intval($_POST['element_type']);
            $params = $_POST['params'];

            if ($element_type == TrainingLesson::ELEMENT_TYPE_ATTACH && isset($_FILES['attach']) && $_FILES["attach"]["size"] != 0) {
                $params['attach'] = System::getSecureString($_FILES["attach"]["name"]);
                $dir_path = ROOT . "/load/training/lessons/$lesson_id";
                if (!file_exists($dir_path)) {
                    mkdir($dir_path);
                }

                $path = "$dir_path/{$params['attach']}";
                if (is_uploaded_file($_FILES["attach"]["tmp_name"])) {
                    move_uploaded_file($_FILES["attach"]["tmp_name"], $path);
                }
            }

            if ($element_type == TrainingLesson::ELEMENT_TYPE_PLAYLIST) {
                $data = [
                    'name' => $_POST['name'],
                    'title' => $_POST['title'],
                ];
                $el_id = TrainingLesson::addElement($element_type, $lesson_id, json_encode($data));
                $add = $el_id ? TrainingLesson::addPlaylistItem($el_id, json_encode($params)) : false;
            } else {
                $add = TrainingLesson::addElement($element_type, $lesson_id, json_encode($params));
            }

            if ($element_type == TrainingLesson::ELEMENT_TYPE_FORUM && !TrainingLesson::getCountElements2Lesson($lesson_id, TrainingLesson::ELEMENT_TYPE_FORUM)) {
                if (isset($_POST['topics'])) {
                    Forum2::addTopics2Lesson($lesson_id, $_POST['topics']);
                }
            }

            System::redirectUrl("/admin/training/editlesson/{$_POST['training_id']}/$lesson_id", $add);
        }
    }


    /**
     * РЕДАКТИРОВАТЬ ЭЛЕМЕНТ ДЛЯ УРОКА
     * @param $id
     */
    public function actionEditElement($id) {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['edit_element']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $el = TrainingLesson::getElement($id);
            if (!$el) {
                require_once (ROOT . "/template/{$this->setting['template']}/404.php");
            }

            $params = $_POST['params'];

            if ($el['type'] == TrainingLesson::ELEMENT_TYPE_ATTACH) {
                if ($params['type'] == 2) {
                    TrainingLesson::delLessonAttach($el['lesson_id'], $el['params']['attach']);
                } elseif ($params['type'] == 1 && isset($_FILES['attach']) && $_FILES["attach"]["size"] != 0) {
                    TrainingLesson::delLessonAttach($el['lesson_id'], $el['params']['attach']);
                    $params['attach'] = TrainingLesson::AddLessonAttach($el['lesson_id'], $_FILES);
                }
            }

            $upd = TrainingLesson::updElement($id, json_encode($params));
            if ($el['type'] == TrainingLesson::ELEMENT_TYPE_FORUM) {
                $topics = isset($_POST['topics']) ? $_POST['topics'] : null;
                Forum2::saveTopics2Lesson($el['lesson_id'], $topics);
            }
            System::redirectUrl("/admin/training/editlesson/{$_POST['training_id']}/{$el['lesson_id']}", $upd);
        }
    }


    /**
     * ДОБАВИТЬ ЭЛЕМЕНТ ПЛЭЙЛИСТА
     */
    public function actionAddPlaylistItem() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['add_playlist_item']) && isset($_POST['playlist_id']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $playlist_id = (int)$_POST['playlist_id'];
            $playlist = TrainingLesson::getElement($playlist_id);
            if (!$playlist) {
                require_once (ROOT . "/template/{$this->setting['template']}/404.php");
            }

            $upd = TrainingLesson::addPlaylistItem($playlist_id, json_encode($_POST['params']));
            System::redirectUrl("/admin/training/editlesson/{$_POST['training_id']}/{$playlist['lesson_id']}", $upd);
        }
    }


    /**
     * РЕДАКТИРОВАТЬ ЭЛЕМЕНТ ПЛЭЙЛИСТА
     * @param $id
     */
    public function actionEditPlaylistItem($id) {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['edit_playlist_item']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $playlist_item = TrainingLesson::getPlaylistItem($id);
            $playlist = TrainingLesson::getElement($playlist_item['playlist_id']);
            if (!$playlist_item || !$playlist) {
                require_once (ROOT . "/template/{$this->setting['template']}/404.php");
            }

            $upd = TrainingLesson::updPlaylistItem($id, json_encode($_POST['params']));
            System::redirectUrl("/admin/training/editlesson/{$_POST['training_id']}/{$playlist['lesson_id']}", $upd);
        }
    }


    /**
     * УДАЛИТЬ ЭЛЕМЕНТ ПЛЭЙЛИСТА
     * @param $id
     */
    public function actionDelPlaylistItem($id) {
        $acl = self::checkAdmin();
        if (!isset($acl['del_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $playlist_item = TrainingLesson::getPlaylistItem($id);
            $playlist = TrainingLesson::getElement($playlist_item['playlist_id']);
            if (!$playlist_item || !$playlist) {
                require_once (ROOT . "/template/{$this->setting['template']}/404.php");
            }

            $del = TrainingLesson::delPlaylistItem($id);
            $lesson = TrainingLesson::getLesson($playlist['lesson_id']);
            System::redirectUrl("/admin/training/editlesson/{$lesson['training_id']}/{$lesson['lesson_id']}", $del);
        }
    }


    /**------------------------------ЗАДАНИЯ------------------------------*/


    /**
     * ОТВЕТЫ К ДОМАШНИМ ЗАДАНИЯМ
     */
    public function actionAnswers() {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['filter'])) {
            if ($_POST['lesson_complete_status'] == 'unchecked') {
                $lesson_complete_status = [1];
            } elseif ($_POST['lesson_complete_status'] == 'checked') {
                $lesson_complete_status = [2,3];
            }

            $_SESSION['admin']['training']['answers_filter'] = [
                'training_id' => $_POST['training_id'] ? (int)$_POST['training_id'] : null,
                'answer_status' => $_POST['answer_status'] != 'all' ? (int)$_POST['answer_status'] : null,
                'answer_type' => $_POST['answer_type'] ? (int)$_POST['answer_type'] : null,
                'lesson_complete_status' => isset($lesson_complete_status) ? $lesson_complete_status : null,
                'lesson_id' => $_POST['lesson_id'] ? (int)$_POST['lesson_id'] : null,
                'user_email' => htmlentities($_POST['user_email']),
            ];
        }

        if (isset($_POST['reset'])) {
            unset($_SESSION['admin']['training']['answers_filter']);
        }

        $filter = isset($_SESSION['admin']['training']['answers_filter']) ? $_SESSION['admin']['training']['answers_filter'] : [
            'lesson_complete_status' => [1],
            'answer_status' => 0
        ];

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $total = 0;//TrainingLesson::getTotalAnswers($filter);
        $pagination = new Pagination($total, $page, $this->setting['show_items']);

        $answer_list = $total ? TrainingLesson::getAnswerList($filter, $page, $this->setting['show_items']) : false;
        require_once (ROOT . '/extensions/training/views/admin/lesson/answers.php');
    }


    /**
     * ОТВЕТ К ДОМАШНЕМУ ЗАДАНИЮ
     * @param $lesson_id
     * @param $user_id
     */
    public function actionAnswer($lesson_id, $user_id) {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $admin_id = self::checkLogged();
        $admin = User::getUserById($admin_id);

        $last_answer = TrainingLesson::getLastAnswer($lesson_id, $user_id);
        if (!$last_answer) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $lesson = TrainingLesson::getLesson($last_answer['lesson_id']);
        $task = TrainingLesson::getTask2Lesson($last_answer['lesson_id']);
        $answer_list = TrainingLesson::getAnswers2Lesson($lesson['lesson_id'], $last_answer['user_id'], $task['task_id']);
        $lesson_complete_status = TrainingLesson::getLessonCompleteStatus($last_answer['lesson_id'], $last_answer['user_id']);

        if (isset($_POST['admin_token']) && $_POST['admin_token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_courses'])) {
                System::redirectUrl('/admin');
            }

            if (isset($_POST['accept']) || isset($_POST['noaccept'])) {
                $status = isset($_POST['accept']) ? Traininglesson::HOMEWORK_ACCEPTED : Traininglesson::HOMEWORK_DECLINE;
                $result = TrainingLesson::updLessonCompleteStatus($lesson_id, $user_id, $status);
                System::redirectUrl("/admin/training/answers", $result);
            }

            if (isset($_POST['reply'])) {
                $reply = base64_encode(htmlentities($_POST['reply']));
                $answer_id = (int)$_POST['answer_id'];
                $user_type = $admin['is_curator'] ? Training::USER_TYPE_CURATOR : Training::USER_TYPE_ADMIN;

                $attach = null;
                if (isset($_FILES['lesson_attach']) && !empty($_FILES['lesson_attach'])) {
                    $attach = TrainingLesson::uploadAttach2Answer($_FILES, $lesson_id, $user_type);
                }

                $write = TrainingLesson::writeAnswer($lesson_id, $_SESSION['admin_user'], $reply, $answer_id, $attach);
                if ($write) {
                    // TODO переделать функцию под новые таблицы
                    TrainingLesson::updateStatusAnswers($answer_list, TrainingLesson::ANSWER_IS_ANSWERED); // обновить статус у ответов и вложенных ответов, как отвеченный
                }

                System::redirectUrl("/admin/training/answers", $write);
            }
        }

        if ($answer_list && $admin['is_curator']) {
            // TODO переделать функцию под новые таблицы
            TrainingLesson::updateStatusAnswers($answer_list, 1); // обновить статус у ответов и вложенных ответов, как прочитанный
        }

        require_once (ROOT . '/extensions/training/views/admin/lesson/answer.php');
    }

    /**
     * ИЗМЕНИТЬ ОПЦИЮ В ЗАДАНИИ
     * @param $lesson_id
     * @param $option_id
     */
    public function actionEditOption($lesson_id, $option_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            System::redirectUrl('/admin');
        }

        $lesson = TrainingLesson::getLesson($lesson_id);
        $option = Training::getOptionByTest($option_id);

        if (isset($_POST['editoption']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $title = htmlentities($_POST['title']);
            $value = htmlentities($_POST['value']);
            $valid = intval($_POST['option_valid']);
            $points = intval($_POST['points']);
            $sort = intval($_POST['sort']);

            $edit = Training::editOptionByTest($option_id, $title, $value, $valid, $points, $sort);
            System::redirectUrl("/admin/training/lessons/editoption/$lesson_id/$option_id", $edit);
        }

        require_once (ROOT . '/extensions/training/views/admin/lesson/edit_option.php');
    }


    /**
     * УДАЛИТЬ ОПЦИЮ В ТЕСТИРОВАНИИ
     * @param $lesson_id
     * @param $option_id
     */
    public function actionDelOption($lesson_id, $option_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_courses'])) {
            System::redirectUrl('/admin');
        }

        $lesson_id = intval($lesson_id);
        $option_id = intval($option_id);
        $lesson = TrainingLesson::getLesson($lesson_id);
        $training_id = $lesson['training_id'];

        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = Training::DelOptionTest($option_id);
            System::redirectUrl("/admin/training/editlesson/$training_id/$lesson_id", $del);
        }
    }


    /**
     * @param $lesson_id
     */
    public function actionStatistics($lesson_id) {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $lesson = TrainingLesson::getLessons($lesson_id);
        $users = TrainingLesson::getUsersPass2lesson($lesson_id);
        $time = time();

        if (isset($_GET['reset'])) {
            unset($_SESSION['filter_tr_less_stat']);
        }

        $filter = !isset($_POST['filter']) && isset($_SESSION['filter_tr_less_stat']) ? $_SESSION['filter_tr_less_stat'] : [
            'start' => isset($_POST['start']) && $_POST['start'] ? strtotime($_POST['start']) : null,
            'finish' => isset($_POST['finish']) && $_POST['finish'] ? strtotime($_POST['finish']) : null,
        ];

        $start_date = $filter['start'] ? $filter['start'] : $time - 2592000; // 30 дней назад
        $finish_date = $filter['finish'] ? $filter['finish'] : $time; // сегодня

        if (isset($_POST['load_csv']) && isset($_SESSION['filter_tr_less_stat'])) {
            $filter['is_filter'] = true;
        } else {
            $filter['is_filter'] = array_filter($filter, 'strlen') ? true : false;
            if ($filter['is_filter']) {
                $_SESSION['filter_tr_less_stat'] = $filter;
            }
        }

        require_once (ROOT . '/extensions/training/views/admin/lesson/statistics.php');
    }
}