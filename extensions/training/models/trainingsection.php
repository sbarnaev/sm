<?php defined('BILLINGMASTER') or die;


class TrainingSection {

    use ResultMessage;


    const STATUS_SECTION_COMPLETE = 1; // РАЗДЕЛ ПРОЙДЕН
    const STATUS_SECTION_OPEN = 2; // РАЗДЕЛ ОТКРЫТ
    const STATUS_SECTION_NOT_ACCESS = 3; // НЕТ ДОСТУПА (НЕТ НУЖНОЙ ГРУППЫ ИЛИ ПОДПИСКИ)
    const STATUS_SECTION_PREV_SECTION_NOT_COMPLETE = 4; // НЕ ПРОЙДЕН ПРЕДЫДУЩИЙ РАЗДЕЛ
    const STATUS_SECTION_NOT_YET = 5; // ЕЩЕ НЕ ПОДОШЛО ВРЕМЯ

    private static $open_date;


    /**
     * ОБРАБОТАТЬ ДАННЫЕ ПЕРЕД СОХРАНЕНИЕМ ДЛЯ АДМИНКИ
     * @param $data
     * @param null $section_id
     * @return mixed
     */
    public static function beforeSaveSectionData2Admin($data, $section_id = null) {
        $fields = [
            'safe' => [
                'curators',
            ],
            'int' => [
                'status', 'access_type', 'open_type', 'close_type', 'is_show_before_open',
                'open_wait_days', 'close_wait_days', 'start_type', 'finish_type',
                'image_type', 'shedule_how_fast_open', 'open_wait_weekday', 'open_skip_weekdays'
            ],
            'str' => [
                'name', 'img_alt', 'section_desc', 'service_header', 'meta_desc', 'meta_keys',
            ],
            'date' => [
                'open_date', 'close_date',
            ],
            'json' => [
                'access_groups', 'access_planes', 'by_button', 'start_lessons', 'finish_lessons',
            ],
        ];

        $data['alias'] = $data['alias'] ?: System::Translit($data['name']);
        if (System::searchDuplicateAliases($data['alias'], 'training_sections', $section_id, 'section_id')) {
            $data['alias'] .= '-1';
        }

        return Training::beforeSaveData2Admin($fields, $data);
    }


    /**
     * ДОБАВИТЬ РАЗДЕЛ ТРЕНИНГА
     * @param $training_id
     * @param $data
     * @return bool|PDOStatement
     */
    public static function addSection($training_id, $data)
    {
        $sort = self::getFreeSort($training_id);
        $sql = 'INSERT INTO '.PREFICS.'training_sections (training_id, name, title, alias, section_desc, cover, img_alt, access_type,
                    access_groups, access_planes, status, meta_desc, meta_keys, service_header, by_button, open_type,
                    open_wait_days, open_date, close_type, close_wait_days, close_date, is_show_before_open, start_type,
                    start_lessons, finish_type, finish_lessons, image_type, sort, shedule_how_fast_open, open_wait_weekday, open_skip_weekdays)
                VALUES (:training_id, :name, :title, :alias, :section_desc, :cover, :img_alt, :access_type, :access_groups, :access_planes,
                    :status, :meta_desc, :meta_keys, :service_header, :by_button, :open_type, :open_wait_days, :open_date,
                    :close_type, :close_wait_days, :close_date, :is_show_before_open, :start_type, :start_lessons, :finish_type,
                    :finish_lessons, :image_type, :sort, :shedule_how_fast_open, :open_wait_weekday, :open_skip_weekdays)';

