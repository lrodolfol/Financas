<?php

namespace App\Models\DAO;

use App\Models\Entidades\Debito;
use App\Models\Entidades\Caixa;
use App\Models\Entidades\AgendaLancamento;
use App\Models\DAO\DebitoDAO;
use App\Models\DAO\CaixaDAODAO;
use App\Models\DAO\AgendaLancamentoDAO;
use App\Lib\Sessao;

class TarefasDAO extends BaseDAO {

    public function DebitaLancamentosFuturos(AgendaLancamento $AgendaLancamento) {
        $DebitoDAO = new DebitoDAO();
        $Debito = new Debito();

        $Debito->setAtivo($AgendaLancamento->getAtivo());
        $Debito->setDataCompra($AgendaLancamento->getDataCompra());
        $dataHoje = DATE('Y-m-d');
        $Debito->setDataDebito($dataHoje);
        //$Debito->setDataDebito($AgendaLancamento->getDataDebito());
        $Debito->setEstabelecimento($AgendaLancamento->getEstabelecimento());

        //BUSCO A DESCRIÇÃO DO ESTABELECIMENTO PARA SER EXIBIDO NO CAIXA, INCLUSIVE...
        $row = $this->RetornaDado("SELECT nome FROM estabelecimentos WHERE codigo = " . $AgendaLancamento->getEstabelecimento());
        $nomeEstabelecimento = $row[0];

        $Debito->setFormaPagamento($AgendaLancamento->getFormaPagamento());
        $Debito->setObs("Contas a pagar cod " . $AgendaLancamento->getCodigo() . ' em: ' . $nomeEstabelecimento . ". " . $AgendaLancamento->getObs());
        $Debito->setQtdParcelas($AgendaLancamento->getQtdParcelas());
        $Debito->setValorTotal($AgendaLancamento->getValorTotal());
        $Debito->setJuros($AgendaLancamento->getJuros());

        //CASO ESSE LANÇAMENTO JA TENHA SIDO DEBITADO, ENTAO SELE SERÁ EXCLUIDO DO DEBITO E RETORNARA PARA CREDITO
        //DO CONTRARIO, ELE SERA DEBITADO EM CAIXA
        if ($AgendaLancamento->getDebitado() == 'S') {
            $Debito->setCodigo($AgendaLancamento->getCodigoDebito());
            $rowDebito = $DebitoDAO->excluirItens($Debito);
            $rowDebito = $DebitoDAO->excluir($Debito);
        } else {
            $rowDebito = $DebitoDAO->salvar($Debito);
            if ($rowDebito) {

                //CARREGA OS ITENS DO LANÇAMENTOS PARA JOGAR COMO ITEM DO DÉBITO
                $AgendaLancamentoDAO = new AgendaLancamentoDAO();
                $DebitoItens = new Debito();
                $DebitoItens = $AgendaLancamentoDAO->carregaLancamentoFuturosItens($AgendaLancamento);
                $DebitoItensAtualizar = new Debito();
                //SE TIVER ITENS DO LANÇAMENTO FUTURO ENTAO JOGA ESSES ITENS NO ITENS DE DÉBITO
                foreach ($DebitoItens as $key => $value) {
                    $DebitoItensAtualizar->setCodigoCabecalho($Debito->getCodigo());
                    $DebitoItensAtualizar->setProduto($value['produto']);
                    $DebitoItensAtualizar->setQtdProduto(1);
                    $DebitoItensAtualizar->setValorProduto($value['valor_produto'] / $Debito->getQtdParcelas());
                    $DebitoItensAtualizar->setAtivo("S");
                    $DebitoItensAtualizar->setUnidadeMedida($value['unidade_medida']);

                    $DebitoDAO->salvarItens($DebitoItensAtualizar);
                }

                if (!$DebitoItens) {
                    //SE NÃO TIVER ITENS DESSE LANÇAMENTO FUTURO, ENTÃO CRIA UM ITEM PARA ELE
                    $DebitoItensAtualizar->setCodigoCabecalho($Debito->getCodigo());
                    $DebitoItensAtualizar->setProduto($nomeEstabelecimento);
                    $DebitoItensAtualizar->setDataCompra($AgendaLancamento->getDataCompra());
                    $DebitoItensAtualizar->setValorTotal($AgendaLancamento->getValorTotal());
                    $DebitoItensAtualizar->setEstabelecimento($AgendaLancamento->getEstabelecimento());
                    $DebitoItensAtualizar->setFormaPagamento($AgendaLancamento->getFormaPagamento());
                    $DebitoItensAtualizar->setObs($AgendaLancamento->getObs());
                    $DebitoItensAtualizar->setJuros($AgendaLancamento->getJuros());
                    $DebitoItensAtualizar->setDesconto($AgendaLancamento->getDebitado());
                    $DebitoItensAtualizar->setQtdProduto(1);
                    $DebitoItensAtualizar->setUnidadeMedida($AgendaLancamento->getUnidadeMedida());
                    $DebitoItensAtualizar->setValorProduto($AgendaLancamento->getValorTotal());
                    $DebitoItensAtualizar->setAtivo($AgendaLancamento->getAtivo());
                    $DebitoDAO->salvarItens($DebitoItensAtualizar);
                }
            }
        }

        $AgendaLancamento->setCodigoDebito($Debito->getCodigo());

        if ($rowDebito) {
            $this->update2(
                    'lancamentos_futuros',
                    [
                        'debitado' => $AgendaLancamento->getDebitado() == 'S' ? "'N'" : "'S'",
                        'codigo_debito' => $AgendaLancamento->getCodigoDebito(),
                    ],
                    "id = " . $AgendaLancamento->getId()
            );

            $Caixa = new Caixa();
            $CaixaDAO = new CaixaDAO();
            $Caixa->setCodigoSaidaCabecalho($Debito->getCodigo());
            $Caixa->setData($dataHoje);
            $Caixa->setDescricao($Debito->getObs());
            $Caixa->setObs($Debito->getObs());
            $Caixa->setSaldo($AgendaLancamento->getDebitado() == 'S' ? $Debito->getValorTotal() : $Debito->getValorTotal() * -1);
            $CaixaDAO->salvar($Caixa);
            if (Sessao::retornaRecebeEmail() == "S") {
                $CaixaDAO->mandaEmail($Caixa);
            }
            return true;
        } else {
            return false;
        }
    }

}
