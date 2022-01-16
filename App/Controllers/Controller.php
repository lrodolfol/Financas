<?php

namespace App\Controllers;

use App\Lib\Sessao;
use Dompdf\Dompdf;

abstract class Controller {

    protected $app;
    private $viewVar;

    public function __construct($app) {
        $this->setViewParam('nameController', $app->getControllerName());
        $this->setViewParam('nameAction', $app->getAction());
    }

    public function print($params) {
        $this->setViewParam('jsonDados', $params);
        $this->render('/debito/print');
    }

    public function openFile($caminho) {
        $this->setViewParam('arquivo', $caminho[0]);
        $this->render("/arquivo/index");
    }

    public function render($view) {
        $viewVar = $this->getViewVar();
        $Sessao = Sessao::class;

        //SÓ MOSTRA O HEADER COM JS E CSS SE NÃO FOR PRINT
        //JÁ QUE O DOM PDF MOSTRA ERRO SE TIVER ALGO NO CABECALHO: Unable to stream pdf: headers already sent
        if ($view != "/debito/print") { 
            require_once PATH . '/App/Views/layouts/header.php';
        }
        
        if ($view == "login/index") {
            require_once PATH . '/App/Views/' . $view . '.php';
        } else {
            //NÃO CARREGA TELA INICIAL SE NENHUM USUÁRIO ESTIVER LOGADO
            if (!Sessao::retornaUsuario() && !$view == "/usuario/novo") {
                $this->render("login/index");
                return;
            }
            if ($view != "/usuario/novo" && $view != "/usuario/recuperaSenha" && $view != "home/notFound" && $view != "/debito/print") {
                require_once PATH . '/App/Views/layouts/menu.php';
            }
            require_once PATH . '/App/Views/' . $view . '.php';
        }
        if ($view != "/debito/print") {
            require_once PATH . '/App/Views/layouts/footer.php';
        }
    }

    public function redirect($view) {
        header('Location: http://' . APP_HOST . $view);
        exit;
    }

    public function getViewVar() {
        return $this->viewVar;
    }

    public function setViewParam($varName, $varValue) {
        if ($varName != "" && $varValue != "") {
            $this->viewVar[$varName] = $varValue;
        }
    }

