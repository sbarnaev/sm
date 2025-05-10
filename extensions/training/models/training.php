<?php defined('BILLINGMASTER') or die;


class Training {

    use ResultMessage;

    const ELEMENT_TYPE_TRAINING = 1;
    const ELEMENT_TYPE_SECTION = 2;
    const ELEMENT_TYPE_LESSON = 3;

    const ACCESS_FREE = 0; // ТИП ДОСТУПА - СВОБОДНЫЙ
    const ACCESS_TO_GROUP = 1; // ТИП ДОСТУПА - ПО ГРУППЕ
    const ACCESS_TO_SUBS = 2; // ТИП ДОСТУПА - ПО ПОДПИСКЕ
    const ACCESS_TO_INHERIT = 3; // ТИП ДОСТУПА - НАСЛЕДОВАТЬ

    const NO_ACCESS_TO_GROUP = 4; // ТИП НЕТ ДОСТУПА - ПО ГРУППЕ
    const NO_ACCESS_TO_SUBS = 5; // ТИП НЕТ ДОСТУПА - ПО ПОДПИСКЕ
    const NO_ACCESS_TO_INHERIT = 6; // ТИП НЕТ ДОСТУПА - НАСЛЕДОВАТЬ
    const NO_ACCESS_TO_DATE = 7; // ТИП НЕТ ДОСТУПА - ПО ДАТЕ
    const NO_DATE_FOR_CALCULATION = 8; // ТИП НЕТ ДОСТУПА - ПО ДАТЕ (нет предыдущей даты для расчета даты октрытия)

    const USER_TYPE_ADMIN = 1;
    const USER_TYPE_CURATOR = 2;
    const USER_TYPE_USER = 3;

    const BY_BUTTON_TYPE_NOT_BUTTON = 0; // НЕТ КНОПКИ
    const BY_BUTTON_TYPE_PRODUCT_ORDER = 1; // ЗАКАЗ ТОВАРА
    const BY_BUTTON_TYPE_RATE = 2; // ВЫБОР ТАРИФА
    const BY_BUTTON_TYPE_PRODUCT_DESC = 3; // ОПИСАНИЕ ТОВАРА
    const BY_BUTTON_TYPE_YOUR_URL = 4; // СВОЙ URL your_url
    const BY_BUTTON_TYPE_PRODUCT_LENDING = 5; // ТОВАР - ЛЕНДИНГ
    const BY_BUTTON_IN_TO_TRAINING = 6; // КНОПКА ВОЙТИ В ТРЕНИНГ
    const BY_BUTTON_TYPE_PRODUCT_DESC_MODAL = 7; // ОПИСАНИЕ ТОВАРА

    const GROUP_DIST_DATE = 1; // ДАТА НАЗНАЧЕНИЯ ГРУППЫ
    const SUBS_DIST_DATE = 2; // ДАТА НАЗНАЧЕНИЯ ПОДПИСКИ
    const ENTER_IN_LESSON_DATE = 3; // ДАТА ВХОДА В УРОК
    const ANSWER_IN_LESSON_DATE = 4; // ДАТА ОТВЕТА В УРОКЕ
    const LESSON_COMPLETED_DATE = 5; // ДАТА ВЫПОЛНЕНИЯ УРОКА
    const START_DATE = 6; // ДАТА НАЧАЛА
    const END_DATE = 6; // ДАТА ОКОНЧАНИЯ

    const ACTION_OPEN_TRAINING = 1;
    const ACTION_OPEN_SECTION = 2;
    const ACTION_OPEN_LESSON = 3;
    const ACTION_TRAINING_COMPLETED_SM = 4; // ОТПРАВКА СООБЩЕНИЯ SEND_MESSAGE
    const ACTION_TRAINING_COMPLETED_GS = 5; // ГЕНЕРАЦИЯ СЕРТИФИКАТА ПО ОКОНЧАНИЮ ТРЕНИНГА GIVE_SERTIFICATE
    const ACTION_TRAINING_COMPLETED_GA = 6; // ВЫДАЧА ДОСТУПА ЮЗЕРУ ПО ОКОНЧАНИЮ ТРЕНИНАГА 


    /**
     * ОБРАБОТАТЬ ДАННЫЕ ПЕРЕД СОХРАНЕНИЕМ ДЛЯ АДМИНКИ
     * @param $fields
     * @param $data
     * @return mixed
     */
    public static function beforeSaveData2Admin($fields, $data) {
        foreach ($fields as $type => $keys) {
            foreach ($keys as $key) {
                switch($type) {
                    case 'safe': // данные без обработки
                        $data[$key] = isset($data[$key]) ? $data[$key] : null;
                        break;
                    case 'int': // приведение данных к integer
                        $data[$key] = isset($data[$key]) ? (int)$data[$key] : null;
                        break;
                    case 'str': // преобразование в данных html символов в соответствующие html сущности + удаление пробелов по краям.
                        $data[$key] = isset($data[$key]) ? htmlentities(trim($data[$key])) : null;
                        break;
                    case 'date': // преобразование даты в timestamp
                        $data[$key] = isset($data[$key]) ? strtotime($data[$key]) : null;
                        break;
                    case 'date2': // запонение данных текущей датой, если данные пусты (для даты начала)
                        if (!isset($data[$key])) {
                            $data[$key] = null;
                        } else {
                            $data[$key] = !empty($data[$key]) ? strtotime($data[$key]) : time();
                        }
                        break;
                    case 'date3': // запонение данных текущей датой на 15 лет вперед, если данные пусты (для даты окончания)
                        if (!isset($data[$key])) {
                            $data[$key] = null;
                        } else {
                            $start_date = isset($data['start_date']) ? $data['start_date'] : time();
                            $data[$key] = !empty($data[$key]) ? strtotime($data[$key]) : $start_date + 230720000; // 15 лет
                        }
                        break;
                    case 'json':
                        $data[$key] = isset($data[$key]) ? json_encode($data[$key]) : null;
                        break;
                }
            }
        }

        return $data;
    }


    /**
     * ОБРАБОТАТЬ ДАННЫЕ ПЕРЕД СОХРАНЕНИЕМ
     * @param $data
     * @param null $training_id
     * @return mixed
     */
    public static function beforeSaveTrainingData2Admin($data, $training_id = null) {
        $fields = [
            'safe' => [
                'access_planes', 'access_groups', 'curators', 'mastercurators'
            ],
            'int' => [
                'status', 'cat_id', 'is_free', 'count_free_lessons',
                'allow_user_notes', 'show_in_main', 'authors_can_edit',
                'curators_can_edit', 'access_type', 'show_in_lk2not_access',
                'access_task_type', 'start_type', 'show_before_start', 'finish_type',
                'duration_type', 'complexity', 'count_lessons_type',
                'count_lessons', 'sort_lessons', 'show_start_date', 'show_desc', 'show_price',
                'show_count_lessons', 'show_complexity', 'show_progress2list',
                'show_widget_training', 'show_widget_progress', 'show_in_lk2not_buy',
                'text_in_lk2not_buy', 'confirm_phone', 'binding_tg', 'show_watermark_phone',
                'show_watermark_email', 'lock_comment', 'on_public_homework',
                'curators_auto_assign, homework_edit, homework_comment_add', 'lessons_tmpl',
                'send_email_to_curator','send_email_to_all_curators','send_email_to_user','send_email_to_user_for_open_lesson', 'show_lesson_cover_2mobile',
            ],
            'str' => [
                'name', 'img_alt', 'padding', 'short_desc', 'full_desc', 'title', 'meta_desc',
                'alias', 'meta_keys', 'cover', 'start_date', 'end_date', 'price', 'duration',
                'subject_letter_to_curator','subject_letter_to_user',
                'subject_letter_to_user_for_open_lesson',
            ],
            'date' => [

            ],
            'date2' => [
                'start_date',
            ],
            'date3' => [
                'end_date',
            ],
            'json' => [
                'start_lessons', 'finish_lessons', 'access_task_groups', 'access_task_planes', 'big_button', 'small_button', 'authors',
                'by_button_curator_hw','by_button_autocheck_hw','by_button_self_hw', 'full_cover_param', 'sertificate' 
            ],
        ];

        $data = self::beforeSaveData2Admin($fields, $data);
        $data['alias'] = $data['alias'] ?: System::Translit($data['name']);
        if (System::searchDuplicateAliases($data['alias'], 'training', $training_id, 'training_id')) {
            $data['alias'] .= '-1';
        }

        $data['title'] = !empty($data['title']) ? $data['title'] : $data['name'];

        if ($data['access_type'] != 1) {
            $data['access_groups'] = null;
        }

        if ($data['access_type'] != 2) {
            $data['access_planes'] = null;
        }

        // TODO Пока это все временное и эксперементируемое будем переделывать наверное ))
        // пока не будет понятна окончательная схема
        if (isset($data['access_task_type_curator']) && $data['access_task_type_curator'] == 0){
            $access_task_curator['curator']['free'] = 0;
            $data['access_task_type_automat'] = 0; // Это что нижний блок условий сработал тоже на free, типа наследование!
        } elseif (isset($data['access_task_type_curator']) && $data['access_task_type_curator'] == 1){
            $access_task_curator['curator']['groups'] = isset($data['access_task_groups_for_curator']) ? $data['access_task_groups_for_curator'] : 0;
        } elseif (isset($data['access_task_type_curator']) && $data['access_task_type_curator'] == 2){
            $access_task_curator['curator']['planes'] = $data['access_task_planes_for_curator'];
        } else {
            $access_task_curator['curator']['free'] = 0;
        }


        if (isset($data['access_task_type_automat']) && $data['access_task_type_automat']==0){
            $access_task_automat['automat']['free'] = 0;
            $data['access_task_type_bezproverki'] = 0; // Это что нижний блок условий сработал тоже на free
        } elseif (isset($data['access_task_type_automat']) && $data['access_task_type_automat']==1){
            $access_task_automat['automat']['groups'] = isset($data['access_task_groups_for_automat']) ? $data['access_task_groups_for_automat'] : 0;
        } elseif (isset($data['access_task_type_automat']) && $data['access_task_type_automat']==2){
            $access_task_automat['automat']['planes'] = $data['access_task_planes_for_automat'];
        } else {
            $access_task_automat['automat']['free'] = 0;
        }

        if (isset($data['access_task_type_bezproverki']) && $data['access_task_type_bezproverki']==0){
            $access_task_automat_bezproverki['bezproverki']['free'] = 0;
        } elseif (isset($data['access_task_type_bezproverki']) && $data['access_task_type_bezproverki']==1){
            $access_task_automat_bezproverki['bezproverki']['groups'] = isset($data['access_task_groups_for_bezproverki']) ? $data['access_task_groups_for_bezproverki'] : 0;
        } elseif (isset($data['access_task_type_bezproverki']) && $data['access_task_type_bezproverki']==2){
            $access_task_automat_bezproverki['bezproverki']['planes'] = $data['access_task_planes_for_bezproverki'];
        } else {
            $access_task_automat_bezproverki['bezproverki']['free'] = 0;
        }

        $access_task_global = json_encode($access_task_curator + $access_task_automat + $access_task_automat_bezproverki);
        $data['access_task_global'] = $access_task_global;

        return $data;
    }


    /**
     * ДОБАВИТЬ ТРЕНИНГ
     * @param $data
     * @return bool|PDOStatement
     */
    public static function addTraining($data)
    {
        $time = time();
        $sort = self::getFreeSort();

        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'training (name, status, sort, cat_id, is_free, count_free_lessons, allow_user_notes,
                    img_alt, padding, show_in_main, authors_can_edit, curators_can_edit, short_desc, full_desc, access_type,
                    show_in_lk2not_access, access_task_type, start_type, show_before_start, finish_type, big_button, small_button,
                    start_date, duration_type, duration, complexity, end_date, count_lessons_type, count_lessons, sort_lessons,
                    show_start_date, show_desc, show_price, price, show_count_lessons, show_complexity, show_progress2list, show_widget_training,
                    show_widget_progress, show_in_lk2not_buy, text_in_lk2not_buy, confirm_phone, binding_tg, show_watermark_phone,
                    show_watermark_email, title, meta_desc, alias, meta_keys, access_task_groups, access_task_planes, access_task_global, cover,
                    full_cover, full_cover_param, curators_auto_assign, lock_comment, on_public_homework, homework_edit, homework_comment_add, lessons_tmpl, create_date, show_lesson_cover_2mobile)  
                             
                VALUES (:name, :status, :sort, :cat_id, :is_free, :count_free_lessons, :allow_user_notes,
                    :img_alt, :padding, :show_in_main, :authors_can_edit, :curators_can_edit, :short_desc, :full_desc, :access_type,
                    :show_in_lk2not_access, :access_task_type, :start_type, :show_before_start, :finish_type, :big_button,
                    :small_button, :start_date, :duration_type, :duration, :complexity, :end_date, :count_lessons_type, :count_lessons,
                    :sort_lessons, :show_start_date, :show_desc, :show_price, :price, :show_count_lessons, :show_complexity, :show_progress2list,
                    :show_widget_training, :show_widget_progress, :show_in_lk2not_buy, :text_in_lk2not_buy, :confirm_phone,
                    :binding_tg, :show_watermark_phone, :show_watermark_email, :title, :meta_desc, :alias, :meta_keys, :access_task_groups,
                    :access_task_planes, :access_task_global, :cover, :full_cover, :full_cover_param, :curators_auto_assign, :lock_comment, :on_public_homework,
                    :homework_edit, :homework_comment_add, :lessons_tmpl, :create_date, :show_lesson_cover_2mobile)';

