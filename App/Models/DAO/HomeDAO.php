<?php

namespace App\Models\DAO;

use App\Models\Entidades\Produto;
use App\Models\Entidades\RelatoErro;

class HomeDAO extends BaseDAO {

    public function listar($id = null) {
        if ($id) {
            $resultado = $this->select(
                    "SELECT * FROM produto WHERE id = $id"
            );

            return $resultado->fetchObject(Produto::class);
        } else {
            $resultado = $this->select(
                    'SELECT * FROM produto'
            );
            return $resultado->fetchAll(\PDO::FETCH_CLASS, Produto::class);
        }

        return false;
    }

    public function mostraSaldo($id = null) {
        $sql = "select coalesce(sum(valor),0)  - "
                . " ( select coalesce(sum(valor_total),0) from saida_cabecalho where  "
                . " extract(month from data_debito) = extract(month from current_date()) AND extract(year from data_debito) = extract(year from current_date() ) ) as lucro, "
                . " (SELECT saldo FROM caixa  WHERE data <= current_date() AND ativo = 'S' ORDER BY id DESC LIMIT 1) as saldo "
                . " from  entradas where extract(month from data) = extract(month from current_date()) AND extract(year from data) = extract(year from current_date()) ; ";
        //$resultado = $this->RetornaDado("SELECT saldo FROM caixa  WHERE data <= current_date() AND ativo = 'S' ORDER BY id DESC LIMIT 1 ");
        $resultado = $this->RetornaDado($sql);
        //return $resultado->fetchAll(\PDO::FETCH_CLASS, Home::class); 
        return $resultado;
    }

    public function consultaDebitosCreditosFuturos($proximosDiasDebito, $proximosDiasCredito) {
        $sql =  "SELECT sum(valor_total) as total_debitos, "
                . " (SELECT sum(valor) FROM contas_receber c WHERE data_compensacao >= current_date() AND ativo = 'S' "
                . " and creditado = 'N' AND data_compensacao < DATE_ADD(current_date(), INTERVAL " . $proximosDiasCredito . " DAY) ) as total_creditos, "
                . " ( (SELECT saldo FROM caixa  WHERE data <= current_date() AND ativo = 'S' ORDER BY id DESC LIMIT 1) + "
                . " (SELECT coalesce(sum(valor),0) FROM contas_receber WHERE data_compensacao > current_date() AND "
                . " data_compensacao <= date_add(current_date(), INTERVAL 1 month) AND creditado = 'N'  ) ) - "
                . " (SELECT coalesce(sum(total_geral),0) FROM lancamentos_futuros WHERE debitado = 'N' AND data_debito > current_date and data_debito <= "
                . " date_add(current_date(), INTERVAL 1 month) ) as proximo_mes "
                . " FROM lancamentos_futuros WHERE "
                . "data_debito > current_date() and ativo = 'S' AND debitado = 'N' AND data_debito < DATE_ADD(current_date(), INTERVAL " . $proximosDiasDebito . " DAY) ";
        $resultado = $this->RetornaDado($sql);
        return $resultado;
    }

    public function consultaDebitosCreditosVencidos() {
        $sql = "SELECT COUNT(*) as creditoVencido, (SELECT count(*) FROM lancamentos_futuros WHERE data_debito <= current_date() and ativo = 'S' AND debitado = 'N')"
                . " FROM contas_receber c WHERE c.ativo = 'S' AND creditado = 'N' AND data_compensacao <= current_date() ";
        $resultado = $this->RetornaDado($sql);
        return $resultado;
    }

    public function geraGraficoSaldoMax() {
        $sql = "SELECT CONCAT(EXTRACT(month from data),'/',EXTRACT(year from data)) AS periodo,  "
        . "CASE WHEN EXTRACT(month from data) = EXTRACT(month from current_date) then "
        . "(SELECT saldo FROM caixa  WHERE data <= current_date() AND ativo = 'S' "
        . "ORDER BY id DESC LIMIT 1) ";
        /*. " + " //SUBTRAI AJUSTES
	. " COALESCE( (SELECT SUM(valor_total) FROM saida_cabecalho s inner join estabelecimentos e "
        . " on s.estabelecimento = e.codigo  AND lower(e.nome) LIKE '%ajuste mensal%'  AND "
        . " date(CONCAT(EXTRACT(YEAR FROM s.data_debito),'.',EXTRACT(MONTH FROM s.data_debito),'.01')) = "
        . " date(CONCAT(EXTRACT(YEAR FROM CURRENT_DATE()),'.',EXTRACT(MONTH FROM CURRENT_DATE()),'.01') ) ),0) " //FIM SUBTRAI AJUSTE    */           
        $sql = $sql . " else (SELECT x.saldo FROM caixa x WHERE "
        . "CONCAT(EXTRACT(MONTH FROM x.data),'/',EXTRACT(YEAR FROM x.DATA)) = "
        . "CONCAT(EXTRACT(MONTH FROM c.data),'/',EXTRACT(YEAR FROM c.DATA))  "
        . "ORDER BY x.id DESC LIMIT 1)   END AS total FROM caixa c "
        . "WHERE date(CONCAT(EXTRACT(YEAR FROM c.data),'.',EXTRACT(MONTH FROM c.data),'.01')) > "
        . "date(CONCAT(EXTRACT(YEAR FROM CURRENT_DATE)-1,'.',EXTRACT(MONTH FROM CURRENT_DATE),'.01')) "
        . "GROUP BY EXTRACT(month from data), EXTRACT(year from data) ORDER BY c.data";
        $resultado = $this->select($sql);
        $row = $resultado->fetchAll();
        return $row;
        //var_dump($row);
        //die();
    }

