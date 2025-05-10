<?php defined('BILLINGMASTER') or die;


class TrainingStatistics {

    use ResultMessage;


    /**
     * @param $training_id
     * @param $filter
     * @param $count_lessons
     * @return array
     */
    public static function getUsersStatistics($training_id, $filter, $count_lessons) {
        $db = Db::getConnection();
        $where = 'WHERE tum.training_id = :training_id';
        $having = $sub_query = '';

        if ($filter['is_filter']) {
            $clauses = $clauses2 = [];
            if ($filter['email']) {
                $clauses[] = "u.email = '{$filter['email']}'";
            }
            if ($filter['curator']) {
                $clauses[] = "tc.curator_id = '{$filter['curator']}'";
                $sub_query = "INNER JOIN ".PREFICS."training_curator_to_user AS tc ON tc.user_id = tum.user_id 
                              AND tc.training_id = :training_id";
            }

            if ($filter['start_date']) {
                $clauses[] = "tum.open >= {$filter['start_date']}";
            }
            if ($filter['finish_date']) {
                $clauses[] = "tum.open < {$filter['finish_date']}";
            }
            if ($filter['completed_lessons']) {
                $clauses2[] = "completed = {$filter['completed_lessons']}";
            }
            if ($filter['last_lesson_complete']) {
                $clauses2[] = "last_lesson_complete = {$filter['last_lesson_complete']}";
            }

            if ($filter['lesson_id'] && $filter['lesson_status']) {
                $sub_sql = '';

                switch ($filter['lesson_status']) {
                    case 1: // прошли урок
                        $_where = "tum.status = 3";
                        break;
                    case 2: // Не выслали дз
                        $_where = "tum.status = 0";
                        break;
                    case 3: // Отправили дз на проверку
                        $_where = "tum.status = 1";
                        break;
                    case 4:
                    case 5: // Не проходили тест/не сдали тест
                        $_where = $filter['lesson_status'] == 4 ? 'thw.test IS NULL OR thw.test = 0' : 'thw.test = 2';
                        $sub_sql = "INNER JOIN ".PREFICS."training_home_work AS thw ON thw.user_id = tum.user_id 
                                    AND thw.lesson_id = tum.lesson_id";
                        break;
                    case 6: // Получили незачет
                        $_where = "tum.status = 2";
                        break;
                }

                $sql = "SELECT tum.user_id FROM ".PREFICS."training_user_map AS tum
                        $sub_sql WHERE tum.lesson_id = {$filter['lesson_id']} AND $_where";
                $clauses[] = "tum.user_id IN ($sql)";
            }

            if ($filter['pass_status']) {
                switch ($filter['pass_status']) {
                    case 1: // Ни разу не входили в курс
                        $clauses[] = "tum.user_id NOT IN (SELECT user_id FROM ".PREFICS."training_user_visits
                                      WHERE training_id = :training_id)";
                        break;
                    case 2: // Заходили, но не проходили уроки
                        $clauses[] = "tum.user_id IN (SELECT user_id FROM ".PREFICS."training_user_visits
                                      WHERE training_id = :training_id)";
                        $clauses[] = "tum.user_id NOT IN (SELECT user_id FROM ".PREFICS."training_user_map
                                      WHERE training_id = :training_id AND status = 3)";
                        break;
                    case 3: // Остановились в процессе обучения
                        $clauses2[] = "completed < $count_lessons";
                        break;
                    case 4: // Закончил
                        $clauses2[] = "completed >= $count_lessons";
                        break;
                }
            }

            $where .= !empty($clauses) ? ' AND ' . implode(' AND ', $clauses) : '';
            $having .= !empty($clauses2) ? 'HAVING ' . implode(' AND ', $clauses2) : '';
        }

        $query = "SELECT tum.user_id, CONCAT_WS(' ', u.user_name, u.surname) AS user_name, COUNT(IF(tum.status = 3,1,null)) AS completed,
                  MIN(tum.open) AS open, MAX(tum.lesson_id) AS last_lesson_complete
                  FROM ".PREFICS."training_user_map AS tum
                  LEFT JOIN ".PREFICS."users AS u ON u.user_id = tum.user_id
                  $sub_query $where GROUP BY tum.user_id $having";
        $result = $db->prepare($query);

        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->execute();

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $row['progress'] = $count_lessons ? round(($row['completed'] / $count_lessons) * 100) .'%' : 0;
            $last_lesson_complete = $row['last_lesson_complete'] ? TrainingLesson::getLesson($row['last_lesson_complete']) : null;
            $row['last_lesson_complete_name'] = $last_lesson_complete ? $last_lesson_complete['name'] : '';
            $last_visit = TrainingUserVisits::getVisit($row['user_id'], $training_id);
            $row['was_not'] = $last_visit ? round((time() - $last_visit['date'])/86400) : null;
            $data[] = $row;
        }

        return $data;
    }


    public static function getCommonStatistics($training, $section, $filter) {
        $db = Db::getConnection();
        $query = $where = $condition = $having = '';
        $stat = [
            'users' => 0,
            'started' => 0,
            'progress_started' => '0%',
        ];
        $time = time();

        if ($filter['is_filter']) {
            $clauses = [];
            if ($filter['start_date']) {
                $clauses[] = "tum.open >= {$filter['start_date']}";
            }
            if ($filter['finish_date']) {
                $clauses[] = "tum.open < {$filter['finish_date']}";
            }

            $where .= !empty($clauses) ? ' AND ' . implode(' AND ', $clauses) : '';
        }

        $where_section = $section ? "AND tl.section_id = {$section['section_id']}" : '';

        $access_type = !$section || $section['access_type'] == 3 ? $training['access_type'] : $section['access_type'];
        if ($access_type == 1) { // Группа
            $groups = !$section || $section['access_type'] == 3 ? $training['access_groups'] : $section['access_groups'];
            if ($groups) {
                $groups = implode(',', json_decode($groups, true));
                $query = "SELECT MAX(t1.users) as users,max(t1.started) as started FROM(
                    SELECT COUNT(distinct ugm.user_id) AS users, 0 AS started
                    FROM ".PREFICS."user_groups_map as ugm
                    WHERE ugm.group_id IN ($groups) 
                    UNION ALL
                    SELECT 0, count(distinct tum.user_id) AS started
                    FROM ".PREFICS."training_user_map tum
                    LEFT JOIN ".PREFICS."user_groups_map AS ugm ON tum.user_id = ugm.user_id 
                    LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = tum.lesson_id 
                    WHERE tum.training_id = {$training['training_id']} $where $where_section) as t1";
            }
        } elseif ($access_type == 2) { // Подписка
            $planes = !$section || $section['access_type'] == 3 ? $training['access_planes'] : $section['access_planes'];
            if ($planes) {
                $planes = implode(',', json_decode($planes, true));
                $query = "SELECT MAX(t1.users) as users, MAX(t1.started) as started FROM
                        (SELECT COUNT(DISTINCT mm.user_id) AS users, 0 AS started
                        FROM ".PREFICS."member_maps AS mm
                        WHERE mm.subs_id IN ($planes) AND mm.begin < $time AND mm.end > $time $where 
                        UNION ALL
                        SELECT 0 AS users, COUNT(DISTINCT tum.user_id) AS started
                        FROM ".PREFICS."training_user_map AS tum
                        LEFT JOIN ".PREFICS."member_maps AS mm ON tum.user_id = mm.user_id
                        LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = tum.lesson_id
                        WHERE tum.training_id = {$training['training_id']} $where_section) as t1";
            }
        } else { // Свободный доступ
            $count_users = User::countUsers();
            $query = "SELECT $count_users AS users, COUNT(DISTINCT tum.user_id) AS started
                      FROM ".PREFICS."training_user_map AS tum
                      RIGHT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = tum.lesson_id
                      AND tum.training_id = {$training['training_id']} $where $where_section";
        }

        if ($query) {
            $result = $db->query($query);
            $stat = $result->fetch(PDO::FETCH_ASSOC);
            $stat['progress_started'] = $stat['users'] ? round(($stat['started'] / $stat['users']) * 100) .'%' : 0;
        }

        if (!$section) { // статистика по тренингу (3 - вошёл в урок, 4 - ответил в уроке, 5 - выполнил урок, 6 - дата окончания)
            $count_keys = [
                3 => 'entered', // вошёл в урок
                4 => 'answered', // ответил в уроке
                5 => 'completed', // выполнил урок
            ];
        } else { // статистика по разделу (0 - не учитывать прохождение, 1 - дата окончания тренинга, 2 - вошёл в урок, 3 - выполнил урок)
            $count_keys = [
                2 => 'entered', // вошёл в урок
                3 => 'completed', // выполнил урок
            ];
        }

        $finish_type = !$section ? $training['finish_type'] : $section['finish_type'];
        $finish_lessons = !$section ? json_decode($training['finish_lessons'], true) : json_decode($section['finish_lessons'], true);
        $finish_lessons_str = $finish_lessons ? implode(',', $finish_lessons) : null;

        if ((!$section && in_array($finish_type, [3,4,5]) && $finish_lessons_str) 
            || ($section && in_array($finish_type, [2, 3]) && $finish_lessons_str)) {
            $query = "SELECT COUNT(DISTINCT tum.user_id) AS entered, COUNT(IF(tum.status>0,1,null)) AS answered,
                  COUNT(IF(tum.status=3,1,null)) AS completed
                  FROM ".PREFICS."training_user_map AS tum
                  LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.lesson_id = tum.lesson_id
                  WHERE tum.training_id = {$training['training_id']} AND tum.lesson_id IN ($finish_lessons_str)
                  $where_section $where";
            $result = $db->query($query);
            $data = $result->fetch(PDO::FETCH_ASSOC);
            $stat['finished'] = $data[$count_keys[$finish_type]];
        } elseif((!$section && $finish_type == 6) || ($section && $finish_type == 1)) {
            $stat['finished'] = $time >= $training['end_date'] ? $stat['users'] : 0;
        } else {
            $stat['finished'] = 0;
        }

        $stat['progress_finished'] = $stat['users'] ? round(($stat['finished'] / $stat['users']) * 100) .'%' : 0;

        return $stat;
    }


    public static function getLessonsStatistics($training_id, $filter, $count_lessons) {

        $db = Db::getConnection();
        $where = 'WHERE tl.training_id = :training_id';

        if ($filter['is_filter']) {
            $clauses = [];
            if ($filter['start_date']) {
                $clauses[] = "tum.open >= {$filter['start_date']}";
            }
            if ($filter['finish_date']) {
                $clauses[] = "tum.open < {$filter['finish_date']}";
            }

            $where .= !empty($clauses) ? ' AND ' . implode(' AND ', $clauses) : '';
        }

        $query = "SELECT tl.lesson_id, tl.name, COUNT(IF(tum.status=0,1,null)) as no_send,
                  COUNT(IF(tum.status=1,1,null)) as on_check, COUNT(IF(tum.status=2,1,null)) as fail,
                  COUNT(IF(tum.status=3,1,null)) as passed, COUNT(IF(thw.test=2,1,null)) as fail_test
                  FROM ".PREFICS."training_lessons AS tl
                  LEFT JOIN ".PREFICS."training_user_map as tum ON tum.lesson_id = tl.lesson_id
                  LEFT JOIN ".PREFICS."training_home_work as thw ON thw.lesson_id = tl.lesson_id 
                  AND thw.user_id = tum.user_id
                  $where GROUP BY tl.lesson_id, tl.name ORDER BY tl.sort ASC";
        
        $result = $db->prepare($query);

        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->execute();

        $data = $result->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    public static function getCuratorsStatistics($training, $filter, $count_lessons) {
        
        $db = Db::getConnection();
        $where = '';
        $training_id = $training['training_id']; 
        $finish_type = $training['finish_type'];
        $stop_out_day = isset($filter['stop_out_day']) ? $filter['stop_out_day'] : 30;

        if (in_array($finish_type, [3,4,5]) && isset($training['finish_lessons'])){
            $finish_lessons = json_decode($training['finish_lessons']);
            $finish_lessons_str = implode(',', $finish_lessons); 
            $count_lessons = count($finish_lessons);
        }

        if ($filter['is_filter']) {
            $clauses = [];
            if ($filter['start_date']) {
                $clauses[] = "tall.create_date >= {$filter['start_date']}";
            }
            if ($filter['finish_date']) {
                $clauses[] = "tall.create_date < {$filter['finish_date']}";
            }
            if ($filter['stop_out_day']) {
                $stop_out_day = $filter['stop_out_day'];
            }

            $where .= !empty($clauses) ? ' WHERE ' . implode(' AND ', $clauses) : '';
        }

        if ($finish_type == 3 && isset($finish_lessons_str)) {
            $queries_complete = "CREATE TEMPORARY TABLE complete_users 
            SELECT user_id, count(open) as less, 1 AS complete
            FROM ".PREFICS."training_user_map 
            WHERE training_id = $training_id AND lesson_id IN($finish_lessons_str) 
            GROUP BY user_id
            HAVING less = $count_lessons;";
        } elseif ($finish_type == 5 && isset($finish_lessons_str)) {
            $queries_complete = "CREATE TEMPORARY TABLE complete_users 
            SELECT user_id, if(sum(if(status = 3,1,0))-$count_lessons = 0, 1, 0) AS complete
            FROM ".PREFICS."training_user_map 
            WHERE training_id = $training_id AND lesson_id IN($finish_lessons_str) 
            GROUP BY user_id;";
        } elseif ($finish_type == 4 && isset($finish_lessons_str)) {
            $queries_complete = "CREATE TEMPORARY TABLE complete_users 
            SELECT user_id, count(user_id) as less, 1 AS complete
            FROM ".PREFICS."training_user_map 
            WHERE training_id = $training_id AND lesson_id IN($finish_lessons_str) 
            AND date is not null 
            GROUP BY user_id
            HAVING less = $count_lessons;";
        } else { // тут вообщем типо по дате должно быть, но мы сделаем по общему кол-ву
            $queries_complete = "CREATE TEMPORARY TABLE complete_users 
            SELECT user_id, if(sum(if(status = 3,1,0))-$count_lessons = 0, 1, 0) AS complete
            FROM ".PREFICS."training_user_map 
            WHERE training_id = $training_id 
            GROUP BY user_id;";
        }


        $querys_helper = "CREATE TEMPORARY TABLE osn_table
        SELECT tall.*, tcomp.complete, tuv.date as lastenter, tuv.countday 
        FROM (SELECT MAX(create_date),MAX(user_name) AS user_name,MAX(surname) AS surname,curator_id,SUM(countanswer) as countanswer, count(DISTINCT user_id) as countuser, MAX(user_id) AS user_id,MAX(homework_id)
        from (SELECT thwc.create_date, u.user_name as user_name, u.surname as surname, thwc.user_id as curator_id, 
        count(thwc.homework_id) as countanswer,  thw.user_id as user_id, thwc.homework_id as homework_id 
        FROM ".PREFICS."training_home_work_comments as thwc 
        LEFT JOIN ".PREFICS."users AS u ON u.user_id = thwc.user_id
        LEFT JOIN ".PREFICS."training_home_work as thw ON thwc.homework_id = thw.homework_id
        WHERE thwc.status = 2 and thw.lesson_id IN (select distinct lesson_id from ".PREFICS."training_lessons WHERE training_id = $training_id) 
        AND thw.curator_id != '0' GROUP BY thwc.create_date, homework_id, thwc.user_id , thw.user_id
        UNION
        SELECT thw.create_date, u.user_name as user_name, u.surname as surname, thw.curator_id as curator_id, 
        count(distinct thw.user_id) as countanswer, thw.user_id as user_id, thw.homework_id as homework_id
        FROM ".PREFICS."training_home_work as thw
        LEFT JOIN ".PREFICS."users AS u ON u.user_id = thw.curator_id
        WHERE lesson_id IN(select distinct lesson_id from ".PREFICS."training_lessons WHERE training_id = $training_id) 
        AND thw.curator_id <> 0 GROUP BY thw.create_date, u.user_name, u.surname, thw.curator_id, thw.user_id, 
        thw.homework_id) as t2
        group by curator_id) as tall
        LEFT JOIN complete_users as tcomp ON tall.user_id = tcomp.user_id
        LEFT JOIN (SELECT a.*, DATEDIFF(now(),FROM_UNIXTIME(a.date)) as countday FROM ".PREFICS."training_user_visits a
        LEFT OUTER JOIN ".PREFICS."training_user_visits b ON a.user_id = b.user_id AND b.date > a.date
        WHERE b.date IS NULL AND a.training_id = $training_id) as tuv ON tall.user_id = tuv.user_id
        $where;";

        $query = "SELECT t1.user_name as user_name, t1.surname as sur_name, t1.curator_id, max(t1.countanswer) as checked, 
        max(t1.countuser) as students, IFNULL(sum(distinct t1.complete), 0) as passed, sum(if(t1.countday>$stop_out_day,1,0)) as countday,
        max(t1.countuser)-IFNULL(sum(distinct t1.complete), 0)-sum(if(t1.countday>$stop_out_day,1,0)) as in_progress
        from osn_table as t1
        GROUP BY t1.user_name, t1.surname, t1.curator_id;";
        
        $db->query($queries_complete);
        $db->query($querys_helper);
        $result = $db->prepare($query);

        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->execute();

        $data = $result->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }


    /**
     * @param $training
     * @param $sections
     * @param $stats
     * @return string
     */
    public static function getCommonCsv($training, $sections, $stats) {
        $csv_fields = [
            'name' => 'Прохождение',
            'users' => 'Учеников',
            'started' => 'Начали',
            'finished' => 'Закончили',
        ];
        $count_fields = count($csv_fields);
        $csv = implode(';', $csv_fields) . PHP_EOL;

        foreach ($stats as $key => $stat) {
            foreach ($csv_fields as $_key => $field) {
                if ($_key == 'name') {
                    $value = $key == 0 ? $training['name'] : $sections[$key-1]['name'];
                } else {
                    $value = $stat[$_key] . ($_key < $count_fields - 1 ? ';' : '');
                }
                $csv .= $value.($_key < $count_fields - 1 ? ';' : '');
            }
            $csv .= PHP_EOL;
        }

        return $csv;
    }


    /**
     * @param $stats
     * @return string
     */
    public static function getUsersCsv($stats) {
        $csv_fields = [
            'user_id' => 'Клиент ID',
            'user_name' => 'Клиент',
            'progress' => 'Прогресс',
            'open' => 'Начал',
            'last_lesson_complete_name' => 'Последний пройденный',
            'was_not' => 'Не был (дней)',
        ];

        $count_fields = count($csv_fields);
        $csv = implode(';', $csv_fields) . PHP_EOL;

        foreach ($stats as $key => $stat) {
            foreach ($csv_fields as $_key => $field) {
                $value = $_key != 'open' ? $stat[$_key] : date("d.m.Y H:i:s", $stat[$_key]);
                $csv .= $value . ($_key < $count_fields - 1 ? ';' : '');
            }
            $csv .= PHP_EOL;
        }

        return $csv;
    }


    /**
     * @param $stats
     * @return string
     */
    public static function getLessonsCsv($stats) {
        $csv_fields = [
            'name' => 'Урок',
            'no_send' => 'Не выслали',
            'on_check' => 'На проверке',
            'fail_test' => 'Не сдали тест',
            'fail' => 'Незачет',
            'passed' => 'Прошли',
        ];

        $count_fields = count($csv_fields);
        $csv = implode(';', $csv_fields) . PHP_EOL;

        foreach ($stats as $key => $stat) {
            foreach ($csv_fields as $_key => $field) {
                $csv .= $stat[$_key] . ($_key < $count_fields - 1 ? ';' : '');
            }
            $csv .= PHP_EOL;
        }

        return $csv;
    }


    /**
     * @param $stats
     * @return string
     */
    public static function getCuratorsCsv($stats) {
        $csv_fields = [
            'user_name' => 'Куратор',
            'students' => 'Учеников',
            'countday' => 'Бросили',
            'in_progress' => 'В процессе',
            'passed' => 'Прошли',
            'checked' => 'Проверено заданий',
//            'response_rate' => 'Скорость ответа',
        ];

        $count_fields = count($csv_fields);
        $csv = implode(';', $csv_fields) . PHP_EOL;

        foreach ($stats as $key => $stat) {
            foreach ($csv_fields as $_key => $field) {
                if ($_key == 'user_name') {
                    $value = $stat['sur_name'] ? "{$stat['user_name']} {$stat['sur_name']}" : $stat['user_name'];
                } else {
                    $value = $stat[$_key];
                }
                $csv .= $value.($_key < $count_fields - 1 ? ';' : '');
            }
            $csv .= PHP_EOL;
        }

        return $csv;
    }
    
    /**
     * @param $stats
     * @return string
     */
    public static function getCertificatesStatistics($training_id) {
    
        $db = Db::getConnection();
        $query = "SELECT *, CONCAT_WS(' ', u.user_name, u.surname) AS user_name FROM ".PREFICS."training_sertificates AS ts
                LEFT JOIN ".PREFICS."users AS u ON u.user_id = ts.user_id 
                WHERE ts.training_id = :training_id";

        $result = $db->prepare($query);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->execute();

        $data = $result->fetchAll(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;

    }

}