        $db = Db::getConnection();
        $result = $db->prepare($sql);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $result->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $result->bindParam(':alias', $data['alias'], PDO::PARAM_STR);
        $result->bindParam(':section_desc', $data['section_desc'], PDO::PARAM_STR);
        $result->bindParam(':cover', $data['img'], PDO::PARAM_STR);
        $result->bindParam(':img_alt', $data['img_alt'], PDO::PARAM_STR);
        $result->bindParam(':access_groups', $data['access_groups'], PDO::PARAM_STR);
        $result->bindParam(':access_type', $data['access_type'], PDO::PARAM_INT);
        $result->bindParam(':access_planes', $data['access_planes'], PDO::PARAM_STR);
        $result->bindParam(':status', $data['status'], PDO::PARAM_INT);
        $result->bindParam(':meta_desc', $data['meta_desc'], PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $data['meta_keys'], PDO::PARAM_STR);
        $result->bindParam(':service_header', $data['service_header'], PDO::PARAM_STR);
        $result->bindParam(':by_button', $data['by_button'], PDO::PARAM_STR);
        $result->bindParam(':open_type', $data['open_type'], PDO::PARAM_INT);
        $result->bindParam(':open_wait_days', $data['open_wait_days'], PDO::PARAM_INT);
        $result->bindParam(':open_date', $data['open_date'], PDO::PARAM_INT);
        $result->bindParam(':close_type', $data['close_type'], PDO::PARAM_INT);
        $result->bindParam(':close_wait_days', $data['close_wait_days'], PDO::PARAM_INT);
        $result->bindParam(':close_date', $data['close_date'], PDO::PARAM_INT);
        $result->bindParam(':is_show_before_open', $data['is_show_before_open'], PDO::PARAM_INT);
        $result->bindParam(':start_type', $data['start_type'], PDO::PARAM_INT);
        $result->bindParam(':start_lessons', $data['start_lessons'], PDO::PARAM_STR);
        $result->bindParam(':finish_type', $data['finish_type'], PDO::PARAM_INT);
        $result->bindParam(':finish_lessons', $data['finish_lessons'], PDO::PARAM_STR);
        $result->bindParam(':image_type', $data['image_type'], PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':shedule_how_fast_open', $data['shedule_how_fast_open'], PDO::PARAM_INT);
        $result->bindParam(':open_wait_weekday', $data['open_wait_weekday'], PDO::PARAM_INT);
        $result->bindParam(':open_skip_weekdays', $data['open_skip_weekdays'], PDO::PARAM_INT);

        $result = $result->execute();

        $section_id = $result ? $db->lastInsertId() : null;
        if ($section_id) {
            Training::saveCuratorsToTraining($training_id, null, $data['curators'], $section_id);
        }

