<?php

namespace App\Models\DAO;

use App\Models\Entidades\Debito;
use App\Models\Entidades\Estabelecimentos;

class DebitoDAO extends BaseDAO {

    public function sqlExportaCabecalho() {
        $sql = "SELECT * FROM saida_cabecalho s ";
        $resultado = $this->select($sql);
        return $resultado->fetchAll();
    }

    public function sqlExportaItens() {
        $sql = "SELECT * FROM saidas_itens i ";
        $resultado = $this->select($sql);
        return $resultado->fetchAll();
    }

    public function listar($codigo = null, $dataInicial = null, $dataFinal = null, $palavra = null, $paginaSelecionada, $totalPorPagina) {
        $where = "";
        if ($codigo) {
            $resultado = $this->select("SELECT * FROM saida_cabecalho c WHERE c.codigo = $codigo  ");
            if (!empty($dataInicial)) {
                $sql .= " AND data_debito >= '$dataInicial' ";
            }
            if (!empty($dataFinal)) {
                $sql .= " AND data_debito <= '$dataFinal' ";
            }
            if (!empty($palavra)) {
                $sql .= " AND obs like '%$palavra%' ";
            }
            $sql .= " ORDER BY codigo DESC, ativo ";

            //return $resultado->fetchObject(Debito::class);
            return $resultado->fetchAll(\PDO::FETCH_CLASS, Debito::class);
        } else {
            $inicio = (($paginaSelecionada - 1) * $totalPorPagina);

            $sql = "SELECT c.*, e.nome as estabelecimento, f.descricao as forma_pagamento FROM saida_cabecalho c LEFT JOIN saidas_itens s ON c.codigo = s.codigo_cabecalho INNER JOIN estabelecimentos e ON c.estabelecimento = e.codigo "
                    . " INNER join formas_pagamento f ON c.forma_pagamento = f.codigo WHERE c.id IS NOT NULL  ";
            $sqlContador = "SELECT count(*) as total_linhas FROM saida_cabecalho c LEFT JOIN saidas_itens s ON c.codigo = s.codigo_cabecalho INNER JOIN estabelecimentos e ON c.estabelecimento = e.codigo "
                    . " INNER join formas_pagamento f ON c.forma_pagamento = f.codigo WHERE c.id IS NOT NULL ";

            if (!empty($dataInicial)) {
                $where .= " AND data_debito >= '$dataInicial' ";
            }
            if (!empty($dataFinal)) {
                $where .= " AND data_debito <= '$dataFinal' ";
            }
            if (!empty($palavra)) {
                $where .= " AND obs like '%$palavra%' OR s.produto like '%$palavra%' OR e.nome like '%$palavra%' OR f.descricao like '%$palavra%' OR s.produto like '%$palavra%' OR c.obs like '%$palavra%' ";
            }

            $sql .= $where . " GROUP BY c.codigo ORDER BY c.data_debito DESC, c.codigo DESC, ativo ";
            $sql .= " LIMIT " . $inicio . "," . $totalPorPagina;
            $sqlContador .= $where;

            $resultado = $this->select($sql);
            $resultadoLinhas = $this->select($sqlContador);
            $totalLinhas = $resultadoLinhas->fetch()['total_linhas'];

            return ['paginaSelecionada' => $paginaSelecionada,
                'totalPorPagina' => $totalPorPagina,
                'totalLinhas' => $totalLinhas,
                'resultado' => $resultado->fetchAll(\PDO::FETCH_CLASS, Debito::class)];
        }

        return false;
    }

    public function listarEdicao($codigo = null) {
        if ($codigo) {
            $sql = "SELECT c.*, e.codigo as codigo_estabelecimento, "
                    . " e.nome as nome_estabelecimento, f.codigo as codigo_forma_pagamento, "
                    . " f.descricao as nome_forma_pagamento  FROM "
                    . " saida_cabecalho c INNER JOIN estabelecimentos e ON "
                    . " c.codigo = " . base64_decode($codigo) . " and c.estabelecimento = e.codigo INNER JOIN formas_pagamento f ON"
                    . " c.forma_pagamento = f.codigo "
                    . " ORDER BY c.ativo";
            $resultado = $this->select($sql);

            //return $resultado->fetchObject(Debito::class);
            return $resultado->fetchAll(\PDO::FETCH_CLASS, Debito::class);
        } else {
            $resultado = $this->select("SELECT * FROM saida_cabecalho c ORDER BY ativo ");
            return $resultado->fetchAll(\PDO::FETCH_CLASS, Debito::class);
        }

        return false;
    }

