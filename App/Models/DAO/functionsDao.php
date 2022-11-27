<?php

namespace App\Models\DAO;

use App\Models\Entidades\Caixa;
use App\Lib\Sessao;

class functions extends BaseDAOFinancas{

    public function consultaFeriado(string $data) : ? string{
        $dia = $ObjData->format('d');
        $mes = $ObjData->format('m');
        
        $sql = "SELECT descricao FROM feriado WHERE dia_mes = '" . $dia . "/" . $mes  . "'";
        $row = $this->FinancasRetornaDado($sql);
        if(! $row) {
            return null;
        }else{
            return $row[0];
        }
    }

}
