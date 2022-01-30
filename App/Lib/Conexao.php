<?php

namespace App\Lib;

use PDO;
use PDOException;
use Exception;

class Conexao
{
    private static $connection;

    private function __construct(){}

    public static function getConnection() {
        if(!Sessao::retornaUsuario()) {
            throw new Exception("Conexão suspensa. Faça o login novamente");
        }
        var_dump(DB_NAME);
        $pdoConfig  = DB_DRIVER . ":". "host=" . DB_HOST . ";";
        $pdoConfig .= "dbname=".DB_NAME.";";
        $pdoConfig .= "charset=utf8;";

        try { 
            if(!isset(self::$connection)){
                self::$connection =  new PDO($pdoConfig, DB_USER, DB_PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return self::$connection;
        } catch (PDOException $e) {
            Sessao::limpaUsuario();
            
            throw new Exception("Erro de conexão com o banco de dados: " . DB_NAME,500);
        }
    }
    
    
    public static function getConnectionFinancas() {

        $pdoConfig  = "mysql:". "host=localhost;";
        //$pdoConfig .= "dbname=financas;";
        //echo strpos(PATH, 'wamp64'); die();
        if(strpos(PATH, 'wamp64') || strpos(PATH, 'htdocs')) {
            $pdoConfig .= "dbname=financas;";   
        }else {
            $pdoConfig .= "dbname=kellye90_financas;";   //TROCAR SEMPRE QUE MUDAR DE SERVIDOR DE HOSPEDAGEM
        }
        $pdoConfig .= "charset=utf8;";

        try { 
            if(!isset(self::$connection) || (strpos(PATH, 'wamp64') || strpos(PATH, 'htdocs')) ){
                self::$connection =  new PDO($pdoConfig, DB_USER, DB_PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return self::$connection;
        } catch (PDOException $e) {
            Sessao::limpaUsuario();
            throw new Exception("Erro de conexão com o banco de dados:: financas" . DB_NAME . ' Erro: ' . $e,500);
        }
    }
    
    
}