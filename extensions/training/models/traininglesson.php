<?php defined('BILLINGMASTER') or die;


class TrainingLesson {

    use ResultMessage;


    // TODO Перд релизом, после тщательного тестирования проверить не использованые константы через глобальный поиск
    // и удалить не используемые
    const STATUS_LESSON_NOT_ACCESS = 1;
    const STATUS_LESSON_COMPLETE = 2;
    const STATUS_LESSON_OPEN = 3;
    const STATUS_LESSON_HOMEWORK_NOT_COMPLETED = 4;
    const STATUS_LESSON_NOT_YET = 5; // ЕЩЕ НЕ ПОДОШЛО ВРЕМЯ
    const STATUS_LESSON_NO_DATE_FOR_CALCULATION = 8;

    const ELEMENT_TYPE_MEDIA = 1;
    const ELEMENT_TYPE_PLAYLIST = 2;
    const ELEMENT_TYPE_TEXT = 3;
    const ELEMENT_TYPE_ATTACH = 4;
    const ELEMENT_TYPE_HTML = 5;
    const ELEMENT_TYPE_POLL = 6;
    const ELEMENT_TYPE_FORUM = 7;

    const ELEMENT_TEXT_TYPE_TEXT = 1;
    const ELEMENT_TEXT_TYPE_ACCORDEON = 2;
    const ELEMENT_TEXT_TYPE_POPAP = 3;

    const MSG_TYPE_ANSWER = 1;
    const MSG_TYPE_COMMENT = 2;

    /// Статусы в таблице training_user_map
    const LESSON_STARTED = 0; // НАЧАТ УРОК (ВОШЕЛ В УРОК)
    const HOMEWORK_SUBMITTED = 1; // ОТПРАВЛЕНО ДЗ
    const HOMEWORK_DECLINE = 2; // ДЗ ОТКЛОНЕНО
    const HOMEWORK_ACCEPTED = 3; // ДЗ ПРИНЯТО
    const HOMEWORK_AUTOCHECK = 4; // АВТОПРОВЕРКА

    /// Статусы в таблице training_home_work
    /// там еще есть 0 статус это когда тест начат или пройден, но само задание еще не отправлено
    const HOME_WORK_ACCEPTED = 1;
    const HOME_WORK_DECLINE = 2;
    const HOME_WORK_IN_VERIFICATION = 3;
    const HOME_WORK_SEND = 4;

    /// TODO нужно добавить(переделать старые) новые константы под новые таблицы
    /// пока совосем старые не удаляем, только добавляем новые!!!
    const ANSWER_IS_NOT_READ = 0;
    const ANSWER_IS_READ = 1;
    const ANSWER_IS_ANSWERED = 2;

    /// Это статусы в таблице training_home_work_comments
    const COMMENT_IS_NOT_READ = 0;
    const COMMENT_IS_READ = 1;
    const COMMENT_IS_ANSWERED = 2; // под этим статусом пишутся комментарии куратора!!!
    const COMMENT_DELETED = 3;

    const HOMEWORK_ACCESS_FREE = 0; // ТИП ДОСТУПА - СВОБОДНЫЙ
    const HOMEWORK_ACCESS_TO_GROUP = 1; // ТИП ДОСТУПА - ПО ГРУППЕ
    const HOMEWORK_ACCESS_TO_SUBS = 2; // ТИП ДОСТУПА - ПО ПОДПИСКЕ

    /// это будут статусы в таблице lesson 
    const ENTER_IN_PREVIOUS_LESSON_DATE = 1; // ДАТА ВХОДА В ПРЕДЫДУ́ЩИЙ УРОК
    const ENTER_IN_FIRST_LESSON_DATE = 2; // ДАТА ВХОДА В ПЕРВЫЙ УРОК
    const START_DATE_TRAINING = 3; // ДАТА НАЧАЛА ТРЕНИНГА 
    const START_BUY_DATE_TRAINING = 4; // ДАТА НАЧАЛА ТРЕНИНГА


    private static $open_lesson_date;


    /**
     * ОБРАБОТАТЬ ДАННЫЕ УРОКА ПЕРЕД СОХРАНЕНИЕМ ДЛЯ АДМИНКИ
     * @param $data
     * @param $training_id
     * @param null $lesson_id
     * @return mixed
     */
    public static function beforeSaveLessonData2Admin($data, $training_id, $lesson_id = null) {
        $fields = [
            'safe' => [
                'less_desc', 'title', 'alias'
            ],
            'int' => [
                'block_id', 'duration', 'status', 'access_type', 'show_hits', 'show_comments',
                'shedule', 'shedule_type', 'shedule_relatively', 'shedule_how_fast_open',
                'shedule_count_days', 'shedule_access_time_weekday', 'auto_access_lesson', 'shedule_hidden'
            ],
            'str' => [
                'name', 'img_alt', 'meta_desc', 'meta_keys'
            ],
            'date' => ['public_date', 'end_date', 'shedule_open_date'],
            'json' => [
                'access_groups', 'access_planes', 'by_button'
            ],
        ];

        $data = Training::beforeSaveData2Admin($fields, $data);
        $data['title'] = $data['title'] ?: $data['name'];

        $block = $data['block_id'] ? TrainingBlock::getBlock($data['block_id']) : null;
        $data['section_id'] = $block ? $block['section_id'] : intval($data['section_id']);
        $data['alias'] = $data['alias'] ?: System::Translit($data['name']);
        if (System::searchDuplicateAliases($data['alias'], 'training_lessons', $lesson_id, 'lesson_id')) {
            $data['alias'] = self::getUniqueAlias2Lesson($training_id, $data['alias']);
        }

        return $data;
    }


    /**
     * ОБРАБОТАТЬ ДАННЫЕ ЗАДАНИЯ ПЕРЕД СОХРАНЕНИЕМ ДЛЯ АДМИНКИ
     * @param $data
     * @return mixed
     */
    public static function beforeSaveLessonTaskData2Admin($data) {
        $fields = [
            'safe' => [
                'text', 'stop_lesson'
            ],
            'int' => [
                'task_type', 'check_type', 'autocheck_time', 'show_upload_file', 'show_work_link', 'completed_on_time',
                'not_completed_on_time', 'access_type', 'access_time', 'access_time_weekday', 'access_time_type', 'access_time_days'
            ],
            'str' => [],
            'date' => [],
            'json' => [
                'completed_time_add_group', 'completed_time_del_group'
            ],
        ];

        $data = Training::beforeSaveData2Admin($fields, $data);

        $data['hint'] = isset($_POST['task']['hint']) ? html_entity_decode($_POST['task']['hint']) : '';
        $data['auto_answer'] = isset($_POST['task']['auto_answer']) ? html_entity_decode($_POST['task']['auto_answer']) : '';
        $data['stop_lesson'] = (int)$data['stop_lesson'];
        if (!$data['stop_lesson']) {
            $data['access_type'] = 0;
        }

        return $data;
    }

    /**
     * ОБРАБОТАТЬ ДАННЫЕ ТЕСТА ПЕРЕД СОХРАНЕНИЕМ ДЛЯ АДМИНКИ
     * @param $data
     * @return mixed
     */
    public static function beforeSaveLessonTestData2Admin($data) {
        $fields = [
            'safe' => [],
            'int' => [
                'test_finish', 'test_try', 'test_time', 'show_questions_count', 'help_hint_success',
                'help_hint_fail',
            ],
            'str' => ['test_desc'],
            'date' => [],
            'json' => [],
        ];

        return Training::beforeSaveData2Admin($fields, $data);
    }


    /**
     * ДОБАВИТЬ УРОК
     * @param $training_id
     * @param $data
     * @return bool
     */
    public static function addLesson($training_id, $data)
    {
        $sort = self::getFreeSort($training_id);
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'training_lessons (training_id, section_id, block_id, name, alias, title, status, sort,  
                    access_time_type, access_time_value, create_date, access_type, meta_desc, meta_keys, shedule, access_groups,
                    access_planes, by_button, shedule_type, shedule_relatively, shedule_count_days, shedule_how_fast_open) 
                VALUES (:training_id, :section_id, :block_id, :name, :alias, :title, :status, :sort,
                    :access_time_type, :access_time_value, :create_date, :access_type, :meta_desc, :meta_keys, :shedule, :access_groups,
                    :access_planes, :by_button, :shedule_type, :shedule_relatively, :shedule_count_days, :shedule_how_fast_open)';

        $access_type = isset($data['access_type']) ? $data['access_type'] : 3; // По умолчанию доступ - Наследовать
        $access_time_type = 0;
        $access_time_value = 0;
        $create_date = time();
        $meta_desc = ""; 
        $meta_keys = "";
        $shedule = isset($data['shedule']) ? $data['shedule'] : 1;

