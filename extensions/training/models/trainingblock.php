<?php defined('BILLINGMASTER') or die;


class TrainingBlock {

    use ResultMessage;


    /**
     * ПОЛУЧИТЬ БЛОК ТРЕНИНГА
     * @param $block_id
     * @return bool|mixed
     */
    public static function getBlock($block_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."training_blocks WHERE block_id = $block_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ БЛОКИ ТРЕНИНГА
     * @param $training_id
     * @param null $section_id
     * @param bool $whith_lessons
     * @return array|bool
     */
    public static function getBlocks($training_id, $section_id = null, $whith_lessons = true)
    {
        $db = Db::getConnection();

        $where = "WHERE tb.training_id = $training_id" . ($section_id !== null ? " AND tb.section_id = $section_id" : '');
        if ($whith_lessons !== null) {
            $where .= $whith_lessons ? ' AND tl.block_id > 0' : ' AND tl.block_id = 0';
        }

        $query = "SELECT tb.* FROM ".PREFICS."training_blocks AS tb
                  LEFT JOIN ".PREFICS."training_lessons AS tl
                  ON tl.block_id = tb.block_id $where GROUP BY tb.block_id ORDER BY sort ASC";

        $result = $db->query($query);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ДОБАВИТЬ БЛОК ТРЕНИНГА
     * @param $name
     * @param $training_id
     * @param $section_id
     * @param $sort
     * @param $access_type
     * @param $groups
     * @param $planes
     * @return bool
     */
    public static function addBlock($name, $training_id, $section_id, $sort)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'training_blocks (name, training_id, section_id, sort) 
                VALUES (:name, :training_id, :section_id, :sort)';

        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result = $result->execute();

        return $result ? $db->lastInsertId() : false;
    }


    /**
     * ИЗМЕНИТЬ БЛОК ТРЕНИНГА
     * @param $block_id
     * @param $name
     * @param $training_id
     * @param $section_id
     * @param $sort
     * @return bool
     */
    public static function editBlock($block_id, $name, $training_id, $section_id, $sort)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS."training_blocks SET name = :name, training_id = :training_id,
                section_id = :section_id, sort = :sort WHERE block_id = $block_id";

        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ СОРТИРОВКИ ДЛЯ БЛОКОВ ПО ВОЗРАСТАНИЮ
     * @param array $blocks
     * @return array|bool
     */
    public static function getSortBlocks($blocks = []) {
        $place_holders = implode(',', array_fill(0, count($blocks), '?'));
        $db = Db::getConnection();
        $result = $db->prepare("SELECT sort FROM ".PREFICS."training_blocks WHERE block_id IN ($place_holders) ORDER BY sort ASC");
        $result->execute($blocks);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row['sort'];
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СОРТИРОВКУ ДЛЯ ДОБАВЛЯЕМОГО БЛОКА
     * @param $training_id
     * @return int
     */
    public static function getFreeSort($training_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT MAX(sort) FROM ".PREFICS."training_blocks WHERE training_id = $training_id");
        $count = $result->fetch();

        return (int)$count[0] + 1;
    }

    /**
     * ОБНОВИТЬ СОРТИРОВКУ ДЛЯ БЛОКА
     * @param $block_id
     * @param $sort
     * @return bool
     */
    public static function updSortBlock($block_id, $sort) {
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE '.PREFICS.'training_blocks SET sort = :sort WHERE block_id = :block_id');

        $result->bindParam(':block_id', $block_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ БЛОК ТРЕНИНГА
     * @param $block_id
     * @return bool
     */
    public static function DelBlock($block_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(lesson_id) FROM ".PREFICS."training_lessons WHERE block_id = $block_id");
        $count = $result->fetch();

        if ($count[0] == 0) {
            $db = Db::getConnection();
            $sql = 'DELETE FROM '.PREFICS.'training_blocks WHERE block_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $block_id, PDO::PARAM_INT);

            return $result->execute();
        }

        self::addError('Не возможно удалить: Блок содержит уроки!');
        return false;
    }
}