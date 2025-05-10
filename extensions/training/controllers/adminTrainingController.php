<?php

defined('BILLINGMASTER') or die;


class adminTrainingController extends AdminBase {

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
     * СПИСОК ТРЕНИНГОВ
     */
    public function actionIndex()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $status = 1;
        $filter = false;

        if (isset($_POST['filter'])) {
            $filter = ['category' => $_POST['cat_id'] ? [(int)$_POST['cat_id']] : false];
            $status = $_POST['status'] != 'all' ? (int)$_POST['status'] : null;
        }

        if(isset($_POST['exportcsv']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            $training_id = intval($_POST['training_id']);
            $copy = Training::exportTrainingtoCSV($training_id);
            if($copy) header("Location: /admin/courses?success");

        }

        if(isset($_POST['copy']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){

            
            $training_id = intval($_POST['training_id']);
            $copy = Training::fullCopyTraining($training_id);
            if ($copy) {
                Training::addSuccess('Успешно! Тренинг скопирован и имеет статус выключен. <a href="/admin/training/edit/'.$copy.'">Перейти в тренинг</a>');
                //System::redirectUrl("/admin/training", $copy);
            }

        }

        $trainings_list = Training::getTrainingList(null, 1, $filter, $status);
        require_once (ROOT . '/extensions/training/views/admin/training/index.php');
    }


    /**
     * ДОБАВИТЬ ТРЕНИНГ
     */
    public function actionAdd()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['add_training']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_courses'])) {
                System::redirectUrl('/admin');
            }

