<?php

abstract class Api
{
    public $apiName = ''; //users

    protected $method = ''; 
    
    public $requestUri = [];
    public $requestParams = [];

    protected $action = ''; //Название метод для выполнения


    public function __construct() {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        //Массив GET параметров разделенных слешем
        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        $this->requestParams = $_REQUEST;

        //Определение метода запроса
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                return $this->response('Unexpected Header', 'error', 404);
            }
        }
    }

    public function run() {
        //Первые 2 элемента массива URI должны быть "api" и название api (в нашем случае users)
        if(array_shift($this->requestUri) !== 'api' || array_shift($this->requestUri) !== $this->apiName){
           return $this->response('API Not Found', 'error', 404);
        }
        //Определение действия для обработки
        $this->action = $this->getAction();

        //Если метод(действие) определен в дочернем классе (в нашем случае userApi)
        if (method_exists($this, $this->action)) {
            return $this->{$this->action}();
        } else {
            return $this->response('Invalid Method', 'error', 500);
        }
    }
    // Ответ сервера в формате Json
    protected function response($data, $result, $status = 500) {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        if($result == 'ok'){
            $res = [
                'result' => $result,
                'tarifs' => $data
            ];
        
        } else {
            $res = [
            'result' => $result,
            'message' => $data
            ];
        }
        return json_encode($res, JSON_UNESCAPED_UNICODE );
    }

    private function requestStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            500 => 'Internal Server Error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }

    protected function getAction()
    {
        $method = $this->method;
        switch ($method) {
            case 'GET':
                return 'viewAction';
                break;
            case 'PUT':
                return 'updateAction';
                break;
            default:
                return null;
        }
    }

    abstract protected function viewAction();
    abstract protected function updateAction();
}


?>