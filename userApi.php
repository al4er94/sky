<?php
require_once 'Api.php';
require_once 'db.php';
//require_once 'Users.php';

class UsersApi extends Api
{
    public $apiName = 'users';

   

    /**
     * Метод GET
     */
    public function viewAction()
    {  
        //Получаем данные из Uri
        $requestUri = $this->requestUri;
        
        $user_id = $requestUri[0];
        $service_id = $requestUri[2];
        $tarifs = $requestUri[3];
        if($tarifs == 'tarifs'){
            //Подключение к БД
            db::_connect();
            //Получение тарифов
            $tarifs = db::get_tarifs($user_id, $service_id);
            if($tarifs == 'Invalid db query'){
                return $this->response($tarifs, 'error', 404);
            } else {
                return $this->response($tarifs, 'ok', 200);
            }
        } else {
                return $this->response('error_api', 'error',  500);
        }
        return $this->response('Data not found', 'error', 404);
    }

    

    /**
     * Метод PUT
     */
    public function updateAction()
    {
        //Получаем данные из Uri
        $requestUri = $this->requestUri;
        
        $user_id = $requestUri[0];
        $service_id = $requestUri[2];
        $tarif = $requestUri[3];
       
        //Получаем данные из запроса
        $str_json = file_get_contents('php://input');
        $data = json_decode($str_json, true);
        $tarif_id = $data['tarif_id'];
        
        if($tarif == 'tarif'){
            //Подключение к БД
            db::_connect();
            //Изменение в БД
            $res = db::get_tarif_by_id($tarif_id, $user_id, $service_id);
            if(($res == 'wrong user id') || ($res == 'wrong server id')){
                return $this->response($res, 'error', 500);
            } else {
                return $this->response($res, 'ok', 200); 
            }       
        } else {
            return $this->response('error_api', 'error',  500);
        }  
        
        
    }

    

}