        return $section_id ? $section_id : false;
    }


    /**
     * РЕДАКТИРОВАТЬ РАЗДЕЛ ТРЕНИНГА
     * @param $section_id
     * @param $training_id
     * @param $data
     * @return bool|PDOStatement
     */
    public static function editSection($section_id, $training_id, $data)
    {
        $sql = 'UPDATE '.PREFICS."training_sections SET name = :name, title = :title, alias = :alias, section_desc = :section_desc,
                cover = :cover, img_alt = :img_alt, access_type = :access_type, access_groups = :access_groups, access_planes = :access_planes,
                status = :status, meta_desc = :meta_desc, meta_keys = :meta_keys, service_header = :service_header, by_button = :by_button,
                open_type = :open_type, open_wait_days = :open_wait_days, open_date = :open_date, close_type = :close_type,
                close_wait_days = :close_wait_days, close_date = :close_date, is_show_before_open = :is_show_before_open, start_type = :start_type,
                start_lessons = :start_lessons,finish_type = :finish_type, finish_lessons = :finish_lessons, image_type = :image_type,
                shedule_how_fast_open = :shedule_how_fast_open, open_wait_weekday = :open_wait_weekday, open_skip_weekdays = :open_skip_weekdays
                WHERE section_id = $section_id";

        $db = Db::getConnection();
        $result = $db->prepare($sql);
        $result->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $result->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $result->bindParam(':alias', $data['alias'], PDO::PARAM_STR);
        $result->bindParam(':section_desc', $data['section_desc'], PDO::PARAM_STR);
        $result->bindParam(':cover', $data['img'], PDO::PARAM_STR);
        $result->bindParam(':img_alt', $data['img_alt'], PDO::PARAM_STR);
        $result->bindParam(':access_groups', $data['access_groups'], PDO::PARAM_STR);
        $result->bindParam(':access_type', $data['access_type'], PDO::PARAM_INT);
        $result->bindParam(':access_planes', $data['access_planes'], PDO::PARAM_STR);
        $result->bindParam(':status', $data['status'], PDO::PARAM_INT);
        $result->bindParam(':meta_desc', $data['meta_desc'], PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $data['meta_keys'], PDO::PARAM_STR);
        $result->bindParam(':service_header', $data['service_header'], PDO::PARAM_STR);
        $result->bindParam(':by_button', $data['by_button'], PDO::PARAM_STR);
        $result->bindParam(':open_type', $data['open_type'], PDO::PARAM_INT);
        $result->bindParam(':open_wait_days', $data['open_wait_days'], PDO::PARAM_INT);
        $result->bindParam(':open_date', $data['open_date'], PDO::PARAM_INT);
        $result->bindParam(':close_type', $data['close_type'], PDO::PARAM_INT);
        $result->bindParam(':close_wait_days', $data['close_wait_days'], PDO::PARAM_INT);
        $result->bindParam(':close_date', $data['close_date'], PDO::PARAM_INT);
        $result->bindParam(':is_show_before_open', $data['is_show_before_open'], PDO::PARAM_INT);
        $result->bindParam(':start_type', $data['start_type'], PDO::PARAM_INT);
        $result->bindParam(':start_lessons', $data['start_lessons'], PDO::PARAM_STR);
        $result->bindParam(':finish_type', $data['finish_type'], PDO::PARAM_INT);
        $result->bindParam(':finish_lessons', $data['finish_lessons'], PDO::PARAM_STR);
        $result->bindParam(':image_type', $data['image_type'], PDO::PARAM_INT);
        $result->bindParam(':shedule_how_fast_open', $data['shedule_how_fast_open'], PDO::PARAM_INT);
        $result->bindParam(':open_wait_weekday', $data['open_wait_weekday'], PDO::PARAM_INT);
        $result->bindParam(':open_skip_weekdays', $data['open_skip_weekdays'], PDO::PARAM_INT);
        $result = $result->execute();

        if ($result) {
            Training::saveCuratorsToTraining($training_id, null, $data['curators'], $section_id);
        }

        return $result;
    }


    /**
     * ПОЛУЧИТЬ РАЗДЕЛ ТРЕНИНГА
     * @param $section_id
     * @param int $status
     * @return bool|mixed
     */
    public static function getSection($section_id, $status = 1)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS.'training_sections WHERE section_id = :section_id';
        $sql .= ($status !== null ? " AND status = $status" : '').' LIMIT 1';

        $result = $db->prepare($sql);
        $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СПИСОК РАЗДЕЛОВ ТРЕНИНГА
     * @param $training_id
     * @param $status
     * @return array|bool
     */
    public static function getSections($training_id, $status = 1)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."training_sections WHERE training_id = $training_id";
        $sql .= ($status !== null ? " AND status = $status" : '') . ' ORDER BY sort ASC';
        $result = $db->query($sql);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СПИСОК РАЗДЕЛОВ, КОТОРЫЕ ДОЛЖНЫ ОТКРЫТЬСЯ У ПОЛЬЗОВАТЕЛЕЙ
     * @return array|bool
     */
    public static function getShouldOpenSections()
    {
        $time = time();
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."training_sections WHERE ((open_type = 2 AND open_date >= $time)
                OR (open_type = 1 AND open_wait_days > 0)) AND status = 1 ORDER BY training_id ASC";
        $result = $db->query($sql);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ РАЗДЕЛ ПО АЛИАСУ
     * @param $tr_id
     * @param $alias
     * @param int $status
     * @return bool|mixed
     */
    public static function getSectionByAlias($tr_id, $alias, $status = 1)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS.'training_sections WHERE training_id = :training_id AND alias = :alias';
        $sql .= ($status !== null ? " AND status = $status" : '').' LIMIT 1';
        $result = $db->prepare($sql);
        $result->bindParam(':training_id', $tr_id, PDO::PARAM_INT);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }

     /**
     * ПОЛУЧИТЬ РАЗДЕЛ(Секцию) ПО ID lesson
     * @param $lesson_id
     * @return bool|mixed
     */
    public static function getSectionByLessonId($lesson_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT section_id FROM ".PREFICS."training_lessons WHERE lesson_id = $lesson_id LIMIT 1");
        
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['section_id'] : false;
    }



    public static function getSectionBySort($sort, $training_id) {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS.'training_sections WHERE sort = :sort AND training_id = :training_id';
        $result = $db->prepare($sql);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СОРТИРОВКИ ДЛЯ РАЗДЕЛОВ ПО ВОЗРАСТАНИЮ
     * @param array $sections
     * @return array|bool
     */
    public static function getSortSections($sections = []) {
        $place_holders = implode(',', array_fill(0, count($sections), '?'));
        $db = Db::getConnection();
        $result = $db->prepare("SELECT sort FROM ".PREFICS."training_sections WHERE section_id IN ($place_holders) ORDER BY sort ASC");
        $result->execute($sections);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row['sort'];
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СОРТИРОВКУ ДЛЯ ДОБАВЛЯЕМОГО РАЗДЕЛА
     * @param $training_id
     * @return int
     */
    public static function getFreeSort($training_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT MAX(sort) FROM ".PREFICS."training_sections WHERE training_id = $training_id");
        $count = $result->fetch();

        return  (int)$count[0] + 1;
    }


    /**
     * ОБНОВИТЬ СОРТИРОВКУ ДЛЯ РАЗДЕЛА
     * @param $section_id
     * @param $sort
     * @return bool
     */
    public static function updSortSection($section_id, $sort) {
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE '.PREFICS.'training_sections SET sort = :sort WHERE section_id = :section_id');

        $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ РАЗДЕЛ
     * @param $sect_id
     * @return bool
     */
    public static function delSection($sect_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(section_id) FROM ".PREFICS."training_blocks WHERE section_id = $sect_id");
        $count = $result->fetch();

        if ($count[0] == 0) {
            $db = Db::getConnection();
            $sql = 'DELETE FROM '.PREFICS.'training_sections WHERE section_id = :id;
            UPDATE '.PREFICS.'training_lessons SET section_id = 0 WHERE section_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $sect_id, PDO::PARAM_INT);

            return $result->execute();
        } else {
            self::addError('Не возможно удалить: Раздел содержит блоки!');
        }

        return false;
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВ УРОКОВ ДЛЯ РАЗДЕЛА
     * @param $section_id
     * @return mixed
     */
    public static function getCountLessons($section_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(lesson_id) FROM ".PREFICS."training_lessons WHERE section_id = $section_id AND status = 1");
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * НАЧАЛ/НЕ НАЧАЛ ПРОХОДИТЬ РАЗДЕЛ
     * @param $section
     * @param $user_id
     * @param $open_date
     * @return bool
     */
    public static function isStartPassSection($section, $user_id, $open_date) {
        $is_start = false;

        switch ($section['start_type']) {
            case 1: // дата старта
                if ($open_date <= time()) {
                    $is_start = true;
                }
                break;
            case 2: // вошел в уроки
                $lessons = json_decode($section['start_lessons'], true);
                if ($lessons && TrainingLesson::isEnterInLessons($lessons, $user_id)) {
                    $is_start = true;
                }
                break;
            case 3: // прошел уроки
                $lessons = json_decode($section['start_lessons'], true);
                if ($lessons && TrainingLesson::isLessonsCompleted($lessons, $user_id)) {
                    $is_start = true;
                }
                break;
        }

        return $is_start;
    }


    /**
     * ПРОЙДЕН/НЕ ПРОЙДЕН РАЗДЕЛ
     * @param $section
     * @param $user_id
     * @param $open_date
     * @return bool
     */
    public static function isSectionComplete($section, $user_id, $open_date) {
        $is_complete = false;

        switch ($section['finish_type']) {
            case 0: // не учитывать проходение
                $is_complete = false;
                break;
            case 1: // дата окончания
                $close_date = self::getCloseDate($section, $open_date);
                if ($close_date < time()) {
                    $is_complete = true;
                }
                break;
            case 2: // вошел в уроки
                $lessons = json_decode($section['finish_lessons'], true);
                if ($lessons && TrainingLesson::isEnterInLessons($lessons, $user_id)) {
                    $is_complete = true;
                }
                break;
            case 3: // прошел уроки
                $lessons = json_decode($section['finish_lessons'], true);
                if ($lessons && TrainingLesson::isLessonsCompleted($lessons, $user_id)) {
                    $is_complete = true;
                }
                break;
        }

        return $is_complete;
    }


    /**
     * ПОЛУЧИТЬ ДАТУ ОТКРЫТИЯ РАЗДЕЛА
     * @param $training
     * @param $section
     * @param $user_id
     * @param $user_groups
     * @param $user_planes
     * @return float|int|mixed|null
     */
    public static function getOpenDate($training, $section, $user_id, $user_groups, $user_planes) {
        $date = null;
        $access_date_2training = Training::getAccessDate2Training($training, $user_id, $user_groups, $user_planes); // дата доступа к тренингу

        if (!$access_date_2training) {
            return false;
        }

        if ($section && $section['open_type'] == 1) { // если указана относительная дата открытия
            if ($section['shedule_how_fast_open'] == 1) { // через Х дней
                $date = $access_date_2training + 86400 * $section['open_wait_days'];
            } else { // дождаться дня недели
                $open_training_wd = date('w', $access_date_2training) != 0 ? date('w', $access_date_2training) : 7; // день недели, когда доступен тренинг
                $open_section_wd = $section['open_wait_weekday']; // день недели, когда доступен раздел
                $wait_days = $open_section_wd - $open_training_wd + ($open_section_wd < $open_training_wd ? 7 : 0); // сколько ждать дней до открытия раздела
                $wait_days += $section['open_skip_weekdays'] * 7;
                $date = strtotime(date('d.m.Y 00:00:00', $access_date_2training + $wait_days * 86400));
            }
        } elseif($section && $section['open_type'] ==  2 && $section['open_date']) { // если указана фиксированная дата
            $date = $section['open_date'] > $access_date_2training ? $section['open_date'] : $access_date_2training;
        }

        return $date ? $date : false;
    }


    /**
     * ПОЛУЧИТЬ ДАТУ ЗАКРЫТИЯ РАЗДЕЛА
     * @param $section
     * @param $open_date
     * @return bool|float|int|null
     */
    public static function getCloseDate($section, $open_date) {
        $date = null;

        if ($section['close_type'] ==  1 && $section['close_wait_days'] && $open_date) { // если указана относительная дата открытия
            $date = $open_date + 86400 * $section['close_wait_days'];
        } elseif($section['close_type'] ==  2 && $section['close_date']) { // если указана фиксированная дата
            $date = $section['close_date'];
        }

        return $date ? $date : false;
    }


    /**
     * ПОЛУЧИТЬ СТАТУС РАЗДЕЛА
     * @param $training
     * @param $section
     * @param $user_id
     * @param $user_groups
     * @param $user_planes
     * @return int
     * @throws Exception
     */
    public static function getSectionStatus($training, $section, $user_id, $user_groups, $user_planes) {
        $access = Training::getAccessData($user_groups, $user_planes, $training, $section);
        if (!Training::checkUserAccess($access) && $access['status'] !== Training::NO_ACCESS_TO_DATE) {
            return self::STATUS_SECTION_NOT_ACCESS;
        }

        $open_date = self::getOpenDate($training, $section, $user_id, $user_groups, $user_planes);
    //    $prev_section = null;
    //   if ($section['sort'] > 1) { // если есть предыдущий раздел
    //       $prev_section = self::getSectionBySort($section['sort'] - 1, $section['training_id']);
    //       $is_prev_section_complete = self::isSectionComplete($prev_section, $user_id, $open_date);
    //
    //        if (!$is_prev_section_complete) { // не пройден предыдущий раздел
    //           return self::STATUS_SECTION_PREV_SECTION_NOT_COMPLETE;
    //       }
    //   }

        if ($open_date && $open_date > time()) { // если дата открытия раздела еще не подошла
            self::$open_date = $open_date;
            return self::STATUS_SECTION_NOT_YET;
        }

        $is_complete = self::isSectionComplete($section, $user_id, $open_date);
        if ($is_complete) { // раздел пройден
            return self::STATUS_SECTION_COMPLETE;
        }

        return self::STATUS_SECTION_OPEN;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ СТУТУСА РАЗДЕЛА
     * @param $status
     * @param $training
     * @param $section
     * @return array
     */
    public static function getSectionStatusData($status, $training, $section) {
        $text = $class = $link = '';

        switch ($status) {
            case self::STATUS_SECTION_COMPLETE:
                $text = System::Lang('STATUS_SECTION_COMPLETE');
                $class = 'lesson-title-green-check';
                $link = "/training/view/{$training['alias']}/section/{$section['alias']}";
                break;
            case self::STATUS_SECTION_OPEN:
                $text = System::Lang('STATUS_SECTION_OPEN');
                $class = 'lesson-title-yellow-circle';
                $link = "/training/view/{$training['alias']}/section/{$section['alias']}";
                break;
            case self::STATUS_SECTION_NOT_YET:
                $text = System::Lang('STATUS_SECTION_NOT_YET');
                $class = 'lesson-title-lock';
                break;
            case self::STATUS_SECTION_PREV_SECTION_NOT_COMPLETE:
                $text = System::Lang('STATUS_SECTION_PREV_SECTION_NOT_COMPLETE');
                $class = 'lesson-title-lock';
                break;
            case self::STATUS_SECTION_NOT_ACCESS:
                $text = System::Lang('STATUS_SECTION_NOT_ACCESS');
                $class = 'lesson-title-lock modal-access';
                $link = "#ModalAccess";
                break;
        }

        $replace = [
            '[COUNT_LESSONS]' => self::getCountLessons($section['section_id']),
            '[OPEN_DATE]' => System::dateSpeller(self::$open_date),
        ];

        $text = strtr($text, $replace);

        return compact('text', 'class', 'link');
    }


    /**
     * ОПРЕДЕЛЕНИЕ ВЫВОДИТЬ КНОПКУ ПОКУПКИ ИЛИ НЕТ
     * @param $status
     * @return bool
     */
    public static function isShowByButton($status) {
        return $status === self::STATUS_SECTION_NOT_ACCESS ? false : true;
    }


    /**
     * УВЕДОМЛЕНИЯ ПОЛЬЗОВАТЕЛЯМ ПРИ ОТРЫТИИ РАЗДЕЛА
     * @param $users
     */
    public static function userNoticesToOpenSection($users) {
        $sections = self::getShouldOpenSections(null, 1); // получить разделы у которых фиксированная дата меньше или равна текущей или она относительная
        $training = null;

        if ($sections) {
            foreach ($sections as $section) {
                $training = !$training || $section['training_id'] != $training['training_id'] ? Training::getTraining($section['training_id']) : $training;

                if (!$training || (!$training['access_groups'] && !$training['access_planes'])) {
                    continue;
                }

                $replace = [
                    '[SECTION_NAME]' => $section['name'],
                ];

                $letter = json_encode([
                    'subject' => strtr(System::Lang("TRAINING_OPEN_SECTION_NOTICE_SUBJECT"), $replace),
                    'body' => System::Lang("TRAINING_OPEN_SECTION_NOTICE_BODY"),
                ]);

                foreach ($users as $user) {
                    if (Training::isActionCompleted($section['section_id'], $user['user_id'], Training::ELEMENT_TYPE_SECTION, Training::ACTION_OPEN_SECTION)) {
                        continue;
                    }

                    $user_groups = User::getGroupByUser($user['user_id']);
                    $user_planes = Member::getPlanesByUser($user['user_id'], 1, true);

                    $time = time();
                    $date = self::getOpenDate($training, $section, $user['user_id'], $user_groups, $user_planes);

                    if (!$date || $date > $time) {
                        continue;
                    }

                    $task = Responder::AddTask(0, 0, $user['email'], $time, 0, $letter);
                    if ($task) {
                        Training::addPerformedAction($section['section_id'], $user['user_id'], Training::ELEMENT_TYPE_SECTION, Training::ACTION_OPEN_SECTION);
                    }
                }
            }
        }
    }
}