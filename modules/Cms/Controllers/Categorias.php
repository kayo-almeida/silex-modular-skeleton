<?php

namespace Modules\Cms\Controller;

use Modules\Base\Controller\Base;
use Modules\Base\Helper\SessionHelper;
use Modules\Base\Helper\ViewHelper;
use Modules\Cms\Modules\CategoriasModel;
use Modules\Cms\Modules\CategoriasValidation;
use Symfony\Component\HttpFoundation\Request;

class Categorias implements Base
{

    private $uri = "categorias";

    private $startRequestTime;

    private $model;

    private $validation;

    private $view;

    private $viewPath = "/Views/";

    private $session;

    private $paginationItemsPerPage = ITEMS_PERPAGE;

    private $foreignKey = array();

    private $table = "categorias";

    public function __construct(\Silex\Application $app)
    {
        // Registra Helper indispensvel para geração de URLs
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

        // Pega o horario de inicio da requisição.
        $this->startRequestTime = $this->getStartRequestTime();

        // Instancia Model que será usada em todas rotas
        $this->model = new CategoriasModel($this->table);
        $this->model->setForeignKey($this->foreignKey);

        // Instancia da classe responsavel por validar os dados para inserção/atualização
        $this->validation = new CategoriasValidation();

        // Instancia da classe que utilizaremos para renderizar views
        $this->view = new ViewHelper( $app );
        $this->view->setPath(dirname(__DIR__) . $this->viewPath);

        // Instancia da classe que utilizaremos para controlar sessões em geral
        $this->session = new SessionHelper();

        // Compartilha algumas variaveis com a VIEW
        $this->view->assign(array(
            "alert" => $this->session->get_flash('alert') ? $this->session->get_flash('alert') : false
        ));

        if( $this->session->get_flash("form_data") )
            $this->view->assign(array("form" => array( "data" => (array) $this->session->get_flash("form_data") )));


        // Roda as rotas
        $this->routes($app);
    }

    private function routes(\Silex\Application $app)
    {

        /*
         * Renderização de telas
         */

        // List
        $app->get("/" . $this->uri, function(\Silex\Application $app, Request $request) {
            $fetchAll   = $this->model->fetchAll($request->get('page'), $this->paginationItemsPerPage);
            $foreignKey = $this->model->getForeignKey();

            $this->view->assign(array_merge_recursive(
                $fetchAll,
                array(
                    "foreignKey" => $foreignKey,
                    "requestDelay" => $this->getFinishRequestTime()
                )
            ));

            $this->view->render("layout/header");
            $this->view->render("categorias/list");
            $this->view->render("layout/footer");
            return PHP_EOL;

        })->bind($this->uri . "_list");

        // Update
        $app->get("/" . $this->uri . "/update/{id}", function(\Silex\Application $app, $id) {
            $fetchOne = $this->model->fetchOneByID($id);
            $foreignKey = $this->model->getForeignKey();
            if( empty($fetchOne['data']) )
                return $app->redirect($app['url_generator']->generate($this->uri . "_list"));

            $this->view->assign(array(
                    "foreignKey" => $foreignKey,
                    "requestDelay" => $this->getFinishRequestTime()
                )
            );

            if( !$this->session->get_flash("form_data") )
                $this->view->assign(array("form" => array( "data" => (array) $fetchOne['data'] )));

            $this->view->render("layout/header");
            $this->view->render("categorias/form");
            $this->view->render("layout/footer");
            return PHP_EOL;
        })->bind($this->uri . "_update");

        // Insert
        $app->get("/" . $this->uri . "/insert", function(\Silex\Application $app) {
            $foreignKey = $this->model->getForeignKey();
            $data = false;

            $this->view->assign(array(
                    "foreignKey" => $foreignKey,
                    "requestDelay" => $this->getFinishRequestTime(),
                    "data" => $data
                )
            );

            $this->view->render("layout/header");
            $this->view->render("categorias/form");
            $this->view->render("layout/footer");
            return PHP_EOL;
        })->bind($this->uri . "_insert");

        /*
         * Manipulacão de dados
         */

        // Insert / Update
        $app->post("/" . $this->uri . "/save", function(\Silex\Application $app, Request $request) {
            $post = $request->request->all();
            $this->session->set_flash("form_data", $post);
            $validationReturn = $this->validation->run($post);
            if( isset($validationReturn['error']) &&  $validationReturn['error']) {
                $this->session->set_flash('alert', array(
                    "type" => "error",
                    "message" => isset($validationReturn['errorDesc']) && !empty($validationReturn['errorDesc']) ?
                        $validationReturn['errorDesc'] :
                        ALERT_SAVE_ERROR
                ));
            }else {
                $saveReturn = $this->model->save($validationReturn);
                if( $saveReturn ) {
                    $this->session->set_flash('alert', array(
                        "type" => "success",
                        "message" => ALERT_SAVE_SUCCESS
                    ));
                }else {
                    $this->session->set_flash('alert', array(
                        "type" => "error",
                        "message" => ALERT_SAVE_ERROR
                    ));
                }
            }
            // Caso seja UPDATE
            if( isset($post['id']) )
                return $app->redirect($app['url_generator']->generate($this->uri . "_update", array("id" => $post['id'])));
            // Caso seja INSERT
            else
                return $app->redirect($app['url_generator']->generate($this->uri . "_insert"));
        })->bind($this->uri . "_save");

        // Delete
        $app->get("/" . $this->uri . "/delete/{id}", function(\Silex\Application $app, $id) {
            $deleteReturn = $this->model->delete($id);

            if( $deleteReturn ) {
                $this->session->set_flash('alert', array(
                    "type" => "success",
                    "message" => ALERT_DELETE_SUCCESS
                ));
            }else {
                $this->session->set_flash('alert', array(
                    "type" => "error",
                    "message" => ALERT_DELETE_ERROR
                ));
            }
            return $app->redirect($app['url_generator']->generate($this->uri . "_list"));
        })->bind($this->uri . "_delete");

    }

    private function getStartRequestTime()
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        return $time;
    }

    private function getFinishRequestTime($start)
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $finish = $time;
        return round(($finish - $start), 4);
    }
}