        $result = $db->prepare($sql);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':section_id', $data['section_id'], PDO::PARAM_INT);
        $result->bindParam(':block_id', $data['block_id'], PDO::PARAM_INT);
        $result->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $result->bindParam(':alias', $data['alias'], PDO::PARAM_STR);
        $result->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':status', $data['status'], PDO::PARAM_INT);
        $result->bindParam(':access_groups', $data['access_groups'], PDO::PARAM_STR);
        $result->bindParam(':access_planes', $data['access_planes'], PDO::PARAM_STR);
        $result->bindParam(':access_time_type', $access_time_type, PDO::PARAM_INT);
        $result->bindParam(':access_time_value', $access_time_value, PDO::PARAM_INT);
        $result->bindParam(':create_date', $create_date, PDO::PARAM_INT);
        $result->bindParam(':access_type', $access_type, PDO::PARAM_INT);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':by_button', $data['by_button'], PDO::PARAM_STR);
        $result->bindParam(':shedule', $shedule, PDO::PARAM_INT);
        $result->bindParam(':shedule_type', $data['shedule_type'], PDO::PARAM_INT);
        $result->bindParam(':shedule_relatively', $data['shedule_relatively'], PDO::PARAM_INT);
        $result->bindParam(':shedule_count_days', $data['shedule_count_days'], PDO::PARAM_INT);
        $result->bindParam(':shedule_how_fast_open', $data['shedule_how_fast_open'], PDO::PARAM_INT);
        
        
        $res = $result->execute();

        return $res ? $db->lastInsertId('lesson_id') : false;
    }


    /**
     * ИЗМЕНИТЬ УРОК
     * @param $lesson_id
     * @param $data
     * @return bool
     */
    public static function editLesson($lesson_id, $data)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS."training_lessons SET name = :name, section_id = :section_id,
                block_id = :block_id, alias = :alias, title = :title, cover = :cover, img_alt = :img_alt, status = :status,
                less_desc = :less_desc, meta_desc = :meta_desc, meta_keys = :meta_keys, access_type = :access_type,
                access_groups = :access_groups, access_planes = :access_planes, access_time_type = :access_time_type, 
                access_time_value = :access_time_value, duration = :duration, public_date = :public_date, end_date = :end_date,
                show_hits = :show_hits, show_comments = :show_comments, by_button = :by_button, shedule = :shedule,
                shedule_type = :shedule_type, shedule_relatively = :shedule_relatively, shedule_open_date = :shedule_open_date,
                shedule_how_fast_open = :shedule_how_fast_open, shedule_count_days = :shedule_count_days, 
                shedule_access_time_weekday = :shedule_access_time_weekday, auto_access_lesson =:auto_access_lesson,
                shedule_hidden = :shedule_hidden
                WHERE lesson_id = $lesson_id";

        $access_time_type = 0;
        $access_time_value = 0;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $result->bindParam(':alias', $data['alias'], PDO::PARAM_STR);
        $result->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $result->bindParam(':cover', $data['img'], PDO::PARAM_STR);
        $result->bindParam(':img_alt', $data['img_alt'], PDO::PARAM_STR);
        $result->bindParam(':less_desc', $data['less_desc'], PDO::PARAM_STR);

        $result->bindParam(':meta_desc', $data['meta_desc'], PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $data['meta_keys'], PDO::PARAM_STR);
        $result->bindParam(':access_groups', $data['access_groups'], PDO::PARAM_STR);
        $result->bindParam(':access_planes', $data['access_planes'], PDO::PARAM_STR);

        $result->bindParam(':section_id', $data['section_id'], PDO::PARAM_INT);
        $result->bindParam(':block_id', $data['block_id'], PDO::PARAM_INT);
        $result->bindParam(':status', $data['status'], PDO::PARAM_INT);
        $result->bindParam(':auto_access_lesson', $data['auto_access_lesson'], PDO::PARAM_INT);
        $result->bindParam(':access_type', $data['access_type'], PDO::PARAM_INT);
        $result->bindParam(':access_time_type', $access_time_type, PDO::PARAM_INT);
        $result->bindParam(':access_time_value', $access_time_value, PDO::PARAM_INT);
        $result->bindParam(':duration', $data['duration'], PDO::PARAM_INT);
        $result->bindParam(':public_date', $data['public_date'], PDO::PARAM_INT);
        $result->bindParam(':end_date', $data['end_date'], PDO::PARAM_INT);
        $result->bindParam(':show_hits', $data['show_hits'], PDO::PARAM_INT);
        $result->bindParam(':show_comments', $data['show_comments'], PDO::PARAM_INT);
        $result->bindParam(':by_button', $data['by_button'], PDO::PARAM_STR);
        $result->bindParam(':shedule', $data['shedule'], PDO::PARAM_INT);
        $result->bindParam(':shedule_type', $data['shedule_type'], PDO::PARAM_INT);
        $result->bindParam(':shedule_relatively', $data['shedule_relatively'], PDO::PARAM_INT);
        $result->bindParam(':shedule_open_date', $data['shedule_open_date'], PDO::PARAM_INT);
        $result->bindParam(':shedule_how_fast_open', $data['shedule_how_fast_open'], PDO::PARAM_INT);
        $result->bindParam(':shedule_count_days', $data['shedule_count_days'], PDO::PARAM_INT);
        $result->bindParam(':shedule_access_time_weekday', $data['shedule_access_time_weekday'], PDO::PARAM_INT);
        $result->bindParam(':shedule_hidden', $data['shedule_hidden'], PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ УРОК
     * @param $lesson_id
     * @return bool
     */
    public static function DelLesson($lesson_id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'training_lessons WHERE lesson_id = :id';
        $sql .= '; DELETE FROM '.PREFICS.'training_answers WHERE lesson_id = :id';
        $sql .= '; DELETE FROM '.PREFICS.'training_task WHERE lesson_id = :id';
        $sql .= '; DELETE FROM '.PREFICS.'training_user_map WHERE lesson_id = :id';
        $sql .= '; DELETE FROM '.PREFICS.'training_test WHERE lesson_id = :id';
        $sql .= '; DELETE thw, thwh, thwc FROM '.PREFICS.'training_home_work AS thw 
        LEFT JOIN '.PREFICS.'training_home_work_history AS thwh ON thwh.homework_id = thw.homework_id 
        LEFT JOIN '.PREFICS.'training_home_work_comments AS thwc ON thwc.homework_id = thw.homework_id 
        WHERE thw.lesson_id = :id';

        $result = $db->prepare($sql);
        $result->bindParam(':id', $lesson_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ УРОКА
     * @param $lesson_id
     * @return bool|mixed
     */
    public static function getLesson($lesson_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."training_lessons WHERE lesson_id = $lesson_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ УРОК ПО АЛИАСУ
     * @param $tr_id
     * @param $alias
     * @param int $status
     * @return bool
     */
    public static function getLessonByAlias($tr_id, $alias, $status = 1)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS.'training_lessons WHERE training_id = :training_id AND alias = :alias';
        $sql .= ($status !== null ? " AND status = $status" : '') . ' LIMIT 1';
        $result = $db->prepare($sql);

        $result->bindParam(':training_id', $tr_id, PDO::PARAM_INT);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ УРОК ПО ЗНАЧЕНИЮ СОРТИРОВКИ
     * @param $tr_id
     * @param $sort
     * @param int $status
     * @return bool|mixed
     */
    public static function getLessonBySort($tr_id, $sort, $status = 1, $route = null)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS.'training_lessons WHERE training_id = :training_id AND sort = :sort';
        $sql .= ($status !== null ? " AND status = $status" : '') . ' LIMIT 1';
        $result = $db->prepare($sql);

        $result->bindParam(':training_id', $tr_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (!empty($data)) {
            if ($data['end_date'] > 0 &&  $data['end_date'] < time()) {
                if ($route == 1) {
                    $new_sort = $sort-1;
                } else {
                    $new_sort = $sort+1;
                }
                return self::getLessonBySort($tr_id, $new_sort, $status, $route);
            } else {
                return $data;
            }
        } else {
            return false;
        }
    }
    
    /**
     * КОПИРОВАНИЕ УРОКА
     * @param $training_id
     * @param $lesson
     * @return int
     */

    public static function copyLesson($training_id, $lesson) {
        $new_name = "{$lesson['name']} копия";
        $new_alias = self::getUniqueAlias2Lesson($training_id, $lesson['alias']);
        $lesson['status'] = 0;
        $new_sort = self::getFreeSort($training_id);
        $old_lesson_id = $lesson['lesson_id'];
        $db = Db::getConnection();
        $db->beginTransaction();
        $result = $db->query("INSERT INTO ".PREFICS."training_lessons (training_id, section_id, block_id, name, alias, cover, img_alt, title, 
                            less_desc, status, sort, access_time_type, access_time_value, duration, create_date, show_hits, show_comments, 
                            access_type, meta_desc, meta_keys, shedule, access_groups, access_planes, by_button, shedule_hidden, shedule_type, 
                            shedule_relatively, shedule_count_days, shedule_how_fast_open, shedule_access_time_weekday)
                            SELECT training_id, section_id, block_id, '$new_name', '$new_alias', cover, img_alt, title, 
                            less_desc, 0, $new_sort, access_time_type, access_time_value, duration, create_date, show_hits, show_comments, 
                            access_type, meta_desc, meta_keys, shedule, access_groups, access_planes, by_button, shedule_hidden, shedule_type, 
                            shedule_relatively, shedule_count_days, shedule_how_fast_open, shedule_access_time_weekday 
                            FROM ".PREFICS."training_lessons WHERE lesson_id = $old_lesson_id LIMIT 1");
        
        $new_lesson_id = $result ? $db->lastInsertId() : null;

        if (isset($new_lesson_id) && !empty($new_lesson_id)) {
            /// Запрос копирования заданий(task)
            $result = $db->query("INSERT INTO ".PREFICS."training_task (lesson_id,task_type,check_type,text,auto_answer,stop_lesson,
                                autocheck_time,access_type,check_status_type,access_time,access_time_days,access_time_weekday,
                                show_upload_file,show_work_link,hint,completed_on_time,not_completed_on_time,completed_time_add_group,
                                completed_time_del_group)
                                SELECT $new_lesson_id,tt.task_type,tt.check_type,tt.text,tt.auto_answer,tt.stop_lesson,
                                tt.autocheck_time,tt.access_type,tt.check_status_type,tt.access_time,tt.access_time_days,tt.access_time_weekday,
                                tt.show_upload_file,tt.show_work_link,tt.hint,tt.completed_on_time,tt.not_completed_on_time,tt.completed_time_add_group,
                                tt.completed_time_del_group
                                FROM ".PREFICS."training_task AS tt WHERE tt.lesson_id = $old_lesson_id");
            /// Запрос копирования элементов урока
            $result = $db->query("INSERT INTO ".PREFICS."training_lesson_elements (type, lesson_id, sort, params)
                                SELECT type, $new_lesson_id, sort, params
                                FROM ".PREFICS."training_lesson_elements WHERE lesson_id = $old_lesson_id");
            /// Запрос по Плейлисту 
            $result = $db->query("INSERT INTO ".PREFICS."training_playlist_items (playlist_id, sort, params)
                                SELECT tle2.id as new_play_list_id, tpi.sort, tpi.params
                                FROM ".PREFICS."training_playlist_items AS tpi
                                LEFT JOIN ".PREFICS."training_lesson_elements AS tle ON tpi.playlist_id = tle.id
                                LEFT JOIN ".PREFICS."training_lesson_elements AS tle2 ON tle2.lesson_id = $new_lesson_id  AND tle2.type = 2
                                WHERE tle.lesson_id = $old_lesson_id AND tle.type = 2");
            /// Запрос копирования тестов
            $result = $db->query("INSERT INTO ".PREFICS."training_test (task_id, lesson_id, test_desc, finish, test_try, test_time,
                                show_questions_count, help_hint_success, help_hint_fail, ratings) 
                                SELECT ttask.task_id, $new_lesson_id, tt.test_desc, tt.finish, tt.test_try, tt.test_time, tt.show_questions_count, 
                                tt.help_hint_success, tt.help_hint_fail, tt.ratings
                                FROM ".PREFICS."training_test as tt
                                LEFT JOIN ".PREFICS."training_task AS ttask ON ttask.lesson_id = $new_lesson_id
                                WHERE tt.lesson_id = $old_lesson_id;
                                INSERT INTO ".PREFICS."training_questions (test_id, question, help, true_answer, require_all_true, status, sort, image)
                                SELECT tt2.test_id, tq.question, tq.help, tq.true_answer, tq.require_all_true, tq.status, tq.sort, tq.image
                                FROM  ".PREFICS."training_questions AS tq
                                LEFT JOIN  ".PREFICS."training_test as tt ON tt.test_id = tq.test_id
                                LEFT JOIN  ".PREFICS."training_task AS ttask ON ttask.lesson_id = $new_lesson_id
                                LEFT JOIN  ".PREFICS."training_test as tt2 ON tt2.lesson_id = $new_lesson_id
                                WHERE tt.lesson_id = $old_lesson_id;
                                INSERT INTO ".PREFICS."training_test_options (quest_id, title, value, sort, valid, points, cover)
                                SELECT tq2.quest_id, testop.title, testop.value, testop.sort, testop.valid, testop.points, testop.cover
                                FROM ".PREFICS."training_test_options AS testop
                                LEFT JOIN ".PREFICS."training_questions AS tq ON tq.quest_id = testop.quest_id
                                LEFT JOIN ".PREFICS."training_test as tt ON tt.test_id = tq.test_id
                                LEFT JOIN ".PREFICS."training_test as tt2 ON tt2.lesson_id = $new_lesson_id 
                                LEFT JOIN ".PREFICS."training_questions AS tq2 ON tq2.test_id = tt2.test_id AND tq.sort= tq2.sort
                                WHERE tt.lesson_id = $old_lesson_id;");

            $result = $db->query("SELECT tle.type, tle.lesson_id AS old_id, tle.sort, tle.params
                                FROM ".PREFICS."training_lesson_elements AS tle
                                WHERE tle.lesson_id = $old_lesson_id AND tle.type = 4");
            
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $params = json_decode($row['params']);
                $dir_new_path = ROOT . "/load/training/lessons/{$new_lesson_id}";
                if (!file_exists($dir_new_path)) {
                    mkdir($dir_new_path);
                }
                $file_old = ROOT . "/load/training/lessons/{$old_lesson_id}" . "/" . $params->attach;
                $file_new = $dir_new_path . "/" . $params->attach;
                if (is_file($file_old)){
                    copy($file_old, $file_new);
                } 
            }
            $db->commit();                   
        }

        return $new_lesson_id;
    }


    /**
     * КОПИРОВАНИЕ/ПЕРЕНОС УРОКА В ДРУГОЙ ТРЕНИНГ
     * @param $training_id
     * @param $lesson
     * @return int
     */

    public static function copyTransfer($training_id, $lesson) {
        
        $new_alias = self::getUniqueAlias2Lesson($training_id, $lesson['alias']);
        $lesson['status'] = 0;
        $new_sort = self::getFreeSort($training_id);
        $old_lesson_id = $lesson['lesson_id'];
        $db = Db::getConnection();
        $db->beginTransaction();
        $result = $db->query("INSERT INTO ".PREFICS."training_lessons (training_id, name, section_id, block_id, alias, cover, img_alt, title, 
                            less_desc, status, sort, access_time_type, access_time_value, duration, create_date, show_hits, show_comments, 
                            access_type, meta_desc, meta_keys, shedule, access_groups, access_planes, by_button, shedule_hidden, shedule_type, 
                            shedule_relatively, shedule_count_days, shedule_how_fast_open, shedule_access_time_weekday)
                            SELECT $training_id, name, 0, 0, '$new_alias', cover, img_alt, title, 
                            less_desc, 0, $new_sort, access_time_type, access_time_value, duration, create_date, show_hits, show_comments, 
                            access_type, meta_desc, meta_keys, shedule, access_groups, access_planes, by_button, shedule_hidden, shedule_type, 
                            shedule_relatively, shedule_count_days, shedule_how_fast_open, shedule_access_time_weekday 
                            FROM ".PREFICS."training_lessons WHERE lesson_id = $old_lesson_id LIMIT 1");
        
        $new_lesson_id = $result ? $db->lastInsertId() : null;

        if (isset($new_lesson_id) && !empty($new_lesson_id)) {
            /// Запрос копирования заданий(task)
            $result = $db->query("INSERT INTO ".PREFICS."training_task (lesson_id,task_type,check_type,text,auto_answer,stop_lesson,
                                autocheck_time,access_type,check_status_type,access_time,access_time_days,access_time_weekday,
                                show_upload_file,show_work_link,hint,completed_on_time,not_completed_on_time,completed_time_add_group,
                                completed_time_del_group)
                                SELECT $new_lesson_id,tt.task_type,tt.check_type,tt.text,tt.auto_answer,tt.stop_lesson,
                                tt.autocheck_time,tt.access_type,tt.check_status_type,tt.access_time,tt.access_time_days,tt.access_time_weekday,
                                tt.show_upload_file,tt.show_work_link,tt.hint,tt.completed_on_time,tt.not_completed_on_time,tt.completed_time_add_group,
                                tt.completed_time_del_group
                                FROM ".PREFICS."training_task AS tt WHERE tt.lesson_id = $old_lesson_id");
            /// Запрос копирования элементов урока
            $result = $db->query("INSERT INTO ".PREFICS."training_lesson_elements (type, lesson_id, sort, params)
                                SELECT type, $new_lesson_id, sort, params
                                FROM ".PREFICS."training_lesson_elements WHERE lesson_id = $old_lesson_id");
           /// Запрос по Плейлисту 
           $result = $db->query("INSERT INTO ".PREFICS."training_playlist_items (playlist_id, sort, params)
                                SELECT tle2.id as new_play_list_id, tpi.sort, tpi.params
                                FROM ".PREFICS."training_playlist_items AS tpi
                                LEFT JOIN ".PREFICS."training_lesson_elements AS tle ON tpi.playlist_id = tle.id
                                LEFT JOIN ".PREFICS."training_lesson_elements AS tle2 ON tle2.lesson_id = $new_lesson_id  AND tle2.type = 2
                                WHERE tle.lesson_id = $old_lesson_id AND tle.type = 2");
            /// Запрос копирования тестов
            $result = $db->query("INSERT INTO ".PREFICS."training_test (task_id, lesson_id, test_desc, finish, test_try, test_time,
                                show_questions_count, help_hint_success, help_hint_fail, ratings) 
                                SELECT ttask.task_id, $new_lesson_id, tt.test_desc, tt.finish, tt.test_try, tt.test_time, tt.show_questions_count, 
                                tt.help_hint_success, tt.help_hint_fail, tt.ratings
                                FROM ".PREFICS."training_test as tt
                                LEFT JOIN ".PREFICS."training_task AS ttask ON ttask.lesson_id = $new_lesson_id
                                WHERE tt.lesson_id = $old_lesson_id;
                                INSERT INTO ".PREFICS."training_questions (test_id, question, help, true_answer, require_all_true, status, sort, image)
                                SELECT tt2.test_id, tq.question, tq.help, tq.true_answer, tq.require_all_true, tq.status, tq.sort, tq.image
                                FROM  ".PREFICS."training_questions AS tq
                                LEFT JOIN  ".PREFICS."training_test as tt ON tt.test_id = tq.test_id
                                LEFT JOIN  ".PREFICS."training_task AS ttask ON ttask.lesson_id = $new_lesson_id
                                LEFT JOIN  ".PREFICS."training_test as tt2 ON tt2.lesson_id = $new_lesson_id
                                WHERE tt.lesson_id = $old_lesson_id;
                                INSERT INTO ".PREFICS."training_test_options (quest_id, title, value, sort, valid, points, cover)
                                SELECT tq2.quest_id, testop.title, testop.value, testop.sort, testop.valid, testop.points, testop.cover
                                FROM ".PREFICS."training_test_options AS testop
                                LEFT JOIN ".PREFICS."training_questions AS tq ON tq.quest_id = testop.quest_id
                                LEFT JOIN ".PREFICS."training_test as tt ON tt.test_id = tq.test_id
                                LEFT JOIN ".PREFICS."training_test as tt2 ON tt2.lesson_id = $new_lesson_id 
                                LEFT JOIN ".PREFICS."training_questions AS tq2 ON tq2.test_id = tt2.test_id AND tq.sort= tq2.sort
                                WHERE tt.lesson_id = $old_lesson_id;");

            $result = $db->query("SELECT tle.type, tle.lesson_id AS old_id, tle.sort, tle.params
                                FROM ".PREFICS."training_lesson_elements AS tle
                                WHERE tle.lesson_id = $old_lesson_id AND tle.type = 4");
            
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $params = json_decode($row['params']);
                $dir_new_path = ROOT . "/load/training/lessons/{$new_lesson_id}";
                if (!file_exists($dir_new_path)) {
                    mkdir($dir_new_path);
                }
                $file_old = ROOT . "/load/training/lessons/{$old_lesson_id}" . "/" . $params->attach;
                $file_new = $dir_new_path . "/" . $params->attach;
                if (is_file($file_old)){
                    copy($file_old, $file_new);
                } 
            }
            $db->commit();                   
        }

        return $new_lesson_id;
    }


    /**
     * ПОЛУЧИТЬ УНИКАЛЬНЫЙ АЛИАС ДЛЯ УРОКА
     * @param $training_id
     * @param $alias
     * @return string
     */
    public static function getUniqueAlias2Lesson($training_id, $alias) {
        $alias_number = 2;
        $new_alias = "$alias-$alias_number";

        while (self::getCountLessons2TrainingByAlias($training_id, $new_alias)) {
            $new_alias = "$alias-" . ++$alias_number;
        }

        return $new_alias;
    }



    /**
     * СПИСОК УРОКОВ ТРЕНИНГА
     * @param $training_id
     * @param null $section_id
     * @param null $block_id
     * @param int $status
     * @return array|bool
     */
    public static function getLessons($training_id = null, $section_id = null, $block_id = null, $status = 1)
    {
        $db = Db::getConnection();

        $clauses = [];
        if ($training_id) {
            $clauses[] = "training_id = $training_id";
        }

        if ($section_id !== null) {
            $clauses[] = "section_id = $section_id";
        }

        if ($block_id !== null) {
            $clauses[] = "block_id = $block_id";
        }

        if ($status !== null) {
            $clauses[] = "status = $status";
        }

        $where = !empty($clauses) ? 'WHERE '.implode(' AND ' , $clauses) : '';
        $result = $db->query("SELECT * FROM ".PREFICS."training_lessons $where ORDER BY sort ASC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ КОЛ-ВО УРОКОВ В ТРЕНИНГЕ
     * @param $training
     * @return mixed
     */
    public static function getCountLessons2Training($training)
    {
        if ($training['count_lessons_type'] == 1) {
            $db = Db::getConnection();
            $result = $db->query("SELECT COUNT(lesson_id) FROM ".PREFICS."training_lessons WHERE training_id = {$training['training_id']} AND status = 1");
            $count = $result->fetch();

            return $count[0];
        } elseif ($training['count_lessons_type'] == 2) {
            return $training['count_lessons'];
        }

        return 0;
    }


    /**
     * ПОЛУЧИТЬ КОЛ-ВО УРОКОВ В ТРЕНИНГЕ
     * @param $training_id
     * @param $lesson_alias
     * @return mixed
     */
    public static function getCountLessons2TrainingByAlias($training_id, $lesson_alias)
    {
        $db = Db::getConnection();
        $result = $db->prepare("SELECT COUNT(lesson_id) FROM ".PREFICS."training_lessons WHERE training_id = :training_id AND alias = :alias");
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':alias', $lesson_alias, PDO::PARAM_STR);

        $result->execute();
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * СПИСОК УРОКОВ ДЛЯ ТРЕНИНГА
     * @param $training_id
     * @param null $sort
     * @return array|bool
     */
    public static function getLessons2Training($training_id, $sort = null)
    {
        $db = Db::getConnection();
        $query = "SELECT * FROM ".PREFICS."training_lessons WHERE training_id = $training_id";

        if ($sort !== null) {
            $query .= ' ORDER BY sort '.($sort == 1 ? 'DESC' : 'ASC');
        }

        $result = $db->query( $query);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ОБНОВИТЬ СОРТИРОВКУ ДЛЯ УРОКА
     * @param $lesson_id
     * @param $sort
     * @return bool
     */
    public static function updSortLesson($lesson_id, $sort) {
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE '.PREFICS.'training_lessons SET sort = :sort WHERE lesson_id = :lesson_id');

        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ СОРТИРОВКУ ДЛЯ ДОБАВЛЯЕМОГО УРОКА
     * @param $training_id
     * @return int
     */
    public static function getFreeSort($training_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT MAX(sort) FROM ".PREFICS."training_lessons WHERE training_id = $training_id");
        $count = $result->fetch();

        return (int)$count[0] + 1;
    }


    /**
     * ОБНОВИТЬ РАЗДЕЛ ДЛЯ УРОКОВ
     * @param $block_id
     * @param $section_d
     * @return bool
     */
    public static function updSectionLessons($block_id, $section_d) {
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE '.PREFICS.'training_lessons SET section_id = :section_id WHERE block_id = :block_id');

        $result->bindParam(':block_id', $block_id, PDO::PARAM_INT);
        $result->bindParam(':section_id', $section_d, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ЭЛЕМЕНТЫ ДЛЯ УРОКА
     * @param $lesson_id
     * @param null $type
     * @return array|bool
     */
    public static function getElements2Lesson($lesson_id, $type = null) {
        $db = Db::getConnection();
        $where = 'WHERE lesson_id = :lesson_id' . ($type ?  " AND type = $type" : '');

        $result = $db->prepare("SELECT * FROM ".PREFICS."training_lesson_elements $where ORDER BY sort, type ASC");
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->execute();

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $row['params'] = json_decode($row['params'], true);
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО ЭЛЕМЕНТОВ ДЛЯ УРОКА
     * @param $lesson_id
     * @param null $type
     * @return array|bool
     */
    public static function getCountElements2Lesson($lesson_id, $type = null) {
        $db = Db::getConnection();
        $where = 'WHERE lesson_id = :lesson_id' . ($type ?  " AND type = $type" : '');

        $result = $db->prepare("SELECT COUNT(id) FROM ".PREFICS."training_lesson_elements $where");
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->execute();
        $data = $result->fetch();

        return $data[0];
    }


    /**
     * ПОЛУЧИТЬ ЭЛЕМЕНТ УРОКА
     * @param $id
     * @return array|bool
     */
    public static function getElement($id) {
        $db = Db::getConnection();
        $result = $db->prepare("SELECT * FROM ".PREFICS."training_lesson_elements WHERE id = :id");
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            $data['params'] = json_decode($data['params'], true);
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ДОБАВИТЬ ЭЛЕМЕНТ
     * @param $type
     * @param $lesson_id
     * @param $params
     * @return bool
     */
    public static function addElement($type, $lesson_id, $params) {
        $db = Db::getConnection();
        $result = $db->query("SELECT MAX(sort) FROM ".PREFICS."training_lesson_elements WHERE lesson_id = $lesson_id");
        $count = $result->fetch();
        $sort = (int)$count[0] + 1;

        $sql = 'INSERT INTO '.PREFICS."training_lesson_elements (type, lesson_id, sort, params) 
                VALUES (:type, :lesson_id, :sort, :params)";

        $result = $db->prepare($sql);
        $result->bindParam(':type', $type, PDO::PARAM_INT);
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        $result = $result->execute();

        return $result ? $db->lastInsertId() : false;
    }


    /**
     * ОБНОВИТЬ ЭЛЕМЕНТ
     * @param $id
     * @param $type
     * @param $params
     * @return bool
     */
    public static function updElement($id, $params) {
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE '.PREFICS.'training_lesson_elements SET params = :params WHERE id = :id');
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->bindParam(':params', $params, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ОБНОВИТЬ СОРТИРОВКУ ДЛЯ ЭЛЕМЕНТА
     * @param $id
     * @param $sort
     * @return bool
     */
    public static function updSortElement($id, $sort) {
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE '.PREFICS.'training_lesson_elements SET sort = :sort WHERE id = :id');

        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ ЭЛЕМЕНТ УРОКА
     * @param $id
     * @return bool
     */
    public static function delElement($id) {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'training_lesson_elements WHERE id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ЭЛЕМЕНТЫ ПЛЭЙЛИСТА
     * @param $playlist_id
     * @return bool|mixed
     */
    public static function getPlaylistItems($playlist_id) {
        $db = Db::getConnection();
        $result = $db->prepare("SELECT * FROM ".PREFICS."training_playlist_items WHERE playlist_id = :playlist_id ORDER BY sort ASC");
        $result->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);
        $result->execute();

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $row['params'] = json_decode($row['params'], true);
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ЭЛЕМЕНТ ПЛЭЙЛИСТА
     * @param $id
     * @return bool|mixed
     */
    public static function getPlaylistItem($id) {
        $db = Db::getConnection();
        $result = $db->prepare("SELECT * FROM ".PREFICS."training_playlist_items WHERE id = :id");
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->execute();

        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $data['params'] = json_decode($data['params'], true);
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ДОБАВИТЬ ЭЛЕМЕНТ ПЛЭЙЛИСТА
     * @param $playlist_id
     * @param $params
     * @return bool
     */
    public static function addPlaylistItem($playlist_id, $params) {
        $db = Db::getConnection();
        $result = $db->query("SELECT MAX(sort) FROM ".PREFICS."training_playlist_items WHERE playlist_id = $playlist_id");
        $count = $result->fetch();
        $sort =  (int)$count[0] + 1;

        $result = $db->prepare('INSERT INTO '.PREFICS.'training_playlist_items (playlist_id, sort, params) VALUES (:playlist_id, :sort, :params)');
        $result->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':params', $params, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ОБНОВИТЬ ЭЛЕМЕНТ ПЛЭЙЛИСТА
     * @param $id
     * @param $params
     * @return bool
     */
    public static function updPlaylistItem($id, $params) {
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE '.PREFICS.'training_playlist_items SET params = :params WHERE id = :id');
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->bindParam(':params', $params, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ОБНОВИТЬ СОРТИРОВКУ У ЭЛЕМЕНТА ПЛЭЙЛИСТА
     * @param $id
     * @param $sort
     * @return bool
     */
    public static function updSortPlaylistItem($id, $sort) {
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE '.PREFICS.'training_playlist_items SET sort = :sort WHERE id = :id');

        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ ЭЛЕМЕНТ ПЛЭЙЛИСТА
     * @param $id
     * @return bool
     */
    public static function delPlaylistItem($id) {
        $db = Db::getConnection();
        $result = $db->prepare('DELETE FROM '.PREFICS.'training_playlist_items WHERE id = :id');
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ ЭЛЕМЕНТЫ ПЛЭЙЛИСТА
     * @param $playlist_id
     * @return bool
     */
    public static function delPlaylistItems($playlist_id) {
        $db = Db::getConnection();
        $result = $db->prepare('DELETE FROM '.PREFICS.'training_playlist_items WHERE playlist_id = :playlist_id');
        $result->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ДОБАВИТЬ ВЛОЖЕНИЕ ДЛЯ УРОКА
     * @param $lesson_id
     * @param $files
     * @return string
     */
    public static function AddLessonAttach($lesson_id, $files) {
        $attach_name = System::getSecureString($files["attach"]["name"]);
        $dir_path = ROOT . "/load/training/lessons/$lesson_id";
        if (!file_exists($dir_path)) {
            mkdir($dir_path);
        }

        $path = "$dir_path/$attach_name";
        if (is_uploaded_file($files["attach"]["tmp_name"])) {
            move_uploaded_file($files["attach"]["tmp_name"], $path);
        }

        return $attach_name;
    }


    /**
     * УДАЛИТЬ ВЛОЖЕНИЕ У УРОКА
     * @param $lesson_id
     * @param $attach_name
     */
    public static function delLessonAttach($lesson_id, $attach_name) {
        if ($attach_name) {
            $path = ROOT . "/load/training/lessons/$lesson_id/$attach_name";
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }


    /**
     * ПОЛУЧИТЬ НАЗВАНИЕ ЭЛЕМЕНТА
     * @param $type
     * @return string
     */
    public static function getElementName($type) {
        $element_name = '';

        switch ($type) {
            case $type == self::ELEMENT_TYPE_MEDIA:
                $element_name = 'media';
                break;
            case $type == self::ELEMENT_TYPE_PLAYLIST:
                $element_name = 'playlist';
                break;
            case $type == self::ELEMENT_TYPE_TEXT:
                $element_name = 'text';
                break;
            case $type == self::ELEMENT_TYPE_ATTACH:
                $element_name = 'attach';
                break;
            case $type == self::ELEMENT_TYPE_HTML:
                $element_name = 'html';
                break;
            case $type == self::ELEMENT_TYPE_POLL:
                $element_name = 'poll';
                break;
            case $type == self::ELEMENT_TYPE_FORUM:
                $element_name = 'forum';
                break;
        }

        return $element_name;
    }


    /*=================  ПРОВЕРКА ЗАДАНИЯ | ОТВЕТЫ НА ВОПРОСЫ  =================*/


    /**
     * ПОЛУЧИТЬ ЗАДАНИЕ К УРОКУ
     * @param $lesson_id
     * @return bool|mixed
     */
    public static function getTask2Lesson($lesson_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."training_task WHERE lesson_id = $lesson_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }



    /**
     * ДОБАВИТЬ ОПЦИЮ К УРОКУ
     * @param $task_id
     * @param $title
     * @param $value
     * @param $valid
     * @param $sort
     * @param $points
     * @return bool
     */
    public static function addOption($task_id, $title, $value, $valid, $sort, $points)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'training_test_options (task_id, title, value, sort, valid, points ) 
                VALUES (:task_id, :title, :value, :sort, :valid, :points)';

        $result = $db->prepare($sql);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':value', $value, PDO::PARAM_STR);
        $result->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':valid', $valid, PDO::PARAM_INT);
        $result->bindParam(':points', $points, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ИЗМЕНИТЬ ОПЦИЮ К ТЕСТУ
     * @param $option_id
     * @param $title
     * @param $value
     * @param $valid
     * @param $points
     * @param $sort
     * @return bool
     */
    public static function editOptionByTest($option_id, $title, $value, $valid, $points, $sort)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'training_test_options SET title = :title, value = :value, sort = :sort, valid = :valid, points = :points WHERE option_id  = '.$option_id;
        $result = $db->prepare($sql);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':value', $value, PDO::PARAM_STR);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':valid', $valid, PDO::PARAM_INT);
        $result->bindParam(':points', $points, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ ОПЦИЮ У ТЕСТА
     * @param $option_id
     * @return bool
     */
    public static function DelOptionTest($option_id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'training_test_options WHERE option_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $option_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ОПЦИИ ТЕСТА
     * @param $option_id
     * @return bool|mixed
     */
    public static function getOptionByTest($option_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."training_test_options WHERE option_id = $option_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (!empty($data)) return $data;
        else return false;
    }


    /**
     * ЗАПИСАТЬ ТЕСТ В БД
     * @param $lesson_id
     * @param $user_id
     * @param $test_result
     * @param $points
     * @param $status
     * @return bool
     */
    public static function writeTestResultInDB($lesson_id, $user_id, $test_result, $points, $status)
    {
        $create_date = time();
        $db = Db::getConnection();

        $sql = 'INSERT INTO '.PREFICS.'training_test_results (lesson_id, user_id, test_result, test_points, status, create_date ) 
                VALUES (:lesson_id, :user_id, :test_result, :points, :status, :create_date)';

        $result = $db->prepare($sql);
        $result->bindParam(':test_result', $test_result, PDO::PARAM_STR);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':points', $points, PDO::PARAM_INT);
        $result->bindParam(':create_date', $create_date, PDO::PARAM_INT);

        return $result->execute();
    }



    /**
     * СОХРАНИТЬ ЗАДАНИЕ К УРОКУ
     * @param $lesson_id
     * @param $data
     * @return bool
     */
    public static function addTask($lesson_id)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'training_task (lesson_id)
                VALUES (:lesson_id)';

        $result = $db->prepare($sql);
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);

        return $result = $result->execute();
    }


    /**
     * ИЗМЕНИТЬ ЗАДАНИЕ К УРОКУ
     * @param $lesson_id
     * @param $data
     * @return bool
     */
    public static function updateTask($lesson_id, $data)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS."training_task SET task_type = :task_type, check_type = :check_type, text = :text,
                autocheck_time = :autocheck_time, show_upload_file = :show_upload_file, show_work_link = :show_work_link,
                hint = :hint, completed_on_time = :completed_on_time, completed_time_add_group = :completed_time_add_group,
                completed_time_del_group = :completed_time_del_group, not_completed_on_time = :not_completed_on_time,
                stop_lesson = :stop_lesson, auto_answer = :auto_answer, access_type = :access_type, access_time = :access_time,
                access_time_days = :access_time_days, access_time_weekday = :access_time_weekday WHERE lesson_id = $lesson_id";

        $result = $db->prepare($sql);
        $result->bindParam(':task_type', $data['task_type'], PDO::PARAM_INT);
        $result->bindParam(':check_type', $data['check_type'], PDO::PARAM_INT);
        $result->bindParam(':text', $data['text'], PDO::PARAM_STR);
        $result->bindParam(':autocheck_time', $data['autocheck_time'], PDO::PARAM_INT);
        $result->bindParam(':show_upload_file', $data['show_upload_file'], PDO::PARAM_INT);
        $result->bindParam(':show_work_link', $data['show_work_link'], PDO::PARAM_INT);
        $result->bindParam(':hint', $data['hint'], PDO::PARAM_STR);
        $result->bindParam(':auto_answer', $data['auto_answer'], PDO::PARAM_STR);
        $result->bindParam(':completed_on_time', $data['completed_on_time'], PDO::PARAM_INT);
        $result->bindParam(':completed_time_add_group', $data['completed_time_add_group'], PDO::PARAM_STR);
        $result->bindParam(':completed_time_del_group', $data['completed_time_del_group'], PDO::PARAM_STR);
        $result->bindParam(':not_completed_on_time', $data['not_completed_on_time'], PDO::PARAM_INT);
        $result->bindParam(':stop_lesson', $data['stop_lesson'], PDO::PARAM_INT);
        $result->bindParam(':access_type', $data['access_type'], PDO::PARAM_INT);
        $result->bindParam(':access_time', $data['access_time'], PDO::PARAM_INT);
        $result->bindParam(':access_time_days', $data['access_time_days'], PDO::PARAM_INT);
        $result->bindParam(':access_time_weekday', $data['access_time_weekday'], PDO::PARAM_INT);

        return $result->execute();
    }
    


    /**
     * ПОЛУЧИТЬ ТЕСТ К УРОКУ
     * @param $lesson_id
     * @return bool|mixed
     */
    public static function getTest2Lesson($lesson_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."training_test WHERE lesson_id = $lesson_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СТАТУС О ПРОХОЖДЕНИИ УРОКА
     * @param $lesson_id
     * @param $user_id
     * @return bool
     */
    public static function getLessonCompleteStatus($lesson_id, $user_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT status FROM ".PREFICS."training_user_map WHERE lesson_id = $lesson_id AND user_id = $user_id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? (int)$data['status'] : false;
    }

     /**
     * ПОЛУЧИТЬ СТАТУС О ПРОХОЖДЕНИИ УРОКА ИЗ HOME-WORK
     * @param $lesson_id
     * @param $user_id
     * @return bool
     */
    public static function getHomeworkStatus($lesson_id, $user_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT status FROM ".PREFICS."training_home_work WHERE lesson_id = $lesson_id AND user_id = $user_id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? (int)$data['status'] : false;
    }

    /**
     * ПОЛУЧИТЬ СТАТУС СТОП-УРОКА 
     * @param $lesson_id
     * @return bool
     */
    public static function isLessonStopStatus($lesson_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT stop_lesson FROM ".PREFICS."training_task WHERE lesson_id = $lesson_id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['stop_lesson'] : false;
    }

    /**
     * ПОЛУЧИТЬ ПРЕДИДУЩИЕ СТОП-УРОКИ ТРЕНИНГА
     * @param $current_sort_lesson
     * @param $training_id
     * @return bool
     */
    public static function isLessonLastStopStatus($current_sort_lesson, $training_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT t1.lesson_id, t1.stop_lesson, t2.sort, t2.name, t2.access_type, t2.access_groups, t2.access_planes FROM ".PREFICS."training_task 
                            as t1 left join ".PREFICS."training_lessons as t2 ON t1.lesson_id = t2.lesson_id 
                            where t2.training_id = $training_id and t1.stop_lesson = 1 and t2.sort < $current_sort_lesson
                            ORDER BY t2.SORT DESC LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ О ПРОХОЖДЕНИИ УРОКА
     * @param $lesson_id
     * @param $user_id
     * @param $status
     * @return bool
     */
    public static function getLessonCompleteData($lesson_id, $user_id, $status = null) {
        $db = Db::getConnection();
        $query = "SELECT * FROM ".PREFICS."training_user_map WHERE lesson_id = :lesson_id AND user_id = :user_id";
        $query .= $status !== null ? ' AND status = :status' : '';
        $result = $db->prepare($query);

        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        if ($status !== null) {
            $result->bindParam(':status', $status, PDO::PARAM_INT);
        }

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ О ПРОХОЖДЕНИЯХ УРОКОВ ПО СТАТУСУ
     * @param $status
     * @return bool
     */
    public static function getLessonsCompleteDataByStatus($status) {
        $db = Db::getConnection();
        $result = $db->prepare("SELECT * FROM ".PREFICS."training_user_map WHERE status = :status ORDER BY id ASC");
        $result->bindParam(':status', $status, PDO::PARAM_INT);

        $result->execute();
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ КОЛ-ВО ПРОЙДЕННЫХ УРОКОВ
     * @param $user_id
     * @param $training_id
     * @return bool
     */
    public static function getCountLessonsCompleted($user_id, $training_id)
    {
        $db = Db::getConnection();
        $query = "SELECT COUNT(id) FROM ".PREFICS."training_user_map WHERE user_id = $user_id 
                  AND status = ".self::HOMEWORK_ACCEPTED." AND training_id = $training_id";
        $result = $db->query($query);
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * ВОШЕЛ ЛИ В УРОКИ ПОЛЬЗОВАТЕЛЬ
     * @param $lessons
     * @param $user_id
     * @return bool
     */
    public static function isEnterInLessons ($lessons, $user_id) {
        $db = Db::getConnection();
        $query = "SELECT COUNT(id) FROM ".PREFICS."training_user_map WHERE user_id = $user_id AND lesson_id IN (".implode(',', $lessons).')';
        $result = $db->query($query);
        $count = $result->fetch();

        return $count[0] == count($lessons) ? true : false;
    }


       /**
     * ВОШЕЛ ЛИ ПОЛЬЗОВАТЕЛЬ В ПРЕДЫДУЩИЙ УРОК ТРЕНИНГА
     * возвращает дату и время входа
     * @param $training_id
     * @param $user_id
     * @param $sort
     * @return bool
     */
    public static function isEnterInRelativelyLesson ($training_id, $user_id, $sort) {
        $db = Db::getConnection();
        $query = "SELECT open FROM ".PREFICS."training_user_map where lesson_id =(
            select lesson_id FROM ".PREFICS."training_lessons where training_id = $training_id and sort < $sort order by sort DESC limit 1) and 
            user_id = $user_id";
        $result = $db->query($query);
        $time_open_lesson = $result->fetch(PDO::FETCH_ASSOC);

        return $time_open_lesson['open'] ? $time_open_lesson['open'] : false;
    }


    /**
     * ВОШЕЛ ЛИ ПОЛЬЗОВАТЕЛЬ В ПЕРВЫЙ УРОК ТРЕНИНГА
     * возвращает дату и время входа
     * @param $training_id
     * @param $user_id
     * @return bool
     */
    public static function isEnterInFirstLesson ($training_id, $user_id) {
        $db = Db::getConnection();
        $query = "SELECT open FROM ".PREFICS."training_user_map where lesson_id =(
            select lesson_id FROM ".PREFICS."training_lessons where training_id = $training_id order by sort ASC limit 1) and 
            user_id = $user_id";
        $result = $db->query($query);
        $time_open_lesson = $result->fetch(PDO::FETCH_ASSOC);

        return $time_open_lesson['open'] ? $time_open_lesson['open'] : false;
    }

    /**
     * ОТВЕТИЛ ЛИ В УРОКАХ ПОЛЬЗОВАТЕЛЬ (ОТПРАВИЛ РАБОТУ)
     * @param $lessons
     * @param $user_id
     * @return bool
     */
    public static function isAnswerInLessons ($lessons, $user_id) {
        $db = Db::getConnection();
        $query = "SELECT COUNT(id) FROM ".PREFICS."training_user_map WHERE user_id = $user_id 
                  AND lesson_id IN (".implode(',', $lessons).")
                  AND status = ".self::HOMEWORK_SUBMITTED;
        $result = $db->query($query);
        $count = $result->fetch();

        return $count[0] == count($lessons) ? true : false;
    }


    /**
     * ПРОШЕЛ ЛИ УРОКИ ПОЛЬЗОВАТЕЛЬ
     * @param $lessons
     * @param $user_id
     * @return bool
     */
    public static function isLessonsCompleted ($lessons, $user_id) {
        $db = Db::getConnection();
        $query = "SELECT COUNT(id) FROM ".PREFICS."training_user_map WHERE user_id = $user_id 
                  AND lesson_id IN (".implode(',', $lessons).")
                  AND status = ".self::HOMEWORK_ACCEPTED;
        $result = $db->query($query);
        $count = $result->fetch();

        return $count[0] == count($lessons) ? true : false;
    }


    /**
     * ОБНОВИТЬ СТАТУС ПРОХОЖДЕНИЯ УРОКА
     * @param $lesson_id
     * @param $user_id
     * @param $status: 0 - начат урок, 1 - отправлено дз, 2 - не сдано, 3 - сдано, 4 - автопроверка
     * @param null $date
     * @return bool
     */
    public static function updLessonCompleteStatus($lesson_id, $user_id, $status, $date = null) {
        $time = $date ?: time();
        $db = Db::getConnection();
        $query = 'UPDATE '.PREFICS.'training_user_map SET status = :status, date = :date 
                  WHERE lesson_id = :lesson_id AND user_id = :user_id';
        $result = $db->prepare($query);

        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':date', $time, PDO::PARAM_INT);
        
        return $result->execute();
    }


     /**
     * ОБНОВИТЬ ДАННЫЕ В HOMEWORK
     * @param $homework_id
     * @param $user_id
     * @param $status
     * @param null $test
     * @param null $curator_id
     * @return bool
     */
    public static function updHomeworkData($homework_id, $user_id, $status, $test = null, $curator_id = null) {
        $db = Db::getConnection();
        $clauses = [];
        
        if ($status !== null) {
            $clauses[] = 'status = :status';
        }
        if ($test !== null) {
            $clauses[] = 'test = :test';
        }
        if ($curator_id !== null) {
            $clauses[] = 'curator_id = :curator_id';
        }
        
        $query = 'UPDATE '.PREFICS.'training_home_work SET ' . implode(', ' , $clauses);
        $where = 'WHERE homework_id = :homework_id AND user_id = :user_id';
        $result = $db->prepare("$query $where");
    
        if ($status !== null) {
            $result->bindParam(':status', $status, PDO::PARAM_INT);
        }
        if ($test !== null) {
            $result->bindParam(':test', $test, PDO::PARAM_INT);
        }
        if ($curator_id !== null) {
            $result->bindParam(':curator_id', $curator_id, PDO::PARAM_INT);
        }
        $result->bindParam(':homework_id', $homework_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ДАТУ ОТКРЫТИЯ УРОКА ПО РАСПИСАНИЮ (shedule)
     * lesson[shedule_how_fast_open] - Как быстро открыть доступ 1 - на след. день, 2 - через Х дней, 3 - в день недели
     * lesson[shedule_relatively] - Относительно чего рассчитывать задержку открытия  1 - вход в предыдущий урок, 2 - вход в первый урок, 3 - в день начала
     * @param $training
     * @param $lesson
     * @param $user_id
     * @param $user_groups
     * @param $user_planes
     * @return array|bool|int|null
     * @throws Exception
     */
    public static function getLessonOpenDate($training, $lesson, $user_id, $user_groups, $user_planes) {
        self::$open_lesson_date = $lesson_success_date = $date = $status = null;

        if ($lesson['shedule_type'] == 2) { // конкретная дата открытия
            $start_date = $lesson['shedule_open_date'];
            if ($start_date > time()) {
                self::$open_lesson_date = $start_date;
                $status = self::STATUS_LESSON_NOT_YET;
            } else {
                return true;
            }
        } else {
            if ($lesson['shedule_relatively'] == 2) { // тут дата входа в первый урок тренинга
                $lesson_success_date = self::isEnterInFirstLesson($training['training_id'], $user_id);
            } elseif ($lesson['shedule_relatively'] == 1) { // тут дата входа в предыдущий урок тренинга
                $lesson_success_date = self::isEnterInRelativelyLesson($training['training_id'], $user_id, $lesson['sort']);
            } elseif ($lesson['shedule_relatively'] == 3) { // тут дата старта тренинга 
                $lesson_success_date = $training['start_date'];
            } elseif ($lesson['shedule_relatively'] == 4) { // Дата назначения группы или подписки (ТРЕНИНГА!)
                $lesson_success_date = Training::getAccessDate2Training($training, $user_id, $user_groups, $user_planes);
            }

            if ($lesson['shedule_how_fast_open'] == 3) { // конкретный день недели
                if ($lesson_success_date) {
                    //Номер недели открытия прошлого урока
                    $date_lesson_last_open = new DateTime(date("d.m.Y", $lesson_success_date));
                    $number_week_lesson_last_open = date("W", $lesson_success_date);
                    $number_current_week = date("W", time());
                    $day_of_week_for_open = $lesson['shedule_access_time_weekday']; // тут смотрим в какой день недели должно быть открыт следующий урок (1-ПН,2-ВТ,3-СР и т.д.)
                    $current_of_week_day = date('N', time()); // Порядковый номер дня недели в соответствии со стандартом ISO-8601 от 1 (понедельник) до 7 (воскресенье)

                    $date_open_lesson = self::getDataNextWeekDay($day_of_week_for_open, $date_lesson_last_open);
                    if (time() > date_timestamp_get($date_open_lesson)) {
                        return true;
                    }

                    if ($number_current_week > $number_week_lesson_last_open) { // TODO здесь нужно учесть переход года и допилить проверку когда неделя одного года и другого
                        return true;
                    } else {
                        if ($current_of_week_day > $day_of_week_for_open) { // здесь текущая неделя
                            //тут должно открытся на следующей неделе
                            $start_date = self::getDataNextWeekOnWeekDay($day_of_week_for_open);
                            if (date_timestamp_get($start_date) > time()) {
                                self::$open_lesson_date = date_timestamp_get($start_date);
                                $status = self::STATUS_LESSON_NOT_YET;
                            } else {
                                return true;
                            }
                        } else {
                            // а тут должно открытся на этой неделе
                            $start_date = self::getDataCurrentWeekOnWeekDay($day_of_week_for_open);
                            if (date_timestamp_get($start_date) > time()) {
                                self::$open_lesson_date = date_timestamp_get($start_date);
                                $status = self::STATUS_LESSON_NOT_YET;
                            } else {
                                return true;
                            }
                        }
                    }
                } else { // здесь у нас нет даты предыдущего урока и мы пишем в плашке, что дата появится позже после того как пользователь войдет в первый или предыдущий урок в зависимости от настройки...
                    $status = 8; 
                    $start_date = false;
                }
            } elseif ($lesson['shedule_how_fast_open'] == 2) { // через Х дней ---
                if ($lesson_success_date) {
                    $start_date = $lesson_success_date + (86400 * $lesson['shedule_count_days']);
                    if ($start_date > time()) {
                        self::$open_lesson_date = $start_date;
                        $status = self::STATUS_LESSON_NOT_YET;
                    } else {
                        return true;
                    }
                } else {
                    $status = 8; 
                    $start_date = false;
                }
            } elseif ($lesson['shedule_how_fast_open'] == 1) { // на следующий дней
                if ($lesson_success_date) {
                    $start_date = strtotime(date('d.m.Y 00:00:00', $lesson_success_date + 86400));
                    if ($start_date > time()) {
                        $status = self::STATUS_LESSON_NOT_YET;
                        self::$open_lesson_date = $start_date;
                    } else {
                        return true;
                    }
                } else {
                    $status = 8; 
                    $start_date = false;
                }    
            }
        }

        return isset($start_date) ? compact('status', 'start_date') : $status;
    }


    /**
     * ПОЛУЧИТЬ ДАТУ ОТКРЫТИЯ УРОКА КОТОРЫЙ ИДЕТ ПОСЛЕ СТОПОВОГО
     * lesson_id это СТОПОВЫЙ УРОК!
     * task[access_time] - Как быстро открыть доступ 1 - сразу, 2- на след. день, 3 - через Х дней, 4 - в день недели
     * @param $training
     * @param $lesson_id
     * @param $user_id
     * @return array|bool|int
     * @throws Exception
     */
    public static function getLessonStopOpenDate($training, $lesson_id, $user_id) {
        $date = $start_date = $status = null;
        $lesson_data = self::getLessonCompleteData($lesson_id, $user_id);
        $lesson_success_date = $lesson_data['date']; // тут дата/время прохождения урока(проверки куратором)
        $task_access_time = self::getTask2Lesson($lesson_id);

        if ($task_access_time['access_time'] == 4) { // в конкретный день недели
            $more_week = (time()-$lesson_success_date)/86400; //сначала смотрим разницу не прошло ли больше 1 недели после проверки
            if ($more_week > 7) { // если прошло больше недели с момента проверки, то октрываем 100%
                return true;
            }

            $number_week_lesson_success = date("W", $lesson_success_date);
            $number_current_week = date("W", time());
            $day_of_week_for_open = $task_access_time['access_time_weekday']; // тут смотрим в какой день недели должно быть открыт следующий урок (1-ПН,2-ВТ,3-СР и т.д.)
            $current_of_week_day = date('N', time()); // Порядковый номер дня недели в соответствии со стандартом ISO-8601 от 1 (понедельник) до 7 (воскресенье)

            if ($number_current_week > $number_week_lesson_success) { // TODO здесь нужно учесть переход года и допилить проверку когда неделя одного года и другого
                if ($day_of_week_for_open < $current_of_week_day) {
                    return true;
                } else {
                    $start_date = self::getDataCurrentWeekOnWeekDay($day_of_week_for_open);
                    if (date_timestamp_get($start_date) > time()) {
                        self::$open_lesson_date = date_timestamp_get($start_date);
                        $status = self::STATUS_LESSON_NOT_YET;
                    } else {
                        return true;
                    }
                }       
            } else {            
                if ($current_of_week_day > $day_of_week_for_open) {
                    $start_date = self::getDataNextWeekOnWeekDay($day_of_week_for_open); // тут должно открытся на следующей неделе
                    if (date_timestamp_get($start_date) > time()) {
                        self::$open_lesson_date = date_timestamp_get($start_date);
                        $status = self::STATUS_LESSON_NOT_YET;
                    } else {
                        return true;
                    }
                } else {
                    $start_date = self::getDataCurrentWeekOnWeekDay($day_of_week_for_open); // тут должно открытся на этой неделе
                    if (date_timestamp_get($start_date) > time()) {
                        self::$open_lesson_date = date_timestamp_get($start_date);
                        $status = self::STATUS_LESSON_NOT_YET;
                    } else {
                        return true;
                    }
                }
            }
        } elseif ($task_access_time['access_time']==3) { // через Х дней
            $start_date = $lesson_success_date + (86400 * $task_access_time['access_time_days']);
            if ($start_date > time()) {
                self::$open_lesson_date = $start_date;
                $status = self::STATUS_LESSON_NOT_YET;
            } else {
                return true;
            }
        } elseif ($task_access_time['access_time'] == 2) { // на следующий дней
            $start_date = strtotime(date('d.m.Y 00:00:00', $lesson_success_date + 86400));
            if ($start_date > time()) {
                self::$open_lesson_date = $start_date;
                $status = self::STATUS_LESSON_NOT_YET;
            } else {
                return true;
            }
        } elseif ($task_access_time['access_time']==1) { // сразу
            return true;
        }
        
        return isset($start_date) ? compact('status', 'start_date') : $status;
    }


    /**
     * ПОЛУЧИТЬ СЛЕДУЮЩУЮ ДАТУ НУЖНОГО ДНЯ НЕДЕЛИ
     * то есть от например от 15 марта 2021(понедельник), нужно получить когда будет ближайшая суббота
     * в $weekday передаем 6(суббота) и $dateday передает дату 15 марта в формате DateTime
     * @param $week_day
     * @param DateTime $date_day
     * @return DateTime
     */
    public static function getDataNextWeekDay($week_day, DateTime $date_day) {
        switch ($week_day) {
            case 1:
                return $date_day->modify('next monday');
            case 2:
                return $date_day->modify('next tuesday');
            case 3:
                return $date_day->modify('next wednesday');
            case 4:
                return $date_day->modify('next thursday');
            case 5:
                return $date_day->modify('next friday');
            case 6:
                return $date_day->modify('next saturday');
            case 7:              
                return $date_day->modify('next sunday');
        }
    }


    /**
     * ПОЛУЧИТЬ ДАТУ НУЖНОГО ДНЯ НЕДЕЛИ НА СЛЕДУЮЩЕЙ НЕДЕЛЕ
     * @param $weekday
     * @return DateTime
     * @throws Exception
     */
    public static function getDataNextWeekOnWeekDay($weekday) {
        switch ($weekday) {
            case 1:
                return new DateTime('monday next week');
            case 2:
                return new DateTime('tuesday next week');
            case 3:
                return new DateTime('wednesday next week');
            case 4:
                return new DateTime('thursday next week');
            case 5:
                return new DateTime('friday next week');
            case 6:
                return new DateTime('saturday next week');
            case 7:              
                return new DateTime('sunday next week');
        }
    }


    /**
     * ПОЛУЧИТЬ ДАТУ НУЖНОГО ДНЯ НЕДЕЛИ НА ТЕКУЩЕЙ НЕДЕЛЕ
     * @param $weekday
     * @return DateTime
     * @throws Exception
     */
    public static function getDataCurrentWeekOnWeekDay($weekday) {
        switch ($weekday) {
            case 1:
                return new DateTime('monday this week');
            case 2:
                return new DateTime('tuesday this week');
            case 3:
                return new DateTime('wednesday this week');
            case 4:
                return new DateTime('thursday this week');
            case 5:
                return new DateTime('friday this week');
            case 6:
                return new DateTime('saturday this week');
            case 7:              
                return new DateTime('sunday this week');
        }
    }


    /**
     * ПОЛУЧИТЬ СТАТУС ПРОЙДЕН ИЛИ НЕ ПРОЙДЕН УРОК
     * @param $lesson_id
     * @param $user_id
     * @return bool
     */
    public static function isLessonComplete($lesson_id, $user_id) {
        return self::getLessonCompleteStatus($lesson_id, $user_id) == 3 ? true : false;
    }


    /**
     * ПОЛУЧИТЬ СТАТУС УРОКА
     * @param $user_groups
     * @param $user_planes
     * @param $training
     * @param $section
     * @param $lesson
     * @param $user_id
     * @param $access_last_homework
     * @return int
     * @throws Exception
     */
    public static function getLessonStatus($user_groups, $user_planes, $training, $section, $lesson, $user_id, $access_last_homework) {
        $access = Training::getAccessData($user_groups, $user_planes, $training, $section, $lesson);
        if (!Training::checkUserAccess($access) && $access['status'] !== Training::NO_ACCESS_TO_DATE && $access['status'] !== self::STATUS_LESSON_NO_DATE_FOR_CALCULATION) {
            return self::STATUS_LESSON_NOT_ACCESS;
        } elseif ($access['status'] === Training::NO_ACCESS_TO_DATE) {
            return self::STATUS_LESSON_NOT_YET;
        } elseif ($access['status'] === self::STATUS_LESSON_NO_DATE_FOR_CALCULATION) {
            return self::STATUS_LESSON_NO_DATE_FOR_CALCULATION;
        } elseif ($user_id && TrainingLesson::isLessonComplete($lesson['lesson_id'], $user_id)) {
            return self::STATUS_LESSON_COMPLETE;
        } elseif ($access['status'] == true && (isset($access['is_curator']) || isset($access['is_admin']))) {
            return self::STATUS_LESSON_OPEN;
        } elseif (isset($access_last_homework)) {
            if (is_array($access_last_homework)) {
                return self::STATUS_LESSON_NOT_YET;
            } elseif (!$access_last_homework) {
                return self::STATUS_LESSON_HOMEWORK_NOT_COMPLETED;
            }
        }

        return self::STATUS_LESSON_OPEN;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ СТУТУСА УРОКА
     * @param $status
     * @param $training
     * @param $lesson
     * @return array
     */
    public static function getLessonStatusData($status, $training, $lesson) {
        $html = $class = $link = '';

        switch ($status) {
            case self::STATUS_LESSON_COMPLETE: // урок пройден
                $html = System::Lang('LESSON_SUCCESFULLY_COMPLETED');
                $class = 'lesson-title-green-check';
                $link = "/training/view/{$training['alias']}/lesson/{$lesson['alias']}";
                break;
            case self::STATUS_LESSON_OPEN: // урок открыт
                $html = System::Lang('ACCESS_TO_LESSON_IS_OPEN');
                $class = 'lesson-title-yellow-circle';
                $link = "/training/view/{$training['alias']}/lesson/{$lesson['alias']}";
                break;
            case self::STATUS_LESSON_NOT_YET: // урок недоступен (не подошла дата открытия)
                $html = '<div class="accessibility-warning">'.System::Lang('STATUS_LESSON_NOT_YET').'</div>';
                $class = 'lesson-title-lock';
                break;
            case self::STATUS_LESSON_NOT_ACCESS: // нет доступа к уроку
                $html = System::Lang('ACCESS_TO_LESSON_IS_CLOSED_GET');
                $class = 'lesson-title-lock modal-access';
                $link = "#ModalAccess";
                break;
            case self::STATUS_LESSON_HOMEWORK_NOT_COMPLETED:
                $html = System::Lang('ACCESS_TO_LESSON_IS_CLOSED_GO_HOMETASK');
                $class = 'lesson-title-lock modal-access';
                break;
            case self::STATUS_LESSON_NO_DATE_FOR_CALCULATION:
                $html = System::Lang('ACCESS_TO_LESSON_IS_CLOSED_NEED_PREV_LESS');
                $class = 'lesson-title-lock';
                break;
        }
        
        $replace = [
            '[OPEN_DATE]' => System::dateSpeller(self::$open_lesson_date),
        ];

        $html = strtr($html, $replace);

        return compact('html', 'class', 'link');
    }


    /**
     * АВТОПРОВЕРКА ДЗ
     */
    public static function autoConfirmTask() {
        $db = Db::getConnection();
        $data = self::getLessonsCompleteDataByStatus(self::HOMEWORK_AUTOCHECK);

        if ($data && is_array($data)) {
            foreach ($data as $item) {
                $task = self::getTask2Lesson($item['lesson_id']);

                if ($item['open'] < time() - $task['autocheck_time'] * 60) {
                    //TODO здесь нужно сделать проверку, если задание только ТЕСТ и оно не выполенно и урок СТОПовый, тогда
                    // статус в юзермап нужно записать 1 и в хом_ворк 4, что бы Куратор или Админ мог дать попытку или просто принять урок как есть
                    self::updLessonCompleteStatus($item['lesson_id'], $item['user_id'], self::HOMEWORK_ACCEPTED);
                }
            }
        }
    }

    /**
     * ПОЛУЧИТЬ ID HOMEWORK
     * @param null $task_id
     * @param null $lesson_id
     * @param null $user_id
     * @return array|bool
     */
    public static function getHomeWorkId($task_id = null, $lesson_id = null, $user_id = null)
    {
        $db = Db::getConnection();
        $sql = "SELECT homework_id FROM ".PREFICS."training_home_work WHERE 
        task_id = $task_id AND lesson_id = $lesson_id AND user_id = $user_id LIMIT 1";

        $result = $db->query($sql);

        $data = $result->fetch(PDO::FETCH_ASSOC); 
         
        return !empty($data) ? $data['homework_id'] : null;
    }


    /**
     * ПОЛУЧИТЬ ПОСЛЕДНИЙ ОТВЕТ ДЛЯ УРОКА
     * здесь по новой функциональности возвращаем последнюю версию ответа
     * TODO переименовать функцию, но пока есть похожая функция из старой функциональности
     * @param null $lesson_id
     * @param null $user_id
     * @param $homework_id
     * @return array|bool
     */
    public static function getAnswers2Lesson($lesson_id = null, $user_id = null, $homework_id)
    {
        $db = Db::getConnection();
        $sql = "SELECT thwh.history_id, thwh.version_id, t.name AS training_name, t.alias AS training_alias, ta.*, thwh.answer AS answer, 
                thwh.attach AS attach, thwh.create_date AS date_user_send, thwh.work_link AS work_link, u.user_name, u.email AS user_email, 
                u.sex, tl.name AS lesson_name, tl.alias AS lesson_alias
                FROM ".PREFICS."training_home_work AS ta
                LEFT JOIN ".PREFICS."users AS u ON ta.user_id = u.user_id
                LEFT JOIN ".PREFICS."training_lessons AS tl ON ta.lesson_id = tl.lesson_id
                LEFT JOIN ".PREFICS."training AS t ON t.training_id = tl.training_id
                LEFT JOIN ".PREFICS."training_home_work_history AS thwh ON thwh.homework_id = ta.homework_id";

        $clauses = [];

        if ($lesson_id) {
            $clauses[] = "ta.lesson_id = $lesson_id";
        }

        if ($user_id !== null) {
            $clauses[] = "ta.user_id = $user_id";
        }
        
        if ($homework_id !== null) {
            $clauses[] = "ta.homework_id = $homework_id";
        }

        $clauses[] = "thwh.version_id is not null";

        $sql .= (!empty($clauses) ? ' WHERE ' . implode(' AND ', $clauses) : '');
        $sql .= ' ORDER BY thwh.version_id DESC LIMIT 1';
        $result = $db->query($sql);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СПИСОК ОТВЕТОВ ПО УРОКАМ
     * @param null $filter
     * @param null $page
     * @param null $show_items
     * @param null $current_user_id
     * @param null $training_ids
     * @return array|bool
     */
    public static function getAnswerList($filter = null, $page = null, $show_items = null, $current_user_id = null, $training_ids = null) {
        $clauses = [];
        $curator_is_master = false;
        $filter_curator_id = isset($filter['curator_users']) && $filter['curator_users'] == 'choose_curator' ? $filter['curator_id'] : $current_user_id;

        if (isset($filter['training_id']) && $filter['training_id']) {
            $clauses[] = "tl.training_id = {$filter['training_id']}";
            $curator_is_master = Training::isMasterCuratorInTraining($filter_curator_id, $filter['training_id']);
        } elseif ($training_ids) {
            $clauses[] = "tl.training_id IN (".implode(",", $training_ids).")";
        }

        if (isset($filter['lesson_id']) && $filter['lesson_id']) {
            $clauses[] = "tl.lesson_id = {$filter['lesson_id']}";
        }

        if (isset($filter['user_name'])) {
            if (!isset($filter['user_surname'])) {
                $clauses[] = "(u.user_name LIKE CONCAT(:user_name, '%') OR u.user_name LIKE CONCAT('% ', :user_name) OR
                               u.surname LIKE CONCAT(:user_name, '%'))";
            } else {
                $clauses[] = "((u.user_name = CONCAT(:user_name, ' ', :user_surname) OR u.user_name = CONCAT(:user_surname, ' ', :user_name)) OR
                               (u.user_name = :user_name AND u.surname = :user_surname) OR (u.user_name = :user_surname AND u.surname = :user_name))";
            }
        }

        if (isset($filter['user_email']) && $filter['user_email']) {
            $clauses[] = "u.email LIKE CONCAT(:user_email, '%')";
        }

        if (isset($filter['curator_users']) || !$curator_is_master) {
            if ($curator_is_master && in_array($filter['curator_users'], ['my_users', 'choose_curator'])) {
                $clauses[] = "tctu.curator_id = $filter_curator_id";
            } elseif (!$curator_is_master) {
                $clauses[] = isset($filter['curator_users']) && in_array($filter['curator_users'], ['my_users', 'choose_curator'])
                    ? "tctu.curator_id = $filter_curator_id" : "(tctu.curator_id is null or tctu.curator_id = $filter_curator_id 
                       AND (ta.curator_id = $filter_curator_id OR ta.curator_id = 0))";
            }
        }

        $answers_where = $comments_where = '';
        if (!isset($filter['lesson_complete_status']) || in_array($filter['lesson_complete_status'], ['checked', 'unchecked'])) {
            $answers_where = isset($filter['lesson_complete_status']) && $filter['lesson_complete_status'] == 'checked' ?
                'AND ta.status IN (1,2)' : 'AND ta.status IN (4,3)';
        }

        if (!isset($filter['comments_status']) || in_array($filter['comments_status'], ['read', 'unread'])) {
            $comments_where = isset($filter['comments_status']) && $filter['comments_status'] == 'read' ? 'AND thwc.status = 1' :
                'AND thwc.status = 0';
        }

        if (isset($filter['start_date'])) {
            $answers_where .= ' AND thwhmax.create_date >= :start_date';
            $comments_where .= ' AND thwc.create_date >= :start_date';
        }

        if (isset($filter['finish_date'])) {
            $answers_where .= ' AND thwhmax.create_date < :finish_date';
            $comments_where .= ' AND thwc.create_date < :finish_date';
        }

        $where = !empty($clauses) ? ' ' . implode(' AND ', $clauses) : '';
        $comments_query = "(SELECT DISTINCT thwc.homework_id, null, tl.lesson_id, thwc.user_id, null, null, thwc.create_date,
            null, null, null, null, null, null, null, null, null, null, u.user_name, u.surname, u.email AS user_email, u.sex,
            t.name AS training_name, tl.name AS lesson_name, null AS teacher, null AS curator_name, thwc.comment_id,
            thwc.comment_text, thwc.status, 'comment' AS type_items, null, null
            FROM  ".PREFICS."training_home_work_comments AS thwc
            LEFT JOIN tmp_comments AS tmp_com ON thwc.homework_id = tmp_com.homework_id AND tmp_com.comment_id > thwc.comment_id
            LEFT JOIN ".PREFICS."training_home_work AS ta ON thwc.homework_id = ta.homework_id
            LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = ta.lesson_id
            LEFT JOIN ".PREFICS."users AS u ON u.user_id = ta.user_id
            LEFT JOIN ".PREFICS."training AS t ON t.training_id = tl.training_id
            LEFT JOIN ".PREFICS."training_curator_to_user AS tctu ON tctu.user_id = ta.user_id AND tl.training_id = tctu.training_id
            LEFT JOIN ".PREFICS."users AS tctuname ON tctu.curator_id = tctuname.user_id
            WHERE tmp_com.comment_id IS NULL AND thwc.status <> 2 AND $where $comments_where) ORDER BY homework_id";

        $comments_helper_query = "CREATE TEMPORARY TABLE tmp_comments SELECT * FROM ".PREFICS."training_home_work_comments WHERE status <> 2;";

        $sql = "(SELECT DISTINCT ta.*, thwhmax.history_id AS history_id, thwhmax.answer AS answer, thwhmax.attach AS attach,
            thwhmax.work_link AS work_link, thwhmax.create_date AS date_user_send, u.user_name, u.surname, u.email AS user_email,
            u.sex, t.name AS training_name, tl.name AS lesson_name, tctu.curator_id AS teacher, tctuname.user_name AS curator_name,
            null AS comment_id, null AS comment_text, null AS comment_status , 'answer' AS type_items, ttask.auto_answer,
            ttask.task_type AS task_type FROM ".PREFICS."training_home_work AS ta
            LEFT JOIN ".PREFICS."users AS u ON u.user_id = ta.user_id
            LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = ta.lesson_id
            LEFT JOIN ".PREFICS."training AS t ON t.training_id = tl.training_id
            LEFT JOIN (SELECT a.* FROM ".PREFICS."training_home_work_history a
            LEFT OUTER JOIN ".PREFICS."training_home_work_history b ON a.homework_id = b.homework_id AND b.version_id > a.version_id
            WHERE b.version_id IS NULL) AS thwhmax ON ta.homework_id = thwhmax.homework_id
            LEFT JOIN ".PREFICS."training_curator_to_user AS tctu ON tctu.user_id = ta.user_id AND tl.training_id = tctu.training_id
            AND tl.section_id = tctu.section_id
            LEFT JOIN ".PREFICS."users AS tctuname ON tctu.curator_id = tctuname.user_id
            LEFT JOIN ".PREFICS."training_task AS ttask ON tl.lesson_id = ttask.lesson_id
            LEFT JOIN ".PREFICS."training_test AS ttest ON tl.lesson_id = ttest.lesson_id
            WHERE $where $answers_where)";

        if (!isset($filter['answer_type']) || in_array($filter['answer_type'], ['all', 'only_comments'])) {
            $sql = isset($filter['answer_type']) && $filter['answer_type'] == 'only_comments' ? $comments_query : "$sql UNION $comments_query";
        }

        if ($page && $show_items) {
            $offset = ($page - 1) * $show_items;
            $sql .= " LIMIT $show_items OFFSET $offset";
        }

        $db = Db::getConnection();
        $db->query($comments_helper_query);
        $result = $db->prepare($sql);

        if (isset($filter['user_email']) && $filter['user_email']) {
            $result->bindParam(':user_email', $filter['user_email'], PDO::PARAM_STR);
        }
        if (isset($filter['user_name'])) {
            $result->bindParam(':user_name', $filter['user_name'], PDO::PARAM_STR);
        }
        if (isset($filter['user_surname'])) {
            $result->bindParam(':user_surname', $filter['user_surname'], PDO::PARAM_STR);
        }
        if (isset($filter['start_date'])) {
            $result->bindParam(':start_date', $filter['start_date'], PDO::PARAM_INT);
        }
        if (isset($filter['finish_date'])) {
            $result->bindParam(':finish_date', $filter['finish_date'], PDO::PARAM_INT);
        }

        $result->execute();
        $data = [];

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО ОТВЕТОВ
     * @param null $filter
     * @param $current_user_id
     * @param $training_ids
     * @return mixed
     */
    public static function getTotalAnswers($filter = null, $current_user_id = null, $training_ids = null) {
        $clauses = [];
        $curator_is_master = false;
        $filter_curator_id = isset($filter['curator_users']) && $filter['curator_users'] == 'choose_curator' ? $filter['curator_id'] : $current_user_id;

        if (isset($filter['training_id']) && $filter['training_id']) {
            $clauses[] = "tl.training_id = {$filter['training_id']}";
            $curator_is_master = Training::isMasterCuratorInTraining($filter_curator_id, $filter['training_id']);
        } elseif ($training_ids) {
            $clauses[] = "tl.training_id IN (".implode(",", $training_ids).")";
        }

        if (isset($filter['lesson_id']) && $filter['lesson_id']) {
            $clauses[] = "tl.lesson_id = {$filter['lesson_id']}";
        }

        if (isset($filter['user_name'])) {
            if (!isset($filter['user_surname'])) {
                $clauses[] = "(u.user_name LIKE CONCAT(:user_name, '%') OR u.user_name LIKE CONCAT('% ', :user_name) OR
                               u.surname LIKE CONCAT(:user_name, '%'))";
            } else {
                $clauses[] = "((u.user_name = CONCAT(:user_name, ' ', :user_surname) OR u.user_name = CONCAT(:user_surname, ' ', :user_name)) OR
                               (u.user_name = :user_name AND u.surname = :user_surname) OR (u.user_name = :user_surname AND u.surname = :user_name))";
            }
        }

        if (isset($filter['user_email']) && $filter['user_email']) {
            $clauses[] = "u.email LIKE CONCAT(:user_email, '%')";
        }

        if (isset($filter['curator_users']) || !$curator_is_master) {
            if ($curator_is_master && in_array($filter['curator_users'], ['my_users', 'choose_curator'])) {
                $clauses[] = "tctu.curator_id = $filter_curator_id";
            } elseif (!$curator_is_master) {
                $clauses[] = isset($filter['curator_users']) && in_array($filter['curator_users'], ['my_users', 'choose_curator'])
                    ? "tctu.curator_id = $filter_curator_id" : "(tctu.curator_id is null or tctu.curator_id = $filter_curator_id
                       AND (ta.curator_id = $filter_curator_id OR ta.curator_id = 0))";
            }
        }

        $answers_where = $comments_where = '';
        if (!isset($filter['lesson_complete_status']) || in_array($filter['lesson_complete_status'], ['checked', 'unchecked'])) {
            $answers_where = isset($filter['lesson_complete_status']) && $filter['lesson_complete_status'] == 'checked' ?
                'AND ta.status IN (1,2)' : 'AND ta.status IN (4,3)';
        }
        
        if (!isset($filter['comments_status']) || in_array($filter['comments_status'], ['read', 'unread'])) {
            $comments_where = isset($filter['comments_status']) && $filter['comments_status'] == 'read' ? 'AND thwc.status = 1' :
                'AND thwc.status = 0';
        }

        if (isset($filter['start_date'])) {
            $answers_where .= ' AND thwhmax.create_date >= :start_date';
            $comments_where .= ' AND thwc.create_date >= :start_date';
        }

        if (isset($filter['finish_date'])) {
            $answers_where .= ' AND thwhmax.create_date < :finish_date';
            $comments_where .= ' AND thwc.create_date < :finish_date';
        }

        $where = !empty($clauses) ? ' ' . implode(' AND ', $clauses) : '';
        $comments_query = "(SELECT COUNT(DISTINCT thwc.homework_id)
            FROM ".PREFICS."training_home_work_comments AS thwc
            LEFT JOIN tmp_comments AS tmp_com ON thwc.homework_id = tmp_com.homework_id AND tmp_com.comment_id > thwc.comment_id
            LEFT JOIN ".PREFICS."training_home_work AS ta ON thwc.homework_id = ta.homework_id
            LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = ta.lesson_id
            LEFT JOIN ".PREFICS."users AS u ON u.user_id = ta.user_id
            LEFT JOIN ".PREFICS."training AS t ON t.training_id = tl.training_id
            LEFT JOIN ".PREFICS."training_curator_to_user AS tctu ON tctu.user_id = ta.user_id AND tl.training_id = tctu.training_id
            LEFT JOIN ".PREFICS."users AS tctuname ON tctu.curator_id = tctuname.user_id
            WHERE tmp_com.comment_id IS NULL AND thwc.status <> 2 AND $where $comments_where)";

        $comments_helper_query = "CREATE TEMPORARY TABLE tmp_comments SELECT * FROM ".PREFICS."training_home_work_comments WHERE status <> 2;";

        $sql = "(SELECT COUNT(DISTINCT ta.homework_id)
            FROM ".PREFICS."training_home_work AS ta
            LEFT JOIN ".PREFICS."users AS u ON u.user_id = ta.user_id
            LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = ta.lesson_id
            LEFT JOIN ".PREFICS."training AS t ON t.training_id = tl.training_id
            LEFT JOIN (SELECT a.* FROM ".PREFICS."training_home_work_history a
            LEFT OUTER JOIN ".PREFICS."training_home_work_history b ON a.homework_id = b.homework_id AND b.version_id > a.version_id
            WHERE b.version_id IS NULL) AS thwhmax ON ta.homework_id = thwhmax.homework_id
            LEFT JOIN ".PREFICS."training_curator_to_user AS tctu ON tctu.user_id = ta.user_id AND tl.training_id = tctu.training_id
            AND tl.section_id = tctu.section_id
            LEFT JOIN ".PREFICS."users AS tctuname ON tctu.curator_id = tctuname.user_id
            LEFT JOIN ".PREFICS."training_task AS ttask ON tl.lesson_id = ttask.lesson_id
            LEFT JOIN ".PREFICS."training_test AS ttest ON tl.lesson_id = ttest.lesson_id
            WHERE $where $answers_where)";

        if (!isset($filter['answer_type']) || in_array($filter['answer_type'], ['all', 'only_comments'])) {
            $sql = isset($filter['answer_type']) && $filter['answer_type'] == 'only_comments' ? $comments_query : "$sql UNION $comments_query";
        }

        $db = Db::getConnection();
        $db->query($comments_helper_query);
        $result = $db->prepare($sql);

        if (isset($filter['user_email']) && $filter['user_email']) {
            $result->bindParam(':user_email', $filter['user_email'], PDO::PARAM_STR);
        }
        if (isset($filter['user_name'])) {
            $result->bindParam(':user_name', $filter['user_name'], PDO::PARAM_STR);
        }
        if (isset($filter['user_surname'])) {
            $result->bindParam(':user_surname', $filter['user_surname'], PDO::PARAM_STR);
        }
        if (isset($filter['start_date'])) {
            $result->bindParam(':start_date', $filter['start_date'], PDO::PARAM_INT);
        }
        if (isset($filter['finish_date'])) {
            $result->bindParam(':finish_date', $filter['finish_date'], PDO::PARAM_INT);
        }

        $result->execute();
        $count = $result->fetchAll(PDO::FETCH_COLUMN);

        return array_sum($count);
    }


    /**
     * ПОЛУЧИТЬ ПОСЛЕДНЮЮ ВЕРСИЮ ОТВЕТА
     * @param $homework_id
     * @return bool|mixed
     */
    public static function getAnswer($homework_id) {
        $db = Db::getConnection();
        $query = "SELECT t1.*, thw.lesson_id, thw.status  FROM ".PREFICS."training_home_work_history AS t1
                  LEFT JOIN ".PREFICS."training_home_work AS thw ON t1.homework_id = thw.homework_id
                  WHERE t1.homework_id = :homework_id ORDER BY t1.version_id DESC LIMIT 1";
        $result = $db->prepare($query);
        $result->bindParam(':homework_id', $homework_id, PDO::PARAM_INT);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ПЕРВЫЙ ОТВЕТ ПОЛЬЗОВАТЕЛЯ НА ДЗ
     * @param $lesson_id
     * @param $user_id
     * @return bool|mixed
     */
    public static function getFirstUserAnswer($lesson_id, $user_id) {
        $db = Db::getConnection();
        $query = "SELECT thwh.* FROM ".PREFICS."training_home_work_history as thwh
                  LEFT JOIN ".PREFICS."training_home_work AS thw ON thw.homework_id = thwh.homework_id
                  WHERE thw.user_id = :user_id AND thw.lesson_id = :lesson_id
                  ORDER BY thwh.history_id ASC LIMIT 1";
        $result = $db->prepare($query);
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ОТВЕТ ПОЛЬЗОВАТЕЛЯ НА ДЗ
     * @param $history_id
     * @return bool|mixed
     */
    public static function getUserAnswer($history_id) {
        $db = Db::getConnection();
        $result = $db->prepare("SELECT * FROM ".PREFICS."training_home_work_history WHERE history_id = :history_id");
        $result->bindParam(':history_id', $history_id, PDO::PARAM_INT);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СПИСОК КОММЕНТАРИЕВ К ДОМАШНЕМУ ЗАДАНИЮ
     * @param $homework_id
     * @return bool|mixed
     */
    public static function getCommentsByHomeworkID($homework_id) {

        $db = Db::getConnection();
        $sql = "SELECT thwc.*, u.user_name, u.email AS user_email, u.sex FROM ".PREFICS."training_home_work_comments AS thwc
                LEFT JOIN ".PREFICS."users AS u ON u.user_id = thwc.user_id
                WHERE homework_id = :homework_id ORDER BY comment_id ASC";
        $result = $db->prepare($sql);
        $result->bindParam(':homework_id', $homework_id, PDO::PARAM_INT);
        $result->execute();

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ КОММЕНТАРИЙ К ДОМАШНЕМУ ЗАДАНИЮ
     * @param $comment_id
     * @return bool|mixed
     */
    public static function getComment($comment_id) {
        $db = Db::getConnection();
        $result = $db->prepare("SELECT * FROM ".PREFICS."training_home_work_comments WHERE comment_id = :comment_id");
        $result->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        $result->execute();

        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * @param $comment_id
     * @param $comment
     * @param $attach
     * @return bool
     */
    public static function updComment($comment_id, $comment, $attach) {
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE '.PREFICS.'training_home_work_comments SET comment_text = :comment,
                                         attach = :attach WHERE comment_id = :comment_id'
        );
        $result->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        $result->bindParam(':comment', $comment, PDO::PARAM_STR);
        $result->bindParam(':attach', $attach, PDO::PARAM_STR);

        return $result->execute();
    }

    /**
     * ЗАПИСАТЬ ОТВЕТ ПОЛЬЗОВАТЕЛЯ В БД
     * @param $task_id
     * @param $lesson_id
     * @param $user_id
     * @param int $curator_id
     * @param $status 1 - принято, 2 - отклонено, 3 - на рассмотрении (когда куратор открыл ДЗ, чтобы другие не могли ответить)
     *                4 - ученик прислал работу на проверку
     * @param int $public 0 - не публичное, 1 - только работа, 2 - полностью работа и комменты
     * @param $answer
     * @param null $attach
     * @param null $work_link
     * @param int $mark
     * @param int $points
     * @return bool
     */
    public static function writeAnswer($task_id, $lesson_id, $user_id, $curator_id = 0, $status, $public = 0, $answer, $attach = null, $work_link = null, $mark = 0, $points = 0) {
  
        $db = Db::getConnection();
        $date = time();
        $result = $db->query("SELECT COUNT(homework_id) FROM ".PREFICS."training_home_work WHERE lesson_id = $lesson_id AND user_id = $user_id");
        $count = $result->fetch();

        if ($count[0] == 0) {
            $sql = 'INSERT INTO '.PREFICS.'training_home_work (task_id, lesson_id, user_id, curator_id, status, create_date, public, mark, points) 
                    VALUES (:task_id, :lesson_id, :user_id, :curator_id, :status, :create_date, :public, :mark, :points)';
            $result = $db->prepare($sql);
            $result->bindParam(':task_id', $task_id, PDO::PARAM_INT);
            $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
            $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $result->bindParam(':curator_id', $curator_id, PDO::PARAM_INT);
            $result->bindParam(':status', $status, PDO::PARAM_INT);
            $result->bindParam(':create_date', $date, PDO::PARAM_INT);
            $result->bindParam(':public', $public, PDO::PARAM_INT);
            $result->bindParam(':mark', $mark, PDO::PARAM_INT);
            $result->bindParam(':points', $points, PDO::PARAM_INT);
        } else {
            $sql = 'UPDATE '.PREFICS.'training_home_work SET status = :status, create_date = :create_date WHERE lesson_id = :lesson_id AND user_id = :user_id';
            $result = $db->prepare($sql);
            $result->bindParam(':status', $status, PDO::PARAM_INT);
            $result->bindParam(':create_date', $date, PDO::PARAM_INT);
            $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
            $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        }

        if ($result->execute()) {
            $homework_id = $version_id = 0;
            $sql = "SELECT homework_id FROM ".PREFICS."training_home_work WHERE lesson_id = $lesson_id
                    AND user_id = $user_id AND create_date = $date LIMIT 1";
            $result = $db->query($sql);
            $data = $result->fetch(PDO::FETCH_ASSOC);

            if (!empty($data)) { // пишем ответ в таблицу истории training_home_work_history
                $sql = 'INSERT INTO '.PREFICS.'training_home_work_history (homework_id, version_id, answer, attach, work_link, create_date) 
                        VALUES (:homework_id, :version_id, :answer, :attach, :work_link, :create_date)';
                $result = $db->prepare($sql);
                $result->bindParam(':homework_id', $data['homework_id'], PDO::PARAM_INT);
                $result->bindParam(':version_id', $version_id, PDO::PARAM_INT);
                $result->bindParam(':answer', $answer, PDO::PARAM_STR);
                $result->bindParam(':attach', $attach, PDO::PARAM_STR);
                $result->bindParam(':work_link', $work_link, PDO::PARAM_STR);
                $result->bindParam(':create_date', $date, PDO::PARAM_INT);

                return $result->execute();
            }
        }
    }


    /**
     * ОБНОВИТЬ ОТВЕТ К ДЗ
     * @param $homework_id
     * @param $answer
     * @param $attach
     * @param $lesson_id
     * @param $user_id
     * @param null $work_link
     * @return bool
     */
    public static function updAnswer($homework_id, $answer, $attach, $lesson_id, $user_id, $work_link = null) {
        $db = Db::getConnection();
        $date = time();
        $sql = "SELECT version_id FROM ".PREFICS."training_home_work_history 
                WHERE homework_id = $homework_id 
                ORDER BY version_id DESC LIMIT 1";
        $result = $db->query($sql);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        if ($data['version_id']++ >= 0) {
            $sql = 'INSERT INTO '.PREFICS.'training_home_work_history (homework_id, answer, attach, version_id, create_date, work_link)  
                    VALUES (:homework_id, :answer, :attach, :version_id, :create_date, :work_link)';

            $result = $db->prepare($sql);
            $result->bindParam(':homework_id', $homework_id, PDO::PARAM_INT);
            $result->bindParam(':version_id', $data['version_id'], PDO::PARAM_INT);
            $result->bindParam(':answer', $answer, PDO::PARAM_STR);
            $result->bindParam(':attach', $attach, PDO::PARAM_STR);
            $result->bindParam(':create_date', $date, PDO::PARAM_STR);
            $result->bindParam(':work_link', $work_link, PDO::PARAM_STR);

            if ($result->execute()) {
                $upd = self::updLessonCompleteStatus($lesson_id, $user_id, TrainingLesson::HOMEWORK_SUBMITTED, $date);
                if ($upd) {
                    return self::updStatusHomework($homework_id, TrainingLesson::HOME_WORK_SEND);
                }
            }
        }

        return false;
    }


    /**
     * @param $homework_id
     * @param $status
     * @return bool
     */
    public static function updStatusHomework($homework_id, $status) {
        $db = Db::getConnection();
        $query = 'UPDATE '.PREFICS.'training_home_work SET status = :homework_status WHERE homework_id = :homework_id';
        $result = $db->prepare($query);

        $result->bindParam(':homework_id', $homework_id, PDO::PARAM_INT);
        $result->bindParam(':homework_status', $status, PDO::PARAM_INT); //статус 4

        return $result->execute();
    }


    /**
     * ЗАПИСАТЬ КОММЕНТАРИЙ К ОТВЕТУ
     * @param $homework_id
     * @param $user_id
     * @param $parent_id
     * @param $answer
     * @param $status
     * @param $attach
     * @return bool
     */
    public static function writeComment($homework_id, $user_id, $parent_id = 0, $answer, $status = 0, $attach) {
        $db = Db::getConnection();
        $date = time();
        $sql = 'INSERT INTO '.PREFICS.'training_home_work_comments (homework_id, user_id, parent_id, comment_text, create_date, status, attach)  
                VALUES (:homework_id, :user_id, :parent_id, :answer, :create_date, :status, :attach)';

        $result = $db->prepare($sql);
        $result->bindParam(':homework_id', $homework_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
        $result->bindParam(':answer', $answer, PDO::PARAM_STR);
        $result->bindParam(':create_date', $date, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':attach', $attach, PDO::PARAM_STR);

        return $result->execute();
    }

    /**
     * ЗАГРУЗКА ВЛОЖЕНИЯ К ОТВЕТУ
     * @param $files
     * @param $lesson_id
     * @param $user_type
     * @return false|string
     */
    public static function uploadAttach2Answer($files, $lesson_id, $user_type)
    {
        $settings = System::getSetting();
        $attachments = [];
        $attach_folders = [
            Training::USER_TYPE_ADMIN => 'admin',
            Training::USER_TYPE_CURATOR => 'curator',
            Training::USER_TYPE_USER => 'user',
        ];

        foreach($files['lesson_attach']["name"] as $key => $attach_name) {
            if (!$attach_name || $_FILES["lesson_attach"]["size"][$key] == 0) {
                return '';
            }

            $attach_name = System::getSecureString($attach_name, true);

            if ($user_type == Training::USER_TYPE_USER) {
                if (!System::isAllowedUploadAttach($attach_name, $files['lesson_attach']['type'][$key], $_FILES["lesson_attach"]["size"][$key], $settings)) {
                    continue;
                }
            }

            $tmp_name = $files["lesson_attach"]["tmp_name"][$key];
            $relative_path = "load/hometask/lessons/$lesson_id/{$attach_folders[$user_type]}";

            $dir_path = ROOT;
            foreach (explode('/', $relative_path) as $dir) {
                $dir_path .= "/$dir";
                if (!file_exists($dir_path)) {
                    mkdir($dir_path);
                }
            }

            $unique_name = md5(microtime(true).mt_rand(100,999).$attach_name);
            $file_info = pathinfo($attach_name);
            $relative_path = "/$relative_path/$unique_name.{$file_info['extension']}";

            if (is_uploaded_file($tmp_name) && move_uploaded_file($tmp_name, ROOT.$relative_path)) {
                $attachments[] = [
                    'path' => urlencode($relative_path),
                    'name' => $attach_name,
                ];
            }
        }

        return !empty($attachments) ? json_encode($attachments) : '';
    }


    /**
     * ЗАПИСАТЬ ПРОСМОТР УРОКА И/ИЛИ ЗАПИСАТЬ ОТКРЫТИЕ ДЛЯ ЮЗЕРА
     * @param $lesson_id
     * @param $training_id
     * @param $hits
     * @param $user_id
     * @param $status
     * @return bool
     */
    public static function writeHit($lesson_id, $training_id, $hits, $user_id, $status = 0)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'training_lessons SET hits = :hits WHERE lesson_id = '.$lesson_id;
        $result = $db->prepare($sql);
        $result->bindParam(':hits', $hits, PDO::PARAM_INT);
        $res = $result->execute();

        if ($user_id) {
            $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."training_user_map WHERE user_id = $user_id AND lesson_id = $lesson_id ");
            $count = $result->fetch();

            if ($count[0] == 0) {
                $open = time();
                $sql = 'INSERT INTO '.PREFICS.'training_user_map (user_id, lesson_id, training_id, open, status) 
                        VALUES (:user_id, :lesson_id, :training_id, :open, :status)';

                $result = $db->prepare($sql);
                $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
                $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
                $result->bindParam(':open', $open, PDO::PARAM_INT);
                $result->bindParam(':status', $status, PDO::PARAM_INT);
                $res = $result->execute();
            }
        }

        return $res;
    }


    /**
     * ОБНОВИТЬ СТАТУС У ВСЕХ КОММЕНТАРИЕВ К ЗАДАНИЮ
     * @param $homework_id
     * @return bool
     */
    public static function updateStatusAllCommentsByHomework($homework_id) {
        $status = TrainingLesson::COMMENT_IS_READ;
        $answer_curator = TrainingLesson::COMMENT_IS_ANSWERED;
        $delete_comment = TrainingLesson::COMMENT_DELETED;
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE '.PREFICS.'training_home_work_comments SET status = :status 
                                         WHERE homework_id = :homework_id AND status <> :answer_curator AND status <> :delete_comment'
        );
        $result->bindParam(':homework_id', $homework_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':answer_curator', $answer_curator, PDO::PARAM_INT);
        $result->bindParam(':delete_comment', $delete_comment, PDO::PARAM_INT);

        return $result->execute();
    }

      /**
     * УСТАНОВИТЬ СТАТУС И КУРАТОРА В ОТВЕТЕ
     * @param $homework_id
     * @param $status
     * @param $curator_id
     * @return bool
     */
    public static function updateStatusAnswer($homework_id, $status, $curator_id) {
        $current_time = time();
        $db = Db::getConnection();
        $query = 'UPDATE '.PREFICS.'training_home_work SET status = :status, curator_id = :curator_id,
                  create_date = :current_time WHERE homework_id = :homework_id';

        $result = $db->prepare($query);
        $result->bindParam(':homework_id', $homework_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':curator_id', $curator_id, PDO::PARAM_INT);
        $result->bindParam(':current_time', $current_time, PDO::PARAM_INT);

        return $result->execute();
    }

    /** МОЖНО ЛИ ПИСАТЬ ПОЛЬЗОВАТЕЛЮ КОММЕНТАРИЙ К РАБОТЕ
     * @param $training
     * @param $lesson_complete_status
     * @return bool
     */
    public static function isAllowSendComment($training, $lesson_complete_status) {
         //Если в тренинге разрешены комментарии
        if($training['homework_comment_add']) {
            // Проверяем возможность писать комментарии после принятия работы
            if((int)$lesson_complete_status == TrainingLesson::HOME_WORK_ACCEPTED && $training['lock_comment'] == 1){
                return false;  
            } 
            return true;
        } 
        return false;
    }


    /** TODO переделать под новые таблицы и параметры
     * Всю эту функцию нужно будет под нож...
     * МОЖНО ЛИ ОТВЕЧАТЬ ПОЛЬЗОВАТЕЛЮ
     * @param $training
     * @param $lesson_complete_status
     * @param $answer_list
     * @return bool
     */
    public static function isAllowSendAnswer($training, $lesson_complete_status, $answer_list) {
        // тут либо 0 работа только начата или не сдана 2, тогда разрешаем делать ответ
        if (in_array($lesson_complete_status, [TrainingLesson::LESSON_STARTED, TrainingLesson::HOMEWORK_DECLINE])) {
            return true;
        }
        // Можно ли редактировать работу до проверки куратором 
        if($lesson_complete_status != TrainingLesson::HOMEWORK_ACCEPTED && $training['homework_edit']) {
            return true;
        } 
        return false;
    }


    /**
     * МОЖНО ЛИ РЕДАКТИРОВАТЬ ОТВЕТ ПОЛЬЗОВАТЕЛЮ
     * это настройка тренинга homework_edit в таблице training 
     * Возможность редактирования ДЗ до проверки куратором 1- ДА, 0 - нет 
     * @param $training
     * @param $lesson_complete_status
     * @param $answer
     * @return bool
     */
    public static function isAllowEditAnswer($training, $lesson_complete_status, $answer) {
        if (in_array($lesson_complete_status, [2, 4])) {
            return true;
        }
        
        if ($training['homework_edit'] != 0) {
            if ((isset($answer['status']) && ($answer['status'] == 2 || $answer['status'] == 4))) {
                return true;
            }
        }

        return false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ДОМАШНЕГО ЗАДАНИЯ
     * @param $user_id
     * @param $lesson_id
     * @return bool|mixed
     */
    public static function getHomeWork($user_id, $lesson_id)
    {
        $db = Db::getConnection();
        $query = "SELECT * FROM ".PREFICS."training_home_work 
                  WHERE user_id = $user_id AND lesson_id = $lesson_id
                  ORDER BY create_date DESC LIMIT 1";
        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ДОБАВИТЬ ДОМАШНЕЕ ЗАДАНИЕ В ПУБЛИЧНЫЕ
     * @param $lesson_id
     * @param $user_id
     * @return bool
     */
    public static function addPublicHomework($lesson_id, $user_id) {
        $db = Db::getConnection();
        $result = $db->prepare('INSERT INTO '.PREFICS.'training_public_homework (lesson_id, user_id) VALUES (:lesson_id, :user_id)');
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ПУБЛИЧНОЕ ДОМАШНЕЕ ЗАДАНИЕ ЕСЛИ ЕСТЬ
     * @param $lesson_id
     * @param $user_id
     * @return bool|mixed
     */
    public static function getPublicHomework($lesson_id, $user_id) {
        $db = Db::getConnection();
        $result = $db->prepare("SELECT * FROM ".PREFICS."training_public_homework WHERE lesson_id = :lesson_id AND user_id = :user_id");
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ОТВЕТ ИЗ ПОНРАВШИХСЯ
     * @param $answer_id
     * @param $user_id
     * @return bool
     */
    public static function getLikeAnswer($answer_id, $user_id) {
        $db = Db::getConnection();
        $result = $db->prepare('SELECT * FROM '.PREFICS.'training_liked_answers WHERE answer_id = :answer_id AND user_id = :user_id');
        $result->bindParam(':answer_id', $answer_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);
        return !empty($data) ? $data : false;
    }


    /**
     * ДОБАВИТЬ ОТВЕТ В ПОНРАВШИЕСЯ
     * @param $answer_id
     * @param $user_id
     * @return bool
     */
    public static function addLikeAnswer($answer_id, $user_id) {
        $db = Db::getConnection();
        $result = $db->prepare('INSERT INTO '.PREFICS.'training_liked_answers (answer_id, user_id) VALUES (:answer_id, :user_id)');
        $result->bindParam(':answer_id', $answer_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ ОТВЕТ ИЗ ПОНРАВШИХСЯ
     * @param $answer_id
     * @param $user_id
     * @return bool|mixed
     */
    public static function delLikeAnswer($answer_id, $user_id) {
        $db = Db::getConnection();
        $result = $db->prepare("DELETE FROM ".PREFICS."training_liked_answers WHERE answer_id = :answer_id AND user_id = :user_id");
        $result->bindParam(':answer_id', $answer_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ КОММЕНТАРИЙ
     * @param $comment_id
     * @return bool
     */
    public static function delMessage($comment_id) {
        $db = Db::getConnection();
        $status = TrainingLesson::COMMENT_DELETED;
        $result = $db->prepare('UPDATE '.PREFICS.'training_home_work_comments SET status = :status
                                         WHERE comment_id = :comment_id'
        );
        $result->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ ДОМАШНЮЮ РАБОТУ
     * @param $homework_id
     * @return bool
     */
    public static function delHomework($homework_id) {
        $db = Db::getConnection();
        $sql = 'DELETE thw, thwh, thwc, tum, ttr FROM '.PREFICS.'training_home_work AS thw 
                LEFT JOIN '.PREFICS.'training_home_work_history AS thwh ON thwh.homework_id = thw.homework_id 
                LEFT JOIN '.PREFICS.'training_home_work_comments AS thwc ON thwc.homework_id = thw.homework_id
                LEFT JOIN '.PREFICS.'training_user_map AS tum ON tum.lesson_id = thw.lesson_id AND tum.user_id = thw.user_id
                LEFT JOIN '.PREFICS.'training_test_results AS ttr ON ttr.lesson_id = thw.lesson_id AND ttr.user_id = thw.user_id
                WHERE thw.homework_id = :homework_id';

        $result = $db->prepare($sql);
        $result->bindParam(':homework_id', $homework_id, PDO::PARAM_INT);

        return $result->execute();
    }


     /**
     * ОПРЕДЕЛЕНИЕ ДОСТУПА К ДОМАШНЕМУ ЗАДАНИЮ
     * @param $user_groups
     * @param $user_planes
     * @param $training
     * @param $task
     * @return bool
     */
    public static function checkUserAccessHomeWork($user_groups, $user_planes, $training, $task)
    {
        
        // TODO тут еще много работы и самое главное еще с публичностью нужно будет разобраться ...
        $access_task_global = json_decode($training["access_task_global"]);
        // Доступ к проверке куратором ДЗ
        if ($task['check_type']==2) {
            if (!isset($access_task_global->curator->free)){
                if (isset($access_task_global->curator->groups) && $user_groups) {
                    if (array_intersect($user_groups, $access_task_global->curator->groups)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
                if (isset($access_task_global->curator->planes) && $user_planes) {
                    if (array_intersect($user_planes, $access_task_global->curator->planes)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
        // Автопроверка ДЗ
        if ($task['check_type']==1) {
            if (!isset($access_task_global->automat->free)) {
                if (isset($access_task_global->automat->groups) && $user_groups) {
                    if (array_intersect($user_groups, $access_task_global->automat->groups)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
                if (isset($access_task_global->automat->planes) && $user_planes) {
                    if (array_intersect($user_planes, $access_task_global->automat->planes)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
        // Самостоятельная проверка ДЗ
        if ($task['check_type']==0) {
            if (!isset($access_task_global->bezproverki->free) && $user_groups) {
                if (isset($access_task_global->bezproverki->groups)) {
                    if (array_intersect($user_groups, $access_task_global->bezproverki->groups)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
                if (isset($access_task_global->bezproverki->planes) && $user_planes) {
                    if (array_intersect($user_planes, $access_task_global->bezproverki->planes)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
    }

      /**
     * ПОЛУЧИТЬ УРОВЕНЬ ДОСТУПА К ТИПУ ПРОВЕРКИ ДОМАШНЕГО ЗАДАНИЯ
     * Тут 2 ой - уровень самый высокий доступ к кураторской проверке
     * 1 - ый к автоматической и 
     * 0 - ой к самостоятельной
     * false- нет доступа никакого
     * @param $user_groups
     * @param $user_planes
     * @param $training
     * @return bool
     */
    public static function getLevelAccessTypeHomeWork($user_groups, $user_planes, $training)
    {
        
        // TODO 11.06.2021 убрать проверки если куратор или админ, варнинг 
        $access_task_global = json_decode($training["access_task_global"]);
        // Доступ к проверке куратором ДЗ
        if (!isset($access_task_global->curator->free)) {
            if (isset($access_task_global->curator->groups) && $user_groups) {
                if (array_intersect($user_groups, $access_task_global->curator->groups)) {
                    return 2;
                }
            }
            if (isset($access_task_global->curator->planes) && $user_planes) {
                if (array_intersect($user_planes, $access_task_global->curator->planes)) {
                    return 2;
                }
            }
        } else {
            return 2;
        }

        if (!isset($access_task_global->automat->free)) {
            if (isset($access_task_global->automat->groups) && $user_groups) {
                if (array_intersect($user_groups, $access_task_global->automat->groups)) {
                    return 1;
                }
            }
            if (isset($access_task_global->automat->planes) && $user_planes) {
                if (array_intersect($user_planes, $access_task_global->automat->planes)) {
                    return 1;
                }   
            }
        } else {
            return 1;
        }

        // Самостоятельная проверка ДЗ
        if (!isset($access_task_global->bezproverki->free)) {
            if (isset($access_task_global->bezproverki->groups) && $user_groups) {
                if (array_intersect($user_groups, $access_task_global->bezproverki->groups)) {
                    return 0;
                }
            }
            if (isset($access_task_global->bezproverki->planes) && $user_planes) {
                if (array_intersect($user_planes, $access_task_global->bezproverki->planes)) {
                    return 0;
                }
            }
        } else {
            return 0;
        }

        // Тут нет никакого доступа совсем!!!
        return false;
    }


    /**
     * ЕСТЬ ЛИ ДОСТУП К ПОСЛЕДНЕМУ ДЗ
     * @param $has_lesson_last_stop
     * @param $training
     * @param $lesson
     * @param $section
     * @param $user_id
     * @param $user_groups
     * @param $user_planes
     * @return bool|float|int|mixed|null
     * @throws Exception
     */
    public static function isAccessLastHomework($has_lesson_last_stop, $training, $lesson, $section, $user_id, $user_groups, $user_planes)
    {
        if (empty($user_id)) {
            $access_last_homework = false;
            return $access_last_homework;
        }
        // Проверяем доступ к домашнему заданию прошлого СТОПового урока
        $access_last_lesson_homework = TrainingLesson::getLevelAccessTypeHomeWork($user_groups, $user_planes, $training);
        // Проверяем доступ к прошлому СТОПовому уроку
        $access_last_lesson = Training::getAccessData($user_groups, $user_planes, $training, $section, $has_lesson_last_stop);

        if ($access_last_lesson_homework >= 0 && Training::checkUserAccess($access_last_lesson)) {
            $access_last_homework = TrainingLesson::isLessonComplete($has_lesson_last_stop['lesson_id'], $user_id) ? true : false;
        } else {
            $access_last_homework = true;
        }


        if ($access_last_homework) {
            $lesson_open_data = self::getLessonStopOpenDate($training, $has_lesson_last_stop['lesson_id'], $user_id);
            return $lesson_open_data;
        }

        return $access_last_homework;
    }


    /**
     * ПОЛУЧИТЬ ПОЛЬЗОВАТЕЛЕЙ ПРОХОДЯЩИХ УРОК
     * @param $lesson_id
     * @return array|bool
     */
    public static function getUsersPass2lesson($lesson_id) {
        $db = Db::getConnection();
        $query = "SELECT u.user_id, u.user_name FROM ".PREFICS."training_user_map AS tum
                  INNER JOIN ".PREFICS."users AS u ON u.user_id = tum.user_id
                  WHERE lesson_id = :lesson_id";
        $result = $db->prepare($query);

        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->execute();
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * @param $check_type
     * @param $task_type
     * @return string|null
     */
    public static function getTaskTypeText($check_type, $task_type) {
        $text = '';
        if ($check_type == 0) {
            $text = 'с самостоятельной проверкой';
        } elseif($check_type == 1) {
            $text = 'с автопроверкой';
        } elseif($check_type == 2) {
            $text = 'с ручной проверкой(Куратором)';
        }

        if ($task_type == 0) {
            return 'Нет задания';
        } elseif ($task_type == 1) {
            return "Задание $text";
        } elseif ($task_type == 2) {
            return "Задание и тестирование $text";
        } elseif ($task_type == 3) {
            return "Тестирование";
        }

        return null;
    }
}