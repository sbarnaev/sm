<?php defined('BILLINGMASTER') or die;

class Widgets {

    /**
     * ПОЛУЧИТЬ ВИДЖЕТЫ
     * @param $page
     * @param $is_auth
     * @return array|bool
     */
    public static function getWidgets($page, $is_auth)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."widgets WHERE status = 1
                AND widget_id IN (SELECT widget_id FROM ".PREFICS."widgets_map WHERE page = '$page')";
        $sql .= (!$is_auth ? ' AND private = 0' : '') . ' ORDER BY sort ASC';
        $result = $db->query($sql);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }



    // Вывести виджеты
    public static function RenderWidget($widgets, $position)
    {
        $data = [];
        if (!empty($widgets)) {
            foreach ($widgets as $key => $widget) {
                if ($widget['position'] != $position) {
                    continue;
                }

                $data[] = $widget;
            }
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СПИСОК ВИДЖЕТОВ ДЛЯ АДМИНКИ
     * @return array|bool
     */
    public static function getAllWidgets()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."widgets ORDER BY widget_id DESC ");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    // СОЗДАТЬ ВИДЖЕТ
    public static function addWidget($type, $title, $position, $page, $desc, $affix, $params, $sort, $status, $date, $private,
                                     $show_header, $header, $show_subheader, $subheader, $show_right_button, $right_button_name,
                                     $right_button_link, $suffix, $show_for_course, $show_for_training)
    {

        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'widgets (widget_type, widget_title, position, widget_desc, affix, params, sort, status,
                    create_date, private, show_header, header, show_subheader, subheader, show_right_button, right_button_name,
                    right_button_link, suffix, show_for_course, show_for_training) 
                VALUES (:widget_type, :widget_title, :position, :widget_desc, :affix, :params, :sort, :status, :create_date,
                    :private, :show_header, :header, :show_subheader, :subheader, :show_right_button, :right_button_name,
                    :right_button_link, :suffix, :show_for_course, :show_for_training)';

        $result = $db->prepare($sql);
        $result->bindParam(':widget_type', $type, PDO::PARAM_STR);
        $result->bindParam(':widget_title', $title, PDO::PARAM_STR);
        $result->bindParam(':widget_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':position', $position, PDO::PARAM_STR);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':affix', $affix, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':create_date', $date, PDO::PARAM_INT);
        $result->bindParam(':private', $private, PDO::PARAM_INT);

        $result->bindParam(':show_header', $show_header, PDO::PARAM_INT);
        $result->bindParam(':header', $header, PDO::PARAM_STR);
        $result->bindParam(':show_subheader', $show_subheader, PDO::PARAM_INT);
        $result->bindParam(':subheader', $subheader, PDO::PARAM_STR);
        $result->bindParam(':show_right_button', $show_right_button, PDO::PARAM_INT);
        $result->bindParam(':right_button_name', $right_button_name, PDO::PARAM_STR);
        $result->bindParam(':right_button_link', $right_button_link, PDO::PARAM_STR);

        $result->bindParam(':suffix', $suffix, PDO::PARAM_STR);
        $result->bindParam(':show_for_course', $show_for_course, PDO::PARAM_STR);
        $result->bindParam(':show_for_training', $show_for_training, PDO::PARAM_STR);
        $result->execute();

        // Получить id созданного виджета
        $result = $db->query(" SELECT widget_id FROM ".PREFICS."widgets WHERE create_date = $date");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            foreach($page as $item){
                $result = $db->prepare('INSERT INTO '.PREFICS.'widgets_map (widget_id, page) VALUES (:widget_id, :page)');
                $result->bindParam(':widget_id', $data['widget_id'], PDO::PARAM_INT);
                $result->bindParam(':page', $item, PDO::PARAM_STR);
                $result->execute();
            }
        }

        return $data;
    }



    // ИЗМЕНИТЬ ВИДЖЕТ
    public static function editWidget($id, $title, $position, $page, $desc, $affix, $params, $sort, $status, $private, $show_header,
                                      $header, $show_subheader, $subheader, $show_right_button, $right_button_name, $right_button_link,
                                      $suffix, $show_for_course, $show_for_training)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'widgets SET widget_title = :widget_title, widget_desc = :widget_desc, position = :position,
                    params = :params, sort = :sort, affix = :affix, status = :status, private = :private, show_header = :show_header,
                    header = :header,  show_subheader = :show_subheader,  subheader = :subheader,  show_right_button = :show_right_button,
                    right_button_name = :right_button_name,  right_button_link = :right_button_link,
                    suffix = :suffix, show_for_course = :show_for_course, show_for_training = :show_for_training WHERE widget_id = '.$id;

        $result = $db->prepare($sql);
        $result->bindParam(':widget_title', $title, PDO::PARAM_STR);
        $result->bindParam(':widget_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':position', $position, PDO::PARAM_STR);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':affix', $affix, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':private', $private, PDO::PARAM_INT);

        $result->bindParam(':show_header', $show_header, PDO::PARAM_INT);
        $result->bindParam(':header', $header, PDO::PARAM_STR);
        $result->bindParam(':show_subheader', $show_subheader, PDO::PARAM_INT);
        $result->bindParam(':subheader', $subheader, PDO::PARAM_STR);
        $result->bindParam(':show_right_button', $show_right_button, PDO::PARAM_INT);
        $result->bindParam(':right_button_name', $right_button_name, PDO::PARAM_STR);
        $result->bindParam(':right_button_link', $right_button_link, PDO::PARAM_STR);

        $result->bindParam(':suffix', $suffix, PDO::PARAM_STR);
        $result->bindParam(':show_for_course', $show_for_course, PDO::PARAM_STR);
        $result->bindParam(':show_for_training', $show_for_training, PDO::PARAM_STR);
        $result->execute();

        // УДАЛИТЬ все записи из widgets_map с даным id
        $result = $db->prepare('DELETE FROM '.PREFICS.'widgets_map WHERE widget_id = :id');
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->execute();

        // Записать по новой
        foreach ($page as $item) {
            $result = $db->prepare('INSERT INTO '.PREFICS.'widgets_map (widget_id, page) VALUES (:widget_id, :page)');
            $result->bindParam(':widget_id', $id, PDO::PARAM_INT);
            $result->bindParam(':page', $item, PDO::PARAM_STR);
            $result->execute();
        }

        return true;
    }


    // ПОЛУЧИТЬ ДАННЫЕ ВИДЖЕТА
    public static function getWidgetData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."widgets WHERE widget_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    // ПОЛУЧИТЬ СТРАНИЦЫ ВИДЖЕТА
    public static function getWidgetsPage($id)
    {
        $db = Db::getConnection();
        $arr = array();
        $result = $db->query("SELECT page FROM ".PREFICS."widgets_map WHERE widget_id = $id ");
        $i = 0;
        while($row = $result->fetch()){
            $post[$i] = $row['page'];
            $i++;
        }
        if(isset($post)) return $post;
        else return $arr;
    }


    // УДАЛИТЬ ВИДЖЕТ
    public static function deleteWidget($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'widgets WHERE widget_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }

}