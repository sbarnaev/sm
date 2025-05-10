<?php defined('BILLINGMASTER') or die;

class trainingController {

    private $setting;
    private $tr_settings;
    private $en_extension;

    private $title;
    private $meta_desc;
    private $meta_keys;
    private $h1;
    private $h2;
    private $is_page;
    private $view_path;
    private $use_css;

    public function __construct()
    {
        $this->setting = System::getSetting();
        $this->tr_settings = Training::getSettings();
        $this->en_extension = System::CheckExtensension('training', 1);

        if (!$this->en_extension) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }
    }

    /**
     * ГЛАВНАЯ СТРАНИЦА ТРЕНИНГОВ
     */
    public function actionIndex()
    {
        $filter  = [
            'access' => isset($_GET['acc']) && $_GET['acc'] != 'all' ? $_GET['acc'] : false,
            'author' => isset($_GET['aut']) && is_array($_GET['aut'])  ? $_GET['aut'] : false,
            'category' => isset($_GET['cat']) && is_array($_GET['cat'])  ? $_GET['cat'] : false,
        ];

        $cat_list = $training_list = false;
        
        if ($this->tr_settings['show_list'] == 'all') {
            $cat_list = TrainingCategory::getCatList(false);
            if (!$cat_list) {
                $training_list = Training::getTrainingList(null, null, $filter);
            }
        } elseif($this->tr_settings['show_list'] == 'without_categories') {
            $training_list = Training::getTrainingList(null, null, $filter);
        } elseif ($this->tr_settings['show_list'] == 'content_separate_category' && $this->tr_settings['categories_to_content']) {
            $cat_list = TrainingCategory::getSubCategoriesByParentsIds($this->tr_settings['categories_to_content']);
            if (!$cat_list) {
                $training_list = Training::getTrainingList($this->tr_settings['categories_to_content'], null, $filter);
            }
        }

        $user_id = intval(User::isAuth());
        $user_groups = $user_id ? User::getGroupByUser($user_id) : false;
        $user_planes = $user_id ? Member::getPlanesByUser($user_id, 1, true) : false;

        $this->setPageSettings($this->tr_settings, 'training_index', 'training/list.php');
        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    /**
     * ПРОСМОТР ТРЕНИНГА
     * @param $tr_alias
     * @throws Exception
     */
    public function actionTraining($tr_alias)
    {
        $tr_alias = htmlentities($tr_alias);
        $training = Training::getTrainingByAlias($tr_alias);

        if (!$training) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }
        
        require_once (ROOT ."/lib/mobile_detect/Mobile_Detect.php");
        $detect = new Mobile_Detect;

        $user_id = intval(User::isAuth());
        $user_groups = $user_id ? User::getGroupByUser($user_id) : false;
        $user_planes = $user_id ? Member::getPlanesByUser($user_id, 1, true) : false;
        $user_is_curator = Training::isCuratorInTrainingInSection($user_id, $training['training_id']);
        
        $this->setPageSettings($training, 'training', 'training/index.php');
        $big_button = json_decode($training['big_button'], true);
        $small_button = json_decode($training['small_button'], true);
        $access = Training::getAccessData($user_groups, $user_planes, $training);
        if (!Training::checkUserAccess($access) && $big_button['type'] != 6 && $small_button['type'] != 6) {
            $this->setViewPath('layouts/no_access.php');
            require_once (ROOT . "/extensions/training/views/frontend/view.php");
        }
        
        if ($user_id && !isset($_SESSION['training_save_uv']['training'][$training['training_id']])) {
            TrainingUserVisits::saveVisit($user_id, $training['training_id']);
            $_SESSION['training_save_uv']['training'][$training['training_id']] = true;
        }


        $sub_category = $category = false;
        if ($training['cat_id'] != 0) {
            $category = TrainingCategory::getCategory($training['cat_id']);
            if ($category && $category['parent_cat'] != 0) {
                $sub_category = $category;
                $category = TrainingCategory::getCategory($sub_category['parent_cat']);
            }
        }

        $sertificate = json_decode($training['sertificate'], true);
        $have_certificate = Training::getUrlHashCertificate2User($user_id, $training['training_id']);
        $lesson_list = TrainingLesson::getLessons($training['training_id'], 0); // получаем уроки
        $section_list = TrainingSection::getSections($training['training_id']); // получить разделы из тренинга
        $block_list = TrainingBlock::getBlocks($training['training_id'], 0); // получаем обычные блоки без разделов

        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    /**
     * СТРАНИЦА МОИ ТРЕНИНГИ
     */
    public function actionMyTraining()
    {
        $user_id = intval(User::isAuth());
        if(!$user_id) {
            System::redirectUrl('/lk');
        }
        $user_groups = $user_id ? User::getGroupByUser($user_id) : false;
        $user_planes = $user_id ? Member::getPlanesByUser($user_id, 1, true) : false;
        $training_list = $user_id && ($user_groups || $user_planes) ? Training::getTrainingsToUser($user_groups, $user_planes, $user_id) : null;

        $this->setPageSettings([
                'title' => 'Мои тренинги',
                'meta_desc' => '',
                'meta_keys' => '',
                'h1' => 'Мои тренинги'
            ],
            'my_trainings',
            'users/my_trainings.php'
        );

        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    /**
     * СТРАНИЦА КУРАТОРА
     */
    public function actionCurator() {
        $curator_id = intval(User::checkLogged());
        $user = User::getUserById($curator_id);

        if (!$user['is_curator']) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['accept'])) { // ВЫНЕСЕНИЕ ПОЛОЖИТЕЛЬНОГО РЕШЕНИЯ ДЛЯ ДЗ
            $lesson_id = isset($_POST['lesson_id']) ? $_POST['lesson_id'] : null;
            $homework_id = isset($_POST['homework_id']) ? $_POST['homework_id'] : null;
            $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;

            $status = Traininglesson::HOMEWORK_ACCEPTED;
            $assign_to_user = isset($_POST['assign_user']) ? $_POST['assign_user'] : null;
            $result = TrainingLesson::updLessonCompleteStatus($lesson_id, $user_id, $status);
            $result_hw = TrainingLesson::updHomeworkData($homework_id, $user_id, 1, null, $curator_id);

            if ($result && $assign_to_user == "1") {
                $training_id = Training::getTrainingIdByLessonId((int)$lesson_id);
                $section_list = Training::getAllSectionsByCuratorAndByTraining($curator_id, $training_id);
                foreach ($section_list as $section){
                    User::WriteCuratorsToUser($user_id, $curator_id, Training::getTrainingIdByLessonId((int)$lesson_id), (int)$section['section_id']);
                }

                User::WriteCuratorsToUser($user_id, $curator_id, Training::getTrainingIdByLessonId((int)$lesson_id), TrainingSection::getSectionByLessonId((int)$lesson_id));
            }

            System::redirectUrl("/lk/curator", $result);
        }
        
        $trainings_to_curator = Training::getAllTrainingsToCurator($curator_id);
        if (!isset($_SESSION['training']['answers_filter']['training_id'])) {
            if ($trainings_to_curator) {
                $_SESSION['training']['answers_filter']['training_id'] = count($trainings_to_curator) == 1 ? $trainings_to_curator[0]['training_id']: false;
            } else {
                $_SESSION['training']['answers_filter']['training_id'] = null;
            }
        }

        if (isset($_POST['reset'])) {
            unset($_SESSION['training']['answers_filter']);
            System::redirectUrl("/lk/curator");
        }

        if (isset($_POST['filter'])) { // TODO тут надо поменять все статусы и переподвязать их из таблицы homework
            $filter_user_data = isset($_POST['user_name']) ? explode(' ', htmlentities(trim($_POST['user_name']))) : null;
            $_SESSION['training']['answers_filter'] = [
                'training_id' => isset($_POST['training_id']) ? (int)$_POST['training_id'] : null,
                'answer_type' => isset($_POST['answer_type']) ? $_POST['answer_type'] : null,
                'comments_status' => isset($_POST['comments_status']) ? $_POST['comments_status'] : null,
                'lesson_complete_status' => isset($_POST['lesson_complete_status']) ? $_POST['lesson_complete_status'] : 'all',
                'lesson_id' => isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : null,
                'user_email' => htmlentities($_POST['user_email']),
                'user_name' => $filter_user_data && $filter_user_data[0] ? $filter_user_data[0] : null,
                'user_surname' => isset($filter_user_data[1]) ? $filter_user_data[1] : null,
                'curator_users' => isset($_POST['curator_users']) ? htmlentities($_POST['curator_users']) : null,
                'curator_id' => isset($_POST['curator_users']) && isset($_POST['curator_id']) && $_POST['curator_users'] == 'choose_curator'  ? (int)$_POST['curator_id'] : null,
                'start_date' => isset($_POST['start_date']) && $_POST['start_date'] ? strtotime($_POST['start_date']) : null,
                'finish_date' => isset($_POST['finish_date']) && $_POST['finish_date'] ? strtotime($_POST['finish_date']) : null,
            ];

            System::redirectUrl("/lk/curator");
        }

        $filter = isset($_SESSION['training']['answers_filter']) ? $_SESSION['training']['answers_filter'] : null;
        $lesson_list = $filter && $filter['training_id'] ? TrainingLesson::getLessons($filter['training_id']) : null;

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $training_ids2curator = $trainings_to_curator ? array_column($trainings_to_curator, 'training_id') : null;
        $total = TrainingLesson::getTotalAnswers($filter, $curator_id, $training_ids2curator);
        $answer_list = TrainingLesson::getAnswerList($filter, $page, $this->setting['show_items'], $curator_id, $training_ids2curator);
        $pagination = new Pagination($total, $page, $this->setting['show_items']);

        $this->setPageSettings([
                'title' => 'Кабинет куратора курсов',
                'meta_desc' => '',
                'meta_keys' => '',
                'h1' => ''
            ],
            'lk',
            'users/curator.php'
        );

        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    /**
     * УДАЛИТЬ СООБЩЕНИЕ В ДИАЛОГЕ
     * @param $id
     */
    public function actionDelmessage($id)
    {
        $curator_id = intval(User::checkLogged());
        $user = User::getUserById($curator_id);

        if (!$user['is_curator']) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $del = TrainingLesson::delMessage($id);
        if ($del) {
            System::redirectUrl($_SERVER['HTTP_REFERER'], $del);
        }
    }


    /**
     * УДАЛИТЬ ДОМАШНЮЮ РАБОТУ
     * @param $id
     */
    public function actionDelHomework($id)
    {
        $curator_id = intval(User::checkLogged());
        $user = User::getUserById($curator_id);

        if (!$user['is_curator'] && $this->tr_settings['allow_del_homework']) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $del = TrainingLesson::delHomework($id);
        if ($del) {
            System::redirectUrl('/lk/curator/', $del);
        }
    }


    /**
     * СТРАНИЦА ОТВЕТА НА ДЗ В КУРАТОРСКОЙ
     * @param $homework_id
     * @param $user_id
     * @param $lesson_id
     */
    public function actionAnswer($homework_id, $user_id, $lesson_id) {
        $curator_id = intval(User::checkLogged());
        $curator = User::getUserById($curator_id);
        $answer_user_id = $user_id;

        if (!$curator['is_curator']) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $user_info = User::getUserById($user_id);
        $user_groups = User::getGroupByUser($user_id);
        $user_planes = Member::getPlanesByUser($user_id);

        // Назначеный юзеру куртор 
        $assign_curator = Training::getCuratorToUserByLessonId($lesson_id, $user_id);
        
        $training_id = Training::getTrainingIdByLessonId((int)$lesson_id);
        $training = Training::getTraining($training_id);

        // Здесь принятие задания с автоответом 
        if (isset($_GET['auto']) && $_GET['auto'] == 1) {
            $status = Traininglesson::HOMEWORK_ACCEPTED;
            $task = TrainingLesson::getTask2Lesson($lesson_id);
            $auto_reply = base64_encode(htmlentities($task['auto_answer']));
        
            $write = TrainingLesson::writeComment($homework_id, $curator_id, 0, $auto_reply, 2, null);
            $result = TrainingLesson::updLessonCompleteStatus($lesson_id, $user_id, $status);
            $result_hw = TrainingLesson::updHomeworkData($homework_id, $user_id, 1, null, $curator_id);

            System::redirectUrl("/lk/curator", $result);
        }

        // TODO пока убрали вложения в ответах куратора, если критично то нужно добавить в таблицу поле для этого   
        //$attach = TrainingLesson::uploadAttach2Answer($_FILES, $lesson_id, Training::USER_TYPE_CURATOR);
            

        // тут принятие без коментария или прям из списка ответов!!!
        if (isset($_POST['accept'])) { // ВЫНЕСЕНИЕ ПОЛОЖИТЕЛЬНОГО РЕШЕНИЯ ДЛЯ ДЗ
            $status = Traininglesson::HOMEWORK_ACCEPTED;
            $assign_to_user = isset($_POST['assign']) ? $_POST['assign'] : null;
            $result = TrainingLesson::updLessonCompleteStatus($lesson_id, $user_id, $status);
            $result_hw = TrainingLesson::updHomeworkData($homework_id, $user_id, 1, null, $curator_id);
            if ($result && $assign_to_user == "1") {
                $section_list = Training::getAllSectionsByCuratorAndByTraining($curator_id, $training_id);
                if ($section_list) {
                    foreach ($section_list as $section){
                        User::WriteCuratorsToUser($user_id, $curator_id, $training_id, (int)$section['section_id']);
                    }
                }
                User::WriteCuratorsToUser($user_id, $curator_id, $training_id, TrainingSection::getSectionByLessonId((int)$lesson_id));
            }

            $type_message = null;
            if ($training['send_email_to_user'] == 1) {
                Email::SendEmailFromCuratorToUser($user_info, $lesson_id, null, $type_message, $result, $curator);
            }

            System::redirectUrl("/lk/curator", $result);
        }

        if (isset($_GET['testtry'])) { // тут даем попытку на еще одно прохождение теста
            $last_answer = TrainingLesson::getAnswer($homework_id);
            $result = TrainingTest::deleteResultsAnswersUsers($lesson_id, $user_id, $last_answer);
            $type_message = 'Вам предоставлена еще одна попытка на прохождения теста.';
            if ($training['send_email_to_user'] == 1) {
                Email::SendEmailFromCuratorToUser($user_info, $lesson_id, null, $type_message, $result, $curator);
            }

            System::redirectUrl("/lk/curator", $result);
        }

        $last_answer = TrainingLesson::getAnswer($homework_id);
        $test_result = TrainingTest::getTestResult($lesson_id, $user_id);
        
        if (!$last_answer && !$test_result) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $lesson = TrainingLesson::getLesson($lesson_id);
        $task = TrainingLesson::getTask2Lesson($lesson_id);
        $answer_list = TrainingLesson::getAnswers2Lesson($lesson['lesson_id'], $user_id, $homework_id);
        $lesson_complete_status = TrainingLesson::getLessonCompleteStatus($lesson_id, $user_id);
 
        if (isset($_POST['post_message'])) { // ОТВЕТ КУРАТОРА
            $reply = base64_encode(htmlentities($_POST['reply']));
            $attach = TrainingLesson::uploadAttach2Answer($_FILES, $lesson_id, Training::USER_TYPE_CURATOR);
            $write = !empty($reply) ? TrainingLesson::writeComment($homework_id, $curator_id, 0, $reply, 2, $attach) : true;

            if ($write) {
                if ($_POST['status_send_complete']) {
                    $status = $_POST['status_send_complete'] == 1 ? Traininglesson::HOMEWORK_ACCEPTED : $_POST['status_send_complete'];
                    $status_hw = $_POST['status_send_complete'] == "1" ? Traininglesson::HOME_WORK_ACCEPTED : Traininglesson::HOME_WORK_DECLINE;
                    $assign_to_user = isset($_POST['assign_user']) ? $_POST['assign_user'] : null;
                    $res = TrainingLesson::updLessonCompleteStatus($lesson_id, $user_id, $status);
                    $result_hw = TrainingLesson::updHomeworkData($homework_id, $user_id, $status_hw, null, $curator_id);

                    if ($res && $assign_to_user == "on") {
                        $section_list = Training::getAllSectionsByCuratorAndByTraining($curator_id, $training_id);
                        if ($section_list) {
                            foreach ($section_list as $section){
                                User::WriteCuratorsToUser($user_id, $curator_id, $training_id, (int)$section['section_id']);
                            }
                        }
                        User::WriteCuratorsToUser($user_id, $curator_id, $training_id, TrainingSection::getSectionByLessonId((int)$lesson_id));
                    }
                }

                if (isset($_POST['send_email_to_user']) && $training['send_email_to_user'] == 1){
                    $type_message = $reply ?: '';
                    $status = $_POST['status_send_complete'] ?: '';
                    Email::SendEmailFromCuratorToUser($user_info, $lesson_id, null, $type_message, $status, $curator);
                }
            }

            if ($_POST['status_send_complete']) {
                System::redirectUrl('/lk/curator/', $write);
            } else {
                System::redirectUrl("/lk/curator/answers/$homework_id/$user_id/$lesson_id", $write, '#curator_answer');
            }
        }

        if ($answer_list) {
            // Здесь ставим временный статус, что бы другие кураторы не могли видеть ответы.
            if ($answer_list[0]['status'] == TrainingLesson::HOME_WORK_SEND) {
                TrainingLesson::updateStatusAnswer($homework_id, TrainingLesson::HOME_WORK_IN_VERIFICATION, $curator_id); 
            }
        }

        // Тут обновляем статусы комментариев на прочитаные, если они есть.
        TrainingLesson::updateStatusAllCommentsByHomework($homework_id);
        $trainings_to_curator = Training::getAllTrainingsToCurator($curator_id);
        $training_id = Training::getTrainingIdByLessonId($lesson_id);
        $lesson_list = TrainingLesson::getLessons($training_id);

        $this->setPageSettings([
                'title' => 'Кабинет куратора курсов',
                'meta_desc' => '',
                'meta_keys' => '',
                'h1' => ''
            ],
            'lk',
            'users/answer.php'
        );
        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    /**
     * СТРАНИЦА РЕДАКТИРОВАНИЯ ОТВЕТА НА ДЗ
     */
    public function actionEditAnswer() {
        $user_id = intval(User::checkLogged());
        if (!$user_id) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['answer']) && isset($_POST['answer_id'])) {
            $answer_id = (int)$_POST['answer_id'];
            $answer = TrainingLesson::getAnswer($answer_id);
            $lesson = TrainingLesson::getLesson($answer['lesson_id']);
            $training = Training::getTraining($lesson['training_id']);
            $lesson_complete_status = TrainingLesson::getLessonCompleteStatus($answer['lesson_id'], $user_id);

            if ($answer && TrainingLesson::isAllowEditAnswer($training, $lesson_complete_status, $answer)) {
                $user_groups = User::getGroupByUser($user_id);
                $user_planes = Member::getPlanesByUser($user_id, 1, true);
                $section = $lesson['section_id'] ? TrainingSection::getSection($lesson['section_id']) : null;

                $access = Training::getAccessData($user_groups, $user_planes, $training, $section, $lesson);
                if (!Training::checkUserAccess($access)) {
                    $this->setViewPath('layouts/no_access.php');
                    require_once (ROOT . "/extensions/training/views/frontend/view.php");
                }

                if (isset($_FILES['lesson_attach']) && !empty($_FILES['lesson_attach'])) {
                    $attach = TrainingLesson::uploadAttach2Answer($_FILES, $lesson['lesson_id'], Training::USER_TYPE_USER);
                } else {
                    $attach = $_POST['current_attach'];
                }
                
                $work_link = isset($_POST['work_link']) ? $_POST['work_link'] : null;

                $answer_msg = base64_encode(htmlentities($_POST['answer']));
                TrainingLesson::updAnswer($answer_id, $answer_msg, $attach, $answer['lesson_id'], $user_id, $work_link);
            }

            System::redirectUrl("/training/view/{$training['alias']}/lesson/{$lesson['alias']}");
        }
    }


    /**
     * СТРАНИЦА РЕДАКТИРОВАНИЯ КОММЕНТАРИЯ К ДЗ
     * @throws Exception
     */
    public function actionEditComment() {
        $user_id = intval(User::checkLogged());
        if (!$user_id) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['comment']) && isset($_POST['comment_id']) && isset($_POST['lesson_id'])) {
            $comment_id = (int)$_POST['comment_id'];
            $comment = TrainingLesson::getComment($comment_id);
            $lesson_id = (int)$_POST['lesson_id'];
            $lesson = TrainingLesson::getLesson($lesson_id);
            $training = Training::getTraining($lesson['training_id']);

            if (!$comment || $comment['status']) {
                System::redirectUrl("/training/view/{$training['alias']}/lesson/{$lesson['alias']}");
            }

            $user_groups = User::getGroupByUser($user_id);
            $user_planes = Member::getPlanesByUser($user_id, 1, true);
            $section = $lesson['section_id'] ? TrainingSection::getSection($lesson['section_id']) : null;

            $access = Training::getAccessData($user_groups, $user_planes, $training, $section, $lesson);
            if (!Training::checkUserAccess($access)) {
                $this->setViewPath('layouts/no_access.php');
                require_once (ROOT . "/extensions/training/views/frontend/view.php");
            }

            if (isset($_FILES['lesson_attach']) && !empty($_FILES['lesson_attach'])) {
                $attach = TrainingLesson::uploadAttach2Answer($_FILES, $lesson['lesson_id'], Training::USER_TYPE_USER);
            } else {
                $attach = $_POST['current_attach'];
            }

            $comment = base64_encode(htmlentities($_POST['comment']));
            TrainingLesson::updComment($comment_id, $comment, $attach);

            System::redirectUrl("/training/view/{$training['alias']}/lesson/{$lesson['alias']}");
        }
    }


    /**
     * СТРАНИЦА РЕДАКТИРОВАНИЯ ОТВЕТА КУРАТОРА
     */
    public function actionEditCuratorComment() {
        $curator_id = intval(User::checkLogged());
        $curator = $curator_id ? User::getUserById($curator_id) : null;

        if (!$curator || !$curator['is_curator']) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['comment']) && isset($_POST['comment_id']) && isset($_POST['lesson_id'])) {
            $comment_id = (int)$_POST['comment_id'];
            $comment = TrainingLesson::getComment($comment_id);
            $lesson_id = (int)$_POST['lesson_id'];
            $lesson = TrainingLesson::getLesson($lesson_id);
            $training = Training::getTraining($lesson['training_id']);


            if (isset($_FILES['lesson_attach']) && !empty($_FILES['lesson_attach'])) {
                $attach = TrainingLesson::uploadAttach2Answer($_FILES, $lesson_id, Training::USER_TYPE_CURATOR);
            } else {
                $attach = $_POST['current_attach'];
            }

            $comment = base64_encode(htmlentities($_POST['comment']));
            $res = TrainingLesson::updComment($comment_id, $comment, $attach);

            System::redirectUrl("/lk/curator/answers/{$_POST['homework_id']}/{$_POST['user_id']}/{$lesson['lesson_id']}",
                $res, '#curator_answer'
            );
        }
    }


    /**
     * СТРАНИЦА ВЫБОРА ТАРИФА (Продуктов)
     * @param $training_id
     */
    public function actionOptions($training_id)
    {
        $training_id = intval($training_id);
        $training = Training::getTraining($training_id);

        if (!$training) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $user_id = intval(User::isAuth());
        $big_button = json_decode($training['big_button'], true);
        $small_button = json_decode($training['small_button'], true);
        $list_product = isset($big_button['rate']) ? $big_button['rate'] : $small_button['rate'];
        $h1 = $training['name'];

        $lesson = null;
        $section = null;
        
        $sub_category = $category = false;
        if ($training['cat_id'] != 0) {
            $category = TrainingCategory::getCategory($training['cat_id']);
            if ($category && $category['parent_cat'] != 0) {
                $sub_category = $category;
                $category = TrainingCategory::getCategory($sub_category['parent_cat']);
            }
        }

        $this->setViewPath('layouts/rates.php');
        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    /**
     * ПОКАЗАТЬ СЕРТИФИКАТ ПОЛЬЗОВАТЕЛЯ ПО ССЫЛКЕ 
     * @param $hash_url
     */
    public function actionShowCertificate($hash_url)
    {
        if ($hash_url){
            Training::ShowCertificateByUrl($hash_url);
        }
        
    }

    /**
     * ЗАДАТЬ ОСНОВНЫЕ НАСТРОЙКИ СТРАНИЦЫ
     * @param $data
     * @param $is_page
     * @param bool $use_css
     * @param null $view_path
     */
    public function setPageSettings($data, $is_page, $view_path = null, $use_css = true) {
        $this->is_page = $is_page;
        $this->title = $data['title'];
        $this->meta_desc = isset($data['meta_desc']) ? $data['meta_desc'] : $data['desc'];
        $this->meta_keys = isset($data['meta_keys']) ? $data['meta_keys'] : $data['keys'];
        $this->h1 = isset($data['h1']) ? $data['h1'] : $data['name'];
        if ($view_path) {
            $this->view_path = $view_path;
        }
        $this->use_css = $use_css;
    }


    /**
     * ЗАДАТЬ ПУТЬ ДО VIEW ФАЙЛА
     * @param $view_path
     */
    public function setViewPath($view_path) {
        $this->view_path = $view_path;
    }
}