        $result = $db->prepare($sql);
        $result->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $result->bindParam(':status', $data['status'], PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':cat_id', $data['cat_id'], PDO::PARAM_INT);
        $result->bindParam(':is_free', $data['is_free'], PDO::PARAM_INT);
        $result->bindParam(':count_free_lessons', $data['count_free_lessons'], PDO::PARAM_INT);
        $result->bindParam(':allow_user_notes', $data['allow_user_notes'], PDO::PARAM_INT);
        $result->bindParam(':img_alt', $data['img_alt'], PDO::PARAM_STR);
        $result->bindParam(':padding', $data['padding'], PDO::PARAM_STR);
        $result->bindParam(':show_in_main', $data['show_in_main'], PDO::PARAM_INT);
        $result->bindParam(':authors_can_edit', $data['authors_can_edit'], PDO::PARAM_INT);
        $result->bindParam(':curators_can_edit', $data['curators_can_edit'], PDO::PARAM_INT);
        $result->bindParam(':short_desc', $data['short_desc'], PDO::PARAM_STR);
        $result->bindParam(':full_desc', $data['full_desc'], PDO::PARAM_STR);
        $result->bindParam(':access_type', $data['access_type'], PDO::PARAM_INT);
        $result->bindParam(':show_in_lk2not_access', $data['show_in_lk2not_access'], PDO::PARAM_INT);
        $result->bindParam(':access_task_type', $data['access_task_type'], PDO::PARAM_INT);
        $result->bindParam(':start_type', $data['start_type'], PDO::PARAM_INT);
        $result->bindParam(':show_before_start', $data['show_before_start'], PDO::PARAM_INT);
        $result->bindParam(':finish_type', $data['finish_type'], PDO::PARAM_INT);
        $result->bindParam(':big_button', $data['big_button'], PDO::PARAM_STR);
        $result->bindParam(':small_button', $data['small_button'], PDO::PARAM_STR);
        $result->bindParam(':start_date', $data['start_date'], PDO::PARAM_INT);
        $result->bindParam(':duration_type', $data['duration_type'], PDO::PARAM_INT);
        $result->bindParam(':duration', $data['duration'], PDO::PARAM_STR);
        $result->bindParam(':complexity', $data['complexity'], PDO::PARAM_INT);
        $result->bindParam(':end_date', $data['end_date'], PDO::PARAM_INT);
        $result->bindParam(':count_lessons_type', $data['count_lessons_type'], PDO::PARAM_INT);
        $result->bindParam(':count_lessons', $data['count_lessons'], PDO::PARAM_INT);
        $result->bindParam(':sort_lessons', $data['sort_lessons'], PDO::PARAM_INT);
        $result->bindParam(':show_start_date', $data['show_start_date'], PDO::PARAM_INT);
        $result->bindParam(':show_desc', $data['show_desc'], PDO::PARAM_INT);
        $result->bindParam(':show_price', $data['show_price'], PDO::PARAM_INT);
        $result->bindParam(':price', $data['price'], PDO::PARAM_STR);
        $result->bindParam(':show_count_lessons', $data['show_count_lessons'], PDO::PARAM_INT);
        $result->bindParam(':show_complexity', $data['show_complexity'], PDO::PARAM_INT);
        $result->bindParam(':show_progress2list', $data['show_progress2list'], PDO::PARAM_INT);
        $result->bindParam(':show_widget_training', $data['show_widget_training'], PDO::PARAM_INT);
        $result->bindParam(':show_widget_progress', $data['show_widget_progress'], PDO::PARAM_INT);
        $result->bindParam(':show_in_lk2not_buy', $data['show_in_lk2not_buy'], PDO::PARAM_INT);
        $result->bindParam(':text_in_lk2not_buy', $data['text_in_lk2not_buy'], PDO::PARAM_INT);
        $result->bindParam(':confirm_phone', $data['confirm_phone'], PDO::PARAM_INT);
        $result->bindParam(':binding_tg', $data['binding_tg'], PDO::PARAM_INT);
        $result->bindParam(':show_watermark_phone', $data['show_watermark_phone'], PDO::PARAM_INT);
        $result->bindParam(':show_watermark_email', $data['show_watermark_email'], PDO::PARAM_INT);
        $result->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $data['meta_desc'], PDO::PARAM_STR);
        $result->bindParam(':alias', $data['alias'], PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $data['meta_keys'], PDO::PARAM_STR);
        $result->bindParam(':access_task_groups', $data['access_task_groups'], PDO::PARAM_STR);
        $result->bindParam(':access_task_planes', $data['access_task_planes'], PDO::PARAM_STR);
        $result->bindParam(':access_task_global', $data['access_task_global'], PDO::PARAM_STR);
        $result->bindParam(':cover', $data['cover'], PDO::PARAM_STR);
        $result->bindParam(':full_cover', $data['full_cover'], PDO::PARAM_STR);
        $result->bindParam(':full_cover_param', $data['full_cover_param'], PDO::PARAM_STR);
        $result->bindParam(':curators_auto_assign', $data['curators_auto_assign'], PDO::PARAM_INT);
        $result->bindParam(':lock_comment', $data['lock_comment'], PDO::PARAM_INT);
        $result->bindParam(':homework_edit', $data['homework_edit'], PDO::PARAM_INT);
        $result->bindParam(':homework_comment_add', $data['homework_comment_add'], PDO::PARAM_INT);
        $result->bindParam(':on_public_homework', $data['on_public_homework'], PDO::PARAM_INT);
        $result->bindParam(':lessons_tmpl', $data['lessons_tmpl'], PDO::PARAM_INT);
        $result->bindParam(':create_date', $time, PDO::PARAM_INT);
        $result->bindParam(':show_lesson_cover_2mobile', $data['show_lesson_cover_2mobile'], PDO::PARAM_INT);
        $result = $result->execute();

        $training_id = $result ? $db->lastInsertId() : null;
        if ($training_id) {
            self::saveAccessGroups($training_id, $data['access_groups']);
            self::saveAccessPlanes($training_id, $data['access_planes']);
            self::saveCuratorsToTraining($training_id, $data['mastercurators'], $data['curators']);
            $result = self::saveAuthorsToTraining($data['authors'], $training_id);
        }

        return $result ? $training_id : false;
    }


    /**
     * ИЗМЕНИТЬ ТРЕНИНГ
     * @param $id
     * @param $data
     * @return bool|PDOStatement
     */
    public static function editTraining($id, $data)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'training SET name = :name, status = :status, cat_id = :cat_id, is_free = :is_free,
                count_free_lessons = :count_free_lessons, allow_user_notes = :allow_user_notes, img_alt = :img_alt, sertificate = :sertificate,
                padding = :padding, show_in_main = :show_in_main, authors_can_edit = :authors_can_edit, curators_can_edit = :curators_can_edit,
                short_desc = :short_desc, full_desc = :full_desc, access_type = :access_type, show_in_lk2not_access = :show_in_lk2not_access,
                access_task_type = :access_task_type, start_type = :start_type, show_before_start = :show_before_start, start_lessons = :start_lessons,
                finish_type = :finish_type, finish_lessons = :finish_lessons, big_button = :big_button, small_button = :small_button,
                start_date = :start_date, duration_type = :duration_type, duration = :duration, complexity = :complexity, end_date = :end_date,
                count_lessons_type = :count_lessons_type, count_lessons = :count_lessons, sort_lessons = :sort_lessons, show_start_date = :show_start_date,
                show_desc = :show_desc, show_price = :show_price, price = :price, show_count_lessons = :show_count_lessons, show_complexity = :show_complexity,
                show_progress2list = :show_progress2list, show_widget_training = :show_widget_training, show_widget_progress = :show_widget_progress,
                show_in_lk2not_buy = :show_in_lk2not_buy, text_in_lk2not_buy = :text_in_lk2not_buy, confirm_phone = :confirm_phone,
                binding_tg = :binding_tg, show_watermark_phone = :show_watermark_phone, show_watermark_email = :show_watermark_email, title = :title,
                meta_desc = :meta_desc, alias = :alias, meta_keys = :meta_keys, access_task_groups = :access_task_groups,
                access_task_planes = :access_task_planes, access_task_global = :access_task_global, cover = :cover, full_cover =:full_cover, 
                full_cover_param =:full_cover_param, curators_auto_assign =:curators_auto_assign, lock_comment = :lock_comment, 
                on_public_homework = :on_public_homework, homework_edit = :homework_edit, homework_comment_add = :homework_comment_add, 
                by_button_curator_hw =:by_button_curator_hw, by_button_autocheck_hw =:by_button_autocheck_hw,
                by_button_self_hw =:by_button_self_hw, lessons_tmpl = :lessons_tmpl, subject_letter_to_curator = :subject_letter_to_curator,
                letter_to_curator = :letter_to_curator, subject_letter_to_user = :subject_letter_to_user, 
                letter_to_user = :letter_to_user, subject_letter_to_user_for_open_lesson = :subject_letter_to_user_for_open_lesson,
                letter_to_user_for_open_lesson = :letter_to_user_for_open_lesson, send_email_to_curator = :send_email_to_curator,
                send_email_to_all_curators = :send_email_to_all_curators, send_email_to_user = :send_email_to_user,
                send_email_to_user_for_open_lesson = :send_email_to_user_for_open_lesson, show_lesson_cover_2mobile = :show_lesson_cover_2mobile WHERE training_id = '.$id;

        $result = $db->prepare($sql);
        $result->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $result->bindParam(':status', $data['status'], PDO::PARAM_INT);
        $result->bindParam(':cat_id', $data['cat_id'], PDO::PARAM_INT);
        $result->bindParam(':is_free', $data['is_free'], PDO::PARAM_INT);
        $result->bindParam(':count_free_lessons', $data['count_free_lessons'], PDO::PARAM_INT);
        $result->bindParam(':allow_user_notes', $data['allow_user_notes'], PDO::PARAM_INT);
        $result->bindParam(':img_alt', $data['img_alt'], PDO::PARAM_STR);
        $result->bindParam(':sertificate', $data['sertificate'], PDO::PARAM_STR);
        $result->bindParam(':padding', $data['padding'], PDO::PARAM_STR);
        $result->bindParam(':show_in_main', $data['show_in_main'], PDO::PARAM_INT);
        $result->bindParam(':authors_can_edit', $data['authors_can_edit'], PDO::PARAM_INT);
        $result->bindParam(':curators_can_edit', $data['curators_can_edit'], PDO::PARAM_INT);
        $result->bindParam(':short_desc', $data['short_desc'], PDO::PARAM_STR);
        $result->bindParam(':full_desc', $data['full_desc'], PDO::PARAM_STR);
        $result->bindParam(':access_type', $data['access_type'], PDO::PARAM_INT);
        $result->bindParam(':show_in_lk2not_access', $data['show_in_lk2not_access'], PDO::PARAM_INT);
        $result->bindParam(':access_task_type', $data['access_task_type'], PDO::PARAM_INT);
        $result->bindParam(':start_type', $data['start_type'], PDO::PARAM_INT);
        $result->bindParam(':start_lessons', $data['start_lessons'], PDO::PARAM_STR);
        $result->bindParam(':show_before_start', $data['show_before_start'], PDO::PARAM_STR);
        $result->bindParam(':finish_type', $data['finish_type'], PDO::PARAM_INT);
        $result->bindParam(':finish_lessons', $data['finish_lessons'], PDO::PARAM_STR);
        $result->bindParam(':big_button', $data['big_button'], PDO::PARAM_STR);
        $result->bindParam(':small_button', $data['small_button'], PDO::PARAM_STR);
        $result->bindParam(':start_date', $data['start_date'], PDO::PARAM_INT);
        $result->bindParam(':duration_type', $data['duration_type'], PDO::PARAM_INT);
        $result->bindParam(':duration', $data['duration'], PDO::PARAM_STR);
        $result->bindParam(':complexity', $data['complexity'], PDO::PARAM_INT);
        $result->bindParam(':end_date', $data['end_date'], PDO::PARAM_INT);
        $result->bindParam(':count_lessons_type', $data['count_lessons_type'], PDO::PARAM_INT);
        $result->bindParam(':count_lessons', $data['count_lessons'], PDO::PARAM_INT);
        $result->bindParam(':sort_lessons', $data['sort_lessons'], PDO::PARAM_INT);
        $result->bindParam(':show_start_date', $data['show_start_date'], PDO::PARAM_INT);
        $result->bindParam(':show_desc', $data['show_desc'], PDO::PARAM_INT);
        $result->bindParam(':show_price', $data['show_price'], PDO::PARAM_INT);
        $result->bindParam(':price', $data['price'], PDO::PARAM_STR);
        $result->bindParam(':show_count_lessons', $data['show_count_lessons'], PDO::PARAM_INT);
        $result->bindParam(':show_complexity', $data['show_complexity'], PDO::PARAM_INT);
        $result->bindParam(':show_progress2list', $data['show_progress2list'], PDO::PARAM_INT);
        $result->bindParam(':show_widget_training', $data['show_widget_training'], PDO::PARAM_INT);
        $result->bindParam(':show_widget_progress', $data['show_widget_progress'], PDO::PARAM_INT);
        $result->bindParam(':show_in_lk2not_buy', $data['show_in_lk2not_buy'], PDO::PARAM_INT);
        $result->bindParam(':text_in_lk2not_buy', $data['text_in_lk2not_buy'], PDO::PARAM_INT);
        $result->bindParam(':confirm_phone', $data['confirm_phone'], PDO::PARAM_INT);
        $result->bindParam(':binding_tg', $data['binding_tg'], PDO::PARAM_INT);
        $result->bindParam(':show_watermark_phone', $data['show_watermark_phone'], PDO::PARAM_INT);
        $result->bindParam(':show_watermark_email', $data['show_watermark_email'], PDO::PARAM_INT);
        $result->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $data['meta_desc'], PDO::PARAM_STR);
        $result->bindParam(':alias', $data['alias'], PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $data['meta_keys'], PDO::PARAM_STR);
        $result->bindParam(':access_task_groups', $data['access_task_groups'], PDO::PARAM_STR);
        $result->bindParam(':access_task_planes', $data['access_task_planes'], PDO::PARAM_STR);
        $result->bindParam(':access_task_global', $data['access_task_global'], PDO::PARAM_STR);
        $result->bindParam(':cover', $data['cover'], PDO::PARAM_STR);
        $result->bindParam(':full_cover', $data['full_cover'], PDO::PARAM_STR);
        $result->bindParam(':full_cover_param', $data['full_cover_param'], PDO::PARAM_STR);
        $result->bindParam(':curators_auto_assign', $data['curators_auto_assign'], PDO::PARAM_INT);
        $result->bindParam(':lock_comment', $data['lock_comment'], PDO::PARAM_INT);
        $result->bindParam(':homework_edit', $data['homework_edit'], PDO::PARAM_INT);
        $result->bindParam(':homework_comment_add', $data['homework_comment_add'], PDO::PARAM_INT);
        $result->bindParam(':on_public_homework', $data['on_public_homework'], PDO::PARAM_INT);
        $result->bindParam(':by_button_curator_hw', $data['by_button_curator_hw'], PDO::PARAM_STR);
        $result->bindParam(':by_button_autocheck_hw', $data['by_button_autocheck_hw'], PDO::PARAM_STR);
        $result->bindParam(':by_button_self_hw', $data['by_button_self_hw'], PDO::PARAM_STR);
        $result->bindParam(':lessons_tmpl', $data['lessons_tmpl'], PDO::PARAM_STR);
        $result->bindParam(':subject_letter_to_curator', $data['subject_letter_to_curator'], PDO::PARAM_STR);
        $result->bindParam(':letter_to_curator', $data['letter_to_curator'], PDO::PARAM_STR);
        $result->bindParam(':subject_letter_to_user', $data['subject_letter_to_user'], PDO::PARAM_STR);
        $result->bindParam(':letter_to_user', $data['letter_to_user'], PDO::PARAM_STR);
        $result->bindParam(':subject_letter_to_user_for_open_lesson', $data['subject_letter_to_user_for_open_lesson'], PDO::PARAM_STR);
        $result->bindParam(':letter_to_user_for_open_lesson', $data['letter_to_user_for_open_lesson'], PDO::PARAM_STR);
        $result->bindParam(':send_email_to_curator', $data['send_email_to_curator'], PDO::PARAM_INT);
        $result->bindParam(':send_email_to_all_curators', $data['send_email_to_all_curators'], PDO::PARAM_INT);
        $result->bindParam(':send_email_to_user', $data['send_email_to_user'], PDO::PARAM_INT);
        $result->bindParam(':send_email_to_user_for_open_lesson', $data['send_email_to_user_for_open_lesson'], PDO::PARAM_INT);
        $result->bindParam(':show_lesson_cover_2mobile', $data['show_lesson_cover_2mobile'], PDO::PARAM_INT);
        

        $result = $result->execute();
        if ($result) {
            self::saveAccessGroups($id, $data['access_groups']);
            self::saveAccessPlanes($id, $data['access_planes']);
            self::saveCuratorsToTraining($id, $data['mastercurators'], $data['curators']);

            $result = self::saveAuthorsToTraining($data['authors'], $id);
        }

        return $result;
    }


    /**
     * ПОЛУЧИТЬ СОРТИРОВКУ ДЛЯ ДОБАВЛЯЕМОГО ТРЕНИНГА
     * @return int
     */
    public static function getFreeSort() {
        $db = Db::getConnection();
        $result = $db->query("SELECT MAX(sort) FROM ".PREFICS."training");
        $count = $result->fetch();

        return (int)$count[0] + 1;
    }


    /**
     * НАЗВАНИЕ ТРЕНИНГА
     * @param $training_id
     * @return bool
     */
    public static function getTrainingNameByID($training_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT name FROM ".PREFICS."training WHERE training_id = $training_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['name'] : false;
    }


    /**
     * ПОЛУЧИТЬ ТРЕНИНГ ПО ID
     * @param $id
     * @return bool|mixed
     */
    public static function getTraining($id)
    {
        $db = Db::getConnection();

        $query = "SELECT t.*, CONCAT('[',GROUP_CONCAT(tag.group_id),']') AS access_groups, CONCAT('[',GROUP_CONCAT(tap.plane_id),']') AS access_planes,
                  GROUP_CONCAT(DISTINCT ait.author_id) AS authors FROM ".PREFICS."training as t
                  LEFT JOIN ".PREFICS."training_access_groups AS tag
                  ON tag.training_id = t.training_id
                  LEFT JOIN ".PREFICS."training_access_planes AS tap
                  ON tap.training_id = t.training_id
                  LEFT JOIN ".PREFICS."training_authors_in_training AS ait
                  ON ait.training_id = t.training_id
                  WHERE t.training_id = $id LIMIT 1";

        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return isset($data['training_id']) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ТРЕНИННГ ПО АЛИАСУ
     * @param $alias
     * @return bool|mixed
     */
    public static function getTrainingByAlias($alias)
    {
        $db = Db::getConnection();
        $query = "SELECT t.*, CONCAT('[',GROUP_CONCAT(tag.group_id),']') AS access_groups, CONCAT('[',GROUP_CONCAT(tap.plane_id),']') AS access_planes,
                  GROUP_CONCAT(DISTINCT ait.author_id) AS authors FROM ".PREFICS."training as t
                  LEFT JOIN ".PREFICS."training_access_groups AS tag
                  ON tag.training_id = t.training_id
                  LEFT JOIN ".PREFICS."training_access_planes AS tap
                  ON tap.training_id = t.training_id
                  LEFT JOIN ".PREFICS."training_authors_in_training AS ait
                  ON ait.training_id = t.training_id
                  WHERE t.alias = :alias GROUP BY t.training_id LIMIT 1";

        $result = $db->prepare($query);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }

     /**
     * ПОЛУЧИТЬ ID ТРЕНИННГА ПО LessonID
     * @param $lesson_id
     * @return bool|mixed
     */
    public static function getTrainingIdByLessonId($lesson_id)
    {
        $db = Db::getConnection();
        $query = "SELECT training_id FROM ".PREFICS."training_lessons WHERE lesson_id = :lesson_id LIMIT 1";

        $result = $db->prepare($query);
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['training_id'] : 0;
    }


    /**
     * ОБНОВИТЬ СОРТИРОВКУ У ТРЕНИНГА
     * @param $training_id
     * @param $sort
     * @return bool
     */
    public static function updSorTraining($training_id, $sort) {
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE '.PREFICS.'training SET sort = :sort WHERE training_id = :training_id');

        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * СОХРАНИТЬ ГРУППЫ ДОСТУПА К ТРЕНИНГУ
     * @param $training_id
     * @param $access_groups
     * @return bool|false|PDOStatement
     */
    private static function saveAccessGroups($training_id, $access_groups) {
        $db = Db::getConnection();

        $result = $db->query("DELETE FROM ".PREFICS."training_access_groups WHERE training_id = $training_id");
        if ($result && $access_groups) {
            foreach ($access_groups as $key => $access_group) {
                $result = $db->prepare('INSERT INTO '.PREFICS.'training_access_groups (training_id, group_id) VALUES (:training_id, :group_id)');
                $result->bindParam(':training_id', $training_id);
                $result->bindParam(':group_id', $access_group);
                $result->execute();
            }
        }

        return $result;
    }


    /**
     * СОХРАНИТЬ ПОДПИСКИ ДЛЯ ДОСТУПА К ТРЕНИНГУ
     * @param $training_id
     * @param $access_planes
     * @return bool|false|PDOStatement
     */
    private static function saveAccessPlanes($training_id, $access_planes) {
        $db = Db::getConnection();

        $result = $db->query("DELETE FROM ".PREFICS."training_access_planes WHERE training_id = $training_id");
        if ($result && $access_planes) {
            foreach ($access_planes as $key => $access_plane) {
                $result = $db->prepare('INSERT INTO '.PREFICS.'training_access_planes (training_id, plane_id) VALUES (:training_id, :plane_id)');
                $result->bindParam(':training_id', $training_id);
                $result->bindParam(':plane_id', $access_plane);
                $result->execute();
            }
        }

        return $result;
    }


    /**
     * СОХРАНИТЬ АВТОРОВ ДЛЯ ТРЕНИНГА
     * @param $authors
     * @param $training_id
     * @return bool|PDOStatement
     */
    private static function saveAuthorsToTraining($authors, $training_id) {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS."training_authors_in_training WHERE training_id = :training_id";
        $result = $db->prepare($sql);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $_result = $result->execute();

        if ($_result && $authors) {
            foreach (json_decode($authors, true) as $author_id) {
                $sql = 'INSERT INTO '.PREFICS.'training_authors_in_training (author_id, training_id) VALUES (:author_id, :training_id)';
                $result = $db->prepare($sql);
                $result->bindParam(':author_id', $author_id, PDO::PARAM_INT);
                $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);

                if (!$result->execute()) {
                    $_result = false;
                }
            }
        }

        return $_result;
    }


    /**
     * СОХРАНИТЬ КУРТОРОВ ДЛЯ ТРЕНИНГА
     * @param $training_id
     * @param $mastercurators
     * @param $curators
     * @param $section_id
     * @return bool|PDOStatement
     */
    public static function saveCuratorsToTraining($training_id, $mastercurators, $curators, $section_id = 0) {
        $db = Db::getConnection();
        $sql = "DELETE FROM ".PREFICS."training_curators_in_training WHERE training_id = :training_id AND section_id = :section_id";
        $result = $db->prepare($sql);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        $_result = $result->execute();

        if ($_result && $mastercurators) {
            foreach ($mastercurators as $mastercurator) {
                $is_master = true;
                if ($curators){
                    $is_assign = in_array($mastercurator, $curators); 
                } else {
                    $is_assign = false;
                }
                $sql = 'INSERT INTO '.PREFICS.'training_curators_in_training (curator_id, training_id, section_id, is_master, assing_to_users) 
                VALUES (:curator_id, :training_id, :section_id, :is_master, :assing_to_users)'; 
                $result = $db->prepare($sql);
                $result->bindParam(':curator_id', $mastercurator, PDO::PARAM_INT);
                $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
                $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);
                if ($is_assign){
                    $key_for_delete = array_search($mastercurator, $curators); 
                    unset($curators[$key_for_delete]);
                    $result->bindParam(':is_master', $is_master, PDO::PARAM_INT );
                    $result->bindParam(':assing_to_users', $is_assign, PDO::PARAM_INT );
                } else {
                    $result->bindParam(':is_master', $is_master, PDO::PARAM_INT );
                    $result->bindParam(':assing_to_users', $is_assign, PDO::PARAM_INT );
                }
                $result->execute();
            }
        }

        if ($_result && $curators) {
            foreach ($curators as $curator) {
                $is_master = false;
                $is_assign = true;
                $sql = 'INSERT INTO '.PREFICS.'training_curators_in_training (curator_id, training_id, section_id, is_master, assing_to_users) 
                VALUES (:curator_id, :training_id, :section_id, :is_master, :assing_to_users)'; 
                $result = $db->prepare($sql);
                $result->bindParam(':curator_id', $curator, PDO::PARAM_INT);
                $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
                $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);
                $result->bindParam(':is_master', $is_master, PDO::PARAM_INT );
                $result->bindParam(':assing_to_users', $is_assign, PDO::PARAM_INT );
                $result->execute();
            }
        }

        return $_result;
    }

     /**
     * УДАЛИТЬ КУРТОРОВ ТРЕНИНГА
     * @param $training_id
     * @param $section_id
     * @return bool
     */
    public static function delCuratorsTraining($training_id, $section_id = 0) {
        $db = Db::getConnection();
        $sql = "DELETE FROM ".PREFICS."training_curators_in_training WHERE training_id = :training_id AND section_id = :section_id";
        $result = $db->prepare($sql);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        return $result->execute();
 
    }


    /**
     * ПРОДОЛЖИТЕЛЬНОСТЬ ТРЕНИНГА
     * @param $training
     * @return bool|mixed
     */
    public static function countDurationByTraining($training)
    {
        if ($training['duration_type'] == 1) {
            // считаем уроки в минутах
            $db = Db::getConnection();
            $result = $db->query("SELECT SUM(duration) FROM ".PREFICS."training_lessons WHERE training_id = ".$training['training_id']);
            $count = $result->fetch();

            $hours = floor($count[0] / 60); // Считаем количество полных часов
            $minutes = $count[0] - ($hours * 60);  // Считаем количество оставшихся минут
            $result = $hours.' ч. '.$minutes.' мин.';
            
            return $result;

        } elseif ($training['duration_type'] == 2) {
            return $training['duration'];
        }
    }


    /**
     * КОЛ_ВО ПРОСМОТРОВ ТРЕНИНГА
     * @param $training_id
     * @return mixed
     */
    public static function countHitsByTraining($training_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT SUM(hits) FROM ".PREFICS."training_lessons WHERE training_id = $training_id ");
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * СПИСОК ТРЕНИНГОВ
     * @param null $cat_id_data
     * @param null $sort
     * @param bool $filter
     * @param int $status
     * @return array|bool
     */
    public static function getTrainingList($cat_id_data = null, $sort = null, $filter = false, $status = 1)
    {
        $db = Db::getConnection();
        $sort = $sort ? 'DESC' : 'ASC';

        $clauses = [];

        if ($cat_id_data !== null) {
            $cat_id_data = System::getSecureData($cat_id_data);
            $clauses[] = 'cat_id ' . (is_array($cat_id_data) ? 'IN ('.implode(',', $cat_id_data).')' : "= $cat_id_data");
        }

        if ($filter) {
            foreach ($filter as $key => $value) {
                if ($key != 'access') {
                    $value = System::getSecureData($value);
                }

                switch ($key) {
                    case 'access':
                        if(!empty($value)){
                            $clauses[] = 't.is_free = ' . ($value == 'free' ? 1 : 0);
                        }
                        break;
                    case 'author':
                        if (is_array($value) && !empty($value)) {
                            $clauses[] = 'ait.author_id IN (' . implode(',', $value) . ')';
                        }
                        break;
                    case 'category':
                        if (is_array($value) && !empty($value)) {
                            $clauses[] = 't.cat_id IN (' . implode(',', $value) . ')';
                        }
                        break;
                    case 'user_groups':
                        if (is_array($value) && !empty($value)) {
                            $clauses[] = 'tag.group_id IN (' . implode(',', $value) . ')';
                        }
                        break;
                    case 'user_planes':
                        if (is_array($value) && !empty($value)) {
                            $clauses[] = 'tap.plane_id IN (' . implode(',', $value) . ')';
                        }
                        break;
                }
            }
        };

        if ($status !== null) {
            $clauses[] = 'status = '.$status;
        }

        $where = !empty($clauses) ? 'WHERE ' . implode(' AND ' , $clauses) : '';

        $query = "SELECT t.*, CONCAT('[',GROUP_CONCAT(tag.group_id),']') AS access_groups,
                  CONCAT('[',GROUP_CONCAT(tap.plane_id),']') AS access_planes, GROUP_CONCAT(DISTINCT ait.author_id) AS authors
                  FROM ".PREFICS."training AS t
                  LEFT JOIN ".PREFICS."training_access_groups AS tag ON tag.training_id = t.training_id
                  LEFT JOIN ".PREFICS."training_access_planes AS tap ON tap.training_id = t.training_id
                  LEFT JOIN ".PREFICS."training_authors_in_training AS ait ON ait.training_id = t.training_id $where
                  GROUP BY t.training_id ORDER BY t.sort, t.training_id $sort";

        $result = $db->query($query);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    public static function getTrainingsToUser($user_groups, $user_planes, $user_id, $status = 1, $sort = null)
    {
        $db = Db::getConnection();
        $sort = $sort ? 'DESC' : 'ASC';

        $clauses = [];
        if ($user_groups) {
            $clauses[] = 'tag.group_id IN (' . implode(',', $user_groups) . ')';
        }

        if ($user_planes) {
            $clauses[] = 'tap.plane_id IN (' . implode(',', $user_planes) . ')';
        }

        $where = 'WHERE t.status = 1 AND ('.implode(' OR ' , $clauses).' OR t.training_id 
        IN(SELECT DISTINCT training_id from '.PREFICS.'training_user_map WHERE user_id = '.$user_id.')) ';
        $query = "SELECT t.*, CONCAT('[',GROUP_CONCAT(tag.group_id),']') AS access_groups,
                  CONCAT('[',GROUP_CONCAT(tap.plane_id),']') AS access_planes, GROUP_CONCAT(DISTINCT ait.author_id) AS authors
                  FROM ".PREFICS."training AS t
                  LEFT JOIN ".PREFICS."training_access_groups AS tag ON tag.training_id = t.training_id
                  LEFT JOIN ".PREFICS."training_access_planes AS tap ON tap.training_id = t.training_id
                  LEFT JOIN ".PREFICS."training_authors_in_training AS ait ON ait.training_id = t.training_id
                  $where GROUP BY t.training_id ORDER BY t.sort, t.training_id $sort";

        $result = $db->query($query);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * СПИСОК ТРЕНИНГОВ ДЛЯ СПИСКА
     * @param null $status
     * @return array|bool
     */
    public static function getTrainingListToList($status = null)
    {
        $db = Db::getConnection();
        $where = $status !== null ? " WHERE status = $status" : '';
        $query = "SELECT training_id, name FROM ".PREFICS."training $where ORDER BY name DESC";
        $result = $db->query($query);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧЕНИЕ СПИСКА ТРЕНИНГОВ ИЗ ЮЗЕР МАП ПО КОНКРЕТНОМУ ЮЗЕРУ
     * @param $user_id
     * @return array|bool
     */
    public static function getTrainingFromUserMap($user_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."training WHERE training_id IN 
                            (SELECT DISTINCT training_id FROM ".PREFICS."training_user_map WHERE user_id = $user_id)");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }




    /**
     * КОЛ-ВО ТРЕНИНГОВ В КАТЕГОРИИ
     * @param $cat_id
     * @param $status
     * @return bool
     */
    public static function countTrainingInCategory($cat_id, $status)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(training_id) FROM ".PREFICS."training WHERE cat_id = $cat_id AND status = $status");
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * УДАЛИТЬ ТРЕНИНГ
     * @param $training_id
     * @return bool
     */
    public static function delTraining($training_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(lesson_id) FROM ".PREFICS."training_lessons WHERE training_id = $training_id");
        $count = $result->fetch();

        if ($count[0] == 0) {
            $sql =  "DELETE FROM ".PREFICS."training_curators_in_training WHERE training_id = :id;";
            $sql .= "DELETE FROM ".PREFICS."training WHERE training_id = :id;";
            $sql .= "DELETE FROM ".PREFICS."training_curator_to_user WHERE training_id = :id;";
            $sql .= "DELETE FROM ".PREFICS."training_blocks WHERE training_id = :id;";
            $sql .= "DELETE FROM ".PREFICS."training_sections WHERE training_id = :id;";
            $sql .= "DELETE FROM ".PREFICS."training_user_map WHERE training_id = :id;";
            $sql .= "DELETE FROM ".PREFICS."training_authors_in_training WHERE training_id = :id;";
            $sql .= "DELETE FROM ".PREFICS."training_access_groups WHERE training_id = :id;";
            $sql .= "DELETE FROM ".PREFICS."training_access_planes WHERE training_id = :id;";

            $result = $db->prepare($sql);
            $result->bindParam(':id', $training_id, PDO::PARAM_INT);

            return $result->execute();
        }

        return $training_id;
    }

     /**
     * ПОЛУЧИТЬ СПИСОК КУРАТОРОВ ТРЕНИНГА
     * @param $training_id
     * @param $section_id
     * @return bool|mixed
     */
    public static function getCuratorsTraining($training_id, $section_id = 0)
    {
        $db = Db::getConnection();
        $sql = "SELECT curator_id, is_master, assing_to_users FROM ".PREFICS."training_curators_in_training WHERE 
        training_id = :training_id AND section_id = :section_id";
        $result = $db->prepare($sql);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);

        $result->execute();
        $datamaster = [];
        $datacurators = [];
        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($row['is_master']==1) {
                $datamaster[] = $row['curator_id'];
            }
            if ($row['assing_to_users']==1){
                $datacurators[] = $row['curator_id'];
            }
        }

        $data['datamaster'] = $datamaster;
        $data['datacurators'] = $datacurators;

        return !empty($data) ? $data : false;
    }

     /**
     * ПОЛУЧИТЬ СПИСОК КУРАТОРОВ ТРЕНИНГА ДЛЯ СЕКЦИИ
     * @param $training_id
     * @return bool|mixed
     */
    public static function getCuratorsTrainingForSection($training_id)
    {
        $is_assign = true;
        $db = Db::getConnection();
        $sql = "SELECT t1.curator_id as user_id, t2.user_name as user_name, t2.surname as surname FROM ".PREFICS."training_curators_in_training as t1
        LEFT JOIN ".PREFICS."users as t2 ON t1.curator_id = t2.user_id WHERE t1.training_id = :training_id AND t1.assing_to_users = :is_assign
        AND section_id = 0";
        $result = $db->prepare($sql);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':is_assign', $is_assign, PDO::PARAM_INT);

        $result->execute();
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
       
        return !empty($data) ? $data : false;
    }


    /*======================ДОСТУПЫ======================*/


    /**
     * ОПРЕДЕЛЕНИЕ ДОСТУПА К КУРСУ/РАЗДЕЛУ/УРОКУ
     * @param $access
     * @return bool
     */
    public static function checkUserAccess($access) {
        $status = is_array($access) ? $access['status'] : $access;

        return $status === true ? true : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ДОСТУПА
     * @param $user_groups
     * @param $user_planes
     * @param $training
     * @param null $section
     * @param null $lesson
     * @return array
     * @throws Exception
     */
    public static function getAccessData($user_groups, $user_planes, $training, $section = null, $lesson = null) {
        $user_id = intval(User::isAuth());
        $user = User::getUserById($user_id);
        $status = false;
        $start_date = 0;
        $is_admin = isset($_SESSION['admin_token']) && !isset($_SESSION['user']) ? true : false;

        if ($user['is_curator'] || $is_admin) {
            $section_id = empty($section) ? 0 : $section['section_id'];
            if ($is_admin || self::isCuratorInTrainingInSection($user_id, $training['training_id'], $section_id)) {
                return ['status' => true, $is_admin ? 'is_admin' : 'is_curator' => true];
            }
        }

        if ($lesson !== null && $lesson['access_type'] != self::ACCESS_TO_INHERIT) {
            $status = self::getAccessStatus($lesson, $user_groups, $user_planes);
        } elseif ($section && $section['access_type'] != self::ACCESS_TO_INHERIT) {
            $status = self::getAccessStatus($section, $user_groups, $user_planes);
        } else {
            $status = self::getAccessStatus($training, $user_groups, $user_planes);
        }

        $section_open_date = null;
        if ($training['is_free'] || self::checkUserAccess($status)) {
            if ($section !== null) {
                $section_open_date = TrainingSection::getOpenDate($training, $section, $user_id, $user_groups, $user_planes);
            }

            if ($lesson !== null) {
                if (isset($lesson['shedule']) && $lesson['shedule'] == 2) {
                    // открытие урока по расписанию
                    $a = 1;
                    $data = TrainingLesson::getLessonOpenDate($training, $lesson, $user_id, $user_groups, $user_planes);
                    $start_date = $data['start_date'];

                    if ($start_date) {
                        $status = self::NO_ACCESS_TO_DATE;
                        return compact('status', 'start_date');
                    } elseif ($data['status'] === 8) {
                        $status = self::NO_DATE_FOR_CALCULATION;
                        return compact('status', 'start_date');
                    }
                }
            }

            if ($training['start_date'] > time() || $section_open_date && $section_open_date > time()) {
                $status = self::NO_ACCESS_TO_DATE;
                $start_date = $section_open_date ? $section_open_date : $training['start_date'];
            }
        }

        return compact('status', 'start_date');
    }

    /**
     * ПОЛУЧИТЬ СТАТУС ДОСТУПА
     * @param $data
     * @param $user_groups
     * @param $user_planes
     * @return bool
     */
    private static function getAccessStatus($data, $user_groups, $user_planes) {
        
        if ($data === false) {
            return false;
        }

        if ($data['access_type'] == self::ACCESS_FREE) {
            return true;
        }

        if ($data['access_type'] == self::ACCESS_TO_GROUP) {
            if ($user_groups) {
                $access_groups = json_decode($data['access_groups'], true);
                if ($access_groups) {
                    foreach ($user_groups as $group) {
                        if (in_array($group, $access_groups)) {
                            return true;
                        }
                    }
                }
            }

            return self::NO_ACCESS_TO_GROUP;
        } elseif($data['access_type'] == self::ACCESS_TO_SUBS) {
            if ($user_planes) {
                $access_planes =  json_decode($data['access_planes'], true);
                if ($access_planes) {
                    foreach ($user_planes as $plane_id) {
                        if (in_array($plane_id, $access_planes)) {
                            return true;
                        }
                    }
                }
            }

            return self::NO_ACCESS_TO_SUBS;
        }

        return false;
    }


    /**
     * ФОРМИРОВАНИЕ КНОПОК ДЛЯ ТРЕНИНГА/РАЗДЕЛА/УРОКА
     * @param $access_status
     * @param $training
     * @param null $section
     * @param null $lesson
     * @return array
     */
    public static function renderByButtons($access_status, $training, $section = null, $lesson = null) {
        if ($lesson && $lesson['access_type'] != self::ACCESS_TO_INHERIT) { // настройки кнопки для урока
            $bt_settings = ['big_button' => json_decode($lesson['by_button'], true)];
            $type =  self::ELEMENT_TYPE_LESSON;
            $data = $lesson;
        } elseif($section && $section['access_type'] != self::ACCESS_TO_INHERIT ) { // настройки кнопки для раздела
            $bt_settings = ['big_button' => json_decode($section['by_button'], true)];
            $type =  self::ELEMENT_TYPE_SECTION;
            $data = $section;
        } else { // настройки кнопки для тренинга
            $bt_settings = [
                'big_button' => json_decode($training['big_button'], true),
                'small_button' => json_decode($training['small_button'], true),
            ];
            $type = self::ELEMENT_TYPE_TRAINING;
            $data = $training;
        }

        $buttons = [
            'big_button' => false,
            'small_button' => false,
        ];

        if (!self::checkUserAccess($access_status)) { // если доступа нет
            if ($access_status['status'] === self::NO_ACCESS_TO_DATE) { // если дата доступа к тренингу/разделу/уроку еще не наступила
                $buttons = [
                    'big_button' => [
                        'btn-type' => null,
                        'class-type' => 'big_button__type-1',
                        'url' => "javascript:void(0)",
                        'text' => System::Lang('START_WATCH'),
                    ],
                ];

                if ($type == self::ELEMENT_TYPE_LESSON) {
                    $buttons['over_button_text'] = 'Урок откроется '.System::dateSpeller($section['open_date']);
                } elseif($type ==  self::ELEMENT_TYPE_SECTION) {
                    $buttons['over_button_text'] = 'Раздел откроется '.System::dateSpeller($section['open_date']);
                } else {
                    $buttons['over_button_text'] = 'Тренинг откроется '.System::dateSpeller($training['start_date']);
                }
            } else { // если доступ к тренингу по дате есть
                foreach ($bt_settings as $key => $button) {
                    if ($button['type']) {
                        $buttons[$key]['btn-type'] = $button['type'];
                        $buttons[$key]['class-type'] = $button['type'] == self::BY_BUTTON_IN_TO_TRAINING ? "{$key}__type-2" : "{$key}__type-3";
                        $buttons[$key]['text'] = empty($button['text']) ? "Купить" : $button['text']; // тут если название кнопки не указали, то по умолчанию пишем купить!
                    }

                    $link2ByButton = self::getLink2ByButton($button['type'], $button, $data);
                    if ($link2ByButton !== false) {
                        $buttons[$key]['url'] = $link2ByButton;
                    } else {
                        $buttons[$key] = false;
                    }
                }
            }
        } else { // если есть доступ
            if ($type ==  self::ELEMENT_TYPE_LESSON) {
                $url = "/training/view/{$training['alias']}/section/{$lesson['alias']}";
            } elseif($type ==  self::ELEMENT_TYPE_SECTION) {
                $url = "/training/view/{$training['alias']}/section/{$section['alias']}";
            } else {
                $url = "/training/view/{$training['alias']}";
            }

            $buttons['big_button'] = [
                'btn-type' => $type,
                'class-type' => "big_button__type-4",
                'url' => $url,
                'text' => System::Lang('START_WATCH'),
            ];
        }

        return $buttons;
    }


    /**
     * ПОЛУЧИТЬ ССЫЛКУ ДЛЯ КНОПКИ КУПИТЬ
     * @param $type
     * @param $button
     * @param $training
     * @return bool|mixed|string
     */
    public static function getLink2ByButton($type, $button, $training) {
        $result = false;
        $setting = System::getSetting();

        switch ($type) {
            case self::BY_BUTTON_TYPE_NOT_BUTTON: // Нет кнопки
                break;
            case self::BY_BUTTON_TYPE_PRODUCT_ORDER: // Заказ товара
                $result = $button['product_order'] ? "/buy/{$button['product_order']}" : false;
                break;
            case self::BY_BUTTON_TYPE_RATE: //Выбор тарифа (несколько товаров)
                if (isset($training['lesson_id'])) { // для урока
                    $result = "/training/lesson/options/{$training['lesson_id']}";
                    break;
                } elseif (isset($training['section_id'])) { // для секции(раздела)
                    $result = "/training/section/options/{$training['section_id']}";
                    break;
                } else {
                    $result = "/training/options/{$training['training_id']}";
                    break;  
                }
            case self::BY_BUTTON_TYPE_PRODUCT_DESC: // Описание товара
                $product = Product::getProductById($button['product_desc']);
                if (isset($product['external_landing'])) {
                    $result = $product['external_landing'] == 1 ? $product['external_url'] : "/catalog/{$product['product_alias']}";    
                }
                break; 
            case self::BY_BUTTON_TYPE_PRODUCT_DESC_MODAL: // Описание товара в модальном окне
                $product = Product::getProductById($button['product_desc']);
                if (isset($product['external_landing'])) {
                    $result = $product['external_landing'] == 1 ? $product['external_url'] : "/catalog/{$product['product_alias']}?viewmodal";    
                }
                break;
            case self::BY_BUTTON_TYPE_YOUR_URL: // Свой Url
                $result = $button['your_url'] ? $button['your_url'] : false;
                break;
            case self::BY_BUTTON_TYPE_PRODUCT_LENDING: // Продукт - лендинг
                $result = $button['product_lending'] ? "/buy/{$button['product_lending']}" : false;
                break;
            case self::BY_BUTTON_IN_TO_TRAINING: // ВОЙТИ в тренинг - это новая кнопка!
                $result = $setting['script_url'] . "/training/view/{$training['alias']}";
                break;    
        }

        return $result;
    }


    /**
     * ПОЛУЧИТЬ ДАТУ ДОСТУПА У ПОЛЬЗОВАТЕЛЯ К ТРЕНИНГУ
     * @param $training
     * @param $user_id
     * @param $user_groups
     * @param $user_planes
     * @return int|mixed
     */
    public static function getAccessDate2Training($training, $user_id, $user_groups, $user_planes) {
        $date = null;

        if ($training['access_type'] == self::ACCESS_FREE || $training['is_free']) { // если бесплатный тренинг, возвращаем дату старта
            return $training['start_date'];
        } elseif ($training['access_type'] == self::ACCESS_TO_GROUP && is_array($user_groups)) { // доступ по группе
            $access_groups = json_decode($training['access_groups']);
            if ($access_groups && $groups = array_intersect($access_groups, $user_groups)) {
                foreach ($groups as $group_id) {
                    $group = User::getGroupByUserAndGroup($user_id, $group_id);
                    if (!$date || $group['date'] < $date) { // получить наименьшую дату присвоения группы, дающей доступ к тренингу
                        $date = $group['date'];
                    }
                }
            }
        } elseif($training['access_type'] == self::ACCESS_TO_SUBS) { // доступ по подписке
            $access_planes = json_decode($training['access_planes'], true);
            if ($access_planes && $user_planes && $planes = array_intersect($access_planes, $user_planes)) {
                foreach ($planes as $plane_id) {
                    $plane = Member::getPlane2User($user_id, $plane_id);
                    if (!$date || $plane['begin'] < $date) { // получить наименьшую дату присвоения подписки, дающей доступ к тренингу
                        $date = $plane['begin'];
                    }
                }
            }
        }

        if (!$date) {
            return false;
        } else {
            return $date > $training['start_date'] ? $date : $training['start_date'];
        }
    }


    /**
     * ПОЛУЧИТЬ ДАТУ ОКОНЧАНИЯ ПРОХОЖДЕНИЯ ТРЕНИНГА
     * @param $training
     * @param $user_id
     * @return float|int|mixed|null
     */
    public static function getFinishPassDate($training, $user_id) {
        $date = null;

        if ($training['finish_type'] == self::END_DATE) {
            $date = $training['end_date'];
        } elseif(in_array($training['finish_type'], [self::ENTER_IN_LESSON_DATE, self::ANSWER_IN_LESSON_DATE, self::LESSON_COMPLETED_DATE]) && $training['finish_lessons']) {
            $finish_lessons = json_decode($training['finish_lessons']);
            foreach ($finish_lessons as $finish_lesson) {
                if ($training['finish_type'] == self::ENTER_IN_LESSON_DATE) { // вошёл в урок (уроки)
                    $data = TrainingLesson::getLessonCompleteData($finish_lesson, $user_id);
                    $_date = $data ? $data['open'] : null;
                } elseif($training['finish_type'] == self::ANSWER_IN_LESSON_DATE) { // ответил в уроке (уроках)
                    $data = TrainingLesson::getFirstUserAnswer($finish_lesson, $user_id);
                    $_date = $data ? $data['create_date'] : null;
                } else { // выполнил урок (уроки)
                    $data = TrainingLesson::getLessonCompleteData($finish_lesson, $user_id, TrainingLesson::HOMEWORK_ACCEPTED);
                    $_date = $data ? $data['date'] : null;
                }

                $date = !$date || $_date < $date ? $_date : $date;
            }
        }

        return $date ? $date : false;
    }


    /**
     * ПОЛУЧИТЬ ПОЛЬЗОВАТЕЛЕЙ, НАЧАВШИХ ПРОХОДИВШИХ ТРЕНИНГ
     * @return array|bool
     */
    public static function getUsersPassTraining() {
        $db = Db::getConnection();
        $query = 'SELECT tum.*, u.user_name, u.email FROM '.PREFICS.'training_user_map AS tum
                  LEFT JOIN '.PREFICS.'users AS u ON tum.user_id = u.user_id
                  RIGHT JOIN '.PREFICS.'training_events_finish AS tef ON tum.training_id = tef.training_id 
                  WHERE tum.id IS NOT NULL GROUP BY user_id, training_id';

        $result = $db->query($query);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПРОХОДИТ ЛИ ТРЕНИНГ ПОЛЬЗОВАТЕЛЬ
     * @param $training_id
     * @param $user_id
     * @return bool
     */
    public static function isPassTrainingUser($training_id, $user_id) {
        $db = Db::getConnection();
        $query = "SELECT COUNT(id) FROM ".PREFICS."training_user_map WHERE training_id = $training_id 
                  AND user_id = $user_id AND status <> ".TrainingLesson::HOMEWORK_ACCEPTED;
        $result = $db->query($query);
        $data = $result->fetch();

        return $data[0] > 0 ? true : false;
    }

    /**
     * ПОЛУЧИТЬ ПРОГРЕСС ПРОХОЖДЕНИЯ УРОКОВ
     * @param $training
     * @param $user_id
     * @return array
     */
    public static function getLessonsProgressData($training, $user_id) {
        $count_less = TrainingLesson::getCountLessons2Training($training);
        $count_success_less = TrainingLesson::getCountLessonsCompleted($user_id, $training['training_id']); // обновить и улучшить

        $progress = $count_success_less > 0 && $count_less > 0 ? ceil(round($count_success_less / $count_less * 100)) : 0;

        return [
            'progress' => $progress,
            'count_less' => $count_less
        ];
    }



    /* =================  НАСТРОЙКИ  ================= */


    /**
     * Получить статус курсов
     * @return bool
     */
    public static function getStatus()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT enable FROM ".PREFICS."extensions WHERE name = 'training' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['enable'] : false;
    }


    /**
     * ПОЛУЧИТЬ НАСТРОЙКИ ТРЕНИНГОВ
     * @return bool
     */
    public static function getSettings()
    {
        $db = Db::getConnection();
        $query = "SELECT ext.params AS params1, mp.params AS params2 FROM ".PREFICS."extensions AS ext,
                  ".PREFICS."training_page_settings AS mp WHERE ext.name = 'training'";

        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $params1 = json_decode($data['params1'], true)['params'];
            $params2 = json_decode($data['params2'], true)['params'];
            $params = array_merge($params1, $params2);
            $params['allow_del_homework'] = isset($params['allow_del_homework']) ? $params['allow_del_homework'] : 0;
            $params['scroll2comments'] = isset($params['scroll2comments']) ? $params['scroll2comments'] : 0;

            return $params;
        }

        return false;
    }


    /**
     * ПОЛУЧИТЬ НАСТРОЙКИ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ ТРЕНИНГОВ
     * @return bool
     */
    public static function getMPSettings()
    {
        $db = Db::getConnection();
        $query = "SELECT params FROM ".PREFICS."training_page_settings WHERE setting_id = 1";

        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return $data ? json_decode($data['params'], true)['params'] : false;
    }


    /**
     * СОХРАНИТЬ НАСТРОЙКИ ТРЕНИНГОВ
     * @param $params
     * @param $status
     * @return bool
     */
    public static function saveSettings($params, $status)
    {
        $params = json_encode($params);

        $db = Db::getConnection();
        $sql = "UPDATE ".PREFICS."extensions SET params = :params, enable = :enable WHERE name = 'training'";
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        $result->bindParam(':enable', $status, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * СОХРАНИТЬ НАСТРОЙКИ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ ТРЕНИНГОВ
     * @param $data
     * @return bool
     */
    public static function saveMPSettings($data) {
        $params = json_encode(['params' => $data]);

        $db = Db::getConnection();
        $sql = "UPDATE ".PREFICS."training_page_settings SET params = :params WHERE setting_id = 1";
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ХЛЕБНЫЕ КРОШКИ
     * @param $settings
     * @param $category
     * @param $sub_category
     * @param $training
     * @param null $section
     * @param null $lesson
     * @return array
     */
    public static function getBreadcrumbs($settings, $category, $sub_category, $training, $section = null, $lesson = null)
    {
        $breadcrumbs = [
            '/' => System::Lang('MAIN'),
            '/training' => System::Lang('ONLINE_TRAINING'),
        ];

        $url = '/training';

        if ($category && self::canShowCategoryToMP($category['cat_id'], $settings)) {
            $url .= "/category/{$category['alias']}";
            $key = $training || $sub_category ? $url : 0;
            $breadcrumbs[$key] = $category['name'];
        }

        if ($sub_category && self::canShowCategoryToMP($sub_category['cat_id'], $settings)) {
            $url .= "/{$sub_category['alias']}";
            $key = $sub_category ? $url : 0;
            $breadcrumbs[$key] = $sub_category['name'];
        }

        if ($training) {
            $url = "/training/view/{$training['alias']}";
            $key = $section || $lesson ? $url : 0;
            $breadcrumbs[$key] = $training['name'];
        }

        if ($section) {
            $url .= "/section/{$section['alias']}";
            $key = $lesson ? $url : 0;
            $breadcrumbs[$key] = $section['name'];
        }

        if ($lesson) {
            $url .= "/{$lesson['alias']}";
            $breadcrumbs[0] = $lesson['name'];
        }

        return $breadcrumbs;
    }


    /**
     * ВЫВОДИТЬ ИЛИ НЕ ВЫВОДИТЬ КАТЕГОРИЮ В МЕНЮ
     * @param $cat_id
     * @param $settings
     * @return bool
     */
    public static function canShowCategoryToMP($cat_id, $settings) {
        if ($settings['show_list'] == 'without_categories') {
            return false;
        }

        if ($settings['show_list'] == 'content_separate_category' && !empty($settings['categories_to_content'])) {
            return in_array($cat_id, $settings['categories_to_content']) ? true : false;
        }

        return true;
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО АКТИВНЫХ ТРЕНИНГОВ
     * @return mixed
     */
    public static function getCountTrainings() {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(training_id) FROM ".PREFICS."training WHERE status = 1");
        $count = $result->fetch();

        return $count[0];
    }

      /**
     * ПОЛУЧИТЬ ВСЕХ КУРАТОРОВ ПОЛЬЗОВАТЕЛЯ
     * @param $user_id
     * @return mixed
     */
    public static function getAllCuratorsToUser($user_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT t1.training_id, t1.section_id, t1.curator_id, t2.user_name FROM ".PREFICS."training_curator_to_user as t1
                            LEFT JOIN ".PREFICS."users as t2 ON t1.curator_id = t2.user_id WHERE t1.user_id = $user_id");
        $result->bindParam(':user_id', $alias, PDO::PARAM_STR);

        $result->execute();
        $data = $result->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);

        return !empty($data) ? $data : false;
        
    }

     /**
     * НАЗНАЧИТЬ НОВОГО КУРАТОРА ПОЛЬЗОВАТЕЛЮ
     * @param $user_id
     * @param $training_id
     * @param $section_id
     * @param $curator_id
     * @param $newcurator_id
     * @param $clean_curator
     * @return bool
     */
    public static function setNewCuratorToUser($user_id, $training_id, $section_id = 0, $curator_id, $newcurator_id, $clean_curator = null) {
        
        $db = Db::getConnection();

        $sql_delete = 'DELETE FROM '.PREFICS.'training_curator_to_user WHERE user_id = :user_id AND training_id = :training_id 
        AND section_id = :section_id AND curator_id = :curator_id';
        $result = $db->prepare($sql_delete);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        $result->bindParam(':curator_id', $curator_id, PDO::PARAM_INT);
        $result->execute();

        // если параметр указан просто возврат после удаления
        if ($clean_curator){
            return true;
        }

        $sql_write = 'INSERT INTO '.PREFICS.'training_curator_to_user (curator_id, user_id, training_id, section_id) 
                      VALUES (:curator_id, :user_id, :training_id, :section_id)';
        $result = $db->prepare($sql_write);
        
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        $result->bindParam(':curator_id', $newcurator_id, PDO::PARAM_INT);
        
        return $result->execute();
        
    }

     /**
     * ПОЛУЧИТЬ СПИСОК ВСЕХ ТРЕНИНГОВ КУРАТОРA
     * Выбираем уникальные тренинги из таблиц training_curators_in_training 
     * и training_curator_to_user
     * @param $user_id
     * @return mixed
     */
    public static function getAllTrainingsToCurator($user_id) {
        $db = Db::getConnection();
        $query = "SELECT DISTINCT t1.*, t2.name FROM (SELECT DISTINCT training_id, curator_id FROM ".PREFICS."training_curators_in_training
                  WHERE curator_id = $user_id UNION ALL SELECT DISTINCT training_id, curator_id FROM ".PREFICS."training_curator_to_user
                  WHERE curator_id = $user_id) as t1 LEFT JOIN ".PREFICS."training as t2 ON t1.training_id = t2.training_id";
        $result = $db->query($query);
    
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
        
    }

       /**
     * ПОЛУЧИТЬ СПИСОК ВСЕХ СЕКЦИЙ ТРЕНИНГА ПО ВЫБРАННОМУ КУРАТОРУ
     * Выбираем секции по тренингу и куратору из таблицы training_curators_in_training
     * @param $curator_id
     * @param $training_id
     * @return mixed
     */
    public static function getAllSectionsByCuratorAndByTraining($curator_id , $training_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT DISTINCT section_id FROM ".PREFICS."training_curators_in_training
            WHERE curator_id = $curator_id AND training_id = $training_id");
    
        $data = $result->fetchAll(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
        
    }

     /**
     * ПОЛУЧАЕМ ИНФОРМАЦИЮ ЯВЛЯЕТСЯ ЛИ КУРАТОР МАСТЕРОМ В ДАННОМ ТРЕНИНГЕ
     * @param $curator_id
     * @param $training_id
     * @return bool
     */
    public static function isMasterCuratorInTraining($curator_id, $training_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT DISTINCT is_master FROM ".PREFICS."training_curators_in_training WHERE curator_id = $curator_id 
        AND training_id = $training_id LIMIT 1");
    
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['is_master'] : false;
        
    }

     /**
     * ПОЛУЧАЕМ ИНФОРМАЦИЮ ЯВЛЯЕТСЯ ЛИ КУРАТОР НАЗНАЧЕННЫМ В ДАННОМ ТРЕНИНГЕ/РАЗДЕЛЕ
     * @param $curator_id
     * @param $section_id
     * @param $training_id
     * @return bool
     */
    public static function isCuratorInTrainingInSection($curator_id, $training_id, $section_id = 0) {
        $db = Db::getConnection();
        $sql = "(SELECT DISTINCT curator_id FROM ".PREFICS."training_curator_to_user WHERE curator_id = $curator_id 
                AND training_id = $training_id AND section_id = $section_id)
                UNION
                (SELECT DISTINCT curator_id  FROM ".PREFICS."training_curators_in_training WHERE curator_id = $curator_id 
                AND training_id = $training_id AND section_id = $section_id)";
        
        $result = $db->prepare($sql);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        return !empty($data) ? true : false;
        
    }


     /**
     * ПОЛУЧАЕМ КУРАТОРА ЮЗЕРА ПО ID урока 
     * @param $lesson_id
     * @param $user_id
     * @return mixed
     */
    public static function getCuratorToUserByLessonId($lesson_id, $user_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT DISTINCT t2.curator_id, u.user_name, u.email FROM ".PREFICS."training_lessons as t1
        LEFT JOIN ".PREFICS."training_curator_to_user as t2 ON t2.training_id = t1.training_id 
        AND t2.section_id = t1.section_id AND t2.user_id = $user_id
        LEFT JOIN ".PREFICS."users as u ON t2.curator_id = u.user_id
        where t1.lesson_id = $lesson_id LIMIT 1");
    
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
        
    }


    /**
     * ПОЛУЧИТЬ СОБЫТИЕ ДЛЯ ДЕЙСТВИЙ ПО ОКОНЧАНИЮ ТРЕНИНГА ПО ID
     * @param $id
     * @return bool|mixed
     */
    public static function getEventFinish($id) {
        $db = Db::getConnection();
        $result = $db->prepare("SELECT * FROM ".PREFICS."training_events_finish WHERE id = :id");
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СОБЫТИЕ ДЛЯ ДЕЙСТВИЙ ПО ОКОНЧАНИЮ ТРЕНИНГА ПО ТИПУ
     * @param $training_id
     * @param $event_type
     * @return bool|mixed
     */
    public static function getEventFinishByType($training_id, $event_type) {
        $db = Db::getConnection();
        $result = $db->prepare("SELECT * FROM ".PREFICS."training_events_finish WHERE training_id = :training_id AND event_type = :event_type");
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':event_type', $event_type, PDO::PARAM_STR);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СОБЫТИЯ ДЛЯ ДЕЙСТВИЙ ПО ОКОНЧАНИЮ ТРЕНИНГА
     * @param $training_id
     * @param bool $by_key_event_type
     * @return array|bool
     */
    public static function getEventsFinish($training_id, $by_key_event_type = false) {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."training_events_finish WHERE training_id = $training_id ORDER BY id DESC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $key = $by_key_event_type ? $row['event_type'] : count($data);
            $data[$key] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ДОБАВИТЬ СОБЫТИЕ ДЛЯ ДЕЙСТВИЙ ПО ОКОНЧАНИЮ ТРЕНИНГА
     * @param $training_id
     * @param $event_type
     * @param $params
     * @return bool
     */
    public static function addEventsFinish($training_id, $event_type, $params) {
        $db = Db::getConnection();
        $sql = "INSERT INTO ".PREFICS."training_events_finish (training_id, event_type, params) VALUES (:training_id, :event_type, :params)";
        $result = $db->prepare($sql);

        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':event_type', $event_type, PDO::PARAM_STR);
        $result->bindParam(':params', $params, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ОБНОВИТЬ СОБЫТИЕ ДЛЯ ДЕЙСТВИЙ ПО ОКОНЧАНИЮ ТРЕНИНГА
     * @param $id
     * @param $params
     * @return bool
     */
    public static function editEventsFinish($id, $params) {
        $db = Db::getConnection();
        $sql = "UPDATE ".PREFICS."training_events_finish SET params = :params WHERE id = :id";
        $result = $db->prepare($sql);

        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->bindParam(':params', $params, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ СОБЫТИЕ ДЛЯ ДЕЙСТВИЙ ПО ОКОНЧАНИЮ ТРЕНИНГА
     * @param $id
     * @return bool
     */
    public static function delEventFinish($id) {
        $db = Db::getConnection();
        $sql = "DELETE FROM ".PREFICS."training_events_finish WHERE id = :id";
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПРОЦЕСС ОБРАБОТКИ СОБЫТИЯ ПО ОКОНАНИЮ ТРЕНИНГА
     */
    public static function eventsFinishProcess() {
        $users = self::getUsersPassTraining();
        $training = $events = null;

        if ($users) {
            foreach ($users as $user) {
                if (!$training || $user['training_id'] != $training['training_id']) {
                    $training = self::getTraining($user['training_id']);
                    $events = self::getEventsFinish($user['training_id']);
                }


                $finish_date = self::getFinishPassDate($training, $user['user_id']);

                if ($finish_date && time() >= $finish_date && $events) { // если дата финиша есть и она меньше или равна текущей дате, и настроены события для окончания тренинга
                    $completedtasks = self::getActionsCompleted($training['training_id'], $user['user_id'], self::ELEMENT_TYPE_TRAINING);
                    foreach ($events as $event) {
                        $params = json_decode($event['params'], true);

                        switch ($event['event_type']) {
                            case 'give_access':
                                if (in_array(self::ACTION_TRAINING_COMPLETED_GA, $completedtasks)) {
                                    continue;
                                }
                                if ($params['access_groups']) {
                                    foreach ($params['access_groups'] as $group_id) {
                                        User::WriteUserGroup($user['user_id'], $group_id);
                                    }
                                }

                                if ($params['access_planes']) {
                                    foreach ($params['access_planes'] as $plane_id) {
                                        Member::addMember($plane_id, $user['user_id']);
                                    }
                                }
                                self::addPerformedAction($training['training_id'], $user['user_id'], self::ELEMENT_TYPE_TRAINING, self::ACTION_TRAINING_COMPLETED_GA);
                                break;
                            case 'send_message':
                                if (in_array(self::ACTION_TRAINING_COMPLETED_SM, $completedtasks)) {
                                    continue;
                                }
                                $subject = "Поздравляем с окончанием тренинга {$training['name']}!";
                                $send = Email::SendMessageToBlank($user['email'], $user['user_name'], $subject, $params['text']);
                                if ($send) {
                                    self::addPerformedAction($training['training_id'], $user['user_id'], self::ELEMENT_TYPE_TRAINING, self::ACTION_TRAINING_COMPLETED_SM);
                                }
                                break;
                            case 'give_sertificate':
                                if (in_array(self::ACTION_TRAINING_COMPLETED_GS, $completedtasks)) {
                                    continue;
                                }
                                $path_sert = Training::GenerateСertificate($training['training_id'], $user['user_id']);
                                self::addPerformedAction($training['training_id'], $user['user_id'], self::ELEMENT_TYPE_TRAINING, self::ACTION_TRAINING_COMPLETED_GS);
                                break;
                        }
                    }
                }
            }
        }
    }


    /**
     * ДОБАВИТЬ ЗАПИСЬ В ТАБЛИЦУ, ЧТО ДЕЙСТВИЕ БЫЛО ВЫПОЛНЕНО
     * @param $element_id
     * @param $user_id
     * @param $element_type
     * @param $action
     * @return bool
     */
    public static function addPerformedAction($element_id, $user_id, $element_type, $action) {
        $db = Db::getConnection();
        $sql = "INSERT INTO ".PREFICS."training_completed_actions2users (element_id, user_id, element_type, action) VALUES (:element_id, :user_id, :element_type, :action)";
        $result = $db->prepare($sql);

        $result->bindParam(':element_id', $element_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':element_type', $element_type, PDO::PARAM_INT);
        $result->bindParam(':action', $action, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * БЫЛО ЛИ ПРОИЗВДЕНО ДЕЙСТВИЕ ДЛЯ ПОЛЬЗОВАТЕЛЯ
     * @param $element_id
     * @param $user_id
     * @param $element_type
     * @param $action
     * @return bool
     */
    public static function isActionCompleted($element_id, $user_id, $element_type, $action) {
        $db = Db::getConnection();
        $query = "SELECT count(id) FROM ".PREFICS."training_completed_actions2users 
                  WHERE element_id = $element_id AND user_id = $user_id AND
                  element_type = $element_type AND action = $action";
        $result = $db->query($query);
        $data = $result->fetch();

        return $data[0] > 0 ? true : false;
    }

    /**
     * ПОЛУЧИТЬ ВСЕ ДЕЙСТВИЯ ПОЛЬЗОВАТЕЛЯ ПО ТРЕНИНГУ
     * @param $training_id
     * @param $user_id
     * @return array|bool
     */
    public static function getActionsCompleted($training_id, $user_id, $element_type) {
        $db = Db::getConnection();
        $query = "SELECT action FROM ".PREFICS."training_completed_actions2users 
                  WHERE element_id = $training_id AND user_id = $user_id AND element_type = $element_type";
        $result = $db->query($query);
        $data = $result->fetchAll(PDO::FETCH_COLUMN);

        return $data ? $data : false;
    }



    public static function getUsers2Notices() {
        $time = time();
        $db = Db::getConnection();
        $query = "SELECT u.* FROM ".PREFICS.'users AS u
                  LEFT JOIN '.PREFICS.'user_groups_map AS ugm ON ugm.user_id = u.user_id
                  LEFT JOIN '.PREFICS."member_maps AS mm ON mm.user_id = u.user_id AND mm.status = 1 AND mm.end > $time
                  WHERE ugm.group_id IN (SELECT DISTINCT(group_id) FROM ".PREFICS."training_access_groups)
                  OR mm.subs_id IN (SELECT DISTINCT(plane_id) FROM ".PREFICS."training_access_planes)
                  GROUP BY u.user_id";
        $result = $db->query($query);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * УВЕДОМЛЕНИЯ ПОЛЬЗОВАТЕЛЕЙ
     */
    public static function userNotices() {
        $users = self::getUsers2Notices();

        if ($users) {
            TrainingSection::userNoticesToOpenSection($users); // уведомления пользователям при откртыии раздела
        }
    }

    
    /**
     * Получить названия групп доступа по списку
     */
    public static function getGroupNameByList($list) {
        
        $db = Db::getConnection();
        $query = "SELECT DISTINCT group_title FROM ".PREFICS."user_groups WHERE group_id IN ($list)";

        $result = $db->query($query);

        $data = $result->fetchAll(PDO::FETCH_COLUMN);

        return !empty($data) ? implode(", ", $data) : false;
    }

      /**
     * Получить названия планов подписки по списку
     */
    public static function getPlanesNameByList($list) {
        
        $db = Db::getConnection();
        $query = "SELECT DISTINCT name FROM ".PREFICS."member_planes WHERE id IN ($list)";

        $result = $db->query($query);

        $data = $result->fetchAll(PDO::FETCH_COLUMN);

        return !empty($data) ? implode(", ", $data) : false;
    }

     /**
     * Получить настройку даты открытия раздела/урока для отоборажение
     * в структуре тренинга.
     */
    public static function getOpenDateForStructureView($section = null, $lesson = null) {
        
        if ($lesson) {
            
            if ($lesson['shedule']==2) {
            
                if ($lesson['shedule_type']==2){ // Это конкретная дата открытия
                    return date("d.m.Y", $lesson['shedule_open_date']);
                } else {
                    if ($lesson['shedule_relatively'] == 4) { // Дата покупки (назначение группы или подписки)
                        if ($lesson['shedule_how_fast_open'] == 1) { // на следующий день
                            return '+1 д. от даты покупки';
                        } elseif ($lesson['shedule_how_fast_open'] == 2) { // через Х дней
                            return '+'.$lesson['shedule_count_days'].' д. от даты покупки'; 
                        } elseif ($lesson['shedule_how_fast_open'] == 3) { // в день недели
                            return 'ждем '.self::getWeekDayName($lesson['shedule_access_time_weekday']).' от даты покупки';
                        }
                    } elseif ($lesson['shedule_relatively'] == 3) { // Дата начала тренинга(дата старта которая указана в настройках тренинга)
                        if ($lesson['shedule_how_fast_open'] == 1) { // на следующий день
                            return '+1 д. от даты начала тренинга';
                        } elseif ($lesson['shedule_how_fast_open'] == 2) { // через Х дней
                            return '+'.$lesson['shedule_count_days'].' д. от даты начала тренинга'; 
                        } elseif ($lesson['shedule_how_fast_open'] == 3) { // в день недели
                            return 'ждем '.self::getWeekDayName($lesson['shedule_access_time_weekday']).' от даты начала тренинга';
                        }
                    } elseif ($lesson['shedule_relatively'] == 1) { // Вход в предыдущий урок
                        if ($lesson['shedule_how_fast_open'] == 1) { // на следующий день
                            return '+1 д. от даты входа в предыдущий урок';
                        } elseif ($lesson['shedule_how_fast_open'] == 2) { // через Х дней
                            return '+'.$lesson['shedule_count_days'].' д. от даты входа в предыдущий урок'; 
                        } elseif ($lesson['shedule_how_fast_open'] == 3) { // в день недели
                            return 'ждем '.self::getWeekDayName($lesson['shedule_access_time_weekday']).' от даты входа в предыдущий урок';
                        }
                    } elseif ($lesson['shedule_relatively'] == 2) { // Вход в первый урок
                        if ($lesson['shedule_how_fast_open'] == 1) { // на следующий день
                            return '+1 д. от даты входа в первый урок';
                        } elseif ($lesson['shedule_how_fast_open'] == 2) { // через Х дней
                            return '+'.$lesson['shedule_count_days'].' д. от даты входа в первый урок'; 
                        } elseif ($lesson['shedule_how_fast_open'] == 3) { // в день недели
                            return 'ждем '.self::getWeekDayName($lesson['shedule_access_time_weekday']).' от даты входа в первый урок';
                        }
                    }

                }

            }
        }

        if ($section){
            if ($section['open_type']==2){ // Это конкретная дата открытия
                return date("d.m.Y", $section['open_date']);
            } elseif ($section['open_type']==1) { // Это дата группы/попдписки или начала тренинга
                if ($section['open_wait_days']>0) {
                    return '+'.$section['open_wait_days'].' от даты покупки';
                }
            }
        }
    }

     /**
     * Получить название дня недели
     */
    public static function getWeekDayName($id) {
        
       switch ($id){
            case 1:
                return 'пн.';
                break;
            case 2:
                return 'вт.';
                break;
            case 3:
                return 'ср.';
                break;
            case 4:
                return 'чт.';
                break;
            case 5:
                return 'пт.';
                break;
            case 6:
                return 'сб.';
                break;    
            case 7:
                return 'вс.';
                break;
        }
    }

     /**
     * ПРОЦЕСС СМЕНЫ СТАТУСА ДЗ  
     * Когда куратор открывает домашнее задание, ему присваетвается временный 3-ий статус, при котором другие
     * кураторы и даже мастер-кураторы, не видят этого ответа. Но если куратор не дал никакого фидбека по домашнему заданию,
     * то через 30 минут статус у этого ДЗ вернется в доступный к проверке всем. И любой другой куратор сможет так-же 
     * зайти в этот ответ и дать фидбэк.
     */
    public static function eventsChangeStatusHomeWork() {
        
        $time_15min_ago = time()-(15*60); 
        $new_status = 4;
        $curator_id = 0;

        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'training_home_work SET status = :new_status, curator_id = :curator_id WHERE status = 3 AND create_date < '.$time_15min_ago;

        $result = $db->prepare($sql);
        $result->bindParam(':new_status', $new_status, PDO::PARAM_INT);
        $result->bindParam(':curator_id', $curator_id, PDO::PARAM_INT);

        $result->execute();
       
    }


    /**
     * @param $setting
     * @param $val
     * @return mixed
     */
    public static function getCssClasses($setting, $val) {
        $path = ROOT . "/template/{$setting['template']}/extensions/training/web/style/style.php";
        if (!file_exists($path)) {
            $path = ROOT . "/extensions/training/web/frontend/style/style.php";
        }

        require($path);

        return $classes[$val];
    }

    /**
     * УДАЛИТЬ ПРОХОЖДЕНИЕ УРОКА У ЮЗЕРА В НОВЫХ ТРЕНИНГАХ
     * @param $user_id
     * @param $lesson_id
     * @return bool
     */

    public static function delCompleteLessonFull($user_id, $lesson_id) {

        $homework = TrainingLesson::getHomeWork($user_id, $lesson_id);
        $homework_id = $homework['homework_id'];
        $db = Db::getConnection();
        $sql = '';
        if ($homework_id) {
            $sql .= "DELETE FROM ".PREFICS."training_home_work_history WHERE homework_id = :homework_id;";
            $sql .= "DELETE FROM ".PREFICS."training_home_work_comments WHERE homework_id = :homework_id;";
            $sql .= "DELETE FROM ".PREFICS."training_home_work WHERE homework_id = :homework_id;";
        }

        $sql .= "DELETE FROM ".PREFICS."training_test_results WHERE user_id = :user_id AND lesson_id = :lesson_id;";
        $sql .= "DELETE FROM ".PREFICS."training_user_map WHERE user_id = :user_id AND lesson_id = :lesson_id;";
        
        $result = $db->prepare($sql);
        if ($homework_id) {
            $result->bindParam(':homework_id', $homework_id, PDO::PARAM_INT);
        }
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        
        return $result->execute();
    }

        /**
     * ВЫГРУЗИТЬ КУРС(ТРЕНИНГ) В CSV ФАЙЛ ДЛЯ ИМПОРТА В НОВЫЕ ТРЕНИНГИ
     * @param $id
     * @return mixed
     */
    
    public static function exportTrainingtoCSV($training_id){

        $time = time();
        $setting = System::getSetting();
        $fp = fopen(ROOT.'/tmp/training_'.$training_id.'_'.$time.'.csv','w');
        $fplesson = fopen(ROOT.'/tmp/training_lessons_'.$training_id.'_'.$time.'.csv','w');
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."training WHERE training_id = $training_id LIMIT 1");
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        if (isset($data) && !empty($data)) {
            fputcsv($fp, array_keys($data[0]), ',');
            fputcsv($fp, array_values($data[0]), ','); 
        }

        $write = fclose($fp);

        $result_less = $db->query("SELECT tt.*, tl.* FROM ".PREFICS."training_lessons AS tl 
                                            LEFT JOIN ".PREFICS."training_task as tt ON tl.lesson_id = tt.lesson_id
                                            WHERE tl.training_id = $training_id"
        );

        $data = [];
        while($row = $result_less->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $headers = array_keys($data[0]);
        array_push($headers, "tle_params", "tle_sort", "tle_type");
        fputcsv($fplesson, $headers, ',');

        foreach ($data as $lesson) {
            $lesson['section_id'] = 0;
            $lesson['block_id'] = 0;
            $lesson_id = $lesson['lesson_id'];
            $result_elem = $db->query("SELECT * FROM ".PREFICS."training_lesson_elements WHERE lesson_id = $lesson_id");
            $values_tle_params = $values_tle_sort = $values_tle_type = '';

            while($row = $result_elem->fetch(PDO::FETCH_ASSOC)) {
                $values_tle_params .= $row['params'].PHP_EOL;
                $values_tle_sort .= $row['sort'].PHP_EOL;
                $values_tle_type .= $row['type'].PHP_EOL;
            }

            $values_tle_params = $values_tle_params ? '"'.rtrim($values_tle_params).'"' : '';
            $values_tle_sort = $values_tle_sort ? '"'.rtrim($values_tle_sort).'"' : '';
            $values_tle_type = $values_tle_type ? '"'.rtrim($values_tle_type).'"' : '';
            $values = array_values($lesson);
            array_push($values, $values_tle_params, $values_tle_sort, $values_tle_type);
            fputcsv($fplesson, $values, ',');
        } 


        $write_lesson = fclose($fplesson);

        if ($write && $write_lesson){
            $zip = new ZipArchive();
            $filename = "/tmp/full_training_".$training_id."_".$time."_.zip";
            $fullpathzip = ROOT.$filename;
            if ($zip->open($fullpathzip, ZipArchive::CREATE)!==TRUE) {
                exit("Невозможно открыть <$fullpathzip>\n");
            }
            
            $zip->addFile(ROOT.'/tmp/training_'.$training_id.'_'.$time.'.csv', 'training_'.$training_id.'_'.$time.'.csv');
            $zip->addFile(ROOT.'/tmp/training_lessons_'.$training_id.'_'.$time.'.csv', 'training_lessons_'.$training_id.'_'.$time.'.csv');
            $zip->close();
            header("Location: ".$setting['script_url'].$filename);
        }

    }

     /**
     * ПОЛНОЕ КОПИРОВАНИЕ ТРЕНИНГА ВМЕСТЕ С УРОКАМИ ТЕСТАМИ и ВСЕМ ВСЕМ ВСЕМ 
     * @param $training_id
     * @return bool
     */
    public static function fullCopyTraining($training_id) {
        $db = Db::getConnection();
        $db->beginTransaction();
        $result = $db->query("INSERT INTO ".PREFICS."training (
                name, title, alias, full_desc, cat_id, short_desc,
                cover, full_cover, full_cover_param, img_alt, padding, status, access_type, access_task_type, access_task_groups,
                access_task_planes, access_task_global, show_train, show_form_subscribe, count_lessons_type, show_desc, show_widget_progress, 
                show_progress2list, show_comments, show_hits, show_pupil_count, show_pupil, show_start_date, show_end, show_in_main,
                show_in_lk2not_buy, show_before_start, start_date, end_date, start_type, start_time_value, finish_type, finish_value,
                custom_text, create_date, sort, sertificate, count_free_lessons, is_free, meta_desc, meta_keys, type_access_buy,
                product_access, link_access, big_button, small_button, duration_type, count_lessons, price, complexity, duration,
                show_count_lessons, show_complexity, show_price, show_widget_training, sort_lessons, confirm_phone, show_watermark_email,
                show_watermark_phone, binding_tg, show_in_cab2not_access, allow_user_notes, authors_can_edit, curators_can_edit, 
                curators_auto_assign, show_in_cabinet2not_access, show_in_lk2not_access, text_in_lk2not_buy, start_lessons, finish_lessons,
                lock_comment, homework_edit, homework_comment_add, on_public_homework, by_button_curator_hw, by_button_autocheck_hw,
                by_button_self_hw, lessons_tmpl, subject_letter_to_curator, letter_to_curator, subject_letter_to_user, letter_to_user,
                subject_letter_to_user_for_open_lesson, letter_to_user_for_open_lesson, send_email_to_curator, send_email_to_all_curators,
                send_email_to_user, send_email_to_user_for_open_lesson
            )
                SELECT CONCAT(name, ' копия'), title,  CONCAT(alias, '-1'),  full_desc, cat_id, short_desc, cover, full_cover,
                full_cover_param, img_alt, padding, 0, access_type, access_task_type, access_task_groups, access_task_planes, 
                access_task_global, show_train, show_form_subscribe, count_lessons_type, show_desc, show_widget_progress, 
                show_progress2list, show_comments, show_hits, show_pupil_count, show_pupil, show_start_date, show_end, show_in_main,
                show_in_lk2not_buy, show_before_start, start_date, end_date, start_type, start_time_value, finish_type, finish_value,
                custom_text, create_date, sort+1, sertificate, count_free_lessons, is_free, meta_desc, meta_keys, type_access_buy,
                product_access, link_access, big_button, small_button, duration_type, count_lessons, price, complexity, duration,
                show_count_lessons, show_complexity, show_price, show_widget_training, sort_lessons, confirm_phone, show_watermark_email,
                show_watermark_phone, binding_tg, show_in_cab2not_access, allow_user_notes, authors_can_edit, curators_can_edit,
                curators_auto_assign, show_in_cabinet2not_access, show_in_lk2not_access, text_in_lk2not_buy, start_lessons, finish_lessons,
                lock_comment, homework_edit, homework_comment_add, on_public_homework, by_button_curator_hw, by_button_autocheck_hw,
                by_button_self_hw, lessons_tmpl, subject_letter_to_curator, letter_to_curator, subject_letter_to_user, letter_to_user, 
                subject_letter_to_user_for_open_lesson, letter_to_user_for_open_lesson, send_email_to_curator, send_email_to_all_curators,
                send_email_to_user, send_email_to_user_for_open_lesson FROM ".PREFICS."training WHERE training_id = $training_id LIMIT 1
        ");

        $new_training_id = $result ? $db->lastInsertId() : null;
        if (isset($new_training_id) && !empty($new_training_id)) {
             /// Запрос копирования групп
            $result = $db->query("INSERT INTO ".PREFICS."training_access_groups (training_id, group_id) 
                SELECT $new_training_id, group_id FROM ".PREFICS."training_access_groups WHERE training_id = $training_id"
            );

            /// Запрос копирования планов
            $result = $db->query("INSERT INTO ".PREFICS."training_access_planes (training_id, plane_id) 
                SELECT $new_training_id, plane_id FROM ".PREFICS."training_access_planes WHERE training_id = $training_id"
            );

            /// Запрос копирования авторов
            $result = $db->query("INSERT INTO ".PREFICS."training_authors_in_training (author_id, training_id) 
                SELECT author_id, $new_training_id FROM ".PREFICS."training_authors_in_training WHERE training_id = $training_id"
            );

            /// Запрос копирования кураторов
            $result = $db->query("INSERT INTO ".PREFICS."training_curators_in_training (
                    curator_id, training_id, section_id, is_master,assing_to_users
                )
                    SELECT curator_id, $new_training_id, section_id, is_master,assing_to_users 
                    FROM ".PREFICS."training_curators_in_training WHERE training_id = $training_id
            ");

            /// Запрос копирования секций
            $result = $db->query("INSERT INTO ".PREFICS."training_sections (
                    training_id, name, title, alias, section_desc, cover, img_alt, sort, service_header, start_lessons,
                    finish_lessons, by_button, open_type, open_wait_days, open_date, close_type, close_wait_days, close_date,
                    is_show_before_open, start_type, finish_type, image_type, access_type, access_groups, access_planes, status,
                    meta_desc, meta_keys
                )
                    SELECT $new_training_id, name, title, alias, section_desc, cover, img_alt, sort, service_header, start_lessons,
                    finish_lessons, by_button, open_type, open_wait_days, open_date, close_type, close_wait_days, close_date,
                    is_show_before_open, start_type, finish_type, image_type, access_type, access_groups, access_planes, status,
                    meta_desc, meta_keys FROM ".PREFICS."training_sections WHERE training_id = $training_id
            ");

            /// Запрос копирования блоков
            $result = $db->query("INSERT INTO ".PREFICS."training_blocks (
                    name, cover, training_id, section_id, sort
                )
                    SELECT tb.name, tb.cover, $new_training_id, IFNULL(ts2.section_id,0), tb.sort FROM ".PREFICS."training_blocks AS tb
                    LEFT JOIN ".PREFICS."training_sections AS ts ON tb.training_id = ts.training_id AND tb.section_id = ts.section_id
                    LEFT JOIN ".PREFICS."training_sections AS ts2 ON ts.sort = ts2.sort AND ts2.training_id = $new_training_id                           
                    WHERE tb.training_id = $training_id
            ");

            /// Запрос копирования уроков
            $result = $db->query("INSERT INTO ".PREFICS."training_lessons (training_id, section_id, block_id, name, alias,
                    cover, img_alt, status, sort, title, less_desc, attach, hits, meta_desc, meta_keys, auto_access_lesson, access_type,
                    access_groups, access_planes, access_time_type, access_time_value, duration, public_date, end_date, show_hits, 
                    show_comments, rating, by_button, shedule, shedule_hidden, shedule_type, shedule_relatively, shedule_open_date, 
                    shedule_how_fast_open, shedule_count_days, shedule_access_time_weekday, create_date
                )
                    SELECT $new_training_id, IFNULL(ts2.section_id,0), IFNULL(tb2.block_id,0), tl.name, tl.alias, tl.cover, tl.img_alt,
                    tl.status, tl.sort, tl.title, tl.less_desc, tl.attach, tl.hits, tl.meta_desc, tl.meta_keys, tl.auto_access_lesson,
                    tl.access_type, tl.access_groups, tl.access_planes, tl.access_time_type, tl.access_time_value, tl.duration,
                    tl.public_date, tl.end_date, tl.show_hits, tl.show_comments, tl.rating, tl.by_button, tl.shedule, tl.shedule_hidden,
                    tl.shedule_type, tl.shedule_relatively, tl.shedule_open_date, tl.shedule_how_fast_open, tl.shedule_count_days,
                    tl.shedule_access_time_weekday, tl.create_date FROM ".PREFICS."training_lessons AS tl
                    LEFT JOIN ".PREFICS."training_sections AS ts ON tl.training_id = ts.training_id AND tl.section_id = ts.section_id
                    LEFT JOIN ".PREFICS."training_sections AS ts2 ON ts.sort = ts2.sort AND ts2.training_id = $new_training_id      
                    LEFT JOIN ".PREFICS."training_blocks AS tb ON tl.training_id = tb.training_id AND tl.block_id = tb.block_id
                    LEFT JOIN ".PREFICS."training_blocks AS tb2 ON tb.sort = tb2.sort AND tb2.training_id = $new_training_id                       
                    WHERE tl.training_id = $training_id
            ");

            /// Запрос копирования заданий(task)
            $result = $db->query("INSERT INTO ".PREFICS."training_task (
                    lesson_id,task_type,check_type,text,auto_answer,stop_lesson, autocheck_time,access_type,check_status_type,
                    access_time,access_time_days,access_time_weekday, show_upload_file,show_work_link,hint,completed_on_time,
                    not_completed_on_time,completed_time_add_group, completed_time_del_group
                )
                    SELECT tl2.lesson_id,tt.task_type,tt.check_type,tt.text,tt.auto_answer,tt.stop_lesson, tt.autocheck_time, tt.access_type,
                    tt.check_status_type,tt.access_time,tt.access_time_days,tt.access_time_weekday, tt.show_upload_file, tt.show_work_link,
                    tt.hint,tt.completed_on_time,tt.not_completed_on_time,tt.completed_time_add_group, tt.completed_time_del_group
                    FROM ".PREFICS."training_task AS tt
                    LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = tt.lesson_id 
                    LEFT JOIN ".PREFICS."training_lessons AS tl2 ON tl2.sort = tl.sort AND tl2.training_id = $new_training_id                           
                    WHERE tt.lesson_id IN (SELECT lesson_id FROM ".PREFICS."training_lessons WHERE training_id = $training_id)
            ");

            /// Запрос копирования элементов урока
            $result = $db->query("INSERT INTO ".PREFICS."training_lesson_elements (
                    type, lesson_id, sort, params
                )
                    SELECT tle.type, tl2.lesson_id, tle.sort, tle.params FROM ".PREFICS."training_lesson_elements AS tle
                    LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = tle.lesson_id 
                    LEFT JOIN ".PREFICS."training_lessons AS tl2 ON tl2.sort = tl.sort AND tl2.training_id = $new_training_id
                    WHERE tle.lesson_id IN (SELECT lesson_id FROM ".PREFICS."training_lessons WHERE training_id = $training_id)
            ");

            /// Запрос по Плейлисту 
            $result = $db->query("INSERT INTO ".PREFICS."training_playlist_items (
                    playlist_id, sort, params
                )
                    SELECT tle2.id as new_play_list_id, tpi.sort, tpi.params FROM ".PREFICS."training_playlist_items AS tpi
                    LEFT JOIN ".PREFICS."training_lesson_elements AS tle ON tpi.playlist_id = tle.id
                    LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = tle.lesson_id
                    LEFT JOIN ".PREFICS."training_lessons AS tl2 ON tl.sort = tl2.sort AND tl2.training_id = $new_training_id
                    LEFT JOIN ".PREFICS."training_lesson_elements AS tle2 ON tl2.lesson_id = tle2.lesson_id  AND tle2.type = 2
                    WHERE tle.lesson_id IN (SELECT lesson_id FROM ".PREFICS."training_lessons WHERE training_id = $training_id) AND tle.type = 2
            ");

            /// Запрос копирования events_finish
            $result = $db->query("INSERT INTO ".PREFICS."training_events_finish (training_id, event_type, params)
                SELECT $new_training_id, event_type, params FROM ".PREFICS."training_events_finish WHERE training_id = $training_id"
            );

            /// Запрос копирования тестов
            $result = $db->query("INSERT INTO ".PREFICS."training_test (
                    task_id, lesson_id, test_desc, finish, test_try, test_time, show_questions_count, help_hint_success, help_hint_fail, ratings
                )
                    SELECT ttask.task_id, tl2.lesson_id, tt.test_desc, tt.finish, tt.test_try, tt.test_time, tt.show_questions_count, 
                    tt.help_hint_success, tt.help_hint_fail, tt.ratings FROM ".PREFICS."training_test as tt
                    LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = tt.lesson_id 
                    LEFT JOIN ".PREFICS."training_lessons AS tl2 ON tl2.sort = tl.sort AND tl2.training_id = $new_training_id
                    LEFT JOIN ".PREFICS."training_task AS ttask ON ttask.lesson_id = tl2.lesson_id
                    WHERE tt.lesson_id IN (SELECT lesson_id FROM ".PREFICS."training_lessons WHERE training_id = $training_id);
                    INSERT INTO ".PREFICS."training_questions (test_id, question, help, true_answer, require_all_true, status, sort, image)
                    SELECT tt2.test_id, tq.question, tq.help, tq.true_answer, tq.require_all_true, tq.status, tq.sort, tq.image
                    FROM  ".PREFICS."training_questions AS tq
                    LEFT JOIN  ".PREFICS."training_test as tt ON tt.test_id = tq.test_id
                    LEFT JOIN  ".PREFICS."training_lessons AS tl ON tl.lesson_id = tt.lesson_id 
                    LEFT JOIN  ".PREFICS."training_lessons AS tl2 ON tl2.sort = tl.sort AND tl2.training_id = $new_training_id
                    LEFT JOIN  ".PREFICS."training_task AS ttask ON ttask.lesson_id = tl2.lesson_id
                    LEFT JOIN  ".PREFICS."training_test as tt2 ON tt2.lesson_id = tl2.lesson_id
                    WHERE tt.lesson_id IN (SELECT lesson_id FROM  ".PREFICS."training_lessons WHERE training_id = $training_id);
                    INSERT INTO ".PREFICS."training_test_options (quest_id, title, value, sort, valid, points, cover)
                    SELECT tq2.quest_id, testop.title, testop.value, testop.sort, testop.valid, testop.points, testop.cover
                    FROM ".PREFICS."training_test_options AS testop
                    LEFT JOIN ".PREFICS."training_questions AS tq ON tq.quest_id = testop.quest_id
                    LEFT JOIN ".PREFICS."training_test as tt ON tt.test_id = tq.test_id
                    LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = tt.lesson_id 
                    LEFT JOIN ".PREFICS."training_lessons AS tl2 ON tl2.sort = tl.sort AND tl2.training_id = $new_training_id
                    LEFT JOIN ".PREFICS."training_test as tt2 ON tt2.lesson_id = tl2.lesson_id 
                    LEFT JOIN ".PREFICS."training_questions AS tq2 ON tq2.test_id = tt2.test_id AND tq.sort= tq2.sort
                    WHERE tt.lesson_id IN (SELECT lesson_id FROM ".PREFICS."training_lessons WHERE training_id = $training_id);
            ");

            $db->commit();
            /// ТУТ ФИКСИРУЕМ транзакцию и дальше копируем атачи

            $result = $db->query("SELECT tle.type, tle.lesson_id AS old_id, tl2.lesson_id AS new_id, tle.sort, tle.params
                    FROM ".PREFICS."training_lesson_elements AS tle
                    LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = tle.lesson_id 
                    LEFT JOIN ".PREFICS."training_lessons AS tl2 ON tl2.sort = tl.sort AND tl2.training_id = $new_training_id
                    WHERE tle.lesson_id IN (SELECT lesson_id FROM ".PREFICS."training_lessons WHERE training_id = $training_id) AND tle.type = 4"
            );
            
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $params = json_decode($row['params']);
                $dir_new_path = ROOT . "/load/training/lessons/{$row['new_id']}";
                if (!file_exists($dir_new_path)) {
                    mkdir($dir_new_path);
                }

                $file_old = ROOT . "/load/training/lessons/{$row['old_id']}" . "/" . $params->attach;
                $file_new = $dir_new_path . "/" . $params->attach;
                if (is_file($file_old)){
                    copy($file_old, $file_new);
                } 
            }

            return $new_training_id;
        } else {
            $db->rollBack();
            return false;
        }
    }


    /**
     * ПОЛУЧИТЬ ПОЛНЫЙ РАЗМЕР ВСЕХ ВЛОЖЕНИЙ В ТРЕНИНГЕ
     * @param $training_id
     * @return false|int
     */
    public static function getSizeAllAttachInTraining($training_id) {
        
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."training_lesson_elements 
            WHERE lesson_id IN(SELECT lesson_id from ".PREFICS."training_lessons WHERE training_id = $training_id) AND type = 4"
        );
        
        $size_all_files = 0;
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $params = json_decode($row['params']);
            $filename = ROOT . "/load/training/lessons/{$row['lesson_id']}" . "/" . $params->attach;
            $fileExist = is_file($filename);
            $size_all_files += $fileExist ? filesize($filename) : 0;
        }
        
        return $size_all_files;
    }


     /**
     * СГЕНЕРИРОВАТЬ СЕРТИФИКАТ 
     * @param $training_id
     * @param $user_id
     */

    public static function GenerateСertificate($training_id, $user_id) {
        
        $date = time();
        $training_id = intval($training_id);
        $training = Training::getTraining($training_id);

        if (isset($user_id)) {
            $user = User::getUserById($user_id);
        }

        self::addСertificateRecord($user_id, $training_id);
        
    }

     /**
     * ПОЛУЧИТЬ НОВЫЙ/ПОСЛЕДНИЙ НОМЕР СЕРТИФИКАТА
     * @param $training_id
     * @return false|int
     */

    public static function getLastNumberСertificate($training_id) {
        
        $db = Db::getConnection();
        $result = $db->query("SELECT id FROM ".PREFICS."training_sertificates WHERE training_id = $training_id ORDER BY id ASC LIMIT 1");
        
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return $data['id'] > 0 ? $data['id'] : false;

    }

    /**
     * ДОБАВИТЬ ЗАПИСЬ О ВЫДАНОМ СЕРТИФИКАТЕ ПОЛЬЗОВАТЕЛЮ 
     * @param $user_id
     * @param $training_id
     */

    public static function addСertificateRecord($user_id, $training_id) {
        
        $db = Db::getConnection();
        $result = $db->query("SELECT id FROM ".PREFICS."training_sertificates WHERE training_id = $training_id AND user_id = $user_id LIMIT 1");
        
        $data = $result->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
   
            $date = time();
            $url = md5($user_id.$training_id.$date);
    
            $result = $db->prepare('INSERT INTO '.PREFICS.'training_sertificates (user_id, training_id, url, date) VALUES (:user_id, :training_id, :url, :date)');
            $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $result->bindParam(':training_id', $training_id,  PDO::PARAM_INT);
            $result->bindParam(':url', $url,  PDO::PARAM_STR);
            $result->bindParam(':date', $date, PDO::PARAM_INT);
    
            if ($result->execute()) {
                self::ShowCertificateByUrl($url, $training_id);
            }
        }      

    }

      /**
     * ПОЛУЧИТЬ СПИСОК ВЫДАННЫХ СЕРТИФИКАТОВ ПОЛЬЗОВАТЕЛЯ
     * @param $user_id
     * @return array|bool
     */
    public static function getCertificates2User($user_id)
    {
 
        $db = Db::getConnection();
        $query = "SELECT * FROM ".PREFICS."training_sertificates WHERE user_id = :user_id";

        $result = $db->prepare($query);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->execute();

        $data = $result->fetchAll(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }

    /**
     * ПРОВЕРИТЬ И ПОЛУЧИТЬ HASH_URL СЕРТИФИКАТА ЕСЛИ ОН ВЫДАН ПОЛЬЗОВАТЕЛЮ 
     * @param $user_id
     * @param $training_id
     * @return mixed|bool
     */
    public static function getUrlHashCertificate2User($user_id, $training_id)
    {
 
        $db = Db::getConnection();
        $query = "SELECT url FROM ".PREFICS."training_sertificates WHERE user_id = :user_id AND training_id =:training_id LIMIT 1";

        $result = $db->prepare($query);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->execute();

        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОКАЗАТЬ СЕРТИФИКАТ ПО ССЫЛКЕ
     * @param $hash_url
     * @param $training_id
     * @param $preview
     */

    public static function ShowCertificateByUrl($hash_url, $training_id = null, $preview = null) {
        if ($preview) {
            $training = Training::getTraining($training_id);
            $date = time();
            $data = [
                'id' => '123456',
                'date' => $date,
                'sertificate' => $training['sertificate'],
            ];
        } else {

            $db = Db::getConnection();
            $query = "SELECT * FROM ".PREFICS."training_sertificates AS ts
                        LEFT JOIN ".PREFICS."training AS t ON t.training_id = ts.training_id
                        LEFT JOIN ".PREFICS."users AS u ON u.user_id = ts.user_id
                        WHERE ts.url = :hash_url LIMIT 1";
    
            $result = $db->prepare($query);
            $result->bindParam(':hash_url', $hash_url, PDO::PARAM_STR);
            $result->execute();
    
            $data = $result->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $savefolder = ROOT . '/images/training/sertificate/received/'.$data['training_id'];
                if (!file_exists($savefolder)){
                    mkdir($savefolder);    
                }
                $save = ROOT . '/images/training/sertificate/received/'.$data['training_id'].'/'.strtolower($hash_url) .".png";
                if (file_exists($save)) {
                    header('Content-type: image/png');
                    $image = imagecreatefrompng($save);
                    imagepng($image);
                    imagedestroy($image);
                    exit();
                }
            }
        }
     
        $user_name = isset($data['user_name']) ? $data['user_name'] : 'Имя';
        
        if ($preview) {
            $user_surname = 'Фамилия';
        } else {
            $user_surname = isset($data['surname']) ? $data['surname'] : '';
        }

        $sertificate = json_decode($data['sertificate'], true);
        $css_fiokord = explode('/', $sertificate['fio_koord']);
        $css_number_koord = explode('/', $sertificate['number_koord']);
        $css_date_koord = explode('/', $sertificate['date_koord']);
        $css_fiokord_left = isset($css_fiokord[0]) && !empty($css_fiokord[0]) ? $css_fiokord[0] : '0';
        $css_fiokord_top = isset($css_fiokord[1]) && !empty($css_fiokord[1]) ? $css_fiokord[1] : '0';
        $fio_koord_fs = isset($sertificate['fio_koord_fs']) && !empty($sertificate['fio_koord_fs']) ? $sertificate['fio_koord_fs'] : '14';
        $number_koord_fs = isset($sertificate['number_koord_fs']) && !empty($sertificate['number_koord_fs']) ? $sertificate['number_koord_fs'] : '14';
        $css_number_koord_left = isset($css_number_koord[0]) && !empty($css_number_koord[0]) ? $css_number_koord[0] : '0';
        $css_number_koord_top = isset($css_number_koord[1]) && !empty($css_number_koord[1]) ? $css_number_koord[1] : '0';
        $date_koord_fs = isset($sertificate['date_koord_fs']) && !empty($sertificate['date_koord_fs']) ? $sertificate['date_koord_fs'] : '14';
        $css_date_koord_left = isset($css_date_koord[0]) && !empty($css_date_koord[0]) ? $css_date_koord[0] : '0';
        $css_date_koord_top = isset($css_date_koord[1]) && !empty($css_date_koord[1]) ? $css_date_koord[1] : '0';
        $sert_number = $data['id'];
    
  
        header('Content-type: image/png');

        $image = imagecreatefromjpeg(ROOT . '/images/training/sertificate/'.$sertificate['template_file']);
        $color = imagecolorallocate($image, 0, 0, 0);
        // установка номера
        $font_number = ROOT . '/images/training/sertificate/fonts/'.$sertificate['number_koord_font'];
        imagettftext($image, $number_koord_fs, 0, $css_number_koord_left, $css_number_koord_top, $color, $font_number, $sert_number);
        // установка ФИО

        $name = $user_name.' '.$user_surname;
        $font_fio = ROOT . '/images/training/sertificate/fonts/'.$sertificate['fio_koord_font'];

        // создаём рамку вокруг текста
        $bbox = imageftbbox($fio_koord_fs, 0, $font_fio, $name);
        //$x = $bbox[0] + (imagesx($image) / 2) - ($bbox[4] / 2) - 5;
        $x = $css_fiokord_left - ($bbox[4] / 2);
        imagettftext($image, $fio_koord_fs, 0, $x, $css_fiokord_top, $color, $font_fio, $name);

        /// установка даты 
        $font_date = ROOT . '/images/training/sertificate/fonts/'.$sertificate['date_koord_font'];
        $date_text = System::dateSpeller($data['date']);
        imagettftext($image, $date_koord_fs, 0, $css_date_koord_left, $css_date_koord_top, $color, $font_date, $date_text);
        
        if ($preview) {
            imagepng($image);
        } else {
            imagepng($image, $save);
        } 
        imagedestroy($image);
        
    }
}