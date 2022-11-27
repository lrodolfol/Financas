<?php

namespace App\Models\DAO;

use App\Models\Entidades\Debito;
use App\Models\Entidades\AgendaLancamento;
use App\Models\DAO\DebitoDAO;
use App\Lib\functions;
use DateTime;

class AgendaLancamentoDAO extends BaseDAO {

    public function sqlExportaCabecalho() {
        $sql = "SELECT * FROM lancamentos_futuros l ";
        $resultado = $this->select($sql);
        return $resultado->fetchAll();
    }

    public function sqlExportaItens() {
        $sql = "SELECT * FROM lancamentos_futuros_itens i ";
        $resultado = $this->select($sql);
        return $resultado->fetchAll();

    }

    public function agendaLancamentoArray($AgendaLancamentosArray){
        foreach ($AgendaLancamentosArray as $AgendaLancamento) {
            try {
                $functions = new functions();
                //CALCULO DO DIA DE DEBITO DO LANÇAMENTO SOMENTE SE HÁ DIA DE FECHAMENTO E FECHAMENTO PARA O DEBITO. SENÃO SERÁ A DATA INFORMADA PELO USUÁRIO
                if ($AgendaLancamento->getDiaFechamento() && $AgendaLancamento->getDiaVencimento()) {

                    /* $dataCompra = getdate(strtotime($AgendaLancamento->getDataCompra()));
                      $dia = $dataCompra["mday"];
                      $mes = $dataCompra["mon"];
                      $ano = $dataCompra["year"];
                      $dataHoje = new DateTime();
                      $dataVencimento = date("Y/m/d", strtotime("+1month"));

                      $dataVencimento = date("Y-m-d", strtotime("+1 month", strtotime($dataCompra))); */

                    $dataCompra = getdate(strtotime($AgendaLancamento->getDataCompra()));
                    $dia = $dataCompra["mday"];
                    $mes = $dataCompra["mon"];
                    $ano = $dataCompra["year"];

                    $diaFatura = $AgendaLancamento->getDiaVencimento() . "-" . $mes . "-" . $ano;
                    $diaCompra = $dia . "-" . $mes . "-" . $ano;
                    $diaFatura = date("Y-m-d", strtotime("+1 month", strtotime($diaFatura)));
                    $diaFaturaM7Dias = date("Y-m-d", strtotime("-7 days", strtotime($diaFatura)));

                    $diaFatura = new DateTime($diaFatura);
                    $diaFaturaM7Dias = new DateTime($diaFaturaM7Dias);
                    $diaCompra = new DateTime($diaCompra);

                    if($diaCompra->format('d') >= $diaFaturaM7Dias->format('d') ) {
                        $diaFatura = date("Y-m-d", strtotime("+1 month", strtotime($diaFatura->format('Y-m-d'))));
                        //$diaFatura = new DateTime($diaFatura);
                    }

                    /* if ($AgendaLancamento->getDiaVencimento() > $AgendaLancamento->getDiaFechamento()) {
                         $diaFatura = date("Y-m-d", strtotime("+1 month", strtotime($diaFatura)));
                     } else {
                         if ($AgendaLancamento->getDiaFechamento() < $dia) {
                             //$diaFatura = $AgendaLancamento->getDiaVencimento() . "-" . $mes . "-" . $ano;
                             $diaFatura = date("Y-m-d", strtotime("+1 month", strtotime($diaFatura)));
                         } else {
                             //$diaFatura = $AgendaLancamento->getDiaVencimento() . "-" . $mes . "-" . $ano;
                             $diaFatura = date("Y-m-d", strtotime("+0 month", strtotime($diaFatura)));
                         }
                     }
                     if ($AgendaLancamento->getDiaVencimento() < date('d', $AgendaLancamento->getDataCompra())) {
                         $diaFatura = date("Y-m-d", strtotime("+0 month", strtotime($diaFatura)));
                     }*/
                }

                //PEGA O DIA DE DEBITO DO LANÇAMENTO SOMENTE SE HÁ DIA DE FECHAMENTO E FECHAMENTO PARA O DEBITO. SENÃO SERÁ A DATA INFORMADA PELO USUÁRIO
                $dataDebito = isset($diaFatura) ? $diaFatura : $AgendaLancamento->getDataDebito();
                $parcelas = $AgendaLancamento->getQtdParcelas();

                $row = $this->RetornaDado("SELECT codigo FROM lancamentos_futuros ORDER BY codigo DESC LIMIT 1");
                if (!$row) {
                    $codigo = 1;
                } else {
                    $codigo = $row["codigo"] + 1;
                }

                for ($NumParcelas = 1; $NumParcelas <= $parcelas; $NumParcelas++) {
                    if ($NumParcelas > 1) {
                        //$dataDebito->add(new DateInterval('P1M'));
                        //date_add($dataDebito, date_interval_create_from_date_string('1 month'));
                        $dataDebito = date("Y-m-d", strtotime("+1 month", strtotime($dataDebito->format('Y-m-d'))));
                        //$dataDebito = new DateTime($dataDebito);
                    }

                    //A DATA DE DÉBITO SEMPRE DEVERÁ SER UM OBJETO DE DATETIME. SE NÃO FOR, PASSARÁ A SER
                    if(! is_object($dataDebito)) {
                        $dataDebito = new DateTime($dataDebito);
                    }

                    $dataCompra = $AgendaLancamento->getDataCompra();
                    //$dataDebito = $AgendaLancamento->getDataDebito();
                    $dataDebito = $dataDebito;
                    $valorTotal = $AgendaLancamento->getValorTotal() / $parcelas;
                    $estabelecimento = $AgendaLancamento->getEstabelecimento();
                    $formaPagamento = $AgendaLancamento->getFormaPagamento();
                    $qdtParcelas = $AgendaLancamento->getQtdParcelas();
                    $ativo = $AgendaLancamento->getAtivo();
                    $observacao = $AgendaLancamento->getObs();
                    $juros = $AgendaLancamento->getJuros();
                    $totalGeral = $valorTotal + $juros;

                    //RETIRADO ESSE CODIGO POIS OS LANCAMENTOS FUTUROS DEVEM TER O MESMO CÓDIGO PARA CADA LANÇAMENTO
                    /* $row = $this->RetornaDado("SELECT codigo FROM lancamentos_futuros ORDER BY codigo DESC LIMIT 1");
                      if (!$row) {
                      $codigo = 1;
                      } else {
                      $codigo = $row["codigo"] + 1;
                      } */

                    $dataDebito = $functions->proximoDiaUtill($dataDebito->format('Y-m-d'));

                    $AgendaLancamento->setCodigo($codigo);

                    if (!$this->insertLoop(
                        'lancamentos_futuros', "codigo,:data_compra,:data_debito,:valor_total,:estabelecimento,:forma_pagamento,:qtd_parcelas,:ativo,:obs,:numero_parcela,:juros,:total_geral", [
                            ':codigo' => $codigo,
                            ':data_compra' => "'" . $dataCompra . "'",
                            ':data_debito' => "'" . $dataDebito->format('Y-m-d') . "'",
                            ':valor_total' => $valorTotal,
                            ':estabelecimento' => "'" . $estabelecimento . "'",
                            ':forma_pagamento' => $formaPagamento,
                            ':qtd_parcelas' => $qdtParcelas,
                            ':ativo' => "'" . $ativo . "'",
                            ':obs' => "'" . $observacao . "'",
                            ':numero_parcela' => "'" . $NumParcelas . "'",
                            ':juros' => $juros,
                            ':total_geral' => $totalGeral,
                        ]
                    )) {

                    }
                }
            } catch (\Exception $e) {
                throw new \Exception("Erro na gravação de dados.", 500);
            }
        }
        return true;

    }
    public function agendarLancamento(AgendaLancamento $AgendaLancamento) {
        try {
            $functions = new functions();
            //CALCULO DO DIA DE DEBITO DO LANÇAMENTO SOMENTE SE HÁ DIA DE FECHAMENTO E FECHAMENTO PARA O DEBITO. SENÃO SERÁ A DATA INFORMADA PELO USUÁRIO
            if ($AgendaLancamento->getDiaFechamento() && $AgendaLancamento->getDiaVencimento()) {

                /* $dataCompra = getdate(strtotime($AgendaLancamento->getDataCompra()));
                  $dia = $dataCompra["mday"];
                  $mes = $dataCompra["mon"];
                  $ano = $dataCompra["year"];
                  $dataHoje = new DateTime();
                  $dataVencimento = date("Y/m/d", strtotime("+1month"));

                  $dataVencimento = date("Y-m-d", strtotime("+1 month", strtotime($dataCompra))); */

                $dataCompra = getdate(strtotime($AgendaLancamento->getDataCompra()));
                $dia = $dataCompra["mday"];
                $mes = $dataCompra["mon"];
                $ano = $dataCompra["year"];

                $diaFatura = $AgendaLancamento->getDiaVencimento() . "-" . $mes . "-" . $ano;
                $diaCompra = $dia . "-" . $mes . "-" . $ano;
                $diaFatura = date("Y-m-d", strtotime("+1 month", strtotime($diaFatura)));
                $diaFaturaM7Dias = date("Y-m-d", strtotime("-7 days", strtotime($diaFatura)));

                $diaFatura = new DateTime($diaFatura);
                $diaFaturaM7Dias = new DateTime($diaFaturaM7Dias);
                $diaCompra = new DateTime($diaCompra);

                if($diaCompra->format('d') >= $diaFaturaM7Dias->format('d') ) {
                    $diaFatura = date("Y-m-d", strtotime("+1 month", strtotime($diaFatura->format('Y-m-d'))));
                    //$diaFatura = new DateTime($diaFatura);
                }
                 
               /* if ($AgendaLancamento->getDiaVencimento() > $AgendaLancamento->getDiaFechamento()) {
                    $diaFatura = date("Y-m-d", strtotime("+1 month", strtotime($diaFatura)));
                } else {
                    if ($AgendaLancamento->getDiaFechamento() < $dia) {
                        //$diaFatura = $AgendaLancamento->getDiaVencimento() . "-" . $mes . "-" . $ano;
                        $diaFatura = date("Y-m-d", strtotime("+1 month", strtotime($diaFatura)));
                    } else {
                        //$diaFatura = $AgendaLancamento->getDiaVencimento() . "-" . $mes . "-" . $ano;
                        $diaFatura = date("Y-m-d", strtotime("+0 month", strtotime($diaFatura)));
                    }
                }
                if ($AgendaLancamento->getDiaVencimento() < date('d', $AgendaLancamento->getDataCompra())) {
                    $diaFatura = date("Y-m-d", strtotime("+0 month", strtotime($diaFatura)));
                }*/
            }
            
            //PEGA O DIA DE DEBITO DO LANÇAMENTO SOMENTE SE HÁ DIA DE FECHAMENTO E FECHAMENTO PARA O DEBITO. SENÃO SERÁ A DATA INFORMADA PELO USUÁRIO
            $dataDebito = isset($diaFatura) ? $diaFatura : $AgendaLancamento->getDataDebito();
            $parcelas = $AgendaLancamento->getQtdParcelas();

            $row = $this->RetornaDado("SELECT codigo FROM lancamentos_futuros ORDER BY codigo DESC LIMIT 1");
            if (!$row) {
                $codigo = 1;
            } else {
                $codigo = $row["codigo"] + 1;
            }

            for ($NumParcelas = 1; $NumParcelas <= $parcelas; $NumParcelas++) {
                if ($NumParcelas > 1) {
                    //$dataDebito->add(new DateInterval('P1M'));
                    //date_add($dataDebito, date_interval_create_from_date_string('1 month'));
                    $dataDebito = date("Y-m-d", strtotime("+1 month", strtotime($dataDebito->format('Y-m-d'))));
                    //$dataDebito = new DateTime($dataDebito);
                }

                //A DATA DE DÉBITO SEMPRE DEVERÁ SER UM OBJETO DE DATETIME. SE NÃO FOR, PASSARÁ A SER
                if(! is_object($dataDebito)) { 
                    $dataDebito = new DateTime($dataDebito);
                }

                $dataCompra = $AgendaLancamento->getDataCompra();
                //$dataDebito = $AgendaLancamento->getDataDebito();
                $dataDebito = $dataDebito;
                $valorTotal = $AgendaLancamento->getValorTotal() / $parcelas;
                $estabelecimento = $AgendaLancamento->getEstabelecimento();
                $formaPagamento = $AgendaLancamento->getFormaPagamento();
                $qdtParcelas = $AgendaLancamento->getQtdParcelas();
                $ativo = $AgendaLancamento->getAtivo();
                $observacao = $AgendaLancamento->getObs();
                $juros = $AgendaLancamento->getJuros();
                $totalGeral = $valorTotal + $juros;

                //RETIRADO ESSE CODIGO POIS OS LANCAMENTOS FUTUROS DEVEM TER O MESMO CÓDIGO PARA CADA LANÇAMENTO
                /* $row = $this->RetornaDado("SELECT codigo FROM lancamentos_futuros ORDER BY codigo DESC LIMIT 1");
                  if (!$row) {
                  $codigo = 1;
                  } else {
                  $codigo = $row["codigo"] + 1;
                  } */
                
                $dataDebito = $functions->proximoDiaUtill($dataDebito->format('Y-m-d'));

                $AgendaLancamento->setCodigo($codigo);
                
                if (!$this->insertLoop(
                            'lancamentos_futuros', "codigo,:data_compra,:data_debito,:valor_total,:estabelecimento,:forma_pagamento,:qtd_parcelas,:ativo,:obs,:numero_parcela,:juros,:total_geral", [
                            ':codigo' => $codigo,
                            ':data_compra' => "'" . $dataCompra . "'",
                            ':data_debito' => "'" . $dataDebito->format('Y-m-d') . "'",
                            ':valor_total' => $valorTotal,
                            ':estabelecimento' => "'" . $estabelecimento . "'",
                            ':forma_pagamento' => $formaPagamento,
                            ':qtd_parcelas' => $qdtParcelas,
                            ':ativo' => "'" . $ativo . "'",
                            ':obs' => "'" . $observacao . "'",
                            ':numero_parcela' => "'" . $NumParcelas . "'",
                            ':juros' => $juros,
                            ':total_geral' => $totalGeral,
                                ]
                        )) {
                    return false;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados.", 500);
        }
        return true;
    }

    public function salvarItens(AgendaLancamento $AgendaLancamento) {
        try {
            $codigoProduto = "";
            $codigoCabecalho = $AgendaLancamento->getCodigoCabecalho();
            $produto = $AgendaLancamento->getProduto();
            $qtdProduto = $AgendaLancamento->getQtdproduto();
            $valorProduto = $AgendaLancamento->getValorProduto();
            $ativoProduto = $AgendaLancamento->getAtivoProduto();
            $unidadeMedidaProduto = $AgendaLancamento->getUnidadeMedida();

            $row = $this->RetornaDado("SELECT codigo FROM lancamentos_futuros_itens ORDER BY codigo DESC LIMIT 1");
            if (!$row) {
                $codigoProduto = 1;
            } else {
                $codigoProduto = $row["codigo"] + 1;
            }

            $AgendaLancamento->setCodigoProduto($codigoProduto);

            return $this->insert(
                            'lancamentos_futuros_itens',
                            "codigo,:codigo_cabecalho,:produto,:qtd_produto,:valor_produto,:ativo,:unidade_medida",
                            [
                                ':codigo' => $codigoProduto,
                                ':codigo_cabecalho' => $codigoCabecalho,
                                ':produto' => "'" . $produto . "'",
                                ':qtd_produto' => $qtdProduto,
                                ':valor_produto' => $valorProduto,
                                ':ativo' => "'" . $ativoProduto . "'",
                                ':unidade_medida' => "'" . $unidadeMedidaProduto . "'",
                            ]
            );
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados.", 500);
        }
    }

    /* public function salvar(Debito $debito) {
      try {

      $dataCompra = $debito->getDataCompra();
      $dataDebito = $debito->getDataDebito();
      $valorTotal = $debito->getValorTotal();
      $estabelecimento = $debito->getEstabelecimento();
      $formaPagamento = $debito->getFormaPagamento();
      $qdtParcelas = $debito->getQtdParcelas();
      $ativo = $debito->getAtivo();
      $observacao = $debito->getObs();

      if (empty($codigo)) {
      $row = $this->RetornaDado("SELECT codigo FROM saida_cabecalho ORDER BY codigo DESC LIMIT 1");
      if (!$row) {
      $codigo = 1;
      } else {
      $codigo = $row["codigo"] + 1;
      }
      }

      $debito->setCodigo($codigo);

      return $this->insert(
      'saida_cabecalho', "codigo,:data_compra,:data_debito,:valor_total,:estabelecimento,:forma_pagamento,:qtd_parcelas,:ativo,:obs", [
      ':codigo' => $codigo,
      ':data_compra' => "'" . $dataCompra . "'",
      ':data_debito' => "'" . $dataDebito . "'",
      ':valor_total' => $valorTotal,
      ':estabelecimento' => "'" . $estabelecimento . "'",
      ':forma_pagamento' => $formaPagamento,
      ':qtd_parcelas' => $qdtParcelas,
      ':ativo' => "'" . $ativo . "'",
      ':obs' => "'" . $observacao . "'",
      ]
      );
      } catch (\Exception $e) {
      throw new \Exception("Erro na gravação de dados.", 500);
      }
      } */

    public function listar($codigo = null, $proximosDias = null, $antecedenteDias = null, $condicao = null) {
        if ($codigo) {
            $sql = "SELECT * FROM lancamentos_futuros l WHERE l.codigo = " . $codigo . " ORDER BY debitado, data_compra, data_debito ";
        } else {
            $sql = "SELECT l.id, l.codigo, l.data_compra, l.data_debito, l.valor_total, l.ativo, l.debitado, l.qtd_parcelas, l.codigo_debito, l.numero_parcela, "
                    . " (select nome FROM estabelecimentos e WHERE e.codigo = l.estabelecimento) as estabelecimento, "
                    . " (select descricao FROM formas_pagamento f WHERE f.codigo = l.forma_pagamento) as forma_pagamento "
                    . " FROM lancamentos_futuros l WHERE l.id IS NOT NULL ";
            if (($proximosDias) && ($antecedenteDias)) {
                $sql .= " AND l.data_debito BETWEEN DATE_ADD(current_date(), INTERVAL - " . $antecedenteDias . " DAY) and DATE_ADD(current_date(), INTERVAL " . $proximosDias . " DAY) ";
            } elseif ($proximosDias) {
                $sql .= " AND l.data_debito BETWEEN current_date() and DATE_ADD(current_date(), INTERVAL " . $proximosDias . " DAY) ";
            }
            if ($condicao) {
                $sql .= " AND l.debitado = '" . $condicao . "' ";
            }
            $sql .= " ORDER BY debitado, l.data_debito, codigo , numero_parcela ";
        }
        $resultado = $this->select($sql);
        return $resultado->fetchAll(\PDO::FETCH_CLASS, AgendaLancamento::class);
    }

    public function excluir(AgendaLancamento $AgendaLancamento) {
        try {
            $id = $AgendaLancamento->getId();
            return $this->delete('lancamentos_futuros', "id = $id");
        } catch (Exception $e) {
            throw new \Exception("Erro ao deletar", 500);
        }
    }
    
    public function excluirItens(AgendaLancamento $AgendaLancamento) {
        try {
            $codigo = $AgendaLancamento->getCodigoCabecalho();
            return $this->delete('lancamentos_futuros_itens', "codigo_cabecalho = $codigo");
        } catch (Exception $e) {
            throw new \Exception("Erro ao deletar", 500);
        }
    }

    public function detalhes($codigo) {
        if ($codigo) {
            $sql = "SELECT DISTINCT l.data_compra, s.codigo, s.produto, s.unidade_medida, s.qtd_produto, s.valor_produto FROM lancamentos_futuros l INNER JOIN lancamentos_futuros_itens s ON l.codigo = s.codigo_cabecalho AND l.codigo = $codigo ";
            $resultado = $this->select($sql);
            return $resultado->fetchAll(\PDO::FETCH_CLASS, AgendaLancamento::class);
        }
        return false;
    }

    public function carregaLancamentoFuturos(AgendaLancamento $AgendaLancamento) {
        $sql = "SELECT l.*, i.unidade_medida FROM lancamentos_futuros l LEFT JOIN lancamentos_futuros_itens i ON l.codigo = i.codigo_cabecalho WHERE l.ativo = 'S' ";
        if (!empty($AgendaLancamento->getId())) {
            $sql .= " AND l.id = " . $AgendaLancamento->getId() . "  ";
        } else {
            $sql .= " AND l.data_debito <= current_date() ";
        }
        if (empty($AgendaLancamento->getCodigo()) && empty($AgendaLancamento->getCodigoDebito())) {
            $sql .= " AND l.debitado = 'N' ";
        }

        $sql .= " LIMIT 1 ";

        $retorno = $this->RetornaDado($sql);
        if (!$retorno) {
            return false;
        }
        $AgendaLancamento->setAtivo($retorno['ativo']);
        $AgendaLancamento->setDebitado($retorno['debitado']);
        $AgendaLancamento->setDataCompra($retorno['data_compra']);
        $AgendaLancamento->setDataDebito($retorno['data_debito']);
        $AgendaLancamento->setEstabelecimento($retorno['estabelecimento']);
        $AgendaLancamento->setFormaPagamento($retorno['forma_pagamento']);
        $AgendaLancamento->setObs($retorno['obs']);
        $AgendaLancamento->setQtdParcelas($retorno['qtd_parcelas']);
        $AgendaLancamento->setValorTotal($retorno['valor_total']);
        $AgendaLancamento->setCodigo($retorno['codigo']);
        $AgendaLancamento->setId($retorno['id']);
        $AgendaLancamento->setJuros($retorno['juros']);
        $AgendaLancamento->setUnidadeMedida($retorno['unidade_medida']);
        return true;
    }

    public function carregaLancamentoFuturosItens(AgendaLancamento $AgendaLancamento) {
        $sql = "SELECT * FROM lancamentos_futuros_itens WHERE codigo_cabecalho = " . $AgendaLancamento->getCodigo() . "";
        $rowItens = $this->select($sql);
        $resultado = $rowItens->fetchAll();

        return $resultado;
    }

    public function deletaLancamentosAntigos(AgendaLancamento $AgendaLancamento) {
        $diasExclusao = "33";
        $sql = "SELECT * FROM lancamentos_futuros s WHERE data_debito < date_add(current_date(), interval -" . $diasExclusao . " day)";
        $resultado = $this->select($sql);
        $resultado->fetchAll(\PDO::FETCH_CLASS, AgendaLancamento::class);

        try {
            return $this->delete('lancamentos_futuros', "data_debito < date_add(current_date(), interval -" . $diasExclusao . " day)");
        } catch (Exception $e) {
            //throw new \Exception("Erro ao deletar", 500);
        }
    }

    public function mudaDebitado($codigoDebito) {
        try {
            return $this->update2(
                            'lancamentos_futuros',
                            [
                                'debitado' => "'N'",
                            ],
                            "codigo_debito = " . $codigoDebito . ""
            );
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados.", 500);
        }
    }

}
