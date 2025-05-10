<?php defined('BILLINGMASTER') or die;


class TrainingImport {

    public static function checkCountLessons($course_id, $training_id)

    {
        
        $db = Db::getConnection();

        $result = $db->query("SELECT count(distinct lesson_id) AS lesson, sum(sort) AS sortless FROM ".PREFICS."training_lessons WHERE training_id = $training_id
                            UNION ALL
                            SELECT count(distinct lesson_id), sum(sort) FROM ".PREFICS."course_lessons WHERE course_id = $course_id");
        
        $row = $result->fetchAll(PDO::FETCH_ASSOC);
        if ($row[0]['lesson'] === $row[1]['lesson'] && $row[0]['sortless'] === $row[1]['sortless']){
            $clean_ok = self::cleaningTrainingDataMap($training_id);
            if ($clean_ok){
                return self::transferUsers($course_id, $training_id);
            }
        } else {
            Training::addError("В тренингах разное кол-во уроков или нарушена сортировка");
            return false;
        }

    }


    /**
     * ПЕРЕНОС ПОЛЬЗОВАТЕЛЕЙ ИЗ СТАРОГО Lesson_map В НОВЫЙ user_map ИЗ answer в homework_history и прочие... 
     */
    public static function transferUsers($course_id, $training_id)

    {
        
        set_time_limit(600);  // 10 мин. 

        $db = Db::getConnection();
        $result = $db->query("INSERT INTO ".PREFICS."training_user_map (user_id, lesson_id, training_id, open, date, get_answer, status, count_answer)
                            SELECT clm.user_id, tl.lesson_id, $training_id, clm.date, clm.date, null, 
                            IF(clm.status=0,1,IF(clm.status=1,3,IF(clm.status=9,4,0))) AS status, null
                            FROM ".PREFICS."course_lesson_map AS clm
                            LEFT JOIN ".PREFICS."course_lessons AS cl ON cl.lesson_id = clm.lesson_id
                            LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.sort = cl.sort AND tl.training_id = $training_id
                            WHERE clm.course_id = $course_id;");


        $result = $db->query("SELECT ca.id, ca.lesson_id, ca.user_id, ca.status AS status, ca.date, ca.body AS body, ca.attach AS attach, tl.lesson_id,
                            IF(clm.status=0,4,1) AS clmstatus
                            FROM ".PREFICS."course_answers AS ca 
                            LEFT JOIN ".PREFICS."course_lessons AS cl ON cl.lesson_id = ca.lesson_id
                            LEFT JOIN ".PREFICS."course_lesson_map AS clm ON clm.lesson_id = ca.lesson_id AND clm.user_id = ca.user_id
                            LEFT JOIN ".PREFICS."training_lessons AS tl ON tl.sort = cl.sort AND tl.training_id = $training_id
                            WHERE ca.course_id = $course_id AND ca.parent_id = 0
                            ORDER BY ca.id, ca.user_id, ca.lesson_id");

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $homework_id = TrainingLesson::getHomeWork($row['user_id'], $row['lesson_id']);
            if(empty($homework_id)){ //тут если нет хомворк_ид, то создаем новый ответ 
                $task = TrainingLesson::getTask2Lesson($row['lesson_id']);
                $write = TrainingLesson::writeAnswer($task['task_id'], $row['lesson_id'], $row['user_id'], 0, $row['clmstatus'], 0, $row['body'], $row['attach'], null);
                if ($write) {
                    $homework_id = TrainingLesson::getHomeWork($row['user_id'], $row['lesson_id']);
                }
            } else { // тут есть хомворк значит пишем в комментарии
                TrainingLesson::writeComment($homework_id['homework_id'], $row['user_id'], 0, $row['body'], 0, null);
            }
            $hasparent = Course::getAnswerFromMess($row['id']);
            if ($hasparent && $homework_id) {
                foreach($hasparent as $answer){
                    $answer['body'] = System::isBase64($answer['body']) ? $answer['body'] : base64_encode($answer['body']);
                    $is_curator_message = $answer['user_id'] !== $row['user_id'] ? 2 : 0;
                    TrainingLesson::writeComment($homework_id['homework_id'], $answer['user_id'], 0, $answer['body'], $is_curator_message, null);
                }
            }
        }   
                          
        return $result;   

    }

    /**
     * ПЕРЕНОС ПОЛЬЗОВАТЕЛЕЙ ИЗ СТАРОГО Lesson_map В НОВЫЙ user_map ИЗ answer в homework_history и прочие... 
     */
    public static function cleaningTrainingDataMap($training_id)

    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'training_user_map WHERE training_id = :id';
        $sql .= '; DELETE thw, thwh, thwc FROM '.PREFICS.'training_home_work AS thw 
        LEFT JOIN '.PREFICS.'training_home_work_history AS thwh ON thwh.homework_id = thw.homework_id 
        LEFT JOIN '.PREFICS.'training_home_work_comments AS thwc ON thwc.homework_id = thw.homework_id 
        WHERE thw.lesson_id IN (SELECT lesson_id FROM '.PREFICS.'training_lessons WHERE training_id = :id';

        $result = $db->prepare($sql);
        $result->bindParam(':id', $training_id, PDO::PARAM_INT);

        return $result->execute();
    }

    /**
     * ПЕРЕНОС ПОЛЬЗОВАТЕЛЕЙ ИЗ СТАРОГО Lesson_map В НОВЫЙ user_map ИЗ answer в homework_history и прочие... 
     */
    public static function addUserMapInfo($user_id, $start_lesson, $training_id)

    {
        $dateadd = time();

        $db = Db::getConnection();
        $result = $db->query("INSERT INTO ".PREFICS."training_user_map (user_id, lesson_id, training_id, open, date, get_answer, status, count_answer)
                            SELECT $user_id, lesson_id, $training_id, $dateadd, $dateadd, null, 
                            3 AS status, null
                            FROM ".PREFICS."training_lessons WHERE training_id = $training_id AND sort < $start_lesson;");
        
        return $result;                 
    }




}