<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Lib\functions;
use App\Models\DAO\UsuarioDAO;
use App\Models\DAO\HomeDAO;
use App\Models\DAO\CreditoDAO;
use App\Models\DAO\DebitoDAO;
use App\Models\DAO\AgendaLancamentoDAO;
use App\Models\DAO\FormaPagamentoDAO;
use App\Models\DAO\EstabelecimentoDAO;
use App\Models\DAO\ContasReceberDAO;
use App\Models\DAO\CaixaDAO;
use App\Models\DAO\ContaDAO;
use App\Models\Entidades\Usuario;
use App\Models\Entidades\RelatoErro;
use App\Models\Entidades\Email;
use App\Controllers\EmailAutent;
use DateTime;

class ContaController extends Controller {

    private $arquivoNome;
    private $arquivoTipo;
    private $arquivoTamanho;
    private $arquivoNomeTemp;
    private $erro;
    private $extensao;
    private $extensoesPermitidas;
    private $nomeTabelas;
    private $tabelas;
    private $tipoTabelas;

    public function __construct() {
        $this->arquivoNome = $_FILES["arquivo_importacao"]["name"];
        $this->arquivoTipo = $_FILES["arquivo_importacao"]["type"];
        $this->arquivoTamanho = $_FILES["arquivo_importacao"]["size"];
        $this->arquivoNomeTemp = $_FILES["arquivo_importacao"]["tmp_name"];
        $this->erro = $_FILES["arquivo_importacao"]["error"];
        $this->extensao = strrchr($_FILES['arquivo_importacao']['name'], '.');
        $this->extensoesPermitidas = array('.XML', '.JSON', '.CSV');

        $this->nomeTabelas = array("entradas", "estabelecimentos", "formas_pagamento", "caixa", "saida_cabecalho", "saidas_itens", "lancamentos_futuros", "lancamentos_futuros_itens", "contas_receber");
        $this->tabelas = array(
            /* ENTRADAS */ array("codigo", "descricao", "obs", "valor", "ativo", "fixo", "data"),
            /* ESTAB */ array("codigo", "nome", "cnpj", "tipo_comercio", "cidade", "ativo"),
            /* FORM PAG */ array("codigo", "descricao", "ativo", "dia_fechamento", "dia_vencimento"),
            /* CAIXA */ array("descricao", "obs", "ativo", "saldo", "data", "codigo_saida_cabecalho", "codigo_entrada"),
            /* SAIDA CABEÇ */ array("codigo", "data_compra", "data_debito", "valor_total", "estabelecimento", "forma_pagamento", "qtd_parcelas", "ativo", "fixo", "obs", "juros", "total_geral", "desconto"),
            /* SAIDA ITEN */ array("codigo", "codigo_cabecalho", "produto", "qtd_produto", "valor_produto", "ativo", "unidade_medida"),
            /* LANÇ. FUT */ array("codigo", "data_compra", "data_debito", "valor_total", "estabelecimento", "forma_pagamento", "qtd_parcelas", "ativo", "debitado", "obs", "codigo_cabecalho", "codigo_debito", "numero_parcela", "juros", "total_geral"),
            /* LANÇ. ITEN */ array("codigo", "codigo_cabecalho", "produto", "qtd_produto", "valor_produto", "ativo", "unidade_medida"),
            /* CONTAS RECEBER */ array("codigo", "descricao", "obs", "valor", "ativo", "fixo", "data_compensacao", "creditado"),
        );
        //AQUI É PARA INFORMAR QUAL O TIPO DE CAMPO DOS CAMPOS DAS TABELAS. PARA INSERIR '' OU NAO NAS IMPORTAÇÕES.
        $this->tipoTabelas = array(
            /* ENTRADAS */ array("int", "char", "char", "int", "char", "char", "char"),
            /* ESTAB */ array("int", "char", "cnpj", "char", "char", "char"),
            /* FORM PAG */ array("int", "char", "char", "char", "char"),
            /* CAIXA */ array("char", "char", "char", "int", "char", "int", "int"),
            /* SAIDA CABEÇ */ array("int", "char", "char", "int", "int", "int", "int", "char", "char", "char", "int", "int"),
            /* SAIDA ITEN */ array("int", "int", "char", "int", "int", "char", "char"),
            /* LANÇ. FUT */ array("int", "char", "char", "int", "int", "int", "int", "char", "char", "char", "int", "int", "int"),
            /* LANÇ. ITEN */ array("int", "int", "char", "int", "int", "char", "char"),
            /* CONTAS RECEBER */ array("int", "char", "char", "int", "char", "char", "char", "char"),
        );
    }

    public function importarConta() {
        //FAZ UMA BUSCAR NO DISCO PARA VER QUAIS BACKUPS O USUÁRIO TEM
        $caminhoBackXML = RAIZ_SITE . "/public/usuarios/" . Sessao::retornaUsuario() . "/backup_conta/XML";
        $caminhoBackJSON = RAIZ_SITE . "/public/usuarios/" . Sessao::retornaUsuario() . "/backup_conta/JSON";
        $caminhoBackCSV = RAIZ_SITE . "/public/usuarios/" . Sessao::retornaUsuario() . "/backup_conta/CSV";
        $backup = array();
        $contArray = 0;
        if (is_dir($caminhoBackXML)) {
            $dir = dir($caminhoBackXML);
            $backup[0] = "<optgroup label='XML'>";
            $contArray++;
            while ($arquivo = $dir->read()) {
                if (($arquivo != '.') && ($arquivo != '..')) {
                    $backup[$contArray] = $arquivo;
                    $contArray++;
                }
            }
        }
        if (is_dir($caminhoBackJSON)) {
            $dir = dir($caminhoBackJSON);
            $contArray = count($backup) + 1;
            $backup[$contArray] = "<optgroup label='JSON'>";
            $contArray++;
            while ($arquivo = $dir->read()) {
                if (($arquivo != '.') && ($arquivo != '..')) {
                    $backup[$contArray] = $arquivo;
                    $contArray++;
                }
            }
        }
        if (is_dir($caminhoBackCSV)) {
            $dir = dir($caminhoBackCSV);
            $contArray = count($backup) + 1;
            $backup[$contArray] = "<optgroup label='CSV'>";
            $contArray++;
            while ($arquivo = $dir->read()) {
                if (($arquivo != '.') && ($arquivo != '..')) {
                    $backup[$contArray] = $arquivo;
                    $contArray++;
                }
            }
        }

        self::setViewParam('backupXML', $backup);

        $arquivoJSON = $caminhoSalvarJSON . "/financas_" . Sessao::retornaUsuario() . "_" . $dataBackup . ".json";

        $this->render("home/importarConta");
    }

    public function importaConta() {
        try {
            if (!in_array(strtoupper($this->extensao), $this->extensoesPermitidas) === true) {
                Sessao::gravaErro("Escolha um arquivo em formato XML, JSON OU CSV.");
            } else {
                if (strtoupper($this->extensao) == ".XML") {
                    $this->importaContaXML();
                } elseif (strtoupper($this->extensao) == ".JSON") {
                    $this->importaContaJSON();
                }
                Sessao::gravaMensagem("Conta Atualizada com sucesso. ");
            }
        } catch (Exception $exc) {
            Sessao::gravaErro("Erro ao atualizar a conta. " . $exc);
        }
        $this->render('/home/importarConta');
    }

