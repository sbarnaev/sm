<?php defined('BILLINGMASTER') or die; 


class Course {
    
    use ResultMessage;
    
    const USER_TYPE_ADMIN = 1;
    const USER_TYPE_CURATOR = 2;
    const USER_TYPE_USER = 3;
    
    // ПОЛУЧИТЬ ДАТУ ПОКУПКИ КУРСА (Присвоения группы или плана подписки)
    public static function getPaidUserDate($user_id, $group_ids, $plane_id)
    {
        $sql = "SELECT reg_date FROM ".PREFICS."users WHERE user_id = $user_id LIMIT 1";

        if ($group_ids) {
            $sql = "SELECT date FROM ".PREFICS."user_groups_map WHERE user_id = $user_id AND group_id in($group_ids) ORDER BY date ASC LIMIT 1 ";
        }

        if ($plane_id) {
            $sql = "SELECT date FROM ".PREFICS."member_maps WHERE user_id = $user_id AND subs_id = $plane_id ORDER BY date ASC LIMIT 1";
        }
        
        $db = Db::getConnection();
        $result = $db->query($sql);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        if (isset($data) && !empty($data)) {
            if ($group_ids == true || $plane_id == true) {
                return $data['date'];
            } else {
                return $data['reg_date'];
            }
        } else {
            return false;
        }
    }
    
    
    // ПОЛУЧИТЬ СПИСОК ЮЗЕРОВ КТО ПРОХОДИТ/ПРОШЁЛ УРОК
    public static function getLessonStat($lesson_id, $type, $start, $finish, $groups = false, $planes = false)
    {
        if (!$start || !$finish) {
            return false;
        }

        $type = intval($type);
        $db = Db::getConnection();
        
        $status = $type != 100 ? "AND status = $type" : null;

        if ($planes) {
            $sub_sql = "SELECT user_id FROM ".PREFICS."member_maps WHERE subs_id IN ($planes) AND begin > $start AND begin < $finish ORDER BY user_id DESC";
        } elseif ($groups) {
            $sub_sql = "SELECT user_id FROM ".PREFICS."user_groups_map WHERE group_id IN ($groups) AND date > $start AND date < $finish ORDER BY user_id DESC";
        } else {
            $sub_sql = "SELECT user_id FROM ".PREFICS."users WHERE reg_date > $start AND reg_date < $finish ORDER BY user_id DESC";
        }

        $data = [];
        // получить список юзеров кто попал в группы
        $sql = "SELECT * FROM ".PREFICS."course_lesson_map WHERE lesson_id = $lesson_id $status AND user_id IN ($sub_sql) ORDER BY status ASC";
        $result = $db->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }


        return !empty($data) ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ ПРОЙДЕННЫЕ УРОКИ И КУРСЫ ЮЗЕРА
    public static function getCompleteLessonsByUserID($user_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course_lesson_map WHERE user_id = $user_id GROUP BY course_id DESC");

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }


        return !empty($data) ? $data : false;
    }


    // ПОЛУЧИТЬ СТАТУС ПРОЙДЕННОГО УРОКА ЮЗЕРА
    public static function getStatusCompleteLesson($user_id, $lesson_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT status FROM ".PREFICS."course_lesson_map WHERE user_id = $user_id AND lesson_id = $lesson_id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['status'] : false;
    }
    
    
    // УДАЛИТЬ ПРОХОЖДЕНИЕ УРОКА У ЮЗЕРА
    public static function delCompleteLesson($user_id, $lesson_id) {
        $db = Db::getConnection();
        $sql = "DELETE FROM ".PREFICS."course_lesson_map WHERE user_id = :user_id AND lesson_id = :lesson_id";
        
        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        
        return $result->execute();
    }
    
    // ПОЛУЧИТЬ уникальыне ID КУРСОВ из карты прохождения
    public static function getUniqCourseInUserMap($user_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT DISTINCT course_id FROM ".PREFICS."course_lesson_map WHERE user_id = $user_id");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[]['course_id'] = $row['course_id'];
        }

        return !empty($data) ? $data : false;
    }
    
    // КОПИРОВАНИЕ КУРСА
    public static function copyCourse($course_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course WHERE course_id = $course_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data) && !empty($data)) {
            
            $alias = $data['alias'].'-2';
            $status = 0;
            $name = $data['name'].' копия';
            $sql = 'INSERT INTO '.PREFICS.'course (cat_id, name, title, meta_desc, meta_keys, alias, course_desc, cover, img_alt, auto_train, 
                                                show_lessons_count, show_desc, show_progress, show_comments, show_hits,
                                                show_pupil, sort, sort_less, prof_id, start_date, end_date, sertificate_id, status, curators,
                                                type_access_buy, product_access, link_access, show_begin, free_lessons, show_in_main, short_desc, is_free, button_text,
                                                view_desc, access_type, groups, access ) 
                    VALUES (:cat_id, :name, :title, :meta_desc, :meta_keys, :alias, :course_desc, :cover, :img_alt, :auto_train, 
                            :show_lessons_count, :show_desc, :show_progress, :show_comments, :show_hits,
                            :show_pupil, :sort, :sort_less, :prof_id, :start_date, :end_date, :sertificate_id, :status, :curators, 
                            :type_access_buy, :product_access, :link_access, :show_begin, :free_lessons, :show_in_main, :short_desc, :is_free, :button_text, :view_desc,
                            :access_type, :groups, :access)';
            
            $result = $db->prepare($sql);
            $result->bindParam(':cat_id', $data['cat_id'], PDO::PARAM_INT);
            $result->bindParam(':show_in_main', $data['show_in_main'], PDO::PARAM_INT);
            $result->bindParam(':name', $name, PDO::PARAM_STR);
            $result->bindParam(':title', $data['title'], PDO::PARAM_STR);
            $result->bindParam(':meta_desc', $data['meta_desc'], PDO::PARAM_STR);
            $result->bindParam(':meta_keys', $data['meta_keys'], PDO::PARAM_STR);
            $result->bindParam(':alias', $alias, PDO::PARAM_STR);
            $result->bindParam(':button_text', $data['button_text'], PDO::PARAM_STR);
            
            $result->bindParam(':access_type', $data['access_type'], PDO::PARAM_INT);
            $result->bindParam(':groups', $data['groups'], PDO::PARAM_STR);
            $result->bindParam(':access', $data['access'], PDO::PARAM_STR);
            
            $result->bindParam(':short_desc', $data['short_desc'], PDO::PARAM_STR);
            $result->bindParam(':view_desc', $data['view_desc'], PDO::PARAM_STR);
            
            $result->bindParam(':course_desc', $data['course_desc'], PDO::PARAM_STR);
            $result->bindParam(':cover', $data['cover'], PDO::PARAM_STR);
            $result->bindParam(':img_alt', $data['img_alt'], PDO::PARAM_STR);
            $result->bindParam(':auto_train', $data['alias'], PDO::PARAM_INT);
            $result->bindParam(':show_lessons_count', $data['show_lessons_count'], PDO::PARAM_INT);
            
            $result->bindParam(':is_free', $data['is_free'], PDO::PARAM_INT);
            
            $result->bindParam(':show_desc', $data['show_desc'], PDO::PARAM_INT);
            $result->bindParam(':show_progress', $data['show_progress'], PDO::PARAM_INT);
            $result->bindParam(':show_comments', $data['show_comments'], PDO::PARAM_INT);
            $result->bindParam(':show_hits', $data['show_hits'], PDO::PARAM_INT);
            $result->bindParam(':show_pupil', $data['show_pupil'], PDO::PARAM_INT);
            $result->bindParam(':sort', $data['sort'], PDO::PARAM_INT);
            $result->bindParam(':sort_less', $data['sort_less'], PDO::PARAM_INT);
            $result->bindParam(':show_begin', $data['show_begin'], PDO::PARAM_INT);
            
            $result->bindParam(':prof_id', $data['prof_id'], PDO::PARAM_INT);
            $result->bindParam(':start_date', $data['start_date'], PDO::PARAM_INT);
            $result->bindParam(':end_date', $data['end_date'], PDO::PARAM_INT);
            $result->bindParam(':sertificate_id', $data['sertificate_id'], PDO::PARAM_INT);
            $result->bindParam(':status', $status, PDO::PARAM_INT);
            $result->bindParam(':curators', $data['curators'], PDO::PARAM_STR);
            
            $result->bindParam(':type_access_buy', $data['type_access_buy'], PDO::PARAM_INT);
            $result->bindParam(':product_access', $data['product_access'], PDO::PARAM_INT);
            $result->bindParam(':link_access', $data['link_access'], PDO::PARAM_STR);
            $result->bindParam(':free_lessons', $data['free_lessons'], PDO::PARAM_INT);

            return $result->execute();
            
        } else {
            return false;
        }
    }

     /**
     * ВЫГРУЗИТЬ КУРС В CSV ФАЙЛ ДЛЯ ИМПОРТА В НОВЫЕ ТРЕНИНГИ
     * @param $course_id
     * @return mixed
     */
    
    public static function exportTrainingtoCSV($course_id){

        $time = time();
        $setting = System::getSetting();
        $fp = fopen(ROOT.'/tmp/course_'.$course_id.'_'.$time.'.csv','w');
        $fplesson = fopen(ROOT.'/tmp/course_lessons_'.$course_id.'_'.$time.'.csv','w');
        $fpusers = fopen(ROOT.'/tmp/course_users_map_'.$course_id.'_'.$time.'.csv','w');
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course WHERE course_id = $course_id LIMIT 1");
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
       
        if (isset($data) && !empty($data)) {
            $autotrening = $data[0]['auto_train'];
            $data[0]['count_free_lessons']=isset($data[0]['free_lessons']) ? $data[0]['free_lessons'] : '';
            $data[0]['full_desc']=isset($data[0]['desc']) ? $data[0]['desc'] : '';
            $data[0]['show_start_date']=$data[0]['show_begin'];
            $data[0]['duration_type']=1;
            $data[0]['show_complexity']=0;
            $data[0]['complexity']=1;
            $data[0]['count_lessons_type']=1;
            $data[0]['sort_lessons']=1;
            $data[0]['show_price']=0;
            $data[0]['cat_id']=0;
            $data[0]['show_widget_progress']=1;
            $data[0]['show_count_lessons']=1;
            $data[0]['show_progress2list']=1;
            $data[0]['lock_comment']=0;
            $data[0]['homework_edit']=1;
            $data[0]['homework_comment_add']=1;
            $data[0]['lessons_tmpl']=1;
            $data[0]['show_before_start']=1; // виден ли тренинг до даты начала, по умолчанию 1
            unset($data[0]['free_lessons']);
            fputcsv($fp, array_keys($data[0]), ',');
            fputcsv($fp, array_values($data[0]), ','); 
        }
        $write = fclose($fp);

        if ($autotrening) {
            $result = $db->query("SELECT 
            lesson_id, course_id, 0 AS block_id, name, title, meta_desc, meta_keys, alias, less_desc, cover, img_alt, IF(access_type=9,3,access_type) AS access_type, access, groups, 
            content, video_urls, audio_urls, attach, attach_title, dopmat, IF(task_type=0,0,1) AS task_type, task, task_time, allow_comments, sort, show_comments, show_hits_count, 
            create_date, public_date, end_date, rating, hits, type_access_buy, product_access, link_access, status, custom_code, duration, timing, timing_period, 
            custom_code_up, IF(access_type>0,1,0) AS stop_lesson, 0 AS section_id, IF(task_type=3,2,IF(task_type=2,1,0)) AS check_type,
            1 AS show_upload_file, 1 AS show_add_link, 0 AS show_work_link, 1 AS access_time, task AS text, 1 AS completed_on_time, 
            1 AS not_completed_on_time FROM ".PREFICS."course_lessons WHERE course_id = $course_id ORDER BY sort ASC");
        } else {
            $result = $db->query("SELECT 
            lesson_id, course_id, 0 AS block_id, name, title, meta_desc, meta_keys, alias, less_desc, cover, img_alt, IF(access_type=9,3,access_type) AS access_type, access, groups, 
            content, video_urls, audio_urls, attach, attach_title, dopmat, IF(task_type=0,0,1) AS task_type, task, task_time, allow_comments, sort, show_comments, show_hits_count, 
            create_date, public_date, end_date, rating, hits, type_access_buy, product_access, link_access, status, custom_code, duration, timing, timing_period, 
            custom_code_up, 0 AS stop_lesson, 0 AS section_id, IF(task_type=3,2,IF(task_type=2,1,0)) AS check_type,
            1 AS show_upload_file, 1 AS show_add_link, 0 AS show_work_link, 1 AS access_time, task AS text, 1 AS completed_on_time, 
            1 AS not_completed_on_time FROM ".PREFICS."course_lessons WHERE course_id = $course_id ORDER BY sort ASC");
        }
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($data)) {
            fputcsv($fplesson, array_keys($data[0]), ',');
            foreach ($data as $lesson) {
                fputcsv($fplesson, array_values($lesson), ',');
            } 
            $write_lesson = fclose($fplesson);
        }

        $result = $db->query("SELECT u.email AS user_email, MAX(cl.sort) AS lesson_sort
                            FROM ".PREFICS."course_lesson_map clm
                            LEFT JOIN ".PREFICS."users AS u ON u.user_id=clm.user_id
                            LEFT JOIN ".PREFICS."course_lessons AS cl ON cl.lesson_id = clm.lesson_id AND cl.course_id = clm.course_id
                            WHERE clm.course_id = $course_id AND (clm.status =1 OR clm.status = 9) AND u.email is not null
                            GROUP BY user_email ORDER BY lesson_sort ASC");
        $data = $result->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            fputcsv($fpusers, array_keys($data[0]), ',');
            foreach ($data as $item) {
                fputcsv($fpusers, array_values($item), ',');
            } 
            $write_users = fclose($fpusers);
        }

        if (isset($write) && isset($write_lesson)) {
            $zip = new ZipArchive();
            $filename = "/tmp/full_course_".$course_id."_".$time."_.zip";
            $fullpathzip = ROOT.$filename;
            if ($zip->open($fullpathzip, ZipArchive::CREATE)!==TRUE) {
                exit("Невозможно открыть <$fullpathzip>\n");
            }         
            $zip->addFile(ROOT.'/tmp/course_'.$course_id.'_'.$time.'.csv', 'course_'.$course_id.'_'.$time.'.csv');
            $zip->addFile(ROOT.'/tmp/course_lessons_'.$course_id.'_'.$time.'.csv', 'lessons_'.$course_id.'_'.$time.'.csv');
            if (isset($write_users)) {
                $zip->addFile(ROOT.'/tmp/course_users_map_'.$course_id.'_'.$time.'.csv', 'users_'.$course_id.'_'.$time.'.csv');
            }
            $zip->close();
            header("Location: ".$setting['script_url'].$filename);
        }

    }
    
    // ПРОВЕРКА ДОСТУПА К КУРСУ
    // Получает массивы данных курса, списка уроков курса, группы юзера и  флаг $user_id - он ибо false, либо содержить ID юзера 
    //
    public static function checkAccessCourse($course, $user_groups, $user_planes, $user_id)
    {
        $data = array();
        $data['access'] = false;
        $course_id = $course['course_id'];
        $str_anchor = System::Lang('GO_FREE_LESSONS');
        
        // ПРОВЕРЯЕМ ДОСТУП К КУРСУ

        if ($course['access_type'] == 9) { // Доступ свободен = выводим жёлтую кнопку Начать просмотр и направляем внутрь
            if ($course['button_text'] != null) {
                $data['action'] = $course['button_text'];
            }  else {
                $data['action'] = 'Начать просмотр';
            }

            $data['class_link'] = 'btn-yellow'; // Класс ссылки
            $data['button_link'] = '/courses/'.$course['alias']; // Генерация ссылки кнопки
            $data['text_link'] = false; // Текстовая ссылка
        } elseif ($course['access_type'] == 1 || $course['access_type'] == 2) { // ЕСЛИ оступ по группе или подписке
            $access = false;

            if ($course['access_type'] == 1) { // Если доступ по группе
                if ($user_groups) { // Если юзер авторизован и у него есть группы
                    $course_groups = unserialize($course['groups']); // Определяем есть ли у юзера доступ к курсу по группе

                    if ($course_groups) {
                        foreach($user_groups as $group_id) {
                            if (in_array($group_id, $course_groups)) {
                                $access = true;
                                break;
                            }
                        }
                    }

                    if ($access) { // Если доступ есть
                        $data['action'] = 'Начать просмотр'; // Даём войти
                        $data['class_link'] = 'btn-yellow'; // Класс ссылки
                        $data['button_link'] = '/courses/'.$course['alias']; // Генерация ссылки кнопки
                        $data['text_link'] = false; // Текстовая ссылка
                        $data['text_link_anchor'] = false;
                    } else { // Если доступа нет, формируем ссылки

                        if ($course['button_text'] != null) {
                            $data['action'] = $course['button_text'];
                        } else {
                            $data['action'] = 'Начать просмотр';
                        }

                        $data['class_link'] = 'btn-blue'; // Класс ссылки
                        $data['button_link'] = self::getLinkAccess($course); // Генерация ссылки кнопки
                        
                        if ($course['free_lessons'] == 0) {
                            $data['text_link'] = false; 
                            $data['text_link_anchor'] = false;  
                            
                            if ($course['view_desc'] != null) {
                                $view_desc = unserialize(base64_decode($course['view_desc']));
                                $data['text_link_anchor'] = $view_desc['link_anchor']; // анкор 
                                
                                if ($view_desc['type'] == 1) {
                                    $product = Product::getProductData($view_desc['product']);
                                    $data['text_link'] = '/catalog/'.$product['product_alias']; // url
                                } elseif ($view_desc['type'] == 2) {
                                    $data['text_link'] = $view_desc['link']; // url
                                }
                            }
                        } else { // Если free уроки есть
                            $data['text_link'] = '/courses/'.$course['alias'];

                            $replace = array(
                                '[XX]' => $course['free_lessons']
                            );
                            $str_anchor = strtr($str_anchor, $replace);
                            $data['text_link_anchor'] = System::addTermination($course['free_lessons'], $str_anchor);
                        }
                    }
                } else { // ЕСЛИ у юзера НЕТ групп или он не авторизован
                    if ($course['button_text'] != null) {
                        $data['action'] = $course['button_text'];
                    } else {
                        $data['action'] = 'Подробнее о курсе';
                    }

                    $data['class_link'] = 'btn-blue'; // Класс ссылки
                    $data['button_link'] = self::getLinkAccess($course); // Генерация ссылки кнопки Купить

                    if ($course['free_lessons'] == 0) { // Если free уроков нет
                        $data['text_link'] = false; // Текстовая ссылка
                        $data['text_link_anchor'] = false;  
                        
                        if ($course['view_desc'] != null) {
                            $view_desc = unserialize(base64_decode($course['view_desc']));
                            $data['text_link_anchor'] = $view_desc['link_anchor']; // анкор 
                            
                            if ($view_desc['type'] == 1) {
                                $product = Product::getProductData($view_desc['product']);
                                $data['text_link'] = '/catalog/'.$product['product_alias']; // url
                            } elseif ($view_desc['type'] == 2) {
                                $data['text_link'] = $view_desc['link']; // url
                            }
                        }
                    } else { // Если free уроки есть
                        $data['text_link'] = '/courses/'.$course['alias'];

                        $replace = array(
                            '[XX]' => $course['free_lessons']
                        );
                        $str_anchor = strtr($str_anchor, $replace);
                        $data['text_link_anchor'] = System::addTermination($course['free_lessons'], $str_anchor);
                    }
                }
            } else { // ЕСЛИ доступ по подписке
                $course_planes = unserialize($course['access']);

                if ($user_planes) {
                    foreach ($user_planes as $plane) {
                        if (in_array($plane['subs_id'], $course_planes)) {
                            $access = true;
                        }
                    }
				}

                if ($access) { // Если доступ есть
                    $data['action'] = 'Начать просмотр'; // Даём войти
                    $data['class_link'] = 'btn-yellow'; // Класс ссылки
                    $data['button_link'] = '/courses/'.$course['alias']; // Генерация ссылки кнопки

                    $data['text_link'] = false; // Текстовая ссылка
                    $data['text_link_anchor'] = false;
                } else { // Если досутпа НЕТ
                    
                    // Формируем ссылки
                    if ($course['button_text'] != null) {
                        $data['action'] = $course['button_text'];
                    } else {
                        $data['action'] = 'Подробнее о курсе';
                    }

                    $data['class_link'] = 'btn-blue'; // Класс ссылки
                    $data['button_link'] = self::getLinkAccess($course); // Генерация ссылки кнопки Купить

                    if ($course['free_lessons'] == 0) { // Если free уроков нет
                        $data['text_link'] = false; // Текстовая ссылка
                        $data['text_link_anchor'] = false;  
                        
                        if ($course['view_desc'] != null) {
                            $view_desc = unserialize(base64_decode($course['view_desc']));
                            $data['text_link_anchor'] = $view_desc['link_anchor']; // анкор 
                            
                            if ($view_desc['type'] == 1) {
                                $product = Product::getProductData($view_desc['product']);
                                $data['text_link'] = '/catalog/'.$product['product_alias']; // url
                            } elseif ($view_desc['type'] == 2) {
                                $data['text_link'] = $view_desc['link']; // url
                            }
                        }
                    } else { // Если free уроки есть
                        $data['text_link'] = '/courses/'.$course['alias'];
                        $replace = array(
                            '[XX]' => $course['free_lessons']
                        );
                        $str_anchor = strtr($str_anchor, $replace);

                        $data['text_link_anchor'] = System::addTermination($course['free_lessons'], $str_anchor);
                    }
                }
            }
        } else { // ЕСЛИ ДОСТУП НЕ ВЫБРАН
            if ($course['button_text'] != null) { // ПУСКАЕМ ВНУТРЬ
                $data['action'] = $course['button_text'];
            } else {
                $data['action'] = 'Начать просмотр';
            }

            $data['class_link'] = 'btn-yellow'; // Класс ссылки
            $data['button_link'] = '/courses/'.$course['alias']; // Генерация ссылки кнопки
            $data['text_link'] = false; // Текстовая ссылка
        }
        
        return $data;
    }


    // ПРОВЕРКА ДОСТУПА К УРОКУ
    public static function checkAcсessLesson($course, $lesson, $user_groups, $user_planes) {
        $access = false;

        switch ($lesson['access_type']) { // если доступ по группам
            case 0:  // свободный доступ
				return true;
				break;
			
			case 1:
                if ($lesson['groups'] != null && $user_groups) {
                    $less_groups = unserialize($lesson['groups']);

                    foreach($less_groups as $group_id) {
                        if ($user_groups && in_array($group_id, $user_groups)) {
                            $access = true;
                            break;
                        }
                    }
                } elseif ($lesson['groups'] == null) {
                    $access = true;
                }
                break;
            case 2: // если по подписке
                if ($lesson['access'] != null && $user_planes) {
                    $less_planes = unserialize($lesson['access']);

                    foreach($user_planes as $plane) {
                        if (in_array($plane['subs_id'], $less_planes)) {
                            $access = true;
                            break;
                        }
                    }
                } elseif ($lesson['access'] == null) {
                    $access = true;
                }
                break;
            case 9: // ИЗ настроек курса
                if ($course['access_type'] == 1) { // Если по группам
                    if ($course['groups'] != null && $user_groups) {
                        $less_groups = unserialize($course['groups']);

                        foreach($less_groups as $group_id) {
                            if ($user_groups && in_array($group_id, $user_groups)) {
                                $access = true;
                                break;
                            }
                        }
                    } elseif ($course['groups'] == null) {
                        $access = true;
                    }
                } elseif ($course['access_type'] == 2) { // Если по подпискам
                    if ($course['access'] != null && $user_planes) {
                        $course_planes = unserialize($course['access']);

                        foreach($user_planes as $plane) {
                            if (in_array($plane['subs_id'], $course_planes)) {
                                $access = true;
                                break;
                            }
                        }
                    } elseif ($course['access'] == null) {
                        $access = true;
                    }
                } else {
                    $access = true;
                }
                break;
            default:
                true;
        }

        return $access;
    }
    
    
    
    // СОЗДАТЬ ССЫЛКУ НА ПОКУПКУ ДОСТУПА
    public static function getLinkAccess($course)
    {
        $button_link = '';
        // Если страница заказа
        if ($course['type_access_buy'] == 1) $button_link = '/buy/'.$course['product_access'];
                        
        // Если внутренний лендинг
        if ($course['type_access_buy'] == 2) {
            $product = Product::getProductData($course['product_access']);
            $button_link = '/catalog/'.$product['product_alias'];
        }
        if ($course['type_access_buy'] == 3) $button_link = $course['link_access'];
        
        return $button_link;
    }
    
    
    // СОЗДАТЬ БЛОК УРОКА
    public static function addLessonBlock($id, $name, $sort)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'course_lessons_blocks (course_id, sort, block_name ) 
                VALUES (:course_id, :sort, :block_name)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':block_name', $name, PDO::PARAM_STR);
  		$result->bindParam(':course_id', $id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // НАЗВАНИЕ БЛОКА КУРСА
    public static function getBlockLessonName($block_id)
    {
        $db = Db::getConnection();
        $block_id = intval($block_id);
        $result = $db->query("SELECT * FROM ".PREFICS."course_lessons_blocks WHERE block_id = $block_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['block_name'] : false;
    }
    
    
    // ПОЛУЧИТЬ БЛОКИ ДЛЯ КУРСА
    public static function getBlocksFromCourse($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course_lessons_blocks WHERE course_id = $id ORDER BY sort ASC");

        $data = [];
        while($row = $result->fetch()) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    // ИЗМЕНИТЬ или УДАЛИТЬ БЛОК
    public static function editLessonBlock($block_id, $sort, $block_name, $act)
    {
        $db = Db::getConnection();
        if ($act == 0) {
            // проверить наличие блоков у урока
            $check = self::checkLessononBlock($block_id);
            if (!$check) {
                
                $sql = 'DELETE FROM '.PREFICS.'course_lessons_blocks WHERE block_id = :id';
                $result = $db->prepare($sql);
                $result->bindParam(':id', $block_id, PDO::PARAM_INT);
                return $result->execute();
            } else {
                return false;
            }
        } else {
            // изменить блок
            $sql = 'UPDATE '.PREFICS.'course_lessons_blocks SET block_name = :name, sort = :sort WHERE block_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $block_id, PDO::PARAM_INT);
            $result->bindParam(':name', $block_name, PDO::PARAM_STR);
            $result->bindParam(':sort', $sort, PDO::PARAM_INT);

            return $result->execute();
        }
    }
    
    
    // Проверка наличия блока у урока
    public static function checkLessononBlock($block_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(lesson_id) FROM ".PREFICS."course_lessons WHERE block_id = $block_id");
        $count = $result->fetch();

        return $count[0] > 0 ? $count[0] : false;
    }
    
    
    // КОЛ_ВО КУРСОВ В КАТЕГОРИИ
    public static function countCourseinCategory($cat_id, $status)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(course_id) FROM ".PREFICS."course WHERE cat_id = $cat_id AND status = 1");
        $count = $result->fetch();

        return $count[0];
    }
    
    
    
    // АВТОПРОВЕРКА 
    public static function AutoConfirmTask()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT id, lesson_id, date, status FROM ".PREFICS."course_lesson_map WHERE status = 9 ORDER BY id ASC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        if (!empty($data)) {
            foreach ($data as $item) {
                $time = time();

                if ($item['date'] < $time) {
                    $status = 1;
                    $sql = 'UPDATE '.PREFICS.'course_lesson_map SET status = :status WHERE id = '.$item['id'];
                    $result = $db->prepare($sql);
                    $result->bindParam(':status', $status, PDO::PARAM_INT);
                    $result->execute();
                }
            }
        }
    }
    
    
    // ПОЛУЧИТЬ МАКСИМАЛЬНОЕ ЗНАЧЕНИЕ НОМЕРА УРОКА
    public static function getMaxNumLesson($course_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT MAX(sort) FROM ".PREFICS."course_lessons WHERE course_id = $course_id LIMIT 1");
        $count = $result->fetch();

        return $count[0];
    }
    
    
    // ПОЛУЧИТЬ УРОК из КУРСА с нужным sort
    public static function getLessonByCount($course_id, $sort)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course_lessons WHERE course_id = $course_id AND sort = $sort");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ СПИСОК ОТВЕТОВ НА ЗАДАНИЯ
    public static function getAnswerList($status, $page = 1, $show_items = null, $is_pagination = null, $course_id = null, $lesson_id = null)
    {
        $offset = ($page - 1) * $show_items;
        $clauses = array();

        if ($course_id != null) {
            $clauses[] = "c.course_id = $course_id";
        }

        if ($lesson_id != null) {
            $clauses[] = "c.lesson_id = $lesson_id";
        }

        $clauses[] = $status == 0 ? "c.status = $status" : 'c.parent_id = 0';

        $where = !empty($clauses) ? (' WHERE ' . implode(' AND ', $clauses)) : '';
        $sql = "SELECT c.*, u.email AS user_email, u.user_name, u.sex FROM " . PREFICS . "course_answers AS c
                LEFT JOIN " . PREFICS . "users AS u ON c.user_id = u.user_id"
            . "$where GROUP BY c.id, c.user_id, c.lesson_id"
            . ($is_pagination ? " LIMIT $show_items OFFSET $offset" : '');

        $db = Db::getConnection();
        $result = $db->query($sql);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $row['body']  = System::isBase64($row['body']) ? base64_decode($row['body']) : $row['body'];
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ ОБЩЕЕ КОЛ-ВО ОТВЕТОВ
    public static function getTotalAnswers($status = null, $course_id = null, $lesson_id = null)
    {
        $db = Db::getConnection();
        
        $clauses = array();
        
        if ($status !== null) {
            $clauses[] = $status == 0 ? "status = $status" : "parent_id = 0";
        }

        if ($course_id) {
            $clauses[] = "course_id = $course_id";
        }

        if ($lesson_id) {
            $clauses[] = "lesson_id = $lesson_id";
        }

        $where = !empty($clauses) ? (' WHERE ' . implode(' AND ', $clauses)) : '';
        
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."course_answers".$where);
        $count = $result->fetch();

        return $count[0];
    }
    
    
    // ПОЛУЧИТЬ СПИСОК СООБЩЕНИЙ В ДИАЛОГЕ (один юзер в одном уроке)
    public static function getDialogList($user, $lesson)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course_answers WHERE user_id = $user AND lesson_id = $lesson ORDER BY date ASC");

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $row['body']  = System::isBase64($row['body']) ? base64_decode($row['body']) : $row['body'];
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ СПИСОК ОТВЕТОВ на СООБЩЕНИЕ
    public static function getAnswerFromMess($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course_answers WHERE parent_id = $id ORDER BY date ASC");

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $row['body']  = System::isBase64($row['body']) ? base64_decode($row['body']) : $row['body'];
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }

    // ПОЛУЧИТЬ СООБЩЕНИЕ
    public static function getAnswer($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course_answers WHERE id = $id");

        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $data['body']  = System::isBase64($data['body']) ? base64_decode($data['body']) : $data['body'];
        }

        return !empty($data) ? $data : false;
    }


    // ЗАПИСАТЬ ОТВЕТ НА СООБЩЕНИЕ к заданию
    public static function AddAnswertoDialog($id, $curator_id, $message, $success, $lesson, $user_id, $course_id = null, $attach = '')
    {
        $date = time();
        $status = 1;
        
        if ($success == 1) {
            self::UpdateLessonComplete($user_id, $lesson, 1, $date );
            if (empty($message)) {
                return true;
            }
        }

        $message = base64_encode($message);

        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'course_answers (parent_id, lesson_id, user_id, status, date, body, course_id, attach) 
                VALUES (:parent_id, :lesson_id, :user_id, :status, :date, :body, :course_id, :attach)';


        $result = $db->prepare($sql);
        $result->bindParam(':parent_id', $id, PDO::PARAM_INT);
        $result->bindParam(':body', $message, PDO::PARAM_STR);
        $result->bindParam(':lesson_id', $lesson, PDO::PARAM_INT);
        $result->bindParam(':user_id', $curator_id, PDO::PARAM_INT);
        $result->bindParam(':date', $date, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $result->bindParam(':attach', $attach, PDO::PARAM_STR);

        return $result->execute();
    }
    
    
    // ОБНОВИТЬ СТАТУС СООБЩЕНИЙ ПОСЛЕ ОТВЕТА НА НИХ
    public static function UpdateDialogAnswersStatus($lesson_id, $user_id)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'course_answers SET status = 1 WHERE lesson_id = :lesson_id AND user_id = :user_id ';
        $result = $db->prepare($sql);
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $result->execute();
    }
    
    
    // ПОЛУЧИТЬ СПИСОК ПРОЙДЕННЫХ УРОКОВ ЮЗЕРА
    // Принимает ID юзера, ID курса и тип статуса 1 - только пройденные, 0 - все 
    public static function getCompleteLessonsUser($user_id, $course_id, $status = 1, $order_by = null)
    {
        if ($order_by == 1) {
            $order_by = " ORDER BY lesson_id ASC";
        } elseif ($order_by == 2) {
            $order_by = " ORDER BY date ASC";
        }

        if ($status == 0) {
            $sql = 'status IN (1, 9, 0)';
        } elseif ($status == 1) {
            $sql = 'status = 1';
        } else {
            $sql = 'status IN (1, 9)';
        }

        $db = Db::getConnection();
        $result = $db->query("SELECT DISTINCT lesson_id, status, course_id, date FROM ".PREFICS."course_lesson_map WHERE user_id = $user_id AND course_id = $course_id AND $sql $order_by");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    // СЧИТАЕМ КОЛ_ВО пройденных уроков в курсе
    public static function countUniqCompeteLessons ($course_id, $user_id, $status)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(DISTINCT lesson_id) FROM ".PREFICS."course_lesson_map WHERE user_id = $user_id AND course_id = $course_id AND status = $status");
        $count = $result->fetch();

        return $count[0];
    }
    
    
    // ПОЛУЧИТЬ ПОРЯДОК (SORT) для урока по ID 
    public static function getSortLessByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT sort FROM ".PREFICS."course_lessons WHERE lesson_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['sort'] : false;
    }
    


    // ПРОХОЖДЕНИЕ УРОКА и ВЫПОЛНЕНИЕ ЗАДАНИЯ 
    public static function ProcessTask($user, $course_id, $lesson_id, $task_type, $delay, $answer, $date, $curators, $attach = '')
    {
        // Если задание = 1 или 0, без проверки или его нет, то помечаем урок пройден и направляем на следующий урок по порядку.
        if ($task_type == 1 || $task_type == 0) {
            $write = self::WriteLessonComplete($user, $course_id, $lesson_id, 1, $date );
            return $write ? true : false;
        }
        
        // Если задание = 2, автопроверка, то проверяем delay
        // Далее пишем ответ к заданию
        if ($task_type == 2) {
            if ($delay == 0) {
                $write = self::WriteLessonComplete($user, $course_id, $lesson_id, 1, $date );
            } else {
                $write = self::WriteLessonComplete($user, $course_id, $lesson_id, 9, $date + ($delay * 60));
            }

            $result = $write ? true : false;
            
            if ($answer != null) {
                $write = self::WriteLessonAnswer($user, $lesson_id, $answer, $date, 1, $course_id, $attach);
                if ($write) {
                    // Отправить уведомления
                    $send = Email::SendCheckTaskToAdmin($user, $lesson_id, $answer, $curators);
                    $result = true;
                } else {
                    $result = false;
                }
            }
            
            return $result;
        }
        
        // Если задание = 3, ручная проверка, то пишем ответ к заданию, и отправляем письмо админу или кураторам.
        if ($task_type == 3 && $answer != null) {
            // Записать ответ
            $write = self::WriteLessonAnswer($user, $lesson_id, $answer, $date, 0, $course_id, $attach);

            if ($write) {
                $write = self::WriteLessonComplete($user, $course_id, $lesson_id, 0, $date);
                // Отправить уведомления
                $send = Email::SendCheckTaskToAdmin($user, $lesson_id, $answer, $curators);

                return true;
            }

            return false;
        }
    }
    
    
    // ЗАПИСАТЬ ПРОХОЖДЕНИЕ УРОКА
    public static function WriteLessonComplete ($user_id, $course_id, $lesson_id, $status, $date) {
        
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(user_id) FROM ".PREFICS."course_lesson_map WHERE user_id = $user_id AND lesson_id = $lesson_id");
        $count = $result->fetch();

        if ($count[0] == 0) {
            $sql = 'INSERT INTO '.PREFICS.'course_lesson_map (user_id, course_id, lesson_id, status, date) 
                    VALUES (:user_id, :course_id, :lesson_id, :status, :date)';
        
            $result = $db->prepare($sql);
            $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
            $result->bindParam(':course_id', $course_id, PDO::PARAM_INT);
            $result->bindParam(':status', $status, PDO::PARAM_INT);
            $result->bindParam(':date', $date, PDO::PARAM_INT);

            return $result->execute();
        } else {
            return false;
        }
    }
    
    
    // ОБНОВИТЬ СТАТУС ПРОЙДЕНОГО УРОКА
    public static function UpdateLessonComplete($user_id, $lesson, $status = null, $date = null)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'course_lesson_map SET status = 1, date = :date WHERE lesson_id = '.$lesson.' AND user_id = '.$user_id;
        $result = $db->prepare($sql);
        $result->bindParam(':date', $date, PDO::PARAM_INT);

        return $result->execute();
    }
    
    
    // ЗАПИСЬ ОТВЕТА В ЗАДАНИИ УРОКА
    public static function WriteLessonAnswer($user, $lesson_id, $answer, $date, $status, $course_id = null, $attach = '')
    {
        $answer = base64_encode($answer);

        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'course_answers (course_id, lesson_id, user_id, status, date, body, attach) 
                VALUES (:course_id, :lesson_id, :user_id, :status, :date, :body, :attach)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':body', $answer, PDO::PARAM_STR);
        $result->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user, PDO::PARAM_INT);
        $result->bindParam(':date', $date, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':attach', $attach, PDO::PARAM_STR);

        return $result->execute();
    }
    
    
    // ПОЛУЧИТЬ СПИСОК УРОКОВ 
    // Принимает статус: 0 - не опбуликованные, 1 - только опубликованные, 2 - все
    // Принимает ID курса: 0 - все курсы, или ID курса
    public static function getLessonsList($status, $course = 0, $sort = 1)
    {
        $status = intval($status);
        $course = intval($course);
        $sort = $sort == 1 ? 'ASC' : 'DESC';

        $db = Db::getConnection();
        $time = time();
        $sql = "SELECT * FROM ".PREFICS."course_lessons ";

        if ($status == 2) { // для админа
            $sql .= $course == 0 ? "ORDER BY sort $sort" : "WHERE course_id = $course ORDER BY sort $sort";
        } elseif ($status == 0) { // для админа
            $sql .= $course == 0 ? "WHERE status = $status ORDER BY sort $sort" : "WHERE status = $status AND course_id = $course ORDER BY sort $sort";
        } elseif ($status == 1) { // Для юзеров
            $sql .= "WHERE status = 1 AND course_id = $course AND public_date < $time AND end_date > $time ORDER BY sort $sort";
        }

        $result = $db->query($sql);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ СПИСОК УРОКОВ ДЛЯ SITEMAP
    public static function getLessonListFromSitemap()
    {
        $time = time();
        $db = Db::getConnection();
        $result = $db->query("SELECT lesson_id, course_id, alias FROM ".PREFICS."course_lessons WHERE status = 1 AND public_date < $time AND end_date > $time ORDER BY lesson_id DESC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    // ПОЛУЧИТЬ ДАННЫЕ УРОКА по ID 
    // Принимает id урока и флаг 0 - любой статус, 1 - только опубликованные
    public static function getLessonDataByID($id, $status = 0)
    {
        $query = "SELECT * FROM ".PREFICS."course_lessons WHERE lesson_id = $id";
        $query .= $status != 0 ? ' AND status = 1' : '';

        $db = Db::getConnection();
        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
    
    // МИНИ ДАННЫЕ УРКОА ПО ID 
    public static function getLessonMiniData($lesson_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT block_id, name, title, task_type FROM ".PREFICS."course_lessons WHERE lesson_id = $lesson_id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    } 
    
    
    // ПОЛУЧИТЬ ДАННЫЕ УРОКА ПО АЛИАСУ и ID курса + записать хит
    public static function getLessonDataByAlias($alias, $course_id)
    {
        $db = Db::getConnection();
        $time = time();
        $result = $db->query("SELECT * FROM ".PREFICS."course_lessons WHERE course_id = $course_id AND alias = '$alias' AND status = 1 AND public_date < $time AND end_date > $time");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            $hits = $data['hits'] + 1;
            // Записать переход
            $sql = 'UPDATE '.PREFICS.'course_lessons SET hits = :hits WHERE lesson_id = '.$data['lesson_id'];
            $result = $db->prepare($sql);
            $result->bindParam(':hits', $hits, PDO::PARAM_STR);
            $result->execute();
            
            return $data;   
        } else {
            return false;
        }
    }
    
    
    
    // ДОБАВИТЬ УРОК
    public static function AddLesson($name, $course_id, $cover, $img_alt, $type_access, $groups, $accesses, $sort, $status,
                                     $allow_comments, $show_comments, $show_hits_count, $start, $end, $alias, $title, $meta_desc,
                                     $meta_keys, $desc, $content, $dopmat, $task_type, $task_time, $task, $type_access_buy,
                                     $product_access, $link_access, $custom_code, $video_urls, $audio_urls, $duration, $block_id,
                                     $attach = '')
    {
        $db = Db::getConnection();
        
        $rating = 0;
        $hits = 0;
        if (empty($sort)) {
            $result = $db->query("SELECT sort FROM ".PREFICS."course_lessons WHERE course_id = $course_id ORDER BY sort DESC LIMIT 1");
            $data = $result->fetch(PDO::FETCH_ASSOC);
            $sort = !empty($data) ? $data['sort'] + 1 : 0;
        }
        
        $sql = 'INSERT INTO '.PREFICS.'course_lessons (course_id, name, title, meta_desc, meta_keys, alias, less_desc, cover, img_alt,
                        access_type, access, groups, content, allow_comments, sort, show_comments, show_hits_count, public_date, end_date,
                        rating, hits, status, dopmat, task_type, task_time, task, type_access_buy, product_access, link_access, custom_code,
                        video_urls, audio_urls, duration, block_id , attach)
                VALUES (:course_id, :name, :title, :meta_desc, :meta_keys, :alias, :less_desc, :cover, :img_alt, :access_type, :access, 
                        :groups, :content, :allow_comments, :sort, :show_comments, :show_hits_count, :public_date, :end_date, :rating,
                        :hits, :status, :dopmat, :task_type, :task_time, :task, :type_access_buy, :product_access, :link_access, :custom_code,
                        :video_urls, :audio_urls, :duration, :block_id, :attach)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':less_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':cover', $cover, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':block_id', $block_id, PDO::PARAM_INT);
        
        $result->bindParam(':task_type', $task_type, PDO::PARAM_INT);
        $result->bindParam(':task_time', $task_time, PDO::PARAM_INT);
        $result->bindParam(':task', $task, PDO::PARAM_STR);
        
        $result->bindParam(':access_type', $type_access, PDO::PARAM_INT);
        $result->bindParam(':access', $accesses, PDO::PARAM_STR);
        $result->bindParam(':groups', $groups, PDO::PARAM_STR);
        $result->bindParam(':content', $content, PDO::PARAM_STR);
        $result->bindParam(':allow_comments', $allow_comments, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':show_comments', $show_comments, PDO::PARAM_INT);
        $result->bindParam(':show_hits_count', $show_hits_count, PDO::PARAM_INT);
        
        $result->bindParam(':public_date', $start, PDO::PARAM_INT);
        $result->bindParam(':end_date', $end, PDO::PARAM_INT);
        $result->bindParam(':rating', $rating, PDO::PARAM_STR);
        $result->bindParam(':hits', $hits, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':dopmat', $dopmat, PDO::PARAM_STR);
        $result->bindParam(':video_urls', $video_urls, PDO::PARAM_STR);
		$result->bindParam(':audio_urls', $audio_urls, PDO::PARAM_STR);
        
        $result->bindParam(':type_access_buy', $type_access_buy, PDO::PARAM_INT);
        $result->bindParam(':product_access', $product_access, PDO::PARAM_INT);
        $result->bindParam(':link_access', $link_access, PDO::PARAM_STR);
        $result->bindParam(':custom_code', $custom_code, PDO::PARAM_STR);
        $result->bindParam(':duration', $duration, PDO::PARAM_INT);
        $result->bindParam(':attach', $attach, PDO::PARAM_STR);
        
        $res = $result->execute();
        return $res ? $db->lastInsertId() : false;
    }
    
    
    
    // ИЗМЕНИТЬ УРОК
    public static function EditLesson($id, $name, $course_id, $img, $img_alt, $type_access, $groups, $accesses, $sort, $status,
                                      $allow_comments, $show_comments, $show_hits_count, $start, $end, $alias, $title, $meta_desc,
                                      $meta_keys, $desc, $content, $dopmat, $task_type, $task_time, $task, $type_access_buy,
                                      $product_access, $link_access, $custom_code, $custom_code_up, $video_urls, $audio_urls, $block_id,
                                      $duration, $timing, $timing_period, $attach = '')
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'course_lessons SET course_id = :course_id, name = :name, title = :title, meta_desc = :meta_desc,
                meta_keys = :meta_keys, alias = :alias, less_desc = :less_desc, cover = :cover, img_alt = :img_alt, access_type = :access_type,
                access = :access, groups = :groups, content = :content, dopmat = :dopmat, allow_comments = :allow_comments, sort = :sort,
                show_comments = :show_comments, show_hits_count = :show_hits_count, public_date = :public_date, end_date = :end_date,
                status = :status, task_type = :task_type, task_time = :task_time, task = :task, type_access_buy = :type_access_buy,
                product_access = :product_access, link_access = :link_access, custom_code = :custom_code, custom_code_up = :custom_code_up,
                video_urls = :video_urls, audio_urls = :audio_urls, block_id = :block_id, duration = :duration, timing = :timing,
                timing_period = :timing_period, attach = :attach WHERE lesson_id = '.$id;

        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $result->bindParam(':block_id', $block_id, PDO::PARAM_INT);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':less_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':cover', $img, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':dopmat', $dopmat, PDO::PARAM_STR);
        
        $result->bindParam(':task_type', $task_type, PDO::PARAM_INT);
        $result->bindParam(':task_time', $task_time, PDO::PARAM_INT);
        $result->bindParam(':task', $task, PDO::PARAM_STR);
        $result->bindParam(':timing', $timing, PDO::PARAM_INT);
        $result->bindParam(':timing_period', $timing_period, PDO::PARAM_STR);
        
        $result->bindParam(':access_type', $type_access, PDO::PARAM_INT);
        $result->bindParam(':access', $accesses, PDO::PARAM_STR);
        $result->bindParam(':groups', $groups, PDO::PARAM_STR);
        $result->bindParam(':content', $content, PDO::PARAM_STR);
        $result->bindParam(':allow_comments', $allow_comments, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':show_comments', $show_comments, PDO::PARAM_INT);
        $result->bindParam(':show_hits_count', $show_hits_count, PDO::PARAM_INT);
        
        $result->bindParam(':public_date', $start, PDO::PARAM_INT);
        $result->bindParam(':end_date', $end, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':video_urls', $video_urls, PDO::PARAM_STR);
		$result->bindParam(':audio_urls', $audio_urls, PDO::PARAM_STR);
        
        $result->bindParam(':type_access_buy', $type_access_buy, PDO::PARAM_INT);
        $result->bindParam(':product_access', $product_access, PDO::PARAM_INT);
        $result->bindParam(':link_access', $link_access, PDO::PARAM_STR);
        $result->bindParam(':custom_code', $custom_code, PDO::PARAM_STR);
        $result->bindParam(':custom_code_up', $custom_code_up, PDO::PARAM_STR);
        $result->bindParam(':duration', $duration, PDO::PARAM_INT);
        $result->bindParam(':attach', $attach, PDO::PARAM_STR);

        return $result->execute();
    }
    
    
    
    // УДАЛИТЬ УРОК
    public static function DelLesson($id)
    {
        $db = Db::getConnection();
        
        /*$result = $db->query("SELECT cover FROM ".PREFICS."course_lessons WHERE lesson_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        if (isset($data)) {
            $path = ROOT .'/images/lessons/'.$data['cover'];
            if (strlen($data['cover']) > 0 && file_exists($path)) {
                unlink($path);
            }

            $path = ROOT ."/load/lessons/$id";
            if (file_exists($path)) {
                $files = scandir($path);
                foreach ($files as $file) {
                    if ($file != '.' & $file != '..') {
                        $file_path = $path . '/' . $file;
                        unlink($file_path);
                    }
                }
                rmdir ($path);
            }
        }*/

        $sql = 'DELETE FROM '.PREFICS.'course_lessons WHERE lesson_id = :id; DELETE FROM '.PREFICS.'course_answers WHERE lesson_id = :id;
                DELETE FROM '.PREFICS.'course_lesson_map WHERE lesson_id = :id';

        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        return $result->execute();
    }

    // КОПИРОВАТЬ УРОК
    public static function copyLesson($lesson_id)
    {
        $lesson = self::getLessonDataByID($lesson_id);
        foreach ($lesson as $key => $value) {
            $$key = $value;
        }

        $alias .= '-1';
        $name .= ' копия';

        $new_lesson_id = self::AddLesson($name, $course_id, $cover, $img_alt, $access_type, $groups, $access, 100, 0,
            $allow_comments, $show_comments, $show_hits_count, $public_date, $end_date, $alias, $title, $meta_desc, $meta_keys,
            $less_desc, $content, $dopmat, $task_type, $task_time, $task, $type_access_buy, $product_access, $link_access,
            $custom_code, $video_urls, $audio_urls, $duration, $block_id, $attach
        );

        if ($new_lesson_id && $attach) {
            self::copyLessonAttach($attach, $lesson_id, $new_lesson_id);
        }
        
        return $new_lesson_id;
    }

    
    public static function copyLessonAttach($attach, $lesson_id, $new_lesson_id) {
        $folder = ROOT . "/load/lessons/$new_lesson_id"; // папка для сохранения
        mkdir($folder);
        
        $attachments = json_decode($attach, true);

        foreach($attachments as $attach_name) {
            $attach_source = ROOT . "/load/lessons/$lesson_id/$attach_name";
            $attach_dest = "$folder/$attach_name";
            copy($attach_source, $attach_dest);
        }
    }
    
    
    // ОБНОВИТЬ ВЛОЖЕНИЯ У УРОКА
    public static function UpdateLessonAttach($id, $attach)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'course_lessons SET attach = :attach WHERE lesson_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':attach', $attach, PDO::PARAM_STR);

        return $result->execute();
    }


    // ОБНОВИТЬ СОРТИРОВКУ ДЛЯ УРОКА (SORT)
    public static function UpdateSortLesson($lesson_id, $sort)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'course_lessons SET sort = :sort WHERE lesson_id = :lesson_id';
        $result = $db->prepare($sql);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);

        return $result->execute();
    }


    // ПОЛУЧИТЬ СПИСОК КУРСОВ
    // Принимает статус 1 - только опубликованые, 0 - все.
    // Принимает флаг категории, 0 - из любой, либо ID категории 
    public static function getCourseList($status, $cat = 0)
    {
        $cat = intval($cat);
        $status = intval($status);
        $time = time();
        $sql = "SELECT * FROM ".PREFICS."course ";

        if ($status == 0) {
            $sql .= $cat == 0 ? "ORDER BY sort ASC" : "WHERE cat_id = $cat ORDER BY sort ASC";
        } else {
            $sql .= $cat == 0 ? "WHERE show_in_main = 1 AND cat_id = $cat AND status = $status ORDER BY sort ASC" : "WHERE cat_id = $cat AND status = $status ORDER BY sort ASC";
        }

        $db = Db::getConnection();
        $result = $db->query($sql);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ СПИСОК КУРСОВ БЕЗ КАТЕГОРИЙ.
    public static function getAllCourseList($show_in_main = 0)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."course WHERE status = 1 ";
        $sql .= $show_in_main == 0 ? "ORDER BY sort ASC" : "AND show_in_main = 1 ORDER BY sort ASC";
        $result = $db->query($sql);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }

    
    
    // ПОЛУЧИТЬ СПИСОК КУРСОВ ДЛЯ SITEMAP
    public static function getCourseListFromSitemap()
    {
        $time = time();
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course WHERE status = 1 ORDER BY course_id ASC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    // ПОЛУЧИТЬ ДАННЫЕ КУРСА ПО ID 
    public static function getCourseByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course WHERE course_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
    
    
    
    // НАЗВАНИЕ КУРСА ПО ID 
    public static function getCourseNameByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT name FROM ".PREFICS."course WHERE course_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['name'] : '';
    }
    
    
    // ПОЛУЧИТЬ ДАННЫЕ КУРСА ПО АЛИАСУ
    public static function getCourseDataByAlias($alias)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course WHERE alias = '$alias' AND status = 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
    
    
    // ДОБАВИТЬ КУРС
    public static function AddCourse($name, $cat_id, $prof_id, $autotrain, $img, $img_alt, $sort, $sort_less, $status, $alias, $title, $meta_desc, $meta_keys,
                                    $desc, $lessons_count, $show_desc, $show_progress, $show_comments, $show_hits, $show_pupil, $sertificate, $start, $end, $curators, 
                                    $type_access, $product_access, $link_access, $show_begin, $free_lessons, $show_in_main, $short_desc, $is_free, $button_text, 
                                    $view_desc, $course_access, $groups, $access)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'course (cat_id, name, title, meta_desc, meta_keys, alias, course_desc, cover, img_alt, auto_train, 
                                                show_lessons_count, show_desc, show_progress, show_comments, show_hits,
                                                show_pupil, sort, sort_less, prof_id, start_date, end_date, sertificate_id, status, curators,
                                                type_access_buy, product_access, link_access, show_begin, free_lessons, show_in_main, short_desc, is_free, button_text,
                                                view_desc, access_type, groups, access ) 
                VALUES (:cat_id, :name, :title, :meta_desc, :meta_keys, :alias, :course_desc, :cover, :img_alt, :auto_train, 
                        :show_lessons_count, :show_desc, :show_progress, :show_comments, :show_hits,
                        :show_pupil, :sort, :sort_less, :prof_id, :start_date, :end_date, :sertificate_id, :status, :curators, 
                        :type_access_buy, :product_access, :link_access, :show_begin, :free_lessons, :show_in_main, :short_desc, :is_free, :button_text, :view_desc,
                        :access_type, :groups, :access)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $result->bindParam(':show_in_main', $show_in_main, PDO::PARAM_INT);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':button_text', $button_text, PDO::PARAM_STR);
        
        $result->bindParam(':access_type', $course_access, PDO::PARAM_INT);
        $result->bindParam(':groups', $groups, PDO::PARAM_STR);
        $result->bindParam(':access', $access, PDO::PARAM_STR);
        
        $result->bindParam(':short_desc', $short_desc, PDO::PARAM_STR);
        $result->bindParam(':view_desc', $view_desc, PDO::PARAM_STR);
        
        $result->bindParam(':course_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':cover', $img, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':auto_train', $autotrain, PDO::PARAM_INT);
        $result->bindParam(':show_lessons_count', $lessons_count, PDO::PARAM_INT);
        
        $result->bindParam(':is_free', $is_free, PDO::PARAM_INT);
        
        $result->bindParam(':show_desc', $show_desc, PDO::PARAM_INT);
        $result->bindParam(':show_progress', $show_progress, PDO::PARAM_INT);
        $result->bindParam(':show_comments', $show_comments, PDO::PARAM_INT);
        $result->bindParam(':show_hits', $show_hits, PDO::PARAM_INT);
        $result->bindParam(':show_pupil', $show_pupil, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':sort_less', $sort_less, PDO::PARAM_INT);
        $result->bindParam(':show_begin', $show_begin, PDO::PARAM_INT);
        
        $result->bindParam(':prof_id', $prof_id, PDO::PARAM_INT);
        $result->bindParam(':start_date', $start, PDO::PARAM_INT);
        $result->bindParam(':end_date', $end, PDO::PARAM_INT);
        $result->bindParam(':sertificate_id', $sertificate, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':curators', $curators, PDO::PARAM_STR);
        
        $result->bindParam(':type_access_buy', $type_access, PDO::PARAM_INT);
        $result->bindParam(':product_access', $product_access, PDO::PARAM_INT);
        $result->bindParam(':link_access', $link_access, PDO::PARAM_STR);
        $result->bindParam(':free_lessons', $free_lessons, PDO::PARAM_INT);

        return $result->execute();
    }
    
    
    
    // ИЗМЕНИТЬ КУРС
    public static function EditCourse($id, $name, $cat_id, $prof_id, $autotrain, $img, $img_alt, $sort, $sort_less, $status, $alias,
                                      $title, $meta_desc, $meta_keys, $desc, $lessons_count, $show_desc, $show_progress, $show_comments,
                                      $show_hits, $show_pupil, $sertificate, $start, $end, $curators, $type_access, $product_access,
                                      $link_access, $author_id, $padding, $show_begin, $free_lessons, $show_in_main, $short_desc, $is_free,
                                      $button_text, $view_desc, $course_access, $groups, $access)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'course SET cat_id = :cat_id, name = :name, title = :title, meta_desc = :meta_desc, meta_keys = :meta_keys, 
                alias = :alias, course_desc = :course_desc, cover = :cover, img_alt = :img_alt, auto_train = :auto_train,
                show_lessons_count = :show_lessons_count, show_desc = :show_desc, show_progress = :show_progress, show_comments = :show_comments,
                show_hits = :show_hits, show_pupil = :show_pupil, sort = :sort, sort_less = :sort_less, prof_id = :prof_id,
                start_date = :start_date, end_date = :end_date, sertificate_id = :sertificate_id, status = :status, curators = :curators, 
                type_access_buy = :type_access_buy, product_access = :product_access, link_access = :link_access, author_id = :author_id,
                padding = :padding, show_begin = :show_begin, free_lessons = :free_lessons, show_in_main = :show_in_main,
                short_desc = :short_desc, is_free = :is_free, button_text = :button_text, view_desc = :view_desc, access_type = :access_type,
                groups = :groups, access = :access WHERE course_id = '.$id;

        $result = $db->prepare($sql);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $result->bindParam(':show_in_main', $show_in_main, PDO::PARAM_INT);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':padding', $padding, PDO::PARAM_STR);
        
        $result->bindParam(':short_desc', $short_desc, PDO::PARAM_STR);
        $result->bindParam(':is_free', $is_free, PDO::PARAM_INT);
        $result->bindParam(':button_text', $button_text, PDO::PARAM_STR);
        $result->bindParam(':view_desc', $view_desc, PDO::PARAM_STR);
        
        $result->bindParam(':course_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':cover', $img, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':auto_train', $autotrain, PDO::PARAM_INT);
        $result->bindParam(':show_lessons_count', $lessons_count, PDO::PARAM_INT);
        
        $result->bindParam(':access_type', $course_access, PDO::PARAM_INT);
        $result->bindParam(':groups', $groups, PDO::PARAM_STR);
        $result->bindParam(':access', $access, PDO::PARAM_STR);
        
        $result->bindParam(':show_desc', $show_desc, PDO::PARAM_INT);
        $result->bindParam(':show_progress', $show_progress, PDO::PARAM_INT);
        $result->bindParam(':show_comments', $show_comments, PDO::PARAM_INT);
        $result->bindParam(':show_hits', $show_hits, PDO::PARAM_INT);
        $result->bindParam(':show_pupil', $show_pupil, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':sort_less', $sort_less, PDO::PARAM_INT);
        $result->bindParam(':show_begin', $show_begin, PDO::PARAM_INT);
        
        $result->bindParam(':prof_id', $prof_id, PDO::PARAM_INT);
        $result->bindParam(':start_date', $start, PDO::PARAM_INT);
        $result->bindParam(':end_date', $end, PDO::PARAM_INT);
        $result->bindParam(':sertificate_id', $sertificate, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':curators', $curators, PDO::PARAM_STR);
        
        $result->bindParam(':type_access_buy', $type_access, PDO::PARAM_INT);
        $result->bindParam(':product_access', $product_access, PDO::PARAM_INT);
        $result->bindParam(':link_access', $link_access, PDO::PARAM_STR);
        $result->bindParam(':author_id', $author_id, PDO::PARAM_INT);
        $result->bindParam(':free_lessons', $free_lessons, PDO::PARAM_INT);

        return $result->execute();
    }
    
    
    
    // ПОЛУЧИТЬ КАТЕГОРИИ КУРСОВ  
    public static function getCourseCatFromList($status = 0)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."course_category ";
        $sql .= $status == 0 ? "ORDER BY cat_id ASC" : "WHERE status = 1 ORDER BY cat_id ASC";
        $result = $db->query($sql);

        $data = [];
        while($row = $result->fetch()) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ ДАННЫЕ КАТЕГОРИИ
    public static function getCourseCatData($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course_category WHERE cat_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ ДАННЫЕ КАТЕГОРИИ ПО АЛИАСУ
    public static function getCatDataByAlias($cat_name)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course_category WHERE alias = '$cat_name'");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
    
    
    // СОЗДАТЬ КАТЕГОРИЮ 
    public static function AddCategory($name, $img, $img_alt, $alias, $title, $meta_desc, $meta_keys, $status, $desc)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'course_category (name, alias, title, meta_desc, meta_keys, cat_desc, cover, img_alt, status) 
                VALUES (:name, :alias, :title, :meta_desc, :meta_keys, :cat_desc, :cover, :img_alt, :status)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':cover', $img, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);

        return $result->execute();
    }
    
    
    // ИЗМЕНИТЬ КАТЕГОРИЮ
    public static function EditCategory($id, $name, $img, $img_alt, $alias, $title, $meta_desc, $meta_keys, $status, $desc)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'course_category SET name = :name, alias = :alias, title = :title, meta_desc = :meta_desc, 
                                        meta_keys = :meta_keys, cat_desc = :cat_desc, cover = :cover, img_alt = :img_alt, 
                                        status = :status WHERE cat_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':cover', $img, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);

        return $result->execute();
    }
    
    
    // УДАЛИТЬ КАТЕГОРИЮ
    public static function DelCategory($id)
    {
        $db = Db::getConnection();
        
        $result = $db->query("SELECT cover FROM ".PREFICS."course_category WHERE cat_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $path = 'images/course/category/'.$data['cover'];
            if (file_exists($path)) {
                unlink ($path);
            }
        }
        
        $result = $db->query("SELECT COUNT(*) FROM ".PREFICS."course WHERE cat_id = $id");
        $count = $result->fetch();
        if ($count[0] == 0) {
            $sql = 'DELETE FROM '.PREFICS.'course_category WHERE cat_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);

            return $result->execute();
        } else {
            return false;
        }
    }
    
    
    // ПОЛУЧИТЬ СПИСОК ПРОФЕССИЙ КУРСОВ
    public static function getCourseProfList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT prof_id, name, title FROM ".PREFICS."course_professions ORDER BY prof_id ASC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    // ДОБАВИТЬ ПРОФЕССИЮ
    public static function AddProff($name, $alias, $title, $meta_desc, $meta_keys, $prof_desc )
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'course_professions (name, alias, title, prof_desc, meta_desc, meta_keys ) 
                VALUES (:name, :alias, :title, :prof_desc, :meta_desc, :meta_keys)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':prof_desc', $prof_desc, PDO::PARAM_STR);

        return $result->execute();
    }
    
    
    // ИЗМЕНИТЬ ПРОФЕССИЮ
    public static function EditProff($id, $name, $alias, $title, $meta_desc, $meta_keys, $prof_desc)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'course_professions SET name = :name, alias = :alias, title = :title, prof_desc = :prof_desc, 
                meta_desc = :meta_desc, meta_keys = :meta_keys WHERE prof_id = '.$id;

        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':prof_desc', $prof_desc, PDO::PARAM_STR);

        return $result->execute();
    }
    
    
    // ПОЛУЧИТЬ ДАННЫЕ ПО ПРОФЕССИИ
    public static function getCourseProfData($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course_professions WHERE prof_id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
    
    
    // УДАЛИТЬ ПРОФЕССИЮ
    public static function DelProff($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(*) FROM ".PREFICS."course WHERE prof_id = $id");
        $count = $result->fetch();

        if ($count[0] == 0) {
            $sql = 'DELETE FROM '.PREFICS.'course_professions WHERE prof_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);

            return $result->execute();
        } else {
            return false;
        }
    }
    
    
    // УДАЛИТЬ КУРС
    public static function DelCourse($id)
    {
        $db = Db::getConnection();
        
        $count_lessons = self::countLessonByCourse($id);
        
        if (!$count_lessons) {
            $sql = 'DELETE FROM '.PREFICS.'course WHERE course_id = :id;';
            $sql .= 'DELETE FROM '.PREFICS.'course_lessons_blocks WHERE course_id = :id';
            $result = $db->prepare($sql);
            
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $result->execute();
            
            $result ? self::addSuccess('Успешно!') : self::addError('Не возможно удалить: Возникла ошибка в процессе удаления курса');
            
            return $result;
        } else {
            self::addError('Не возможно удалить: Курс содержит уроки');
            return false;
        }
    }


    // ОБНОВИТЬ СОРТИРОВКУ ДЛЯ КУРСА (SORT)
    public static function UpdateSortCourse($course_id, $sort) {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'course SET sort = :sort WHERE course_id = :course_id';
        $result = $db->prepare($sql);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':course_id', $course_id, PDO::PARAM_INT);

        return $result->execute();
    }

    
    // ПОЛУЧИТЬ СПИСОК СЕРТИФИКАТОВ ДЛЯ КУРСОВ
    public static function getCourseSertificateList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT id, name FROM ".PREFICS."course_sertificate ORDER BY id ASC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    
    /**
     *   ДОП,МАТ И ЗАДАНИЯ
     */
    
    // ПОЛУЧИТЬ СПИСОК ДОП, МАТЕРИАЛОВ
    public static function getDopMatList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course_materials ORDER BY cat_id ASC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ ДАННЫЕ МАТЕРИАЛА ПО ID
    public static function getDopMatData($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."course_materials WHERE mat_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
    
    
    // СОЗДАТЬ ДОП МАТ
    public static function AddDopMatItem($name, $cat_id, $file)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'course_materials (file, name, cat_id ) VALUES (:file, :name, :cat_id)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':file', $file, PDO::PARAM_STR);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);

        return $result->execute();
    }


    // ПОЛУЧИТЬ ССЫЛКУ НА ДОПМАТ В УРОКЕ
    public static function getDopmatLink($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT file, name FROM ".PREFICS."course_materials WHERE mat_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
    
    
    // ИЗМЕНИТЬ ДОП МАТ
    public static function EditDopMat($id, $name, $cat_id, $file )
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'course_materials SET file = :file, name = :name, cat_id = :cat_id WHERE mat_id = '.$id;

        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':file', $file, PDO::PARAM_STR);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);

        return $result->execute();
    }
    
    
    // УДАЛИТЬ ФАЙЛ ИЗ ДОПМАТА 
    public static function delFileInDopmat($id)
    {
        $db = Db::getConnection();
        
        $result = $db->query("SELECT file FROM ".PREFICS."course_materials WHERE mat_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data)) {
            $path = 'load/dopmat/'.$data['file'];
            if (file_exists($path)) {
                unlink ($path);
            }
        }
        
        $file = null;  
        $sql = 'UPDATE '.PREFICS.'course_materials SET file = :file WHERE mat_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':file', $file, PDO::PARAM_STR);

        return $result->execute();
    }


    // ДОБАВИТЬ КАТЕГОРИЮ ДОПМАТА
    public static function AddDopmatCat($name)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'course_mat_cats (name) VALUES (:name)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);

        return $result->execute();
    }


    // ИМЯ КАТЕГОРИИ ДОПМАТА 
    public static function getDopmatCatName($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT name FROM ".PREFICS."course_mat_cats WHERE cat_id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['name'] : false;
    }
    
    
    // ПОЛУЧИТЬ СПИСОК КАТЕГОРИЙ ДОПМАТА
    public static function getDopmatCat()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT cat_id, name FROM ".PREFICS."course_mat_cats ORDER BY cat_id ASC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    // УДАЛИТЬ КАТЕГОРИЮ ДОПМАТА
    public static function delDopmatCat($id)
    {
        $db = Db::getConnection();
        
        $result = $db->query("SELECT COUNT(*) FROM ".PREFICS."course_materials WHERE cat_id = $id");
        $count = $result->fetch();
        if ($count[0] == 0) {
            $sql = 'DELETE FROM '.PREFICS.'course_mat_cats WHERE cat_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            return $result->execute();   
        } else {
            return false;
        }
    }


    /**
     * УДАЛИТЬ ДОП МАТ
     * @param $id
     * @return bool
     */
    public static function delDopmat($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT file FROM ".PREFICS."course_materials WHERE mat_id = $id");

        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($data)) {
            $path = 'load/dopmat/'.$data['file'];
            if (file_exists($path)) {
                unlink ($path);
            }
        }

        $sql = 'DELETE FROM '.PREFICS.'course_materials WHERE mat_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * СДЕЛАТЬ (УДАЛИТЬ) ЮЗЕРА КУРАТОРОМ КУРСОВ
     * @param $id
     * @param $value
     * @return bool
     */
    public static function AddIsCurator($id, $value)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'users SET is_curator = :is_curator WHERE user_id = :id ';
        $result = $db->prepare($sql);
        $result->bindParam(':is_curator', $value, PDO::PARAM_INT);
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПОСЧИТАТЬ ПРОСМОТРЫ УРОКОВ КУРСА
     * @param $id
     * @return mixed
     */
    public static function countHitsByCourse($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT SUM(hits) FROM ".PREFICS."course_lessons WHERE course_id = $id");
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * ПОСЧИТАТЬ КОЛ_ВО УРОКОВ КУРСА
     * @param $course_id
     * @param null $status
     * @return mixed
     */
    public static function countLessonByCourse($course_id, $status = null)
    {
        $db = Db::getConnection();
        $sql = "SELECT COUNT(lesson_id) FROM ".PREFICS."course_lessons WHERE course_id = $course_id";
        $sql .= $status != null ? ' AND status = 1' : '';
        $result = $db->query($sql);
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * ПОСЧИТАТЬ ОБЩУЮ ПРОДОЛЖИТЕЛЬНОСТЬ УРОКОВ
     * @param $id
     * @return mixed
     */
    public static function countDurationByCourse($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT SUM(duration) FROM ".PREFICS."course_lessons WHERE course_id = $id");     
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * УДАЛИТЬ ДИАЛОГ
     * @param $user
     * @param $lesson
     * @return bool
     */
    public static function delDialog($user, $lesson)
    {
        $db = Db::getConnection();
        // Получить список всех удаляемых сообщений
        $result = $db->query("SELECT id FROM ".PREFICS."course_answers WHERE user_id = $user AND lesson_id = $lesson");
        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[]['id'] = $row['id'];
        }

        if (!empty($data)) {
            self::delDialogAttach($user, $lesson);

            foreach($data as $item) {
                $mess = $item['id'];
                $sql = 'DELETE FROM '.PREFICS.'course_answers WHERE parent_id = :mess';
                $result = $db->prepare($sql);
                $result->bindParam(':mess', $mess, PDO::PARAM_INT);
                $result->execute();
            }
            
            $sql = 'DELETE FROM '.PREFICS.'course_answers WHERE user_id = :user AND lesson_id = :lesson';
            $result = $db->prepare($sql);
            $result->bindParam(':user', $user, PDO::PARAM_INT);
            $result->bindParam(':lesson', $lesson, PDO::PARAM_INT);

            return $result->execute();
        }
        
    }


    /**
     * УДАЛИТЬ СООБЩЕНИЕ В ДИАЛОГЕ
     * @param $id
     * @return bool
     */
    public static function delDialogMessage($id)
    {
        self::delDialogMessageAttach($id);

        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'course_answers WHERE id = :id OR parent_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        return $result->execute();
    }

    /**
     * УДАЛИТЬ ВЛОЖЕНИЯ В ДИАЛОГЕ
     * @param $user
     * @param $lesson
     */
    public static function delDialogAttach($user, $lesson) {
        $paths = array(
            ROOT ."/load/hometask/lessons/$lesson/curators",
            ROOT ."/load/hometask/lessons/$lesson/users"
        );
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $dirs = scandir($path);
                foreach ($dirs as $dir) {
                    if ($dir != '.' & $dir != '..') {
                        $files = scandir("$path/$dir");
                        foreach ($files as $file) {
                            if ($file != '.' & $file != '..') {
                                $file_path = "$path/$dir/$file";
                                unlink($file_path);
                            }
                        }
                        rmdir("$path/$dir");
                    }
                }
                rmdir($path);
            }
        }
    }


    /**
     * УДАЛИТЬ ВЛОЖЕНИЯ В СООБЩЕНИИ
     * @param $id
     */
    public static function delDialogMessageAttach($id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT lesson_id, attach FROM ".PREFICS."course_answers WHERE id = $id OR parent_id = $id");

        $data = [];
        $i = 0;
        while($row = $result->fetch()) {
            $data[$i]['lesson_id'] = $row['lesson_id'];
            $data[$i++]['attach'] = $row['attach'];
        }

        if (!empty($data)) {
            foreach ($data as $item) {
                if (!empty($item['attach'])) {
                    foreach (json_decode($item['attach'], true) as $attachment) {
                        if (!self::searchDialogMessageAttach($attachment['path'], $id)) {
                            $path = ROOT . urldecode($attachment['path']);
                            if (file_exists($path)) {
                                unlink($path);
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * НАЙТИ ВЛОЖЕНИЯ В ДРУГИХ СООБЩЕНИЯХ
     * @param $attach_path
     * @param $id
     * @return bool
     */
    private static function searchDialogMessageAttach($attach_path, $id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."course_answers WHERE attach like '%$attach_path%' AND id <> $id");
        $count = $result->fetch();

        return $count[0] > 0 ? $count[0] : false;
    }


    /**
     * СОХРАНИТЬ ВЛОЖЕНИЯ И ВЕРНУТЬ ДАННЫЕ
     * @param $lesson_id
     * @param $user_id
     * @param $user_type
     * @return false|string
     */
    public static function attachUpload($lesson_id, $user_id, $user_type) {
        $allowed_mimes = array(
            'application/msword',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/rtf',
            'image/jpeg',
            'image/png',
            'application/pdf',
            'image/gif',
            'application/zip',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/pjpeg',
            'application/xml',
            'application/excel',
            'application/vnd.ms-excel',
            'application/msexcel',
            'application/x-excel',
            'application/x-dos_ms_excel',
            'application/xls',
            'application/x-xls',
            'application/x-msexcel',
            'application/x-ms-excel',
            'application/x-compressed',
            'application/x-zip-compressed',
            'multipart/x-zip',
            'application/octet-stream',
        );
        $allowed_extensions = array('doc','docx','pdf','xls','xlsx','ppt',
            'pptx','zip','rar','7z','jpg','jpeg','jpe','bmp','png','txt',
            'rtf','gif','xps','odt','ods','odp','csv'
        );
        
        $settings = System::getSetting();
        $max_size = $settings['max_upload'] * 1048576 ?: 7340032;
        $attachments = [];
        $user_folder = $user_type == SELF::USER_TYPE_USER ? 'users' : 'curators';
        
        foreach($_FILES['lesson_attach']["name"] as $key => $attach_name) {
            $attach_name = System::getSecureString($attach_name, true);
            
            if ($_FILES["lesson_attach"]["size"][$key] == 0 || $_FILES["lesson_attach"]["size"][$key] > $max_size) {
                continue;
            }
            
            if ($user_type == SELF::USER_TYPE_USER) {
                $pathinfo = pathinfo($attach_name);
                if (!isset($pathinfo['extension'])) {
                    continue;
                }
                
                if (!in_array($pathinfo['extension'], $allowed_extensions)  || !in_array($_FILES['lesson_attach']['type'][$key], $allowed_mimes)) {
                    continue;
                }
            }
            
            $tmp_name = $_FILES["lesson_attach"]["tmp_name"][$key]; // Временное имя файла на сервере
            $relative_path = '/load/hometask/lessons';
            $folders = array("/$lesson_id", "/$user_folder", "/$user_id");
            
            foreach ($folders as $folder) {
                $relative_path .= $folder;
                
                if (!file_exists(ROOT . $relative_path)) {
                    mkdir(ROOT . $relative_path);
                }
            }
            
            $unique_name = md5(microtime(true).mt_rand(100,999).$attach_name);
            $fileinfo = pathinfo($attach_name);
            $relative_path .= "/$unique_name.{$fileinfo['extension']}";
            
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
     * СОХРАНИТЬ НАСТРОЙКИ КУРСОВ
     * @param $params
     * @param $status
     * @return bool
     */
    public static function SaveCourseSetting($params, $status)
    {
        $db = Db::getConnection();  
        $sql = "UPDATE ".PREFICS."extensions SET params = :params, enable = :enable WHERE name = 'courses'";
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        $result->bindParam(':enable', $status, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ НАСТРОЙКИ КУРСОВ
     * @return bool
     */
    public static function getCourseSetting()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT params FROM ".PREFICS."extensions WHERE name = 'courses'");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['params'] : false;
    }


    /**
     * Получить статус курсов
     * @return bool
     */
    public static function getCourseStatus()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT enable FROM ".PREFICS."extensions WHERE name = 'courses'");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['enable'] : false;
    }
}
