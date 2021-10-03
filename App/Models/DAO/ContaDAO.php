<?php

namespace App\Models\DAO;

use App\Models\Entidades\Credito;

class ContaDAO extends BaseDAO {

    public function listaRelatosErros($codigo = null) {            
                
        $sql = "SELECT * FROM relatosErro WHERE id > 0  AND usuario = " . \App\Lib\Sessao::retornaCodigoUsuario() . " ";
        if ($codigo) {
            $sql .= " AND codigo = " . $codigo . " ";
        }
        $resultado = $this->select($sql);
        $erro = $resultado->fetchAll();
        
        return $erro;

    }
    
    public function retornaCompetenciaBloqueada($ano) {
        $sql = "SELECT * FROM encerramento WHERE ano = " . $ano;
        $resultado = $this->select($sql);
        $table = $resultado->fetchall();
        
        return $table;
    }
    
    public function bloquearCompetencia($meses, $ano){
        $sqlArray = array();
        $cont = 0;
        $dataBloqueio = date('Y.m.d');
        foreach ($meses as $key => $value) {
            $sqlArray[$cont] = "INSERT INTO encerramento "
                    . " (encerrado, mes, ano, vr_final, vr_inicial, vr_maior, vr_menor, data_encerramento) VALUES ( "
                    . " '" . $value . "'," . $key . "," . $ano .","
                    . " (SELECT saldo FROM caixa WHERE CONCAT(EXTRACT(MONTH FROM data),'/',"
                    . " EXTRACT(YEAR FROM data)) = '" . $key . "/" . $ano . "' "
                    . " ORDER BY data DESC LIMIT 1), "
                    . " (SELECT saldo FROM caixa WHERE CONCAT(EXTRACT(MONTH FROM data),'/',"
                    . " EXTRACT(YEAR FROM data)) = '" . $key . "/" . $ano . "' "
                    . " ORDER BY data ASC LIMIT 1), "
                    . " (SELECT MAX(saldo) FROM caixa WHERE CONCAT(EXTRACT(MONTH FROM data),'/',"
                    . " EXTRACT(YEAR FROM data)) = '" . $key . "/" . $ano . "' "
                    . " LIMIT 1), "
                    . " (SELECT MIN(saldo) FROM caixa WHERE CONCAT(EXTRACT(MONTH FROM data),'/',"
                    . " EXTRACT(YEAR FROM data)) = '" . $key . "/" . $ano . "' "
                    . " ORDER BY data DESC LIMIT 1), '" . $dataBloqueio . "' ) "
                    . " ON DUPLICATE KEY UPDATE encerrado = '" . $value . "', "
                    . " vr_final = "
                    . " (SELECT saldo FROM caixa WHERE CONCAT(EXTRACT(MONTH FROM data),'/',"
                    . " EXTRACT(YEAR FROM data)) = '" . $key . "/" . $ano . "' "
                    . " ORDER BY data DESC LIMIT 1), "
                    . " vr_inicial = "
                    . " (SELECT saldo FROM caixa WHERE CONCAT(EXTRACT(MONTH FROM data),'/',"
                    . " EXTRACT(YEAR FROM data)) = '" . $key . "/" . $ano . "' "
                    . " ORDER BY data ASC LIMIT 1), "
                    . " vr_maior = "
                    . " (SELECT MAX(saldo) FROM caixa WHERE CONCAT(EXTRACT(MONTH FROM data),'/',"
                    . " EXTRACT(YEAR FROM data)) = '" . $key . "/" . $ano . "' "
                    . " LIMIT 1), "
                    . " vr_menor = "
                    . " (SELECT MIN(saldo) FROM caixa WHERE CONCAT(EXTRACT(MONTH FROM data),'/',"
                    . " EXTRACT(YEAR FROM data)) = '" . $key . "/" . $ano . "' "
                    . " ORDER BY data DESC LIMIT 1) "
                    . " ";
            //$sqlArray[$cont] = "INSERT INTO encerramento (encerrado, mes, ano) VALUES ('" . $value . "'," . $key . "," . $ano .") ON DUPLICATE KEY UPDATE encerrado = '" . $value . "'";
            $cont ++;
        }
        $this->executaSqlArray($sqlArray);
    }

}