    public function geraGraficoSaldoMin() {
        $sql = "SELECT CONCAT(EXTRACT(month from data),'/',EXTRACT(year from data)) AS periodo,  "
                . " CASE WHEN EXTRACT(month from data) = EXTRACT(month from current_date) then "
                . "(SELECT saldo FROM caixa  WHERE data <= current_date() AND ativo = 'S' ORDER BY id DESC LIMIT 1) else min(saldo) END AS total "
                . "FROM caixa GROUP BY EXTRACT(month from data)";
        $resultado = $this->select($sql);
        $row = $resultado->fetchAll();
        return $row;
        //var_dump($row);
        //die();
    }

    public function salvar(Produto $produto) {
        try {

            $nome = $produto->getNome();
            $preco = $produto->getPreco();
            $quantidade = $produto->getQuantidade();
            $descricao = $produto->getDescricao();

            return $this->insert(
                            'produto',
                            ":nome,:preco,:quantidade,:descricao",
                            [
                                ':nome' => $nome,
                                ':preco' => $preco,
                                ':quantidade' => $quantidade,
                                ':descricao' => $descricao
                            ]
            );
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados.", 500);
        }
    }

    public function atualizar(Produto $produto) {
        try {

            $id = $produto->getId();
            $nome = $produto->getNome();
            $preco = $produto->getPreco();
            $quantidade = $produto->getQuantidade();
            $descricao = $produto->getDescricao();

            return $this->update(
                            'produto',
                            "nome = :nome, preco = :preco, quantidade = :quantidade, descricao = :descricao",
                            [
                                ':id' => $id,
                                ':nome' => $nome,
                                ':preco' => $preco,
                                ':quantidade' => $quantidade,
                                ':descricao' => $descricao,
                            ],
                            "id = :id"
            );
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados.", 500);
        }
    }

    public function excluir(Produto $produto) {
        try {
            $id = $produto->getId();

            return $this->delete('produto', "id = $id");
        } catch (Exception $e) {

            throw new \Exception("Erro ao deletar", 500);
        }
    }

    public function relatarErro(RelatoErro $relatoErro) {
        $titulo = $relatoErro->getTitlo();
        $texto = $relatoErro->getTexto();
        $data = $relatoErro->getData();
        $usuario = $relatoErro->getUsuario();
        $codigo = 0;

        $sql = "SELECT coalesce(codigo,0) as codigo FROM relatosErro ORDER BY codigo DESC LIMIT 1";
        $row = $this->RetornaDado($sql);
        if (!$row) {
            $codigo = 1;
        } else {
            $codigo = $row["codigo"] + 1;
        }
        $relatoErro->setCodigo($codigo);

        $sql = "SELECT codigo from usuarios WHERE nome = '" . $usuario . "'"; //BUSCA O CODIGO DO USUÁRIO LOGADO
        $row = $this->RetornaDado($sql);
        if (!$row) {
            RETURN FALSE;
        } else {
            $usuario = $row['codigo'];
        }

        $erro = $this->insert(
                'relatosErro',
                ":codigo, :titulo, :texto, :usuario, :data",
                [
                    ':codigo' => $codigo,
                    ':titulo' => "'" . $titulo . "'",
                    ':texto' => "'" . $texto . "'",
                    ':usuario' => $usuario,
                    ':data' => "'" . $data . "'",
                ]
        );

        return $erro;
    }

    public function zeraConta($tabelas) {
        $sqlZeraConta = array();

        for ($x = 0; $x <= count($tabelas); $x++) {
            if ($tabelas[$x] == "") {
                continue;
            }
            $sqlZeraConta[$x] = "DELETE FROM " . $tabelas[$x] . " WHERE id >= 1 ";
        }
        echo 'ola mundo';
        $rowZeraConta = $this->executaSqlArray($sqlZeraConta);

        return $rowZeraConta;
    }

}
