<?php defined('BILLINGMASTER') or die;

class trainingLessonController
{

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
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }
    }

    /**
     * СТРАНИЦА УРОКА
     * @param $training_alias
     * @param $lesson_alias
     * @throws Exception
     */
    public function actionLesson($training_alias, $lesson_alias)
    {
        $training_alias = htmlentities($training_alias);
        $lesson_alias = htmlentities($lesson_alias);

        $training = Training::getTrainingByAlias($training_alias);
        $lesson = $training ? TrainingLesson::getLessonByAlias($training['training_id'], $lesson_alias) : null;
        if (!$training || !$lesson) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $this->setPageSettings($lesson, 'lesson', 'lesson/index.php');

        // TODO для размышления, в старых тренингах используется params переменная и там в head подгружается код
        // для комментариев, по этому тут костыль, что бы там не городить огород из условий на тренинги(новые или старые)
        $params['params']['commenthead'] = $this->tr_settings['commenthead'];
        if (!empty($params['params']['commenthead'])) {
            $comments = 1;
        }

        $user_id = intval(User::isAuth());
        $user_groups = $user_id ? User::getGroupByUser($user_id) : false;
        $user_planes = $user_id ? Member::getPlanesByUser($user_id, 1, true) : false;
        $section = $lesson['section_id'] ? TrainingSection::getSection($lesson['section_id']) : null;
        $section_id = $section ? $section['section_id'] : 0;
        $user_is_curator = Training::isCuratorInTrainingInSection($user_id, $training['training_id'], $section_id);

        $access = Training::getAccessData($user_groups, $user_planes, $training, $section, $lesson);
        $has_lesson_last_stop = TrainingLesson::isLessonLastStopStatus($lesson['sort'], $training['training_id']);
        if ($has_lesson_last_stop && (!isset($access['is_admin']) && !isset($access['is_curator']))) {
            $access = TrainingLesson::isAccessLastHomework($has_lesson_last_stop, $training, $lesson, $section, $user_id, $user_groups, $user_planes);
        }
        if (!Training::checkUserAccess($access)) {
            $this->setViewPath('layouts/no_access.php');
            require_once (ROOT . "/extensions/training/views/frontend/view.php");
        }

        if ($user_id && !isset($_SESSION['training_save_uv']['lesson'][$lesson['lesson_id']])) {
            TrainingUserVisits::saveVisit($user_id, $training['training_id'], $section_id, $lesson['lesson_id']);
            $_SESSION['training_save_uv']['lesson'][$lesson['lesson_id']] = true;
        }


        $category = $training['cat_id'] != 0 ? TrainingCategory::getCategory($training['cat_id']) : null;
        $sub_category = false;
        if ($category && $category['parent_cat'] != 0) {
            $sub_category = $category;
            $category = TrainingCategory::getCategory($category['parent_cat']);
        }

        if ($user_id && $training['confirm_phone'] && CallPassword::notAccessUser($user_id)) {
            $setting = $this->setting;
            require_once (ROOT . '/extensions/callpassword/views/training/no_access.php');
        }

        $time = time();
        $homework_is_public = TrainingLesson::getPublicHomework($lesson['lesson_id'], $user_id) ? true : false;
        $task = TrainingLesson::getTask2Lesson($lesson['lesson_id']);
        $homework = $task ? TrainingLesson::getHomeWork($user_id, $lesson['lesson_id']) : false;
        $homework_id = $homework ? $homework['homework_id'] : null;
        $answer_list = $user_id ? TrainingLesson::getAnswers2Lesson($lesson['lesson_id'], $user_id, $homework_id) : false;

        // если задания у урока нет, и стоит настройка сразу делать урок пройденым
        $status_usermap = $task['task_type'] == 0 && $lesson['auto_access_lesson'] == 1 ? 3 : 0;

        $hit = TrainingLesson::writeHit($lesson['lesson_id'], $training['training_id'],$lesson['hits'] + 1, $user_id, $status_usermap);
        $lesson_complete_status = Traininglesson::getLessonCompleteStatus($lesson['lesson_id'], $user_id);
        $lesson_homework_status = Traininglesson::getHomeworkStatus($lesson['lesson_id'], $user_id);
        $levelAccessTypeHomeWork = TrainingLesson::getLevelAccessTypeHomeWork($user_groups, $user_planes, $training);

        // тут тип проверки домашнего задания берем из прав доступа юзера
        $task_check_type = $task['check_type'] > $levelAccessTypeHomeWork ? $levelAccessTypeHomeWork : $task['check_type'];

        if (isset($_POST['complete'])) { // ответ к уроку
            $homework = new TrainingHomeWork($training, $lesson, $task, $homework_is_public, $user_id);
            $save = $homework->answerSave($lesson_complete_status, $task_check_type, $levelAccessTypeHomeWork, $answer_list, 1);

            System::redirectUrl("/training/view/$training_alias/lesson/$lesson_alias");
        }

        if (isset($_POST['comment'])) { // ответ к уроку
            $homework = new TrainingHomeWork($training, $lesson, $task, $homework_is_public, $user_id);
            $save = $homework->answerSave($lesson_complete_status, $task_check_type, $levelAccessTypeHomeWork, $answer_list, 2);

            System::redirectUrl("/training/view/$training_alias/lesson/$lesson_alias");
        }

        $attachments = TrainingLesson::getElements2Lesson($lesson['lesson_id'], TrainingLesson::ELEMENT_TYPE_ATTACH);

        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    public function actionLessonAttach($attach_id) {
        $attach = TrainingLesson::getElement($attach_id);
        if ($attach ) {
            $lesson = TrainingLesson::getLesson($attach['lesson_id']);
            $training = Training::getTraining($lesson['training_id']);

            $user_id = intval(User::isAuth());
            $user_groups = $user_id ? User::getGroupByUser($user_id) : false;
            $user_planes = $user_id ? Member::getPlanesByUser($user_id, 1, true) : false;
            $section = $lesson['section_id'] ? TrainingSection::getSection($lesson['section_id']) : null;

            $access = Training::getAccessData($user_groups, $user_planes, $training, $section, $lesson);
            if (Training::checkUserAccess($access)) {
                $path = ROOT . "/load/training/lessons/{$attach['lesson_id']}/{$attach['params']['attach']}";
                if (file_exists($path)) {
                    System::fileForceDownload($path, $attach['params']['attach']);
                }
            }
        }
    }


    /**
     * СТРАНИЦА ПУБЛИЧНОГО ДЗ
     * @param $training_alias
     * @param $lesson_alias
     * @param $user__id
     */
    public function actionPublicHomework($training_alias, $lesson_alias, $user__id) {
        $training_alias = htmlentities($training_alias);
        $lesson_alias = htmlentities($lesson_alias);

        $training = Training::getTrainingByAlias($training_alias);
        if (!$training || !$training['on_public_homework']) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $lesson = TrainingLesson::getLessonByAlias($training['training_id'], $lesson_alias);
        if (!$lesson) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (!TrainingLesson::getPublicHomework($lesson['lesson_id'], $user__id)) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $this->setPageSettings($lesson, 'public_homework', 'lesson/public_homework.php');
        $user_id = intval(User::isAuth());
        if (!$user_id) {
            $this->setViewPath('layouts/no_access.php');
            require_once (ROOT . "/extensions/training/views/frontend/view.php");
        }

        $task = TrainingLesson::getTask2Lesson($lesson['lesson_id']);
        $lesson_complete_status = Traininglesson::getLessonCompleteStatus($lesson['lesson_id'], $user__id);
        $answer_list = TrainingLesson::getAnswers2Lesson($lesson['lesson_id'], $user__id, $task['task_id']);

        $category = $training['cat_id'] != 0 ? TrainingCategory::getCategory($training['cat_id']) : null;
        $sub_category = false;
        if ($category && $category['parent_cat'] != 0) {
            $sub_category = $category;
            $category = TrainingCategory::getCategory($category['parent_cat']);
        }
        $section = $lesson['section_id'] != 0 ? TrainingSection::getSection($lesson['section_id']) : null;
        $attachments = TrainingLesson::getElements2Lesson($lesson['lesson_id'], TrainingLesson::ELEMENT_TYPE_ATTACH);

        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    /**
     * СТРАНИЦА ВЫБОРА ТАРИФА в уроках (Продуктов)
     * @param $lesson_id
     */
    public function actionOptions($lesson_id)
    {
        $lesson_id = intval($lesson_id);
        $lesson_id = $lesson = TrainingLesson::getLesson($lesson_id);

        if (!$lesson_id) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $user_id = intval(User::isAuth());
        $h1 = $lesson_id['name'];
        $training = Training::getTraining($lesson_id['training_id']);
        $section = TrainingSection::getSection($lesson_id['section_id']);

        $sub_category = $category = false;
        if ($training['cat_id'] != 0) {
            $category = TrainingCategory::getCategory($training['cat_id']);
            if ($category && $category['parent_cat'] != 0) {
                $sub_category = $category;
                $category = TrainingCategory::getCategory($sub_category['parent_cat']);
            }
        }

        $big_button = json_decode($lesson_id['by_button'], true);
        $list_product = $big_button['rate'];

        $this->setViewPath('layouts/rates.php');
        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    /**
     * ЗАДАТЬ ОСНОВНЫЕ НАСТРОЙКИ СТРАНИЦЫ
     * @param $data
     * @param $is_page
     * @param null $view_path
     * @param bool $use_css
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
