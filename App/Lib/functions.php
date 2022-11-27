<?php

namespace App\Lib;
use DateTime;

class functions {

    public static $msgErro = "";

    public static function salvaArquivo($nomePasta, $descricao, $conteudo, $extensao, $substitui = false, $somenteBaixar = false) {
        ob_start();
        $caminho = RAIZ_SITE . "/public/usuarios/" . Sessao::retornaUsuario() . "/" . $nomePasta;
        $arquivo = $caminho . "/financas_" . Sessao::retornaUsuario() . "_" . strtoupper($descricao) . "." . $extensao;

        $criarPasta = false;
        if (!is_dir($caminho)) { //SE A PASTA NÃO EXISTE, ENTÃO SERÁ CRIADA
            $criarPasta = mkdir($caminho, 0755, true);
            if (!$criarPasta) {
                $this->msgErro = "Não foi possível criar a pasta";
                return;
            }
        }
        if (file_exists($arquivo)) {
            if ($substitui) {  //SE O ARQUIVO JÁ EXISTE E SE DEVE SER SUBSTITUIDA ENTÃO O ARQUIVO SERA DELETADO
                unlink($arquivo);
            } else {
                $this->msgErro = "Arquivo já existente";
                return;
            }
        }
        $criouArquivo = fopen($arquivo, 'a+');
        if ($criouArquivo) {
            fwrite($criouArquivo, $conteudo);
            fclose($criouArquivo);
            if ($somenteBaixar) {
                header("Content-Type: application/x-msexcel");
                header("Cache-Control: no-cache");
                header("Pragma: no-cache");
                header('Content-Disposition: attachment; filename="' . $descricao . '.xls"');
                echo $criouArquivo;
            }
            return true;
        } else {
            return false;
        }
    }
    
    public function proximoDiaUtill(string $data) : DateTime {
        
        $ObjData = new DateTime($data);
        $dia = $ObjData->format('d');
        $mes = $ObjData->format('m');
        $ano = $ObjData->format('Y');
        $diaDaSemana = $ObjData->format('w');
        if($diaDaSemana == 0 || $diaDaSemana == 6) {
            $maisUmDia = new \DateInterval("P1D");
            return self::proximoDiaUtill($ObjData->add($maisUmDia)->format('Y-m-d'));
        }
        
        return $ObjData;
        
    }

}