    public function carregaImagem($tipo, $codigo) {
        if ($tipo == "CREDITO") {
            $nomePasta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/creditos/credito" . $codigo . "";
            $movePasta = "/credito_codigo_";
            $nomePastaCompleta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/creditos/credito" . $codigo . "/credito_codigo_" . $codigo . ".jpg";
        } elseif ($tipo == "DEBITO") {
            $nomePasta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/debitos/debito" . $codigo . "";
            $movePasta = "/debito_codigo_";
            $nomePastaCompleta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/debitos/debito" . $codigo . "/debito_codigo_" . $codigo . ".jpg";
        } elseif ($tipo == "AGENDAMENTOS") {
            $nomePasta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/agendamentos/agendamento" . $codigo . "";
            $movePasta = "/agendamento_codigo_";
            $nomePastaCompleta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/agendamentos/agendamento" . $codigo . "/agendamento_codigo_" . $codigo . ".jpg";
        } elseif ($tipo == "RELATOSERRO") {
            $nomePasta = RAIZ_SITE . "/public/relatoErros/erro" . $codigo . "";
            //$movePasta = "/erro_codigo_";
            $nomePastaCompleta = RAIZ_SITE . "/public/relatoErros/erro" . $codigo . "/erro" . $codigo . ".jpg";
        } elseif ($tipo == "CONTAS_RECEBER") {
            $nomePasta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/contas_receber/contas_receber" . $codigo . "";
            $movePasta = "/contas_receber_codigo_";
            $nomePastaCompleta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/contas_receber/contas_receber" . $codigo . "/contas_receber_codigo_" . $codigo . ".jpg";
        } else {
            return "Nenhum tipo de função encontrada.";
        }

        $imagemNome = $_FILES["arquivo_imagem"]["name"];
        $imagemTipo = $_FILES["arquivo_imagem"]["type"];
        $imagemTamanho = $_FILES["arquivo_imagem"]["size"];
        $imagemNomeTemp = $_FILES["arquivo_imagem"]["tmp_name"];
        $erro = $_FILES["arquivo_imagem"]["error"];
        $extensao = strrchr($_FILES['arquivo_imagem']['name'], '.');
        $extensoesPermitidas = array('.jpg', '.jpeg', '.png');

        if ($imagemTamanho / 1000000 > 20) {
            return $erro = "A imagem exede o limite de 4MB.";
        }

        /* if(! in_array($extensao, $extensoesPermitidas) === true) {
          return $erro = "A imagem não esta em um formato correto.";
          } */

        if ($erro == 0) {
            if (is_uploaded_file($imagemNomeTemp)) {
                //$nomePasta = RAIZ_SITE . "/public/comprovantes/creditos/credito" . $credito->getCodigo() . "";
                //$nomePasta = RAIZ_SITE . "/public/comprovantes/creditos/credito" . $codigo . "";
                //SE A PASTA JA EXISTE, ELA SERÁ EXCLUIR JUNTO COM OS ARQUIVOS QUE HÁ NELA
                if (is_dir($nomePasta)) {
                    $diretorio = dir($nomePasta);
                    try {
                        while ($arquivo = $diretorio->read()) {
                            if (($arquivo != '.') && ($arquivo != '..')) {
                                unlink($nomePasta . "/" . $arquivo);
                            }
                        }
                        $excluiPasta = rmdir($nomePasta);
                    } catch (Exception $ex) {
                        
                    }
                }

                //FAZ UPLOAD DA IMAGEM PARA A PASTA DESTINO
                /* $pasta = mkdir($nomePasta, 0755, true);
                  if($pasta = true) {
                  if (move_uploaded_file($imagemNomeTemp, $nomePasta . $movePasta . $codigo . ".jpg" )) {
                  return "Imagem enviada. ";
                  } else {
                  return $erro = "Falha ao mover o arquivo (permissão de acesso, caminho inválido).";
                  }
                  } */
                $info = getimagesize($imagemNomeTemp);
                mkdir($nomePasta, 0755, true);
                if ($info['mime'] == 'image/jpeg')
                    $image = imagecreatefromjpeg($imagemNomeTemp);

                elseif ($info['mime'] == 'image/gif')
                    $image = imagecreatefromgif($imagemNomeTemp);

                elseif ($info['mime'] == 'image/png')
                    $image = imagecreatefrompng($imagemNomeTemp);

                if (imagejpeg($image, $nomePastaCompleta, 70)) {
                    return "Imagem enviada";
                } else {
                    return "Erro ao enviar imagem";
                }
            } else {
                return $erro = "Erro no envio: arquivo não recebido com sucesso.";
            }
        } else {
            return "Erro no envio: " . $erro;
        }
    }

    public function deletaImagem($tipo, $codigo) {
        if ($tipo == "CREDITO") {
            $nomePasta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/creditos/credito" . $codigo . "";
            $movePasta = "/credito_codigo_";
            $nomePastaCompleta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/creditos/credito" . $codigo . "/credito_codigo_" . $codigo . ".jpg";
        } elseif ($tipo == "DEBITO") {
            $nomePasta = RAIZ_SITE . "/public/comprovantes/debitos/" . Sessao::retornaUsuario() . "/debito" . $codigo . "";
            $movePasta = "/debito_codigo_";
            $nomePastaCompleta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/debitos/debito" . $codigo . "/debito_codigo_" . $codigo . ".jpg";
        } elseif ($tipo == "AGENDAMENTOS") {
            $nomePasta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/agendamentos/agendamento" . $codigo . "";
            $movePasta = "/agendamento_codigo_";
            $nomePastaCompleta = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/agendamentos/agendamento" . $codigo . "/agendamento_codigo_" . $codigo . ".jpg";
        } else {
            return false;
        }

        if (is_dir($nomePasta)) {
            $diretorio = dir($nomePasta);
            try {
                while ($arquivo = $diretorio->read()) {
                    if (($arquivo != '.') && ($arquivo != '..')) {
                        unlink($nomePasta . "/" . $arquivo);
                    }
                }
                $excluiPasta = rmdir($nomePasta);
            } catch (Exception $ex) {
                
            }
        } else {
            return false;
        }
    }