    public function importaContaXML() {
        $dom = new \DOMDocument();
        $dom->load($this->arquivoNomeTemp);
        $sqlImportar = array();
        $contRegistro = 0;
        $contRegistroAux = 0;

        for ($x = 0; $x <= count($this->nomeTabelas); $x++) {

            $entrada = $dom->getElementsByTagName($this->nomeTabelas[$x]);
            foreach ($entrada as $value) {

                //AQUI DEU UM PEQUENO PROBLEMA, POIS AO LER AS TAG DE DEBITOS, O SCRIPT ESTA LENDO O CAMPO 'forma_pagamente' 
                //E ENTENDENDO COMO SE ELE FOSSE A TABELA 'forma_pagamento'. 
                //POR ISSO COLOQUEI ESSA TRAVA. POIS CADA TABELA NÃO TEM TAMANHO MAIOR QUE 1 NO ARRAY
                if (count($entrada) > 1) {
                    break;
                }

                $sqlImportar[$contRegistro] = "DELETE FROM {$this->nomeTabelas[$x]} WHERE id >= 1 ";
                $contRegistro++;

                $registros = $value->childNodes;
                foreach ($registros as $registro) {

                    $sqlImportar[$contRegistro] = "INSERT INTO {$this->nomeTabelas[$x]} ( ";
                    for ($i = 0; $i <= count($this->tabelas[$x]); $i++) {
                        $sqlImportar[$contRegistro] .= $this->tabelas[$x][$i] . ",";
                    }
                    $sqlImportar[$contRegistro] = substr($sqlImportar[$contRegistro], 0, strlen($sqlImportar[$contRegistro]) - 2);
                    $sqlImportar[$contRegistro] .= ") VALUES (";

                    for ($i = 0; $i <= count($this->tabelas[$x]); $i++) {
                        //ALGUNA CAMPOS INTEGER NA TABELA NÃO PODEM CONTER '' SE NÃO TIVER VALOR, ENTÃO PASSARAM A SER NULL
                        //CAIXA CODIGO SAIDA E CODIGO ENTRADA
                        if (($this->nomeTabelas[$x] == "lancamentos_futuros" && ( ($this->tabelas[$x][$i] == 'codigo_cabecalho' or $this->tabelas[$x][$i] == 'codigo_debito') and ( empty($registro->getElementsByTagName($this->tabelas[$x][$i])->item(0)->nodeValue) ) )) or ( $this->nomeTabelas[$x] == "caixa" && ( ($this->tabelas[$x][$i] == 'codigo_saida_cabecalho' or $this->tabelas[$x][$i] == 'codigo_entrada') and ( empty($registro->getElementsByTagName($this->tabelas[$x][$i])->item(0)->nodeValue) ) ) )) {
                            $sqlImportar[$contRegistro] .= "null,";
                        } else {
                            $sqlImportar[$contRegistro] .= "'" . $registro->getElementsByTagName($this->tabelas[$x][$i])->item(0)->nodeValue . "',";
                        }
                    }
                    $sqlImportar[$contRegistro] = substr($sqlImportar[$contRegistro], 0, strlen($sqlImportar[$contRegistro]) - 4);
                    $sqlImportar[$contRegistro] .= ")";

                    $contRegistro++;
                }
                $DAO = new HomeDAO();
                $DAO->executaSqlArray($sqlImportar);
                $sqlImportar = array();
                $contRegistro = 0;
            }
        }
    }

    public function importaContaJSON() {
        $caminhoJSON = fopen($this->arquivoNomeTemp, "r");
        $tamanhoJSON = filesize($this->arquivoNomeTemp);      //RECUPERA O TAMANHO DO ARQUIVO EM QUESTÃO
        $json = fread($caminhoJSON, $tamanhoJSON);            //ATRIBUI O ARQUIVO A UMA VARIAVEL EM SEU TAMANHO TOTAL

        $jsonDecodificado = json_decode($json);               //DECODIFICA O JSON PARA UM OBJETO
        //var_dump($jsonDecodificado) . '<br>';
        fclose($caminhoJSON);
        $sqlImportar = array();
        $cont = 0;
        $contTabelas = 0;

        try {
            foreach ($jsonDecodificado as $key => $value) {
                $sqlImportar[$cont] = "DELETE FROM " . $key . " WHERE id >= 0 ";
                $cont++;
                foreach ($value as $registro) {
                    $sqlImportar[$cont] = " INSERT INTO " . $key . " (";
                    for ($i = 0; $i < count($this->tabelas[$contTabelas]); $i++) {
                        $sqlImportar[$cont] .= " " . $this->tabelas[$contTabelas][$i] . ",";
                    }
                    $sqlImportar[$cont] = substr($sqlImportar[$cont], 0, strlen($sqlImportar[$cont]) - 1);
                    $sqlImportar[$cont] .= ") VALUES (";
                    for ($i = 0; $i < count($this->tabelas[$contTabelas]); $i++) {
                        if ($this->tipoTabelas[$contTabelas][$i] == "int" && empty($registro->{$this->tabelas[$contTabelas][$i]})) {
                            if ($registro->{$this->tabelas[$contTabelas][$i]} = 0) {
                                $sqlImportar[$cont] .= "0,";
                            } else {
                                $sqlImportar[$cont] .= "0,";
                            }
                        } else {
                            if ($this->tipoTabelas[$contTabelas][$i] == "int") {
                                $sqlImportar[$cont] .= "" . $registro->{$this->tabelas[$contTabelas][$i]} . ",";
                            } else {
                                $sqlImportar[$cont] .= "'" . str_replace("'", "", $registro->{$this->tabelas[$contTabelas][$i]}) . "',";
                            }
                        }
                    }
                    $sqlImportar[$cont] = substr($sqlImportar[$cont], 0, strlen($sqlImportar[$cont]) - 1);
                    $sqlImportar[$cont] .= ")";

                    $cont++;
                }

                $contTabelas++;
                $DAO = new HomeDAO();
                $DAO->executaSqlArray($sqlImportar);
                $sqlImportar = array();
                $cont = 0;
            }
            return true;
        } catch (Exception $exc) {
            return false;
        }
    }

    public function exportarConta() {
        $this->render("home/exportarConta");
    }

    public function exportaConta($zerandoConta = false) {
        $tabelas = array();
        $tabelas[1] = isset($_POST['credito']) ? "entradas" : "";
        $tabelas[2] = isset($_POST['debito']) ? "saida_cabecalho" : "";
        $tabelas[3] = isset($_POST['debito']) ? "saidas_itens" : "";
        $tabelas[4] = isset($_POST['debitoFuturo']) ? "lancamentos_futuros" : "";
        $tabelas[5] = isset($_POST['debitoFuturo']) ? "lancamentos_futuros_itens" : "";
        $tabelas[6] = isset($_POST['estabelecimentos']) ? "estabelecimentos" : "";
        $tabelas[7] = isset($_POST['formaPagamento']) ? "formas_pagamento" : "";
        $tabelas[8] = isset($_POST['caixa']) ? "caixa" : "";
        $tabelas[9] = isset($_POST['contasReceber']) ? "contas_receber" : "";
        $tipoArquivo = $_POST['tipoArquivo'];

        if (count($tabelas) > 0 && isset($tipoArquivo)) {
            if (strtoupper($tipoArquivo) == "XML") {
                $exportar = ($this->exportaContaXML($tabelas));
                if ($exportar) {
                    Sessao::gravaMensagem("Seu arquivo backup foi realizado. Está pronto para Download abaixo. Você pode baixar quando quiser no sistema =) ");
                    self::setViewParam('exportadoXML', "TRUE");
                } else {
                    Sessao::gravaMensagem("Ocorreu um eror ao exportar o arquivo");
                    self::setViewParam('exportadoXML', "FALSE");
                }
            } elseif (strtoupper($tipoArquivo) == "JSON") {
                $exportar = ($this->exportaContaJSON($tabelas));
                if ($exportar) {
                    Sessao::gravaMensagem("Seu arquivo está pronto para Download. Clique em Baixar");
                    self::setViewParam('exportadoJSON', "TRUE");
                } else {
                    Sessao::gravaMensagem("Ocorreu um eror ao exportar o arquivo");
                    self::setViewParam('exportadoJSON', "FALSE");
                }
            } elseif (strtoupper($tipoArquivo) == "TXT") {
                $exporar = $this->exportaContaTXT($tabelas);
                if ($exportar) {
                    Sessao::gravaMensagem("Seu arquivo está pronto para Download. Clique em Baixar");
                    self::setViewParam('exportadoTXT', "TRUE");
                } else {
                    Sessao::gravaMensagem("Ocorreu um eror ao exportar o arquivo");
                    self::setViewParam('exportadoTXT', "FALSE");
                }
            } else {
                Sessao::gravaMensagem("Informe pelo menos um cadastro e um tipo de aruivo para exportação");
            }
        } else {
            Sessao::gravaMensagem("Informe pelo menos um cadastro e um tipo de aruivo para exportação");
        }
        if (!$zerandoConta) {
            $exportar = ($this->render("home/exportarConta"));
        }
    }

