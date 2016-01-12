<?php

namespace Modules\Cms\Modules;

class CategoriasValidation
{

    public function run( $params = array() )
    {
        // Validação de título
        if(!isset($params['titulo'])) return array(
            "error" => true,
            "errorInfo" => "Erro na validação",
            "errorDesc" => "O campo título não foi recebido"
        );

        if(empty($params['titulo'])) return array(
            "error" => true,
            "errorInfo" => "Erro na validação",
            "errorDesc" => "O campo título deve ser preenchido"
        );

        if(strlen($params['titulo']) >= 300) return array(
            "error" => true,
            "errorInfo" => "Erro na validação",
            "errorDesc" => "O campo título deve ter o tamanho máximo de 300 caracteres"
        );

        // Roda os filtros
        $params = $this->filters($params);

        // Caso não haja erros, retorna os parametros passados
        // Utilize esse espaço para adicionar filtros, caso haja necessidade
        return $params;
    }

    public function filters($params)
    {
        // Aqui adiciona os filtros
        // Ex:
        // Adicionar no array a data de alteração e/ou usuario que alterou

        $params['data_cadastro'] = date("Y-m-d H:i:s");

        return $params;
    }

}