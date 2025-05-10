<?php defined('BILLINGMASTER') or die;


class adminTrainingImportController extends AdminBase {

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
     * ОСНОВНАЯ ФОРМА ЗАГРУЗКИ(ИМПОРТА) ТРЕНИНГА
     */
    public function actionIndex()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['import_training']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {

            $import_ok = self::importTraining();
            if ($import_ok){
                Training::addSuccess("Успешно!");
            }
        }
        
        if (isset($_POST['transfer_user']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token'] &&
        isset($_POST['from_course']) && isset($_POST['to_training'])) {

            $transfer = self::transferUsers();
            if ($transfer){
                Training::addSuccess("Успешно!");
            }
        }

        if (isset($_POST['import_user']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token'] &&
        isset($_POST['to_training_from_file'])) {

            $time = time();
 
            $training_id = $_POST['to_training_from_file'];
            $countuser = self::importUsersFromCSV($training_id);
            if ($countuser['good']>0 || $countuser['bad']>0) {
                Training::addSuccess("Успешно импортировано: ".$countuser['good']." строк из файла.");
                if ($countuser['bad']>0) {
                    /// TODO Вот тут надо как-то вывести сообщение плашку и что бы файл скачивался с не найдеными юзерами...
                    Training::addError("Не удалось найти: ".$countuser['bad']." пользователей из файла.");
                    if ($countuser['isfile']) {
                        header("Location: "."/tmp/lost_users_".$training_id.".csv");
                    }
                }
            }

        }

        require_once (ROOT . '/extensions/training/views/admin/import/index.php');
    }

      /**
     * ИМПОРТ ТРЕНИНГА ИЗ CSV ФАЙЛОВ
     */
    public static function importTraining()
    {
        $setting = System::getSetting();

        if (isset($_FILES['training_file']) && $_FILES["training_file"]["size"] != 0) {
            $tmp_name = $_FILES["training_file"]["tmp_name"];
            if (is_uploaded_file($tmp_name)) {
                $file = fopen($tmp_name, "r");
                $keys = fgetcsv($file, 0, ",");
                if ($keys[0] !== 'course_id') {
                    Training::addError('Некорректный формат файла c тренингом');
                    return false;
                }
                while (($line = fgetcsv($file, 0, ',')) !== FALSE) {
                    $data = array_combine($keys, $line);
                    $data['end_date'] = !empty($data['end_date']) ? date("d.m.Y H:i", $data['end_date']) : '';
                    $data['start_date'] = $start_training = !empty($data['start_date']) ? date("d.m.Y H:i", $data['start_date']) : '';
                    $data['on_public_homework'] = 0;
                    $data['access_groups'] = !empty($data['groups']) ? unserialize($data['groups']) : '';
                    $data['access_planes'] = !empty($data['access']) ? unserialize($data['access']) : '';
                    $data['status'] = 0; // При импорте всегда статус выключен!
                    $data['show_lesson_cover_2mobile'] = 0;
                    $curators = unserialize($data['curators']);
                    $data['curators'] = $curators;
                    $data['authors'] = array($data['author_id']);
                    $big_button = [];
                    $big_button['type'] = $data['type_access_buy'] == 2 ? 3 : ($data['type_access_buy'] == 3 ? 4 : $data['type_access_buy']);
                    $big_button['your_url'] = isset($data['link_access_buy']) ? $data['link_access_buy'] : '';
                    $big_button['text'] = $data['button_text'];
                    $big_button['product_order'] = $big_button['product_desc'] = isset($data['product_access']) ? $data['product_access'] : '';
                    $data['big_button'] = $big_button;
                    $data = Training::beforeSaveTrainingData2Admin($data);
                    $add_training_id = Training::addTraining($data);
                }
                fclose($file);
            }
        }

        if (isset($_FILES['training_lessons_file']) && $_FILES["training_lessons_file"]["size"] != 0) {
            $tmp_name = $_FILES["training_lessons_file"]["tmp_name"];

            if (is_uploaded_file($tmp_name) && $add_training_id) {
                $file = fopen($tmp_name, "r");
                $keys = fgetcsv($file, 0, ',');
                if ($keys[0] !== 'lesson_id') {
                    Training::addError('Некорректный формат файла с уроками');
                    return false;
                }
                while (($line = fgetcsv($file, 0, ',')) !== FALSE) {
                    $data = array_combine($keys, $line);
                    $groups = unserialize($data['groups']);
                    $planes = !empty($data['access']) ? unserialize($data['access']) : '';
                    $data['access_groups'] = is_array($groups) ? array_values($groups) : null;
                    $data['access_planes'] = is_array($planes) ? array_values($planes) : null;
                    $buy_button = [];
                    if ($data['type_access_buy'] === "0"){ // тут настроку кнопки покупки нужно взять из настроек курса
                        $buy_button['type'] = $big_button['type'];
                    } else{
                        $buy_button['type'] = $data['type_access_buy'] == 2 ? 5 :($data['type_access_buy'] == 3 ? 4 : $data['type_access_buy'] );
                    }
                    $buy_button['text'] = "Оформить доступ";
                    $buy_button['product_order'] = $buy_button['product_lending'] = $buy_button['product_desc'] = $data['product_access'];
                    $buy_button['your_url'] = isset($data['link_access']) ? $data['link_access'] : '';
                    $data['by_button'] = $buy_button;
                    if ($data['timing'] > 0) { // если есть задержка открытия по расписанию 
                        switch ($data['timing_period']) {
                            case 'hour':
                                $data['shedule_count_days'] = $data['timing'] < 24 ? 1 : ceil($data['timing'] / 24);
                                break;
                            case 'day':
                                $data['shedule_count_days'] = $data['timing'];
                                break;
                            case 'week':
                                $data['shedule_count_days'] = $data['timing'] * 7;
                                break;
                            case 'month':
                                $data['shedule_count_days'] = $data['timing'] * 30;
                                break;
                        }
                        $data['shedule'] = 2;    
                    }

                    $data['shedule_relatively'] = !empty($start_training) ? 3 : 4;
                    $data['shedule_how_fast_open'] = 2;
                    $data['shedule_type'] = 1;
                    $data = TrainingLesson::beforeSaveLessonData2Admin($data, $add_training_id);

                    $lesson_id = TrainingLesson::addLesson($add_training_id, $data);
                    if ($lesson_id) {
                        $add_task = TrainingLesson::addTask($lesson_id); //task
                        if ($add_task) {
                            $data = TrainingLesson::beforeSaveLessonTaskData2Admin($data);
                            $data['access_type'] = 2; // тут для задания будет всегда 2
                            $edit_task = TrainingLesson::updateTask($lesson_id, $data);
                        } 
                        /// HTML блок который сверху
                        if (isset($data['custom_code_up']) && !empty($data['custom_code_up'])) {
                            $params = [];
                            $params['html'] = $data['custom_code_up'];
                            $params['name'] = 'HTML_up';
                            $add_content = TrainingLesson::addElement(TrainingLesson::ELEMENT_TYPE_HTML, $lesson_id, json_encode($params));
                        }

                        if (isset($data['video_urls'])) {
                            $videos = explode(PHP_EOL,$data['video_urls']);
                            $count = 1;
                            foreach ($videos as $key => $video) {
                                $params = [];
                                $params['element_type'] = 2;
                                $params['title'] = 'Видео_'.$count;
                                $params['name'] = 'Видео_'.$count;
                                $params['show_watermark'] = 0;
                                $params['url'] = $video;
                                $params['cover'] = 'Видео_'.$count;
                                $add_content = TrainingLesson::addElement(TrainingLesson::ELEMENT_TYPE_MEDIA, $lesson_id, json_encode($params));
                                $count++;
                            }
                            
                        }

                        if (isset($data['audio_urls'])) {
                            $audios = explode(PHP_EOL,$data['audio_urls']);
                            $count = 1;
                            foreach ($audios as $key => $audio) {
                                $params = [];
                                $params['element_type'] = 3;
                                $params['title'] = 'Аудио_'.$count;
                                $params['name'] = 'Аудио_'.$count;
                                $params['show_watermark'] = 0;
                                $params['url'] = $audio;
                                $params['cover'] = 'Аудио_'.$count;
                                $add_content = TrainingLesson::addElement(TrainingLesson::ELEMENT_TYPE_MEDIA, $lesson_id, json_encode($params));
                                $count++;
                            }
                            
                        }

                        if (isset($data['attach']) && !empty($data['attach'])) {
                            $attachs = json_decode($data['attach']);
                            $count = 1;
                            foreach ($attachs as $key => $attach) {
                                $params = [];
                                $params['type'] = 2;
                                $params['attach'] = '';
                                $params['title'] = $attach;
                                $params['line_up'] = 1;
                                $params['link'] = $setting['script_url'].'/load/lessons/'.$data['lesson_id'].'/'.$attach;
                                $params['name'] = $attach;
                                $params['cover'] = $attach;
                                $add_content = TrainingLesson::addElement(TrainingLesson::ELEMENT_TYPE_ATTACH, $lesson_id, json_encode($params));  
                                $count++;
                            }
                        }

                        if (isset($data['content'])) {
                            $params = [];
                            $params['type'] = 1;
                            $params['text'] = $data['content'];
                            $params['name'] = 'Основной';
                            $add_content = TrainingLesson::addElement(TrainingLesson::ELEMENT_TYPE_TEXT, $lesson_id, json_encode($params));  
                        }
                        /// HTML блок который снизу
                        if (isset($data['custom_code']) && !empty($data['custom_code'])) {
                            $params = [];
                            $params['html'] = $data['custom_code'];
                            $params['name'] = 'HTML_down';
                            $add_content = TrainingLesson::addElement(TrainingLesson::ELEMENT_TYPE_HTML, $lesson_id, json_encode($params));
                        }
                    }
                }
                fclose($file);
            }    
        }

        return;

    }

    /**
     * ПЕРЕНОС ПОЛЬЗОВАТЕЛЕЙ ИЗ СТАРОГО ТРЕНИНГА В НОВЫЙ 
     */
    public static function transferUsers()
    {
        $course_id = $_POST['from_course'];
        $training_id = $_POST['to_training'];
        $checklessons = TrainingImport::checkCountLessons($course_id, $training_id);
        return $checklessons;
    }

    /**
     * ИМПОРТ ПОЛЬЗОВАТЕЛЕЙ ИЗ ФАЙЛА CSV 
     */
    public static function importUsersFromCSV($training_id)
    {

        if (isset($_FILES['training_users_file']) && $_FILES["training_users_file"]["size"] != 0) {
            $tmp_name = $_FILES["training_users_file"]["tmp_name"];

            if (is_uploaded_file($tmp_name)) {
                
                $clean_ok = TrainingImport::cleaningTrainingDataMap($training_id);
                $countuser = [];
                $countuser['good'] = 0;
                $countuser['bad'] = 0;
                $countuser['isfile'] = false;
                $fpusers = fopen(ROOT.'/tmp/lost_users_'.$training_id.'.csv','w');
                $file = fopen($tmp_name, "r");
                $keys = fgetcsv($file, 0, ",");
                if ($keys[0] !== 'user_email') {
                    Training::addError('Некорректный формат файла');
                    return false;
                }
                while (($line = fgetcsv($file, 0, ',')) !== FALSE) {
                    $data = array_combine($keys, $line);
                    $user = User::getUserDataByEmail($data['user_email']);
                    if ($user) {
                        TrainingImport::addUserMapInfo($user['user_id'], $data['lesson_sort'], $training_id);
                        $countuser['good'] = $countuser['good'] + 1;
                    } else {
                        fputcsv($fpusers, $data, ',');
                        $countuser['bad'] = $countuser['bad'] + 1;
                    }
                    
                }
                $countuser['isfile'] = fclose($fpusers);
                fclose($file);
                return $countuser;
            }
        }

        return true;
    }

}
