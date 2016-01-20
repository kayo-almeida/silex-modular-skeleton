<?php

namespace Modules\Cms\Modules;

use Upload\File;
use Upload\Storage\FileSystem;

/**
 * Class UsuariosValidation
 * @package Modules\Cms\Modules
 */
class UsuariosValidation
{


    /**
     * @var array
     */
    private $postRules;

    /**
     * @var array
     */
    private $fileRules;

    /**
     *
     */
    public function __construct()
    {
        // Rules de inputs normais! Veja mais em: https://github.com/Wixel/GUMP
        $this->postRules = array(
            'titulo' => 'required|valid_name|max_len,100|min_len,2',
            'email' => 'required|valid_email|max_len,250',
            'cargo' => 'max_len,100',
            'descricao' => 'max_len,3000',
            'status' => 'required',
            'permissoes' => 'required'
        );

        $this->fileRules = array(
            'imagem' => array(
                "extension" => array('png', 'jpg'),
                "size"      => array('3M')
            ),
        );
    }

    /*
     * Roda a aplicação. Não precisa alterar, apenas se realmente quiser.
     */
    /**
     * @param array $params
     * @param array $files
     * @return array
     */
    public function run( $params = array(), $files = array() )
    {
        // Siga esse modelo para retornar erros
        $error = array(
            "error" => false,
            "errorInfo" => "",
            "errorDesc" => "",
            "errorFields" => array()
        );

        // Roda validação de campos simples
        foreach( $this->postRules as $field => $rule ) {
            $data = array();
            $data[$field] = $rule;
            $validated = \GUMP::is_valid($params, $data);
            if($validated !== true) {
                $error['errorFields'][] = $field;
            }
        }

        foreach( $this->fileRules as $field => $rule ) {
            if( isset($files[$field]['name']) && !empty($files[$field]['name']) ) {

                $storage = new FileSystem('public/uploads', BASEPATH);
                $file    = new File($field, $storage);

                $file->setName(uniqid());
                $file->addValidations(array(
                    new \Upload\Validation\Extension($rule['extension']),
                    new \Upload\Validation\Size($rule['size'])
                ));

                $name = $file->getNameWithExtension();

                try {
                    $file->upload();
                    $params[$field] = $name;
                } catch (\Exception $e) {
                    $error['errorFields'][] = $field;
                }
            }else {
                if( !isset($params[$field]) || empty($params[$field]) )
                    $error['errorFields'][] = $field;
            }
        }


        if( !empty($error['errorFields']) ) {
            $error['error'] = true;
            $error['errorInfo'] = "Erro ao salvar registro.";
            $error['errorDesc'] = "Preencha todos os campos corretamente";

            return array_merge_recursive($error, $params);
        }else {
            // Roda os tratamentos
            return $this->treatment($params, $files);
        }
    }

    /*
     * Use essa área para tratar campos extras. Como upload de arquivos, datas e outros
     * campos que são preenchidos automaticamente.
     */
    /**
     * @param $params
     * @param $files
     * @return mixed
     */
    private function treatment($params, $files)
    {
        // Validação + Upload - Imagem
        $params['permissoes']          = implode(",", $params['permissoes']);
        $params['usuario_atualizacao'] = 1;
        $params['data_atualizacao']    = date("Y-m-d H:i:s");
        return $params;
    }

}