            if ($data = Training::beforeSaveTrainingData2Admin($_POST)) {
                if (isset($_FILES['cover']) && $_FILES["cover"]["size"] != 0) {
                    $tmp_name = $_FILES["cover"]["tmp_name"];
                    $data['cover'] = $_FILES["cover"]["name"];

                    $folder = ROOT . '/images/training/';
                    $path = $folder . $data['cover'];

                    if (is_uploaded_file($tmp_name)) {
                        if (file_exists($path)) {
                            $pathinfoimage = pathinfo($path);
                            $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                            $data['cover'] = $newname;
                            $path = $folder . $newname;
                        }
                        move_uploaded_file($tmp_name, $path);
                        $resize = System::imgResize($path, 550, false);
                    }
                }

                if (isset($_FILES['full_cover']) && $_FILES["full_cover"]["size"] != 0) {
                    $tmp_name = $_FILES["full_cover"]["tmp_name"];
                    $data['full_cover'] = $_FILES["full_cover"]["name"];

                    $folder = ROOT . '/images/training/';
                    $path = $folder . $data['full_cover'];

                    if (is_uploaded_file($tmp_name)) {
                        if (file_exists($path)) {
                            $pathinfoimage = pathinfo($path);
                            $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                            $data['full_cover'] = $newname;
                            $path = $folder . $newname;
                        }
                        move_uploaded_file($tmp_name, $path);
                    }
                }

                $add_training_id = Training::addTraining($data);
                if ($add_training_id) {
                    System::redirectUrl("/admin/training/edit/$add_training_id", true);
                }
            }
        }

        require_once (ROOT . '/extensions/training/views/admin/training/add.php');
    }


    /**
     * ИЗМЕНИТЬ ТРЕНИНГ
     * @param $id
     */
    public function actionEdit($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $training = Training::getTraining($id);
        if (!$training) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['savetraining']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_courses'])) {
                System::redirectUrl('/admin');
            }

            if ($data = Training::beforeSaveTrainingData2Admin($_POST, $id)) {
                if (isset($_FILES['cover']) && $_FILES["cover"]["size"] != 0) {
                    $tmp_name = $_FILES["cover"]["tmp_name"];
                    $data['cover'] = $_FILES["cover"]["name"];

                    $folder = ROOT . '/images/training/';
                    $path = $folder . $data['cover'];

                    if (is_uploaded_file($tmp_name)) {
                        if (file_exists($path)) {
                            $pathinfoimage = pathinfo($path);
                            $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                            $data['cover'] = $newname;
                            $path = $folder . $newname;
                        }
                        move_uploaded_file($tmp_name, $path);
                        //$resize = System::imgResize($path, 550, false);
                    }
                } else {
                    $data['cover'] = $data['current_img'];
                }

                if (isset($_FILES['full_cover']) && $_FILES["full_cover"]["size"] != 0) {
                    $tmp_name = $_FILES["full_cover"]["tmp_name"];
                    $data['full_cover'] = $_FILES["full_cover"]["name"];

                    $folder = ROOT . '/images/training/';
                    $path = $folder . $data['full_cover'];

                    if (is_uploaded_file($tmp_name)) {
                        if (file_exists($path)) {
                            $pathinfoimage = pathinfo($path);
                            $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                            $data['full_cover'] = $newname;
                            $path = $folder . $newname;
                        }
                        move_uploaded_file($tmp_name, $path);
                    }
                } else {
                    $data['full_cover'] = isset($data['full_cover_current_img']) ? $data['full_cover_current_img'] : null; 
                }


                if (isset($_FILES['sertificate_file']) && $_FILES["sertificate_file"]["size"] != 0) {
                    $tmp_name = $_FILES["sertificate_file"]["tmp_name"];
                    $sertjson = json_decode($data['sertificate'], true);
                    $sertjson['template_file'] = $_FILES["sertificate_file"]["name"];

                    $folder = ROOT . '/images/training/sertificate/';
                    $path = $folder . $sertjson['template_file'];

                    if (is_uploaded_file($tmp_name)) {
                        if (file_exists($path)) {
                            $pathinfoimage = pathinfo($path);
                            $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                            $sertjson['template_file'] = $newname;
                            $data['sertificate'] = json_encode($sertjson);
                            $path = $folder . $newname;
                            move_uploaded_file($tmp_name, $path);
                        } else {
                            move_uploaded_file($tmp_name, $path);
                            $data['sertificate'] = json_encode($sertjson);
                        }
                    }
                } else {
                    $sertjson = json_decode($data['sertificate'], true);
                    $sertjson['template_file'] = isset($data['current_sert']) ? $data['current_sert'] : null; 
                    $data['sertificate'] = json_encode($sertjson);
                }


                $edit = Training::editTraining($id, $data);
                System::redirectUrl("/admin/training/edit/$id", $edit);
            }
        }

        if ($handle = opendir(ROOT . '/images/training/sertificate/fonts/')) {
            $fonts_cert = [];
            while (false !== ($file = readdir($handle))) { 
                $pathinfoimage = pathinfo($file);
                if ($pathinfoimage['extension'] == 'ttf') {
                    $fonts_cert[] = $file;
                }
            }
            closedir($handle); 
        }

        $sertificate = json_decode($training['sertificate'], True);
        if (!empty($sertificate['template_file'])) {
            $path_sert = ROOT . '/images/training/sertificate/'.$sertificate['template_file'];
            if (file_exists($path_sert)) {
                $size_sert = getimagesize($path_sert);
                $sert_picture = True;
            }
        }
        

        $by_button_curator_hw = $training['by_button_curator_hw'] ? json_decode($training['by_button_curator_hw'], true) : null;
        $by_button_autocheck_hw = $training['by_button_autocheck_hw'] ? json_decode($training['by_button_autocheck_hw'], true) : null;
        $by_button_self_hw = $training['by_button_self_hw'] ? json_decode($training['by_button_self_hw'], true) : null;
        $tr_curators = Training::getCuratorsTraining($id);
        $events_finish = Training::getEventsFinish($id, true);

        require_once (ROOT . '/extensions/training/views/admin/training/edit.php');
    }


    /**
     * УДАЛИТЬ ТРЕНИНГ
     * @param $training_id
     */
    public function actionDel($training_id)
    {
        $training_id = intval($training_id);
        $acl = self::checkAdmin();
        if (!isset($acl['del_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = Training::delTraining($training_id);
            if (is_bool($del)){
                System::redirectUrl("/admin/training", $del);
            } else {
                $_SESSION['del_trainig_id'] = $del;
                $del = false;
                System::redirectUrl("/admin/training", $del);
            }
            
        }
    }

    /**
     * УДАЛИТЬ ТРЕНИНГ ВМЕСТЕ С УРОКАМИ И СТРУКТУРОЙ
     * @param $training_id
     */
    public function actionDelAll($training_id)
    {
        $training_id = intval($training_id);
        $acl = self::checkAdmin();
        if (!isset($acl['del_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $lessons = TrainingLesson::getLessons($training_id, null, null, null);
            foreach ($lessons as $lesson) {
                $lesson_id = $lesson['lesson_id'];
                $dellesson = TrainingLesson::DelLesson($lesson_id);
            }
            $del = Training::delTraining($training_id);
            if($del && isset($_SESSION['del_trainig_id'])) {
                unset($_SESSION['del_trainig_id']);
            }
            System::redirectUrl("/admin/training", $del);
        }
    }


    /**
     * СТРУКТУРА ТРЕНИНГА
     * @param $training_id
     */
    public function actionStructure($training_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $training = Training::getTraining($training_id);
        if (!$training) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $sections = TrainingSection::getSections($training_id, null);
        require_once (ROOT . '/extensions/training/views/admin/structure/index.php');
    }


    public function actionAddEventsFinish() {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $training_id = (int)$_POST['training_id'];
        $training = Training::getTraining($training_id);
        if (!$training) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['events_save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $event_type = htmlentities($_POST['event_type']);
            $data = [];

            if ($event_type == 'give_access') {
                $data = [
                    'access_groups' => isset($_POST['give_access']['access_groups']) ? $_POST['give_access']['access_groups'] : [],
                    'access_planes' => isset($_POST['give_access']['access_planes']) ? $_POST['give_access']['access_planes'] : [],
                    'title' => 'Выдать доступ'
                ];
            } elseif($event_type == 'send_message') {
                $data = [
                    'text' => $_POST['send_message']['text'],
                    'title' => 'Отправить сообщение'
                ];
            } elseif($event_type == 'give_sertificate') {
                $data = [
                    'title' => 'Выдать сертификат/диплом'
                ];
            }

            $edit = Training::addEventsFinish($training_id, $event_type, json_encode($data));
            System::redirectUrl("/admin/training/edit/$training_id", $edit);
        }
    }


     /**
     * ПРЕДПРОСМОТР СЕРТИФИКАТА ИЗ АДМИНКИ 
     * @param $training_id
     */
    public function actionPreviewCertificate($training_id) {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        return Training::ShowCertificateByUrl(0, $training_id, True);
    
    }

    public function actionEditEventsFinish($id) {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $event_finish = Training::getEventFinish($id);
        if (!$event_finish) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['events_save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $params = $_POST['params'];

            if ($_POST['event_type'] == 'give_access') {
                $params['access_groups'] = isset($params['access_groups']) ? $params['access_groups'] : [];
                $params['access_planes'] = isset($params['access_planes']) ? $params['access_planes'] : [];
            }

            $edit = Training::editEventsFinish($id, json_encode($params));
            System::redirectUrl("/admin/training/edit/{$_POST['training_id']}", $edit);
        }
    }


    /**
     * НАСТРОЙКИ ТРЕНИНГОВ 2.0
     */
    public function actionSetting()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_courses'])) {
                System::redirectUrl('/admin');
            }

            $status = intval($_POST['status']);
            $save = Training::saveSettings($_POST['training'], $status);
            System::redirectUrl("/admin/trainingsetting", $save);
        }

        $enable = Training::getStatus();
        require_once (ROOT . '/extensions/training/views/admin/settings/index.php');
    }


    public function actionStatistics($training_id) {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $training = Training::getTraining($training_id);
        if (!$training) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }
        
        $tr_curators = Training::getCuratorsTraining($training_id);

        if (isset($_GET['reset'])) {
            unset($_SESSION['filter_tr_stat']);
        }

        $stat_type = $csv = null;
        $filter = !isset($_POST['filter']) && isset($_SESSION['filter_tr_stat']) ? $_SESSION['filter_tr_stat'] : [
            'email' => isset($_POST['email']) && $_POST['email'] ? htmlentities($_POST['email']) : null,
            'curator' => isset($_POST['curator']) && $_POST['curator'] ? intval($_POST['curator']) : null,
            'pass_status' => isset($_POST['pass_status']) && $_POST['pass_status'] ? intval($_POST['pass_status']) : null,
            'start_date' => isset($_POST['start_date']) && $_POST['start_date'] ? strtotime($_POST['start_date']) : null,
            'finish_date' => isset($_POST['finish_date']) && $_POST['finish_date'] ? strtotime($_POST['finish_date']) : null,
            'completed_lessons' => isset($_POST['completed_lessons']) && $_POST['completed_lessons'] ? intval($_POST['completed_lessons']) : null,
            'last_lesson_complete' => isset($_POST['last_lesson_complete']) && $_POST['last_lesson_complete'] ? intval($_POST['last_lesson_complete']) : null,
            'lesson_id' => isset($_POST['lesson_id']) && $_POST['lesson_id'] ? intval($_POST['lesson_id']) : null,
            'lesson_status' => isset($_POST['lesson_status']) && $_POST['lesson_status'] ? intval($_POST['lesson_status']) : null,
            'stop_out_day' => isset($_POST['stop_out_day']) && $_POST['stop_out_day'] ? intval($_POST['stop_out_day']) : null,
        ];

        if (isset($_POST['load_csv']) && isset($_SESSION['filter_tr_stat'])) {
            $filter['is_filter'] = true;
        } else {
            $filter['is_filter'] = array_filter($filter, 'strlen') ? true : false;
            if ($filter['is_filter']) {
                $_SESSION['filter_tr_stat'] = $filter;
            }
        }

        if (isset($_POST['stat_type']) && !isset($_POST['filter'])) {
            $stat_type = $_POST['stat_type'];
            $count_lessons = Traininglesson::getCountLessons2Training($training);

            if ($stat_type == 'users') { // Пользователи
                $stats = TrainingStatistics::getUsersStatistics($training_id, $filter, $count_lessons);
                $csv = isset($_POST['load_csv']) ? TrainingStatistics::getUsersCsv($stats) : null;
            } elseif ($stat_type == 'lessons') { // Прохождение уроков
                $stats = TrainingStatistics::getLessonsStatistics($training_id, $filter, $count_lessons);
                $csv = isset($_POST['load_csv']) ? TrainingStatistics::getLessonsCsv($stats) : null;
            } else if ($stat_type == 'curators') { // Кураторы
                $stats = TrainingStatistics::getCuratorsStatistics($training, $filter, $count_lessons);
                $csv = isset($_POST['load_csv']) ? TrainingStatistics::getCuratorsCsv($stats) : null;
            } else if ($stat_type == 'certificates') { // Сертификаты
                $stats = TrainingStatistics::getCertificatesStatistics($training_id);
            } else  { // Общая
                $sections = TrainingSection::getSections($training_id);
                $stat = TrainingStatistics::getCommonStatistics($training, null, $filter);
                if (isset($_POST['load_csv'])) {
                    $stats[] = $stat;
                    if ($sections) {
                        foreach($sections as $section) {
                            $stats[] = TrainingStatistics::getCommonStatistics($training, $section, $filter);
                        }
                    }
                    $csv = TrainingStatistics::getCommonCsv($training, $sections, $stats);
                }
            }

            if ($csv) {
                $time = time();
                $write = file_put_contents(ROOT . "/tmp/training_statistics_$time.csv", $csv);
                if ($write) {
                    System::redirectUrl("/tmp/training_statistics_$time.csv");
                }
            }

            require_once (ROOT . "/extensions/training/views/admin/training/statistics/$stat_type.php");
        } else {
            require_once (ROOT . '/extensions/training/views/admin/training/statistics/index.php');
        }
    }
}