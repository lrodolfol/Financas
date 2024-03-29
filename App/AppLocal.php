<?php
 
namespace App;

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\_404Controller;
use Exception;

class AppLocal {

    private $controller;
    private $controllerFile;
    private $action;
    private $params;
    public $controllerName;

    public function __construct($baseOperante) {
        /*
         * Constantes do sistema
         */
        define('EMAIL_DESENVOLVEDOR', 'financas@tinosnegocios.com.br');
        define('RAIZ_SITE', $_SERVER['DOCUMENT_ROOT'] . "/financas"); 
        define('NOME_SITE', $_SERVER['SERVER_NAME'] . "/financas");
        define('APP_HOST', $_SERVER['HTTP_HOST'] . "/financas");
        define('PATH', realpath('.'));
        define('TITLE', "Financas " . (Lib\Sessao::retornaUsuario() ) ) ;
        define('DB_HOST', "");
        //define('DB_USER', "root");
        define('DB_PASSWORD', "1234");
        if (isset($_POST['User']) && $_POST['User'] = "true") {
            define('DB_NAME', "financas");
            //define('NAME_USER', $_POST['user']);
			define('NAME_USER', $_POST['User']);
			define('DB_USER', "root");
        } else {
			define('DB_USER', "root");
            if(Lib\Sessao::retornaUsuario() ) {
                $userName = Lib\Sessao::retornaUsuario();
                define('DB_NAME', "financas_{$userName}");
            }else{
                //define('DB_NAME', "financas_padrao");
            }
            
        }
        define('DB_DRIVER', "mysql");
        define('BASE_DADOS_OPERANTE', $baseOperante);

        $this->url();
    }

    public function run() {
        
       /* if(! Lib\Sessao::retornaUsuario()) {
            $this->controller = null;
        }*/
        
        if ($this->controller) {
            $this->controllerName = ucwords($this->controller) . 'Controller';
            $this->controllerName = preg_replace('/[^a-zA-Z]/i', '', $this->controllerName);
            //ELSE IF TEM SESS�O DE USUARIO ENTAO home SEN�O login
        } else {
            if (Lib\Sessao::retornaUsuario()) {
                $this->controllerName = "HomeController";
            } else {
                $this->controllerName = "LoginController";
            }
        }

        $this->controllerFile = $this->controllerName . '.php';
        if(!$this->action == null) {
            $this->action = preg_replace('/[^a-zA-Z]/i', '', $this->action);
        }
        
        if (!$this->controller) {
            if (Lib\Sessao::retornaUsuario()) {
                $this->controller = new HomeController($this);
                $this->controller->index();
            } else {
                $this->controller = new LoginController($this);
                $this->controller->index();
            }
        }

        if (!file_exists(PATH . '/App/Controllers/' . $this->controllerFile)) {
			$this->controller = new _404Controller($this);
            $this->controller->index();
			return;
            //throw new Exception("P�gina n�o encontrada.", 404);
        }

        $nomeClasse = "\\App\\Controllers\\" . $this->controllerName;
        $objetoController = new $nomeClasse($this);

        if (!class_exists($nomeClasse)) {
            throw new Exception("Erro na aplicação", 500);
        }
        
        if ($this->action != null && method_exists($objetoController, $this->action)) {
            $objetoController->{$this->action}($this->params);
            return;
        } else if (!$this->action && method_exists($objetoController, 'index')) {
            $objetoController->index($this->params);
            return;
        } else {
            throw new Exception("Nosso suporte já esta verificando desculpe!", 500);
        }
        throw new Exception("Página não encontrada.", 404);
    }

    public function url() {

        if (isset($_GET['url'])) {

            $path = $_GET['url'];
            $path = rtrim($path, '/');
            $path = filter_var($path, FILTER_SANITIZE_URL);

            $path = explode('/', $path);

            $this->controller = $this->verificaArray($path, 0);
            $this->action = $this->verificaArray($path, 1);

            if ($this->verificaArray($path, 2)) {
                unset($path[0]);
                unset($path[1]);
                $this->params = array_values($path);
            }
        }
    }

    public function getController() {
        return $this->controller;
    }

    public function getAction() {
        return $this->action;
    }

    public function getControllerName() {
        return $this->controllerName;
    }

    private function verificaArray($array, $key) {
        if (isset($array[$key]) && !empty($array[$key])) {
            return $array[$key];
        }
        return null;
    }

}
