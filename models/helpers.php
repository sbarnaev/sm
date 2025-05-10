<?php defined('BILLINGMASTER') or die;

class Helpers
{

    use ResultMessage;


    /**
     * @param $array
     * @return mixed
     */
    public static function arraySort($array) {
        usort($array, function($a, $b) {
            global $field;
            if ($a['sort'] == $b['sort']) {
                return 0;
            }

            return $a['sort'] < $b['sort'] ? -1 : 1;
        });

        return $array;
    }
}