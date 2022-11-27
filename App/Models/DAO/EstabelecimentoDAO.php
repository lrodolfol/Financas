<?php

namespace App\Models\DAO;

use App\Models\Entidades\Estabelecimentos;

class EstabelecimentoDAO extends BaseDAO {

    public function salvar(Estabelecimentos $Estabelecimento) {
        $codigo = $Estabelecimento->getCodigo();
        $nome = $Estabelecimento->getNome();
        $cnpj = $Estabelecimento->getCnpj();
        $cidade = $Estabelecimento->getCidade();
        $ativo = $Estabelecimento->getAtivo();
        $tipo = $Estabelecimento->getTipoComercio();

        return $this->insert(
                        'estabelecimentos',
                        "codigo,:nome,:cnpj,:cidade,:ativo,:tipo_comercio",
                        [
                            ':codigo' => $codigo,
                            ':nome' => "'" . $nome . "'",
                            ':cnpj' => "'" . $cnpj . "'",
                            ':cidade' => "'" . $cidade . "'",
                            ':ativo' => "'" . $ativo . "'",
                            ':tipo_comercio' => "'" . $tipo . "'",
                        ]
        );
    }

    public function retornaNovoCodigoEstabelecimento() {
        $sql = "SELECT * FROM estabelecimentos ORDER BY codigo DESC LIMIT 1 ";
        $row = $this->RetornaDado($sql);
        if ($row) {
            return $row["codigo"] + 1;
        } else {
            return 1;
        }
    }

    public function carregaEstabelecimento($SomenteAtivo, $codigo = null) {
        $sql = "SELECT * FROM estabelecimentos WHERE id is not null ";
        if ($SomenteAtivo == "S") {
            $sql .= " AND ativo = 'S' ";
        }
        if ($codigo) {
            $sql .= " AND codigo = $codigo ";
        }
        $sql .= " ORDER BY nome ";
        $resultado = $this->select($sql);
        return $resultado->fetchAll(\PDO::FETCH_CLASS, Estabelecimentos::class);
    }

    public function sqlExporta() {
        $sql = "SELECT * FROM estabelecimentos WHERE id is not null ";
        $resultado = $this->select($sql);
        return $resultado->fetchAll();
    }

    public function excluir(Estabelecimentos $Estabelecimento) {
        try {
            $codigo = $Estabelecimento->getCodigo();

            return $this->delete('estabelecimentos', "codigo = $codigo");
        } catch (Exception $e) {
            throw new \Exception("Erro ao deletar", 500);
        }
    }

    public function edicao(Estabelecimentos $Estabelecimento) {
        try {
            $codigo = $Estabelecimento->getCodigo();
            $nome = "'" . $Estabelecimento->getNome() . "'";
            $cnpj = "'" . $Estabelecimento->getCnpj() . "'";
            $tipoComercio = "'" . $Estabelecimento->getTipoComercio() . "'";
            $cidade = "'" . $Estabelecimento->getCidade() . "'";
            $ativo = "'" . $Estabelecimento->getAtivo() . "'";

            return $this->update2(
                            'estabelecimentos',
                            [
                                'nome' => $nome,
                                'cnpj' => $cnpj,
                                'tipo_comercio' => $tipoComercio,
                                'cidade' => $cidade,
                                'ativo' => $ativo,
                            ],
                            "codigo = $codigo"
            );
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados.", 500);
        }
        return true;
    }

}