    public function moverImagem($tipoOrigem, $tipoDestino, $codigoOrigem, $codigoDestino) {
        //AS IMAGEM SOMENTE VÃO DE CRÉDITO PARA LANCAMENTO OU VICE-VERSA
        if ($tipoOrigem == "DEBITO") {
            $nomePastaOrigem = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/debitos/debito" . $codigoOrigem . "";
            $movePastaOrigem = "/debito_codigo_";
            $nomePastaCompletaOrigem = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/debitos/debito" . $codigoOrigem . "/debito_codigo_" . $codigoOrigem . ".jpg";

            $nomePastaDestino = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/agendamentos/agendamento" . $codigoDestino . "";
            $movePastaDestino = "/agendamento_codigo_";
            $nomePastaCompletaDestino = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/agendamentos/agendamento" . $codigoDestino . "/agendamento_codigo_" . $codigoDestino . ".jpg";
        } elseif ($tipoOrigem == "AGENDAMENTOS") {
            $nomePastaOrigem = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/agendamentos/agendamento" . $codigoOrigem . "";
            $movePastaOrigem = "/agendamento_codigo_";
            $nomePastaCompletaOrigem = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/agendamentos/agendamento" . $codigoOrigem . "/agendamento_codigo_" . $codigoOrigem . ".jpg";

            $nomePastaDestino = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/debitos/debito" . $codigoDestino . "";
            $movePastaDestino = "/debito_codigo_";
            $nomePastaCompletaDestino = RAIZ_SITE . "/public/comprovantes/" . Sessao::retornaUsuario() . "/debitos/debito" . $codigoDestino . "/debito_codigo_" . $codigoDestino . ".jpg";
        } else {
            return "Créditos não movem imagens";
        }

        //VERIFICA SE PASTA DE ORIGEM EXISTE
        if (!is_dir($nomePastaOrigem)) {
            return "Imagem de origem não existe";
        }

        //MOVE A IMAGEM
        //SE A PASTA DESTINO EXISTE, DELETA ELA E TODOS OS ARQUIVOS DENTRO.
        if (is_dir($nomePastaDestino)) {
            $diretorio = dir($nomePastaDestino);
            try {
                while ($arquivo = $diretorio->read()) {
                    if (($arquivo != '.') && ($arquivo != '..')) {
                        unlink($nomePastaDestino . "/" . $arquivo);
                    }
                }
                $excluiPasta = rmdir($nomePastaDestino);
            } catch (Exception $ex) {
                return "Não foi possivel deletar imagem atual de destino";
            }
        }

        //CRIA PASTA DESTINO
        $pasta = mkdir($nomePastaDestino, 0755, true);
        if ($pasta = true) {
            if (rename($nomePastaCompletaOrigem, $nomePastaCompletaDestino)) { //MOVE A IMAGEM DE LUGAR. E DEPOIS APAGA A PASTA DE ORIGEM
                if (is_dir($nomePastaOrigem)) {
                    $diretorio = dir($nomePastaOrigem);
                    try {
                        while ($arquivo = $diretorio->read()) {
                            if (($arquivo != '.') && ($arquivo != '..')) {
                                unlink($nomePastaOrigem . "/" . $arquivo);
                            }
                        }
                        $excluiPasta = rmdir($nomePastaOrigem);
                    } catch (Exception $ex) {
                        return "Imgem enviada. Pasta origem não deletada";
                    }
                }
                return "Imagem enviada. ";
            } else {
                return $erro = "Falha ao mover o arquivo (permissão de acesso, caminho inválido).";
            }
        }
    }

}