    public function exportaContaTXT($tabelas) {
        ECHO 'exportando para txt <br>';

        $creditoDAO = new CreditoDAO();
        $debitoDAO = new DebitoDAO();
        $agendaLancamentoDAO = new AgendaLancamentoDAO();
        $estabelecimentosDAO = new EstabelecimentoDAO();
        $formaPagamentoDAO = new FormaPagamentoDAO();
        $caixaDAO = new CaixaDAO();
        $contasReceberDAO = new ContasReceberDAO();

        $dataBackup = date('d-m-Y');
        $criarPastaTXT = false;
        $caminhoSalvarTXT = RAIZ_SITE . "/public/usuarios/" . Sessao::retornaUsuario() . "/backup_conta/TXT";
        if (!is_dir($caminhoSalvarTXT)) {
            $criarPastaTXT = mkdir($caminhoSalvarTXT, 0755, true);
        }
        $caminhoSalvarTXT .= "/financas_" . Sessao::retornaUsuario() . "_" . $dataBackup . ".txt";
        $criaArquivo = fopen($caminhoSalvarTXT, "w");

        if (!$criarPastaTXT && !$criaArquivo) {
            return false;
        }

        $stringFile = "";
        //0000 = DADOS DO USUÁRIO
        $usuarioDAO = new UsuarioDAO();
        $sql = "SELECT * FROM usuarios WHERE codigo = " . Sessao::retornaCodigoUsuario() . " ";
        $dadosUser = $usuarioDAO->listarUsuario($sql);
                
        $stringFile = "0000|" . $dadosUser->nome . "|" . $dados->sobreNome . "|"
        . "123456" . "|" . $dadosUser->idade . "|" . $dadosUser->email . "|" . 
        $dadosUser->recebe_email . "|" . $dadosUser->desenvolvedor . "|" . 
        $dadosUser->data_cadastro . "|" . $dadosUser->data_nascimento . PHP_EOL;
        fwrite($criaArquivo, $stringFile);
        
        //FAZER ENCERRAMENTO
        //IMPORTANTE FAZER REPLACE DOS |

        foreach ($tabelas as $tabela) {

            switch (strtoupper($tabela)) {
                case "ESTABELECIMENTOS" :
                    $table = $estabelecimentosDAO->retornaObject("SELECT * FROM estabelecimentos");
                    
                    /*foreach ($table as $key => $value) {
                        $stringFile = "0100|" . $value->codigo . "|" . $value->nome . "|";   
                        $stringFile .= $value->cnpj . "|" . $value->tipo_comercio . "|"; 
                        $stringFile .= $value->cidade . "|" . $value->ativo . "|" . PHP_EOL; 
                        fwrite($criaArquivo, $stringFile);
                    }*/  
                    break;
                 case "FORMAS_PAGAMENTO" :
                    $table = $estabelecimentosDAO->retornaObject("SELECT * FROM formas_pagamento");
                    foreach ($table as $key => $value) {
                        $stringFile = "0110|" . $value->codigo . "|" . $value->descricao . "|";   
                        $stringFile .= $value->ativo . "|" . $value->dia_fechamento . "|"; 
                        $stringFile .= $value->dia_vecimento . PHP_EOL; 
                        fwrite($criaArquivo, $stringFile);
                    }
                    break;
                case "ENTRADAS" :
                    $table = $estabelecimentosDAO->retornaObject("SELECT * FROM entradas");
                    foreach ($table as $key => $value) {
                        $stringFile = "0300|" . $value->codigo . "|" . $value->descricao . "|";   
                        $stringFile .= $value->observacao . "|" . $value->valor . "|"; 
                        $stringFile .= $value->data . "|" . $value->lucro_real . PHP_EOL; 
                        fwrite($criaArquivo, $stringFile);
                    }
                    
                    $table = $estabelecimentosDAO->retornaObject("SELECT * FROM contas_receber");                            
                    foreach ($table as $key => $value) {
                        $stringFile = "0310|" . $value->codigo . "|" . $value->descricao . "|";   
                        $stringFile .= $value->observacao . "|" . $value->valor . "|"; 
                        $stringFile .= $value->ativo . "|" . $value->data_compensacao . "|";
                        $stringFile .= $value->lucro_real . "|" . $value->creditado . "|";
                        $stringFile .= $value->codigo_entrada . PHP_EOL; 
                        fwrite($criaArquivo, $stringFile);
                    }
                    break;
                case "SAIDA_CABECALHO" :
                    $table = $estabelecimentosDAO->retornaObject("SELECT * FROM saida_cabecalho");
                    foreach ($table as $key => $value) {
                        $stringFile = "0400|" . $value->codigo . "|" . $value->data_compra . "|";   
                        $stringFile .= $value->data_debito . "|" . $value->valor_total . "|"; 
                        $stringFile .= $value->estabelecimento . "|" . $value->forma_pagamento . "|";
                        $stringFile .= $value->quantidade_parcelas . "|" . $value->ativo . "|";
                        $stringFile .= $value->observacao . "|" . $value->juros . "|";
                        $stringFile .= $value->total_geral . "|" . $value->desconto . "|";
                        $stringFile .= $value->atipico . "|" . PHP_EOL; 
                        fwrite($criaArquivo, $stringFile);
                    }
                            
                    $table = $estabelecimentosDAO->retornaObject("SELECT * FROM saidas_itens");
                    foreach ($table as $key => $value) {
                        $stringFile = "0410|" . $value->codigo . "|" . $value->codigo_cabecalho . "|";   
                        $stringFile .= $value->produto . "|" . $value->quantidade_produto . "|"; 
                        $stringFile .= $value->valor_produto . "|" . $value->ativo . "|";
                        $stringFile .= $value->unidade_medida . PHP_EOL; 
                        fwrite($criaArquivo, $stringFile);
                    }
                    break;
                case "LANCAMENTOS_FUTUROS" :
                    $table = $estabelecimentosDAO->retornaObject("SELECT * FROM lancamentos_futuros");
                    foreach ($table as $key => $value) {
                        $stringFile = "0420|" . $value->codigo . "|" . $value->data_compra . "|";   
                        $stringFile .= $value->data_debito . "|" . $value->valor_total . "|"; 
                        $stringFile .= $value->estabelecimento . "|" . $value->forma_pagamento . "|";
                        $stringFile .= $value->quantidade_parcelas . "|" . $value->ativo . "|";
                        $stringFile .= $value->observacao . "|" . $value->juros . "|";
                        $stringFile .= $value->total_geral . "|" . $value->desconto . "|";
                        $stringFile .= $value->atipico . "|" . PHP_EOL; 
                        fwrite($criaArquivo, $stringFile);
                    }
                    
                    $table = $estabelecimentosDAO->retornaObject("SELECT * FROM lancamentos_futuros_itens");
                    foreach ($table as $key => $value) {
                        $stringFile = "0430|" . $value->codigo . "|" . $value->codigo_cabecalho . "|";   
                        $stringFile .= $value->produto . "|" . $value->quantidade_produto . "|"; 
                        $stringFile .= $value->valor_produto . "|" . $value->ativo . "|";
                        $stringFile .= $value->unidade_medida . PHP_EOL; 
                        fwrite($criaArquivo, $stringFile);
                    }
                    break;
                case "CAIXA" :
                    $table = $estabelecimentosDAO->retornaObject("SELECT * FROM caixa");
                    foreach ($table as $key => $value) {
                        $stringFile = "0500|" . $value->descricao . "|" . $value->observacao . "|";   
                        $stringFile .= $value->ativo . "|" . $value->saldo . "|"; 
                        $stringFile .= $value->data . "|" . $value->codigo_saida . "|";
                        $stringFile .= $value->codigo_entrada . PHP_EOL; 
                        fwrite($criaArquivo, $stringFile);
                    }
                    break;
            }
        }
        fclose($criaArquivo);
        return true;
    }