    public function detalhes($codigo = null) {
        if ($codigo) {
            $sql = "SELECT c.data_compra, s.codigo, s.produto, s.unidade_medida, s.qtd_produto, s.valor_produto FROM saida_cabecalho c "
                    . "INNER JOIN saidas_itens s ON c.codigo = s.codigo_cabecalho AND c.codigo = " . base64_decode($codigo) . " ";
            $resultado = $this->select($sql);
            return $resultado->fetchAll(\PDO::FETCH_CLASS, Debito::class);
        }
        return false;
    }

    public function salvar(Debito $debito) {
        try {
            $dataCompra = $debito->getDataCompra();
            $dataDebito = $debito->getDataDebito();
            $valorTotal = $debito->getValorTotal();
            $estabelecimento = $debito->getEstabelecimento();
            $formaPagamento = $debito->getFormaPagamento();
            $qdtParcelas = $debito->getQtdParcelas();
            $ativo = $debito->getAtivo();
            $atipico = $debito->getAtipico();
            $fixo = ( $debito->getFixo() ? $debito->getFixo() : 0 );
            $observacao = $debito->getObs();
            $juros = $debito->getJuros();
            $desconto = ($debito->getDesconto() ? $debito->getDesconto() : 0);
            $totalGeral = ($valorTotal + $juros) - $desconto;
            $totalGeral = $totalGeral >= 0 ? $totalGeral : 0; //TOTAL NUNCA SERÁ NEGATIVO

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
                            'saida_cabecalho',
                            "codigo,:data_compra,:data_debito,:valor_total,:estabelecimento,:forma_pagamento,:qtd_parcelas,:ativo,:obs,:fixo,:juros,:total_geral,:desconto,:atipico",
                            [
                                ':codigo' => $codigo,
                                ':data_compra' => "'" . $dataCompra . "'",
                                ':data_debito' => "'" . $dataDebito . "'",
                                ':valor_total' => $valorTotal,
                                ':estabelecimento' => "'" . $estabelecimento . "'",
                                ':forma_pagamento' => $formaPagamento,
                                ':qtd_parcelas' => $qdtParcelas,
                                ':ativo' => "'" . $ativo . "'",
                                ':obs' => "'" . $observacao . "'",
                                ':fixo' => "" . $fixo . "",
                                ':juros' => $juros,
                                ':total_geral' => $totalGeral,
                                ':desconto' => $desconto,
                                ':atipico' => "'" . $atipico . "'",
                            ]
            );
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados.", 500);
        }
    }

    public function salvarDebitosArray($debitos) {
        foreach ($debitos as $debito) {

            if(empty($debito->getDataCompra())) {
                continue;
            }

            try {
                $dataCompra = $debito->getDataCompra();
                $dataDebito = $debito->getDataDebito();
                $valorTotal = $debito->getValorTotal();
                $estabelecimento = $debito->getEstabelecimento();
                $formaPagamento = $debito->getFormaPagamento();
                $qdtParcelas = $debito->getQtdParcelas();
                $ativo = $debito->getAtivo();
                $atipico = $debito->getAtipico();
                $fixo = ( $debito->getFixo() ? $debito->getFixo() : 0 );
                $observacao = $debito->getObs();
                $juros = $debito->getJuros();
                $desconto = ($debito->getDesconto() ? $debito->getDesconto() : 0);
                $totalGeral = ($valorTotal + $juros) - $desconto;
                $totalGeral = $totalGeral >= 0 ? $totalGeral : 0; //TOTAL NUNCA SERÁ NEGATIVO

                if (empty($codigo)) {
                    $row = $this->RetornaDado("SELECT codigo FROM saida_cabecalho ORDER BY codigo DESC LIMIT 1");
                    if (!$row) {
                        $codigo = 1;
                    } else {
                        $codigo = $row["codigo"] + 1;
                    }
                }
                $debito->setCodigo($codigo);

                $result = $this->insert(
                    'saida_cabecalho',
                    "codigo,:data_compra,:data_debito,:valor_total,:estabelecimento,:forma_pagamento,:qtd_parcelas,:ativo,:obs,:fixo,:juros,:total_geral,:desconto,:atipico",
                    [
                        ':codigo' => $codigo,
                        ':data_compra' => "'" . $dataCompra . "'",
                        ':data_debito' => "'" . $dataDebito . "'",
                        ':valor_total' => $valorTotal,
                        ':estabelecimento' => "'" . $estabelecimento . "'",
                        ':forma_pagamento' => $formaPagamento,
                        ':qtd_parcelas' => $qdtParcelas,
                        ':ativo' => "'" . $ativo . "'",
                        ':obs' => "'" . $observacao . "'",
                        ':fixo' => "" . $fixo . "",
                        ':juros' => $juros,
                        ':total_geral' => $totalGeral,
                        ':desconto' => $desconto,
                        ':atipico' => "'" . $atipico . "'",
                    ]
                );

                if($result) {
                    $debito->setCodigoCabecalho($codigo);
                    $this->salvarItens($debito);
                }

            } catch (\Exception $e) {
                throw new \Exception("Erro na gravação de dados.", 500);
            }
        }
    }

    public function salvarItens(Debito $debito) {
        try {
            $codigoProduto = "";
            $codigoCabecalho = $debito->getCodigoCabecalho();
            $produto = $debito->getProduto();
            $qtdProduto = $debito->getQtdproduto();
            $valorProduto = $debito->getValorProduto();
            $ativoProduto = $debito->getAtivoProduto();
            $unidadeMedidaProduto = $debito->getUnidadeMedida();

            $row = $this->RetornaDado("SELECT codigo FROM saidas_itens ORDER BY codigo DESC LIMIT 1");
            if (!$row) {
                $codigoProduto = 1;
            } else {
                $codigoProduto = $row["codigo"] + 1;
            }

            $debito->setCodigoProduto($codigoProduto);

            return $this->insert(
                            'saidas_itens',
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

    public function atualizar(Debito $debito) {
        try {

            $codigo = $debito->getCodigoCabecalho();
            $valor = $debito->getValorTotal();
            $dataCompra = $debito->getDataCompra();
            $estabelecimento = $debito->getEstabelecimento();
            $formaPagamento = $debito->getFormaPagamento();
            $parcelas = $debito->getQtdParcelas();
            $ativo = $debito->getAtivo();
            $fixo = $debito->getFixo();
            $observacao = $debito->getObs();
            $juros = $debito->getJuros();
            $desconto = $debito->getDesconto();
            $totalGeral = ($valor + $juros) - $desconto;
            $totalGeral = $totalGeral >= 0 ? $totalGeral : 0; //TOTAL NUNCA SERÁ NEGATIVO

            return $this->update2(
                            'saida_cabecalho',
                            [
                                'valor_total' => $valor,
                                'data_compra' => "'" . $dataCompra . "'",
                                'estabelecimento' => $estabelecimento,
                                'forma_pagamento' => $formaPagamento,
                                'qtd_parcelas' => $parcelas,
                                'ativo' => "'" . $ativo . "'",
                                'fixo' => "" . $fixo . "",
                                'obs' => "'" . $observacao . "'",
                                'juros' => $juros,
                                'desconto' => $desconto,
                                'total_geral' => $totalGeral,
                            ],
                            "codigo = $codigo"
            );
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados.", 500);
        }
    }

    
    public function verificaItensDebitosFixo(Debito $debito){
        $sql = "SELECT i.* FROM saida_cabecalho s INNER JOIN saidas_itens i ON s.codigo = i.codigo_cabecalho "
        . " AND s.codigo = 3";
        $resultado = $this->select($sql);
        $row = $resultado->fetchAll();
        if($row) {
            return $row;
        }else{
            return null;
        }
    }
    
    
    public function excluir(Debito $debito) {
        try {
            $codigo = $debito->getCodigo();

            //PREENCHE A TABELA DE DEBITO PARA ATUALIZAR  CAXA DEPOIS
            $sql = "SELECT * FROM saida_cabecalho WHERE codigo = $codigo";
            $rowDebito = $this->RetornaDado($sql);
            $debito->setAtivo($rowDebito['ativo']);
            $debito->setFixo($rowDebito['fixo']);
            $debito->setDataDebito($rowDebito['data_debito']);
            $debito->setObs("Exclusão do débito: $codigo. Valor R$ " . number_format($rowDebito['valor_total'], '2', ',', '.'));
            $debito->setValorTotal($rowDebito['valor_total']);
            $debito->setJuros($rowDebito['juros']);

            $rowCabecalho = $this->delete('saida_cabecalho', "codigo = $codigo");
            $this->delete('saidas_itens', "codigo_cabecalho = $codigo"); //APÓS EXCLUIR O CABEÇALHO, EXCLUI OS ITENS DE DÉBITO
            return $rowCabecalho;
        } catch (Exception $e) {
            throw new \Exception("Erro ao deletar", 500);
        }
    }

    public function excluirItem(Debito $debito) {
        try {
            $codigo = $debito->getCodigoProduto();

            return $this->delete('saidas_itens', "codigo = $codigo");
        } catch (Exception $e) {

            throw new \Exception("Erro ao deletar", 500);
        }
    }

    public function excluirItens(Debito $debito) {
        try {
            $codigo = $debito->getCodigo();

            return $this->delete('saidas_itens', "codigo_cabecalho = $codigo");
        } catch (Exception $e) {

            throw new \Exception("Erro ao deletar", 500);
        }
    }

    public function retornaUltimoSaldo($codigo) {
        $sql = "SELECT total_geral,ativo FROM saida_cabecalho WHERE codigo = $codigo ORDER BY id LIMIT 1";
        $rowCredito = $this->RetornaDado($sql);
        return $rowCredito['total_geral'] . "," . $rowCredito['ativo'];
    }

}
