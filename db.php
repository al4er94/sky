<?php 
require 'db_cfg.php';

class db{
    public static $db_username = DB_USER;
    public static $db_password = DB_PASSWORD;
    public static $db_host = DB_HOST; 
    public static $db = DB_NAME;
    
    public static $link="";
    
    public static function _connect() {
    self::$link = mysqli_connect(self::$db_host, self::$db_username, self::$db_password);
    if (!self::$link) {
        die('Connection: '.self::$db_host.','.self::$db_username.', '.self::$db_password.' -! ' . mysql_error());
    }

    mysqli_select_db(self::$link,self::$db);
    mysqli_query(self::$link ,"SET CHARACTER SET 'utf8'");
    mysqli_query(self::$link, "SET NAMES 'utf8'");
    mysqli_query(self::$link, "SET SESSION collation_connection = 'utf8_general_ci'");

    }
    
    public static function _disconnect() {
        mysqli_close(self::$link);
    }
    
    public static function get_tarifs($id, $service_id){
        //Берем id тарифов для пользователя с указанным id и id сервиса
        $sql = "SELECT * FROM ".db::$db.".`services` WHERE `ID` = $service_id and `user_id` = $id";
        $result = mysqli_query(db::$link, $sql);
        $row = mysqli_fetch_assoc($result);
        //Проверяем есть ли у нас такая строка. если нет выводм ошибку 
        if($row){
            //Берем массив тарифов
            $tarif_array = self::get_tarifs_by_tarif_id($row['tarif_id']);
        } else {
            return 'Invalid db query';
        }
        //Выводим title в ответ
        $stripos = stripos(($tarif_array[0]['title']), ' ');
        $title = ( !($stripos) ? $tarif_array[0]['title'] : substr($tarif_array[0]['title'], 0, $stripos));
        $i =0;
        
        foreach ($tarif_array as &$val){
            //Расчитывем новое время
            $tarif_array[$i]['new_payday'] = strtotime(date("Y-m-d", strtotime("+ ".$val['pay_period']." month")));
            unset($val['tarif_group_id']);
            $i++;
        }
        //Формируем новый массив
        $resault_array = [
                            'title' => $title,
                            'link' => $tarif_array[0]['link'],
                            'speed' => $tarif_array[0]['speed'],
                            'tarifs' => $tarif_array
                         ];
        
        return $resault_array;
    }
    
    public static function get_tarifs_by_tarif_id($tarif_id){
        //Ищем tarif_group_id по его id тарифа  
        $sql = "SELECT `tarif_group_id` FROM ".db::$db.".`tarifs` where `ID` = $tarif_id";
        $result = mysqli_query(db::$link, $sql);
        $row = mysqli_fetch_assoc($result);
        $tarif_group_id = $row['tarif_group_id'];
        //Берем массив тарифов по tarif_group_id       
        $sql = "SELECT * FROM ".db::$db.".`tarifs` where `tarif_group_id` = $tarif_group_id";
        $result = mysqli_query(db::$link, $sql);
        $res = array();
        while($row = mysqli_fetch_assoc($result)){
            $res[] = $row;
        }
        return $res;
    }
    
    public static function get_tarif_by_id($tarif_id, $user_id, $service_id){
        //Получаем сервис по id 
        $sql = "SELECT * FROM ".db::$db.".`services` where `ID` = $service_id and `user_id` = $user_id";
        $result = mysqli_query(db::$link, $sql);
        $row = mysqli_fetch_assoc($result);
        //Проверяем есть ли данный сервис
        if($row){
            //Если есть изменяем сервис
            $sql = "UPDATE ".db::$db.".`services` SET `tarif_id`= $tarif_id where `ID`= $service_id and `user_id` = $user_id";
            $result = mysqli_query(db::$link, $sql);
            return $result ? 'successful update service' : 'update error' ;
        } else {
            //Если его нет то проверяем, есть ли сервис с таким id  
            $sql = "SELECT * FROM ".db::$db.".`services` where `ID` = $service_id";
            $result_servise = mysqli_query(db::$link, $sql);
            $row_servise = mysqli_fetch_assoc($result_servise);
            if($row_servise){
                //Если есть сервис с таким id, то выводим ошибку по users id
                return 'wrong user id'; 
            } else {
                //Если нет, то выводим ошибку по сервису server id
                return 'wrong server id'; 
            }
        }
        
        
    }
}
?>