    public function exportaContaXML($tabelas) {
        $creditoDAO = new CreditoDAO();
        $debitoDAO = new DebitoDAO();
        $agendaLancamentoDAO = new AgendaLancamentoDAO();
        $estabelecimentosDAO = new EstabelecimentoDAO();
        $formaPagamentoDAO = new FormaPagamentoDAO();
        $caixaDAO = new CaixaDAO();
        $contasReceberDAO = new ContasReceberDAO();

        $entradas = "";
        $saida_cabecalho = "";
        $saidas_itens = "";
        $lancamentos_futuros = "";
        $lancamentos_futuros_itens = "";
        $estabelecimentos = "";
        $formas_pagamento = "";
        $caixa = "";
        $contasReceber = "";

        for ($x = 0; $x <= count($tabelas); $x++) {
            if ($tabelas[$x] == "") {
                continue;
            }
            if ($tabelas[$x] == "entradas") {
                //$sqlExportaXML['entrada'] = $creditoDAO->listar(null, null, null, null, 1, 9999);
                $entradas = $creditoDAO->sqlExporta();
            }
            if ($tabelas[$x] == "saida_cabecalho") {
                $saida_cabecalho = $debitoDAO->sqlExportaCabecalho();
            }
            if ($tabelas[$x] == "saidas_itens") {
                $saidas_itens = $debitoDAO->sqlExportaItens();
            }
            if ($tabelas[$x] == "lancamentos_futuros") {
                $lancamentos_futuros = $agendaLancamentoDAO->sqlExportaCabecalho();
            }
            if ($tabelas[$x] == "lancamentos_futuros_itens") {
                $lancamentos_futuros_itens = $agendaLancamentoDAO->sqlExportaItens();
            }
            if ($tabelas[$x] == "estabelecimentos") {
                $estabelecimentos = $estabelecimentosDAO->carregaEstabelecimento("N", null);
            }
            if ($tabelas[$x] == "formas_pagamento") {
                $forma_pagamento = $formaPagamentoDAO->carregaFormaPagamento("N", "", null);
            }
            if ($tabelas[$x] == "caixa") {
                $caixa = $caixaDAO->listar(null);
            }
            if ($tabelas[$x] == "contas_receber") {
                $contasReceber = $contasReceberDAO->listar(null);
            }
        }

        try {
            //CRIAR UM NOVO ARQUIVO DE INSTANCIA DO DOM
            $dom = new \DOMDocument("1.0", "UTF-8");
            $dom->preserveWhiteSpace = FALSE;
            $dom->formatOutPut = TRUE;

            $root = $dom->createElement("FINANCAS"); //CRIAR UM ELEMENTO PRINCIAL(tag) FINANÇAS

            if (!empty($entradas)) {    //CRIA UM ELEMENTO PARA A TABELA ENTRADA
                $tabela = $dom->createElement("entradas");
                $idTabela = 1;

                foreach ($entradas as $key => $value) {
                    $registroTabela = $dom->createElement("reg"); //CRIAR UM ELEMENTO PARA ARMAZENAR OS REGISTROS

                    $codigo = $dom->createElement("codigo", $value['codigo']); //INFORMA OS REGISTROS E SEUS VALORES
                    $descricao = $dom->createElement("descricao", $value['descricao']);
                    $obs = $dom->createElement("obs", $value['obs']);
                    $valor = $dom->createElement("valor", $value['valor']);
                    $ativo = $dom->createElement("ativo", $value['ativo']);
                    $fixo = $dom->createElement("fixo", $value['fixo']);
                    $data = $dom->createElement("data", $value['data']);

                    $registroTabela->appendChild($codigo); //ATRIBUI OS REGISTROS DENTRO DO ELEMENTO REGISTRO TABELA
                    $registroTabela->appendChild($descricao);
                    $registroTabela->appendChild($obs);
                    $registroTabela->appendChild($valor);
                    $registroTabela->appendChild($ativo);
                    $registroTabela->appendChild($fixo);
                    $registroTabela->appendChild($data);

                    $tabela->appendChild($registroTabela); //ATRIBUI O ELEMENTO TABELA DENTRO DA TABELA
                    $root->appendChild($tabela); //ATRIBUI A TABELA DENTRO DO ROOT
                }

                $idTabela++;
            }

            if (!empty($estabelecimentos)) {    //CRIA UM ELEMENTO PARA A TABELA ENTRADA
                $tabela = $dom->createElement("estabelecimentos");
                $idTabela = 1;

                foreach ($estabelecimentos as $key => $value) {
                    $registroTabela = $dom->createElement("reg"); //CRIAR UM ELEMENTO PARA ARMAZENAR OS REGISTROS
                    //AQUI TO USANDO GET PQ O RETORNO DA FUNÇÃO DE BUSCA DOS ESBELECIMENTO É UM OBJETO E NÃO UM ARRAY
                    $codigo = $dom->createElement("codigo", $value->getCodigo()); //INFORMA OS REGISTROS E SEUS VALORES 
                    $nome = $dom->createElement("nome", $value->getNome());
                    $cnpj = $dom->createElement("cnpj", $value->getCnpj());
                    $tipo_comercio = $dom->createElement("tipo_comercio", $value->tipo_comercio);
                    $cidade = $dom->createElement("cidade", $value->getCidade());
                    $ativo = $dom->createElement("ativo", $value->getAtivo());

                    $registroTabela->appendChild($codigo); //ATRIBUI OS REGISTROS DENTRO DO ELEMENTO REGISTRO TABELA
                    $registroTabela->appendChild($nome);
                    $registroTabela->appendChild($cnpj);
                    $registroTabela->appendChild($tipo_comercio);
                    $registroTabela->appendChild($cidade);
                    $registroTabela->appendChild($ativo);

                    $tabela->appendChild($registroTabela); //ATRIBUI O ELEMENTO TABELA DENTRO DA TABELA
                    $root->appendChild($tabela); //ATRIBUI A TABELA DENTRO DO ROOT
                }

                $idTabela++;
            }

            if (!empty($forma_pagamento)) {    //CRIA UM ELEMENTO PARA A TABELA ENTRADA
                $tabela = $dom->createElement("formas_pagamento");
                $idTabela = 1;

                foreach ($forma_pagamento as $key => $value) {
                    $registroTabela = $dom->createElement("reg"); //CRIAR UM ELEMENTO PARA ARMAZENAR OS REGISTROS
                    //AQUI TO USANDO GET PQ O RETORNO DA FUNÇÃO DE BUSCA DOS ESBELECIMENTO É UM OBJETO E NÃO UM ARRAY
                    $codigo = $dom->createElement("codigo", $value->getCodigo()); //INFORMA OS REGISTROS E SEUS VALORES 
                    $descricao = $dom->createElement("descricao", $value->getDescricao());
                    $ativo = $dom->createElement("ativo", $value->getAtivo());
                    $dia_fechamento = $dom->createElement("dia_fechamento", $value->dia_fechamento);
                    $dia_vencimento = $dom->createElement("dia_vencimento", $value->dia_vencimento);


                    $registroTabela->appendChild($codigo); //ATRIBUI OS REGISTROS DENTRO DO ELEMENTO REGISTRO TABELA
                    $registroTabela->appendChild($descricao);
                    $registroTabela->appendChild($ativo);
                    $registroTabela->appendChild($dia_fechamento);
                    $registroTabela->appendChild($dia_vencimento);

                    $tabela->appendChild($registroTabela); //ATRIBUI O ELEMENTO TABELA DENTRO DA TABELA
                    $root->appendChild($tabela); //ATRIBUI A TABELA DENTRO DO ROOT
                }

                $idTabela++;
            }

            if (!empty($caixa)) {    //CRIA UM ELEMENTO PARA A TABELA ENTRADA
                $tabela = $dom->createElement("caixa");
                $idTabela = 1;

                foreach ($caixa as $key => $value) {
                    $registroTabela = $dom->createElement("reg"); //CRIAR UM ELEMENTO PARA ARMAZENAR OS REGISTROS
                    //AQUI TO USANDO GET PQ O RETORNO DA FUNÇÃO DE BUSCA DOS ESBELECIMENTO É UM OBJETO E NÃO UM ARRAY
                    $descricao = $dom->createElement("descricao", $value['descricao']);  //INFORMA OS REGISTROS E SEUS VALORES 
                    $obs = $dom->createElement("obs", $value['obs']);
                    $ativo = $dom->createElement("ativo", $value['ativo']);
                    $saldo = $dom->createElement("saldo", $value['saldo']);
                    $data = $dom->createElement("data", $value['data']);
                    $codigo_saida_cabecalho = $dom->createElement("codigo_saida_cabecalho", $value['codigo_saida_cabecalho']);
                    $codigo_entrada = $dom->createElement("codigo_entrada", $value['codigo_entrada']);


                    $registroTabela->appendChild($descricao); //ATRIBUI OS REGISTROS DENTRO DO ELEMENTO REGISTRO TABELA
                    $registroTabela->appendChild($obs);
                    $registroTabela->appendChild($ativo);
                    $registroTabela->appendChild($saldo);
                    $registroTabela->appendChild($data);
                    $registroTabela->appendChild($codigo_saida_cabecalho);
                    $registroTabela->appendChild($codigo_entrada);

                    $tabela->appendChild($registroTabela); //ATRIBUI O ELEMENTO TABELA DENTRO DA TABELA
                    $root->appendChild($tabela); //ATRIBUI A TABELA DENTRO DO ROOT
                }

                $idTabela++;
            }

            if (!empty($saida_cabecalho)) {    //CRIA UM ELEMENTO PARA A TABELA SAIDA CABECALHO
                $tabela = $dom->createElement("saida_cabecalho");
                $idTabela = 1;

                foreach ($saida_cabecalho as $key => $value) {
                    $registroTabela = $dom->createElement("reg"); //CRIAR UM ELEMENTO PARA ARMAZENAR OS REGISTROS

                    $codigo = $dom->createElement("codigo", $value['codigo']); //INFORMA OS REGISTROS E SEUS VALORES
                    $data_compra = $dom->createElement("data_compra", $value['data_compra']);
                    $data_debito = $dom->createElement("data_debito", $value['data_debito']);
                    $valor_total = $dom->createElement("valor_total", $value['valor_total']);
                    $estabelecimento = $dom->createElement("estabelecimento", $value['estabelecimento']);
                    $forma_pagamento = $dom->createElement("forma_pagamento", $value['forma_pagamento']);
                    $qtd_parcelas = $dom->createElement("qtd_parcelas", $value['qtd_parcelas']);
                    $ativo = $dom->createElement("ativo", $value['ativo']);
                    $fixo = $dom->createElement("fixo", $value['fixo']);
                    $obs = $dom->createElement("obs", $value['obs']);
                    $juros = $dom->createElement("juros", $value['juros']);
                    $totalGeral = $dom->createElement("total_geral", $value['total_geral']);
                    $desconto = $dom->createElement("desconto", $value['desconto']);

                    $registroTabela->appendChild($codigo);
                    $registroTabela->appendChild($data_compra);
                    $registroTabela->appendChild($data_debito);
                    $registroTabela->appendChild($valor_total);
                    $registroTabela->appendChild($estabelecimento);
                    $registroTabela->appendChild($forma_pagamento);
                    $registroTabela->appendChild($qtd_parcelas);
                    $registroTabela->appendChild($ativo);
                    $registroTabela->appendChild($fixo);
                    $registroTabela->appendChild($obs);
                    $registroTabela->appendChild($juros);
                    $registroTabela->appendChild($totalGeral);
                    $registroTabela->appendChild($desconto);

                    $tabela->appendChild($registroTabela);
                    $root->appendChild($tabela);
                }

                $idTabela++;
            }

            if (!empty($saidas_itens)) {    //CRIA UM ELEMENTO PARA A TABELA SAIDA CABECALHO
                $tabela = $dom->createElement("saidas_itens");
                $idTabela = 1;

                foreach ($saidas_itens as $key => $value) {
                    $registroTabela = $dom->createElement("reg"); //CRIAR UM ELEMENTO PARA ARMAZENAR OS REGISTROS

                    $codigo = $dom->createElement("codigo", $value['codigo']); //INFORMA OS REGISTROS E SEUS VALORES
                    $codigo_cabecalho = $dom->createElement("codigo_cabecalho", $value['codigo_cabecalho']);
                    $produto = $dom->createElement("produto", $value['produto']);
                    $qtd_produto = $dom->createElement("qtd_produto", $value['qtd_produto']);
                    $valor_produto = $dom->createElement("valor_produto", $value['valor_produto']);
                    $ativo = $dom->createElement("ativo", $value['ativo']);
                    $unidade_medida = $dom->createElement("unidade_medida", $value['unidade_medida']);

                    $registroTabela->appendChild($codigo);
                    $registroTabela->appendChild($codigo_cabecalho);
                    $registroTabela->appendChild($produto);
                    $registroTabela->appendChild($qtd_produto);
                    $registroTabela->appendChild($valor_produto);
                    $registroTabela->appendChild($ativo);
                    $registroTabela->appendChild($unidade_medida);

                    $tabela->appendChild($registroTabela);
                    $root->appendChild($tabela);
                }

                $idTabela++;
            }

            if (!empty($lancamentos_futuros)) {    //CRIA UM ELEMENTO PARA A TABELA SAIDA CABECALHO
                $tabela = $dom->createElement("lancamentos_futuros");
                $idTabela = 1;

                foreach ($lancamentos_futuros as $key => $value) {
                    $registroTabela = $dom->createElement("reg"); //CRIAR UM ELEMENTO PARA ARMAZENAR OS REGISTROS

                    $codigo = $dom->createElement("codigo", $value['codigo']); //INFORMA OS REGISTROS E SEUS VALORES
                    $data_compra = $dom->createElement("data_compra", $value['data_compra']);
                    $data_debito = $dom->createElement("data_debito", $value['data_debito']);
                    $valor_total = $dom->createElement("valor_total", $value['valor_total']);
                    $estabelecimento = $dom->createElement("estabelecimento", $value['estabelecimento']);
                    $forma_pagamento = $dom->createElement("forma_pagamento", $value['forma_pagamento']);
                    $qtd_parcelas = $dom->createElement("qtd_parcelas", $value['qtd_parcelas']);
                    $ativo = $dom->createElement("ativo", $value['ativo']);
                    $debitado = $dom->createElement("debitado", $value['debitado']);
                    $obs = $dom->createElement("obs", $value['obs']);
                    $codigo_cabecalho = $dom->createElement("codigo_cabecalho", $value['codigo_cabecalho']);
                    $codigo_debito = $dom->createElement("codigo_debito", $value['codigo_debito']);
                    $numero_parcela = $dom->createElement("numero_parcela", $value['numero_parcela']);
                    $juros = $dom->createElement("juros", $value['juros']);
                    $totalGeral = $dom->createElement("total_geral", $value['total_geral']);

                    $registroTabela->appendChild($codigo);
                    $registroTabela->appendChild($data_compra);
                    $registroTabela->appendChild($data_debito);
                    $registroTabela->appendChild($valor_total);
                    $registroTabela->appendChild($estabelecimento);
                    $registroTabela->appendChild($forma_pagamento);
                    $registroTabela->appendChild($qtd_parcelas);
                    $registroTabela->appendChild($ativo);
                    $registroTabela->appendChild($debitado);
                    $registroTabela->appendChild($obs);
                    $registroTabela->appendChild($codigo_cabecalho);
                    $registroTabela->appendChild($codigo_debito);
                    $registroTabela->appendChild($numero_parcela);
                    $registroTabela->appendChild($juros);
                    $registroTabela->appendChild($totalGeral);

                    $tabela->appendChild($registroTabela);
                    $root->appendChild($tabela);
                }

                $idTabela++;
            }

            if (!empty($lancamentos_futuros_itens)) {    //CRIA UM ELEMENTO PARA A TABELA SAIDA CABECALHO
                $tabela = $dom->createElement("lancamentos_futuros_itens");
                $idTabela = 1;

                foreach ($lancamentos_futuros_itens as $key => $value) {
                    $registroTabela = $dom->createElement("reg"); //CRIAR UM ELEMENTO PARA ARMAZENAR OS REGISTROS

                    $codigo = $dom->createElement("codigo", $value['codigo']); //INFORMA OS REGISTROS E SEUS VALORES
                    $codigo_cabecalho = $dom->createElement("codigo_cabecalho", $value['codigo_cabecalho']);
                    $produto = $dom->createElement("produto", $value['produto']);
                    $qtd_produto = $dom->createElement("qtd_produto", $value['qtd_produto']);
                    $valor_produto = $dom->createElement("valor_produto", $value['valor_produto']);
                    $ativo = $dom->createElement("ativo", $value['ativo']);
                    $unidade_medida = $dom->createElement("unidade_medida", $value['unidade_medida']);

                    $registroTabela->appendChild($codigo);
                    $registroTabela->appendChild($codigo_cabecalho);
                    $registroTabela->appendChild($produto);
                    $registroTabela->appendChild($qtd_produto);
                    $registroTabela->appendChild($valor_produto);
                    $registroTabela->appendChild($ativo);
                    $registroTabela->appendChild($unidade_medida);

                    $tabela->appendChild($registroTabela);
                    $root->appendChild($tabela);
                }

                $idTabela++;
            }

            if (!empty($contasReceber)) {    //CRIA UM ELEMENTO PARA A TABELA ENTRADA
                $tabela = $dom->createElement("contas_receber");
                $idTabela = 1;

                foreach ($contasReceber as $key => $value) {
                    $registroTabela = $dom->createElement("reg"); //CRIAR UM ELEMENTO PARA ARMAZENAR OS REGISTROS

                    $codigo = $dom->createElement("codigo", $value['codigo']); //INFORMA OS REGISTROS E SEUS VALORES
                    $descricao = $dom->createElement("descricao", $value['descricao']);
                    $obs = $dom->createElement("obs", $value['obs']);
                    $valor = $dom->createElement("valor", $value['valor']);
                    $ativo = $dom->createElement("ativo", $value['ativo']);
                    $fixo = $dom->createElement("fixo", $value['fixo']);
                    $dataComepensacao = $dom->createElement("data_compensacao", $value['data_compensacao']);
                    $creditado = $dom->createElement("creditado", $value['creditado']);

                    $registroTabela->appendChild($codigo); //ATRIBUI OS REGISTROS DENTRO DO ELEMENTO REGISTRO TABELA
                    $registroTabela->appendChild($descricao);
                    $registroTabela->appendChild($obs);
                    $registroTabela->appendChild($valor);
                    $registroTabela->appendChild($ativo);
                    $registroTabela->appendChild($fixo);
                    $registroTabela->appendChild($dataComepensacao);
                    $registroTabela->appendChild($creditado);

                    $tabela->appendChild($registroTabela); //ATRIBUI O ELEMENTO TABELA DENTRO DA TABELA
                    $root->appendChild($tabela); //ATRIBUI A TABELA DENTRO DO ROOT
                }
                $idTabela++;
            }



            $dom->appendChild($root); //ATRIBUI O ROOT DENTRO DO ARQUIVO DOM
            //echo RAIZ_SITE . "/financas.xml"; die();
            $dataBackup = date('d-m-Y');
            $caminhoSalvarXML = RAIZ_SITE . "/public/usuarios/" . Sessao::retornaUsuario() . "/backup_conta/XML";
            $arquivoXML = $caminhoSalvarXML . "/financas_" . Sessao::retornaUsuario() . "_" . $dataBackup . ".xml";
            $salvarXML = false;
            if (!is_dir($caminhoSalvarXML)) {
                $salvarXML = mkdir($caminhoSalvarXML, 0755, true);
            }
            $salvar = $dom->save($arquivoXML); //SALVA O XML COM NOME(salvo na raiz da pasta) PARA DOWNLOAD
            if ($salvar) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

    public function exportaContaJSON($tabelas) {
        $json = "{";
        $tabelajson = "";

        $creditoDAO = new CreditoDAO();
        $debitoDAO = new DebitoDAO();
        $agendaLancamentoDAO = new AgendaLancamentoDAO();
        $estabelecimentosDAO = new EstabelecimentoDAO();
        $formaPagamentoDAO = new FormaPagamentoDAO();
        $caixaDAO = new CaixaDAO();
        $contasReceberDAO = new ContasReceberDAO();

        $camposEntrada = array("codigo", "descricao", "obs", "valor", "ativo", "fixo", "data");
        $camposEstabelecimentos = array("codigo", "nome", "cnpj", "tipo_comercio", "cidade", "ativo");
        $camposFormaPagamento = array("codigo", "descricao", "ativo", "dia_fechamento", "dia_vencimento");
        $camposCaixa = array("descricao", "obs", "ativo", "saldo", "data", "codigo_saida_cabecalho", "codigo_entrada");
        $camposSaidaCabecalho = array("codigo", "data_compra", "data_debito", "valor_total", "estabelecimento", "forma_pagamento", "qtd_parcelas", "ativo", "fixo", "obs", "juros", "total_geral", "desconto");
        $camposSaidasItens = array("codigo", "codigo_cabecalho", "produto", "qtd_produto", "valor_produto", "ativo", "unidade_medida");
        $camposLancamentosFuturos = array("codigo", "data_compra", "data_debito", "valor_total", "estabelecimento", "forma_pagamento", "qtd_parcelas", "ativo", "fixo", "obs", "juros", "total_geral");
        $camposLancamentosFuturosItens = array("codigo", "codigo_cabecalho", "produto", "qtd_produto", "valor_produto", "ativo", "unidade_medida");
        $camposContaReceber = array("codigo", "descricao", "obs", "valor", "ativo", "fixo", "data_compensacao", "creditado");

        $entradas = "";
        $estabelecimentos = "";
        $entradas = $creditoDAO->sqlExporta();

        $cont = 0;

        //===============ENTRADAS==============
        $json .= '"entradas":' . '[';
        foreach ($entradas as $key => $value) {
            $tabelajson .= '{';
            $cont = 0;
            foreach ($value as $valor) {
                if ($cont >= count($camposEntrada)) {
                    $tabelajson = substr($tabelajson, 0, strlen($tabelajson) - 1);
                    break;
                }
                $tabelajson .= '"' . $camposEntrada[$cont] . '": ' . '"' . $value[$camposEntrada[$cont]] . '",';
                $cont++;
            }
            $tabelajson .= '},';
            $json .= $tabelajson;
            $tabelajson = "";
        }
        $json = substr($json, 0, strlen($json) - 1);
        $json .= "]";



        //===============ESTABELECIMENTOS==============
        if (strtoupper($tabelas[6]) == "ESTABELECIMENTOS") {
            $estabelecimentos = $estabelecimentosDAO->sqlExporta();
            $json .= ",";
            $json .= '"estabelecimentos":' . '[';
            foreach ($estabelecimentos as $key => $value) {
                $tabelajson .= '{';
                $cont = 0;
                foreach ($value as $valor) {
                    if ($cont >= count($camposEstabelecimentos)) {
                        $tabelajson = substr($tabelajson, 0, strlen($tabelajson) - 1);
                        break;
                    }
                    $tabelajson .= '"' . $camposEstabelecimentos[$cont] . '": ' . '"' . $value[$camposEstabelecimentos[$cont]] . '",';
                    $cont++;
                }
                $tabelajson .= '},';
                $json .= $tabelajson;
                $tabelajson = "";
            }
            $json = substr($json, 0, strlen($json) - 1);
            $json .= "]";
        }

        //===============FORMA PAGAMENTO==============
        if (strtoupper($tabelas[7]) == "FORMAS_PAGAMENTO") {
            $formaPagamento = $formaPagamentoDAO->sqlExporta();
            $json .= ",";
            $json .= '"formas_pagamento":' . '[';
            foreach ($formaPagamento as $key => $value) {
                $tabelajson .= '{';
                $cont = 0;
                foreach ($value as $valor) {
                    if ($cont >= count($camposFormaPagamento)) {
                        $tabelajson = substr($tabelajson, 0, strlen($tabelajson) - 1);
                        break;
                    }
                    $tabelajson .= '"' . $camposFormaPagamento[$cont] . '": ' . '"' . $value[$camposFormaPagamento[$cont]] . '",';
                    $cont++;
                }
                $tabelajson .= '},';
                $json .= $tabelajson;
                $tabelajson = "";
            }
            $json = substr($json, 0, strlen($json) - 1);
            $json .= "]";
        }

        //===============CAIXA==============
        if (strtoupper($tabelas[8]) == "CAIXA") {
            $caixa = $caixaDAO->listar(null);
            $json .= ",";
            $json .= '"caixa":' . '[';
            foreach ($caixa as $key => $value) {
                $tabelajson .= '{';
                $cont = 0;
                foreach ($value as $valor) {
                    if ($cont >= count($camposCaixa)) {
                        $tabelajson = substr($tabelajson, 0, strlen($tabelajson) - 1);
                        break;
                    }
                    $tabelajson .= '"' . $camposCaixa[$cont] . '": ' . '"' . str_replace('"', "'", $value[$camposCaixa[$cont]]) . '",';
                    $cont++;
                }
                $tabelajson .= '},';
                $json .= $tabelajson;
                $tabelajson = "";
            }
            $json = substr($json, 0, strlen($json) - 1);
            $json .= "]";
        }

        //===============SAIDA CABEÇALHO==============
        if (strtoupper($tabelas[2]) == "SAIDA_CABECALHO") {
            $saidaCabecalho = $debitoDAO->sqlExportaCabecalho();
            $json .= ",";
            $json .= '"saida_cabecalho":' . '[';
            foreach ($saidaCabecalho as $key => $value) {
                $tabelajson .= '{';
                $cont = 0;
                foreach ($value as $valor) {
                    if ($cont >= count($camposSaidaCabecalho)) {
                        $tabelajson = substr($tabelajson, 0, strlen($tabelajson) - 1);
                        break;
                    }
                    $tabelajson .= '"' . $camposSaidaCabecalho[$cont] . '": ' . '"' . str_replace('"', "'", $value[$camposSaidaCabecalho[$cont]]) . '",';
                    $cont++;
                }
                $tabelajson .= '},';
                $json .= $tabelajson;
                $tabelajson = "";
            }
            $json = substr($json, 0, strlen($json) - 1);
            $json .= "]";
        }

        //===============SAIDA ITENS==============
        if (strtoupper($tabelas[3]) == "SAIDAS_ITENS") {
            $saidaItens = $debitoDAO->sqlExportaItens();
            $json .= ",";
            $json .= '"saidas_itens":' . '[';
            foreach ($saidaItens as $key => $value) {
                $tabelajson .= '{';
                $cont = 0;
                foreach ($value as $valor) {
                    if ($cont >= count($camposSaidasItens)) {
                        $tabelajson = substr($tabelajson, 0, strlen($tabelajson) - 1);
                        break;
                    }
                    $tabelajson .= '"' . $camposSaidasItens[$cont] . '": ' . '"' . str_replace('"', "'", $value[$camposSaidasItens[$cont]]) . '",';
                    $cont++;
                }
                $tabelajson .= '},';
                $json .= $tabelajson;
                $tabelajson = "";
            }
            $json = substr($json, 0, strlen($json) - 1);
            $json .= "]";
        }

        //===============LANCAMENTOS FUTUROS==============
        if (strtoupper($tabelas[4]) == "LANCAMENTOS_FUTUROS") {
            $lancamentoFuturos = $agendaLancamentoDAO->sqlExportaCabecalho();
            $json .= ",";
            $json .= '"lancamentos_futuros":' . '[';
            foreach ($lancamentoFuturos as $key => $value) {
                $tabelajson .= '{';
                $cont = 0;
                foreach ($value as $valor) {
                    if ($cont >= count($camposLancamentosFuturos)) {
                        $tabelajson = substr($tabelajson, 0, strlen($tabelajson) - 1);
                        break;
                    }
                    $tabelajson .= '"' . $camposLancamentosFuturos[$cont] . '": ' . '"' . str_replace('"', "'", $value[$camposLancamentosFuturos[$cont]]) . '",';
                    $cont++;
                }
                $tabelajson .= '},';
                $json .= $tabelajson;
                $tabelajson = "";
            }
            $json = substr($json, 0, strlen($json) - 1);
            $json .= "]";
        }

        //===============LANCAMENTOS FUTUROS ITENS==============
        if (strtoupper($tabelas[5]) == "LANCAMENTOS_FUTUROS_ITENS") {
            $lancamentoFuturosItens = $agendaLancamentoDAO->sqlExportaItens();
            $json .= ",";
            $json .= '"lancamentos_futuros_itens":' . '[';
            foreach ($lancamentoFuturosItens as $key => $value) {
                $tabelajson .= '{';
                $cont = 0;
                foreach ($value as $valor) {
                    if ($cont >= count($camposLancamentosFuturosItens)) {
                        $tabelajson = substr($tabelajson, 0, strlen($tabelajson) - 1);
                        break;
                    }
                    $tabelajson .= '"' . $camposLancamentosFuturosItens[$cont] . '": ' . '"' . str_replace('"', "'", $value[$camposLancamentosFuturosItens[$cont]]) . '",';
                    $cont++;
                }
                $tabelajson .= '},';
                $json .= $tabelajson;
                $tabelajson = "";
            }
            $json = substr($json, 0, strlen($json) - 1);
            $json .= "]";
        }

        //===============CONTAS RECEBER==============
        if (strtoupper($tabelas[9]) == "CONTAS_RECEBER") {
            $contasReceber = $contasReceberDAO->listar(null);
            $json .= ",";
            $json .= '"contas_receber":' . '[';
            foreach ($contasReceber as $key => $value) {
                $tabelajson .= '{';
                $cont = 0;
                foreach ($value as $valor) {
                    if ($cont >= count($camposContaReceber)) {
                        $tabelajson = substr($tabelajson, 0, strlen($tabelajson) - 1);
                        break;
                    }
                    $tabelajson .= '"' . $camposContaReceber[$cont] . '": ' . '"' . str_replace('"', "'", $value[$camposContaReceber[$cont]]) . '",';
                    $cont++;
                }
                $tabelajson .= '},';
                $json .= $tabelajson;
                $tabelajson = "";
            }
            $json = substr($json, 0, strlen($json) - 1);
            $json .= "]";
        }

        //$json = json_encode($entradas);
        $json .= "}";
        //echo $json;
        $dataBackup = date('d-m-Y');
        $caminhoSalvarJSON = RAIZ_SITE . "/public/usuarios/" . Sessao::retornaUsuario() . "/backup_conta/JSON";
        $arquivoJSON = $caminhoSalvarJSON . "/financas_" . Sessao::retornaUsuario() . "_" . $dataBackup . ".json";
        $salvarJSON = false;
        if (!is_dir($caminhoSalvarJSON)) {
            $salvarXML = mkdir($caminhoSalvarJSON, 0755, true);
        }
        if (file_exists($arquivoJSON)) {
            unlink($arquivoJSON);
        }
        $criouJson = fopen($arquivoJSON, 'a+');
        if ($criouJson) {
            fwrite($criouJson, $json);
            fclose($criouJson);
            return true;
        } else {
            return false;
        }
    }

    public function zeraConta() {
        $this->exportaConta(true);
        $HomeDAO = new HomeDAO();

        $tabelas = array();
        $tabelas[1] = isset($_POST['credito']) ? "entradas" : "";
        $tabelas[2] = isset($_POST['debito']) ? "saida_cabecalho" : "";
        $tabelas[3] = isset($_POST['debito']) ? "saidas_itens" : "";
        $tabelas[4] = isset($_POST['debitoFuturo']) ? "lancamentos_futuros" : "";
        $tabelas[5] = isset($_POST['debitoFuturo']) ? "lancamentos_futuros_itens" : "";
        $tabelas[6] = isset($_POST['estabelecimentos']) ? "estabelecimentos" : "";
        $tabelas[7] = isset($_POST['formaPagamento']) ? "formas_pagamento" : "";
        $tabelas[8] = isset($_POST['caixa']) ? "caixa" : "";
        $tabelas[9] = isset($_POST['contas_receber']) ? "contas_receber" : "";

        if ($HomeDAO->zeraConta($tabelas)) {
            Sessao::gravaMensagem("Contas zeradas com sucesso. ");
        } else {
            Sessao::gravaMensagem("Erro ao zerar as contas. ");
        }

        $this->render("/home/zerarConta");
    }

    public function zerarConta() {
        $this->render("/home/zerarConta");
    }

    public function salvarRelatoErros() {
        $titulo = $_POST['titulo'];
        $texto = $_POST['texto'];
        $dataHoje = $_POST['data'];
        $usuario = $_POST['usuario'];

        $RelatoErro = new RelatoErro();
        $RelatoErro->setData($dataHoje);
        $RelatoErro->setTexto(str_replace("'", "''", $texto));
        $RelatoErro->setTitlo(str_replace("'", "''", $titulo));
        $RelatoErro->setUsuario($usuario);

        $HomeDAO = new HomeDAO();
        $imagemTamanho = $_FILES["arquivo_imagem"]["size"];
        if ($imagemTamanho / 1000000 > 4) {
            Sessao::gravaErro("A imagem exede o limite de 4MB.");
        } else {
            $erro = $HomeDAO->relatarErro($RelatoErro);
            if ($erro) {
                Sessao::gravaMensagem("Obrigado por nos informar o erro. Iremos analizar seu relato e entraremos em contato o mais rápido possível.");
                //SE GRAVOU TUDO CORRETAMENTE, ENTÃO CARREGA A IMAGEM DE CREDITO
                $rowImagemErro = $this->carregaImagem("RELATOSERRO", $RelatoErro->getCodigo());

                //MANDA EMAIL SOBRE ALERTA DE ERRO
                $Email = new Email();
                $Email->setAssunto("Relato de erros");
                $Email->setTexto("O usuário " . Sessao::retornaUsuario() . " enviou um novo relato de erro em: " . $RelatoErro->getData() . ". \r\n" . "" . $RelatoErro->getTitlo() . "");
                $Email->setDestinatario(EMAIL_DESENVOLVEDOR);
                $Email->setRemetente(EMAIL_DESENVOLVEDOR);
                $HomeDAO->enviaEmail($Email);
            } else {
                Sessao::gravaErro("Não foi possível guardar as informações tente novamente mais tarde.");
            }
        }
        $this->render("home/relatarErros");
    }

    public function relatarErros() {
        $dataHoje = new DateTime(DATE('Y/m/d'));
        $dataCadastroUsuario = new DateTime(Sessao::retornaDataCadastroUsuario());
        $dateInterval = $dataCadastroUsuario->diff($dataHoje);
        if ($dateInterval->y == 0) {
            self::setViewParam('permiteCriarErros', "N");
        }
        $this->render("home/relatarErros");
    }

    public function meusRelatosErros() {
        $codigoErro = $_POST['codigoErro'];
        $MeusRelatosErros = new ContaDAO();
        $erros = $MeusRelatosErros->listaRelatosErros();
        $erroPesquisa = $MeusRelatosErros->listaRelatosErros($codigoErro);
        self::setViewParam('detalhes', $codigoErro);
        self::setViewParam('relatosErrosPesquisa', $erroPesquisa);
        self::setViewParam('relatosErros', $erros);

        /* $email = new EmailAutent();
          $email->add("Um novo relato de erro!", "<h1>O usuario " . Sessao::retornaCodigoUsuario() . " criou um novo relato de erro no sistema Finanças</h1>",
          "Rodolfo de Jesus Silva", "rodolfo0ti@gmail.com");
          var_dump($email);
          die(); */

        $this->render("home/meusRelatosErros");
    }

    public function bloqueioCompetencia($params) {

        if (isset($_POST['ano']) or isset($_POST['anoBloq'])) {
            $ano = isset($_POST['ano']) ? $_POST['ano'] : $_POST['anoBloq'];
            if ($ano) {
                $ContaDAO = new ContaDAO();
                $compBloqueado = $ContaDAO->retornaCompetenciaBloqueada($ano);
                self::setViewParam('compBloqueado', $compBloqueado);
                self::setViewParam('anoBloqueado', $ano);
            }
        }

        $this->render('/home/bloqueioCompetencia');
    }

    public function bloquearCompetencia() {
        $mesesBloquear = ([
            "1" => isset($_POST['1']) ? "S" : "N",
            "2" => isset($_POST['2']) ? "S" : "N",
            "3" => isset($_POST['3']) ? "S" : "N",
            "4" => isset($_POST['4']) ? "S" : "N",
            "5" => isset($_POST['5']) ? "S" : "N",
            "6" => isset($_POST['6']) ? "S" : "N",
            "7" => isset($_POST['7']) ? "S" : "N",
            "8" => isset($_POST['8']) ? "S" : "N",
            "9" => isset($_POST['9']) ? "S" : "N",
            "10" => isset($_POST['10']) ? "S" : "N",
            "11" => isset($_POST['11']) ? "S" : "N",
            "12" => isset($_POST['12']) ? "S" : "N",
        ]);
        $ano = $_POST['anoBloq'];

        $ContaDAO = new ContaDAO();
        $ContaDAO->bloquearCompetencia($mesesBloquear, $ano);

        $this->bloqueioCompetencia($ano);
    }

}
