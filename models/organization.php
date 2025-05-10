<?php defined('BILLINGMASTER') or die;

class Organization {
    
    
    
    // СПИСОК ОРГАНИЗАЦИЙ
    public static function getOrgList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."organization");
        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
        	$data[] = $row;
        }
        
        return !empty($data) ? $data : false;
    }
    
    
    // ДАННЫЕ ОРГАНИЗАЦИИ
    public static function getOrgData($org_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."organization WHERE id = $org_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(!empty($data)) return $data;
        else return false;
    }
    
    
    // ИЗМЕНИТЬ ОРГАНИЗАЦИЮ
    public static function editOrganization($org_id, $org_name, $org_desc, $requisits, $oferta, $payments, $status)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'organization SET org_name = :org_name, org_desc = :org_desc, oferta = :oferta, requisits = :requisits, payments = :payments, status = :status WHERE id = '.$org_id;
        $result = $db->prepare($sql);
        $result->bindParam(':org_name', $org_name, PDO::PARAM_STR);
        $result->bindParam(':org_desc', $org_desc, PDO::PARAM_STR);
        $result->bindParam(':oferta', $oferta, PDO::PARAM_STR);
        $result->bindParam(':requisits', $requisits, PDO::PARAM_STR);
        $result->bindParam(':payments', $payments, PDO::PARAM_STR);
  		$result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ДОБАВИТЬ ОРГАНИЗАЦИЮ
    public static function addOrganization($org_name, $org_desc, $requisits, $oferta, $payments, $status)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'organization (org_name, org_desc, oferta, requisits, payments, status ) 
                VALUES (:org_name, :org_desc, :oferta, :requisits, :payments, :status)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':org_name', $org_name, PDO::PARAM_STR);
        $result->bindParam(':org_desc', $org_desc, PDO::PARAM_STR);
        $result->bindParam(':oferta', $oferta, PDO::PARAM_STR);
        $result->bindParam(':requisits', $requisits, PDO::PARAM_STR);
        $result->bindParam(':payments', $payments, PDO::PARAM_STR);
  		$result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ПРОВЕРИТЬ ОРГАНИЗАЦИЮ ДЛЯ ПРОДУКТА
    public static function getOrgByProduct($product_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT org_id FROM ".PREFICS."products_org WHERE product_id = $product_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(!empty($data)) return $data['org_id'];
        else return false;
    }
    
    
    // ДОБАВИТЬ ОРГАНИЗАЦИЮ К ПРОДУКТУ
    public static function addOrgFromProduct($product_id, $org_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."products_org WHERE product_id = $product_id AND org_id = $org_id ");
        $count = $result->fetch();
        if($count[0] > 0) {
            
            // обновляем  
            $sql = 'UPDATE '.PREFICS.'products_org SET org_id = :org_id WHERE product_id = '.$product_id;
            $result = $db->prepare($sql);
            $result->bindParam(':org_id', $org_id, PDO::PARAM_INT);
            return $result->execute();
            
        } else {
            
            // insert
            $sql = 'INSERT INTO '.PREFICS.'products_org (product_id, org_id ) 
                    VALUES (:product_id, :org_id)';
            
            $result = $db->prepare($sql);
            $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
      		$result->bindParam(':org_id', $org_id, PDO::PARAM_INT);
            return $result->execute();
        }
    }
    
}