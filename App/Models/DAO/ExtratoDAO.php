<?php

namespace App\Models\DAO;

use App\Models\Entidades\Extrato;

class ExtratoDAO extends BaseDAO {

    public function Extrato($dataInicial, $dataFinal) {
        $sql = "SELECT id, data, descricao as descricao, saldo as valor, codigo_entrada, codigo_saida_cabecalho,"
                . " case when codigo_entrada is not null then 'ENTRADA' else 'SAIDA' end as tipo"
                . " FROM caixa  WHERE ativo = 'S' AND data BETWEEN '$dataInicial' AND '$dataFinal'  ORDER BY id";
        $resultado = $this->select($sql);
        return $resultado->fetchAll(\PDO::FETCH_CLASS, Extrato::class);
    }

    public function custoProduto($produto = null, $dataInicial = null, $dataFinal = null) {
        $sql = "SELECT s.produto, sum(s.qtd_produto * s.valor_produto) as total_produto, sum(s.qtd_produto) as total_qtd, "
                . " sum(s.qtd_produto * s.valor_produto) / sum(s.qtd_produto) as valor_media, s.unidade_medida , (select sum(valor) from entradas "; 
        
        if(!empty($dataInicial) && !empty($dataFinal)) {
            $sql .= " where data between '" . $dataInicial . "' and '" .$dataFinal . "' ";
        }
        
        $sql .= " ) as total_entradas, "
                . " (select ( (sum(s.qtd_produto * s.valor_produto) * 100) / sum(valor) ) from entradas "; 
        
        if(!empty($dataInicial) && !empty($dataFinal)) {
            $sql .= " where data between '" . $dataInicial . "' and '" .$dataFinal . "' ";
        }
        
        $sql .= " ) as porcentagem_produto "
                . " from saidas_itens s ";
        
        if(!empty($dataInicial) && !empty($dataFinal)) {
            $sql .= "  inner join saida_cabecalho c on s.codigo_cabecalho = c.codigo where c.data_debito between '" . $dataInicial . "' and '" . $dataFinal . "'";
        }
        
        $sql .= " group by s.produto order by sum(s.qtd_produto * s.valor_produto) DESC;";
        $resultado = $this->select($sql);
        return $resultado->fetchAll(\PDO::FETCH_CLASS, Extrato::class);
    }

    public function creditoMensal() {
        $sql = "SELECT SUM(valor) as valor_credito, CONCAT(MONTH(e.data), '/', YEAR(e.data)) as periodo_credito FROM entradas e GROUP BY CONCAT(MONTH(e.data), '/', year(e.data))";
        $resultado = $this->select($sql);
        return $resultado->fetchAll(\PDO::FETCH_CLASS, Extrato::class);
    }

    public function debitoMensal() {
        $sql = "SELECT SUM(valor_total) as valor_debito, CONCAT(MONTH(s.data_debito), '/', YEAR(s.data_debito)) as periodo_debito FROM saida_cabecalho s  GROUP BY CONCAT(MONTH(s.data_debito), '/', year(s.data_debito));";
        $resultado = $this->select($sql);
        return $resultado->fetchAll(\PDO::FETCH_CLASS, Extrato::class);
    }
    
    public function debitosSemItens(){
        $sql = "SELECT s.codigo, s.data_compra, s.valor_total, e.nome as  estabelecimento, f.descricao as forma_pagamento, s.obs FROM saida_cabecalho s " 
        . " LEFT JOIN saidas_itens i ON s.codigo = i.codigo_cabecalho  " 
        . " INNER JOIN estabelecimentos e ON s.estabelecimento  = e.codigo INNER JOIN formas_pagamento f ON f.codigo = s.forma_pagamento " 
        . " WHERE codigo_cabecalho is null " 
        . " ORDER BY s.codigo ";
        $result = $this->select($sql);
        $result = $result->fetchall();
        
        return $result;
        $ff = 5;
    }

}
