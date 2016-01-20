<?php

namespace Modules\Cms\Modules;

use Modules\Base\Helper\DataBaseHelper;
use Modules\Base\Helper\StringHelper;

class UsuariosModel
{

    private $conn;

    private $table;

    private $query;

    private $db;

    private $foreignKey;

    public function __construct($dbTable)
    {
        // Conexão com o banco
        $this->table   = $dbTable;
        $this->conn    = new DataBaseHelper();
        $db            = $this->conn->autoConnect();
        $this->db      = $db;
    }

    public function fetchAll($params = array(), $wheres = array(), $page = null, $perPage = null)
    {
        $db = $this->db;
        $query   = $db::table($this->table);

        // Search
        foreach( $params as $field => $value ) {
            $exp = StringHelper::extractKeyWords($value);
            foreach( $exp as $val ) {
                $query->orWhere($field, 'LIKE', "%" . $val . "%");
            }
        }

        // Where's
        foreach($wheres as $field => $where) {
            $query->where($field, "=", $where);
        }

        // Páginação
        if( !is_null($page) ) {
            $query->limit($perPage)
                ->offset($page);
        }

        // Order By
        $query->orderBy("data_cadastro", "DESC");

        // Obtem resultado (paginados ou não)
        $fetch = $query->get();

        // Obtem total de resultados
        $count = $db::table($this->table)->count();
        
        // Opções de páginação
        if( !is_null($page) ) {
            if( (count($fetch) == $perPage) && ((($page+1) * $perPage) < $count) ) {
                $pagination['next_page'] = $page+1;
            }else {
                $pagination['next_page'] = false;
            }

            if( (int) $page > 0 ) {
                $pagination['prev_page'] = $page-1;
            }else {
                $pagination['prev_page'] = false;
            }

            $pagination['first_page']    = 0;
            $pagination['last_page']     = ($count / $perPage) - 1;
        }

        return array(
            "data" => $fetch,
            "count" => $count,
            "pagination" => $pagination,
        );
    }

    public function countAll($wheres = array())
    {
        // Conexão com o banco
        $db = $this->db;
        $query   = $db::table($this->table);

        // Where's
        foreach($wheres as $field => $where) {
            $query->where($field, "=", $where);
        }

        // Obtem total de resultados
        $count = $query->count();

        return $count;
    }

    public function fetchOneByID($id = false)
    {
        if(!$id) return false;

        $db = $this->db;
        $query   = $db::table($this->table);

        $fetch = $query->find($id);
        foreach( $this->foreignKey as $field => $table ) {
            if( isset($fetch->{$field}) ) {
                $fetch->{$field} = $db::table($table)->find($fetch->{$field});
            }
        }

        return array(
            "data" => $fetch,
        );

    }

    public function save($params)
    {
        $db = $this->db;
        $query   = $db::table($this->table);

        if( isset($params['id']) ) {
            $id = $params['id'];
            unset($params['id']);
            $update = $query->where("id", $id)->update($params);
            if( $update ) {
                $data = $db::table($this->table)->find($id);
                return array(
                    "data" => $data
                );
            }else {
                return array(
                    "data" => false
                );
            }
        }else {
            $insert = $query->insert($params);
            if( $insert ) {
                $data = $db::table($this->table)->find($insert);
                return array(
                    "insertID" => $insert,
                    "data" => $data
                );
            }else {
                return array(
                    "insertID" => false,
                    "data" => false
                );
            }
        }
    }

    public function update($id, $params)
    {
        $db = $this->db;
        $query   = $db::table($this->table);

        $data = $db::table($this->table)->find($id);
        if( empty($data) ) return false;

        $query->where("id", $id)->update($params);

        return true;
    }

    public function delete( $id )
    {
        $db = $this->db;
        $query   = $db::table($this->table);

        $data = $db::table($this->table)->find($id);

        if( !empty($data) ) {
            $query->where("id", $id)->delete();

            return array(
                "data" => $data
            );
        }else {
            return false;
        }
    }

    public function setForeignKey($foreignKey)
    {
        $this->foreignKey = $foreignKey;
    }

    public function getForeignKey()
    {
        $db = $this->db;

        $ret = array();
        foreach( $this->foreignKey as $field => $table ) {
            $ret[$table] = $db::table($table)->get();
        }
        return $ret;
    }

}