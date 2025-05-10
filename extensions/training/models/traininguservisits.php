<?php defined('BILLINGMASTER') or die;


class TrainingUserVisits
{
    use ResultMessage;

    /**
     * @param $user_id
     * @param $training_id
     * @param int $section_id
     * @param int $lesson_id
     * @return bool
     */
    public static function saveVisit($user_id, $training_id, $section_id = 0, $lesson_id = 0) {
        $db = Db::getConnection();
        $time = time();
        $visit = self::getVisit($user_id, $training_id, $section_id, $lesson_id);
        if ($visit) {
            $sql = "UPDATE ".PREFICS."training_user_visits SET date = :date 
                    WHERE user_id = :user_id AND training_id = :training_id AND section_id = :section_id AND lesson_id = :lesson_id";
        } else {
            $sql = "INSERT INTO ".PREFICS."training_user_visits (user_id, training_id, section_id, lesson_id, date)
                    VALUES (:user_id, :training_id, :section_id, :lesson_id, :date)";
        }

        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        $result->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $result->bindParam(':date', $time, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * @param $user_id
     * @param $training_id
     * @param null $section_id
     * @param null $lesson_id
     * @return bool|mixed
     */
    public static function getVisit($user_id, $training_id, $section_id = null, $lesson_id = null) {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."training_user_visits
                WHERE training_id = $training_id AND user_id = $user_id"
                .($section_id !== null ? " AND section_id = $section_id" : '')
                .($lesson_id !== null ? " AND lesson_id = $lesson_id" : '')
                .' ORDER BY date DESC LIMIT 1';
        $result = $db->query($sql);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
}