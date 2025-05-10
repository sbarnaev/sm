<?php defined('BILLINGMASTER') or die;

class Dbo 
{
    
    public static function getConnection($host, $dbname, $user, $pass)
    {
        
        $dsn = "mysql:host=$host;dbname=$dbname";
		
		try {
        $db = new PDO($dsn, $user, $pass);
		} catch (PDOException $e) {
			die($e->getMessage());
		}
        $db->exec("set names utf8");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $db;
    }
    
}