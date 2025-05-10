<?php defined('BILLINGMASTER') or die;

class Db {

    /**
     * @return PDO
     */
    public static function getConnection() {
        
        $paramPath = ROOT . '/config/config.php';
        $params = include($paramPath);
        
        $dsn = "mysql:host=$host;dbname=$dbname";
        $db = new PDO($dsn, $user, $password);
        $db->exec("set names utf8");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $db;
    }


    /**
     * @param $fields
     * @param $table
     * @param int $type
     * @param array $extensions
     * @param string $conditions
     * @return string
     */
    public static function getInsertSQL($fields, $table, $type = 1, $extensions = [], $conditions = "") {
        $str_fields = $str_values = '';
        foreach ($fields as $_fields) {
            foreach ($_fields as $field) {
                $str_fields .= ($str_fields ? ', ' : '').$field;
                if ($type == 1) {
                    $str_values .= ($str_values ? ', ' : '').":$field";
                } else {
                    $str_values .= ($str_values ? ', ' : '')."$field";
                }
            }
        }

        if ($extensions) {
            foreach ($extensions as $field => $value) {
                $str_fields .= ", $field";
                $str_values .= ($type == 1 ? ", :$value" : ", $value");
            }
        }

        if ($type == 1) {
            $query = "INSERT INTO $table ($str_fields) VALUES ($str_values)";
        } else {
            $query = "INSERT INTO $table ($str_fields) SELECT $str_values FROM $table WHERE $conditions";
        }

        return $query;
    }

    /**
     * @param $fields
     * @param $table
     * @param $conditions
     * @return string
     */
    public static function getUpdateSQL($fields, $table, $conditions) {
        $str_fields = '';
        foreach ($fields as $type => $_fields) {
            foreach ($_fields as $field) {
                $str_fields .= ($str_fields ? ', ' : '')."$field = :$field";
            }
        }

        return "UPDATE $table SET $str_fields WHERE $conditions";
    }


    /**
     * @param PDO $db
     * @param $sql
     * @param $fields
     * @param $data
     * @return bool|PDOStatement
     */
    public static function bindParams(PDO $db, $sql, $fields, $data) {
        $result = $db->prepare($sql);
        foreach ($fields as $type => $_fields) {
            foreach ($_fields as $field) {
                if ($type == 'integer') {
                    $result->bindParam(":$field", $data[$field], PDO::PARAM_INT);
                } else {
                    $result->bindParam(":$field", $data[$field], PDO::PARAM_STR);
                }
            }
        }

        return $result;
    }
}