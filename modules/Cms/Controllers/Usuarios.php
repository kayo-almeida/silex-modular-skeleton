<?php

namespace Modules\Cms\Controller;

use Modules\Base\Controller\Base;
use Modules\Base\Helper\SessionHelper;
use Modules\Base\Helper\UrlHelper;
use Modules\Base\Helper\ViewHelper;
use Modules\Cms\Helpers\EchoHelper;
use Modules\Cms\Modules\UsuariosModel;
use Modules\Cms\Modules\UsuariosValidation;
use Symfony\Component\HttpFoundation\Request;

class Usuarios implements Base
{
    private $title = "Usuários";

    private $uri = "usuarios";

    private $startRequestTime;

    private $model;

    private $validation;

    private $view;

    private $viewPath = "/Views/";

    private $session;

    private $echo;

    private $paginationItemsPerPage = 1;

    private $foreignKey = array();

    private $table = "usuarios";

    public function __construct(\Silex\Application $app)
    {
        // Registra Helper indispensvel para geração de URLs
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

        // Pega o horario de inicio da requisição.
        $this->startRequestTime = $this->getStartRequestTime();

        // Instancia Model que será usada em todas rotas
        $this->model = new UsuariosModel($this->table);
        $this->model->setForeignKey($this->foreignKey);

        // Instancia da classe responsavel por validar os dados para inserção/atualização
        $this->validation = new UsuariosValidation();

        // Instancia da classe que utilizaremos para renderizar views
        $this->view = new ViewHelper( $app );
        $this->view->setPath(dirname(__DIR__) . $this->viewPath);

        // Instancia da classe que utilizaremos para controlar sessões em geral
        $this->session = new SessionHelper();

        // Compartilha algumas variaveis com a VIEW
        $this->view->assign(array(
            "alert" => $this->session->get_flash('alert'),
            "echo"  => $this->echo = new EchoHelper( $this->session->get_flash('alert'), $this->session->get_flash("form_data") )
        ));

        $this->view->assign(array(
            "url" => new UrlHelper(),
            "uri" => $this->uri,
            "title" => $this->title
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

        // List ativos
        $app->get("/" . $this->uri, function(\Silex\Application $app, Request $request) {
            // Obtem parametros GET
            $params = $request->query->all();

            $fetchAll   = $this->model->fetchAll($params, ["status" => "ativo"], $request->get('page'), $this->paginationItemsPerPage);
            $foreignKey = $this->model->getForeignKey();

            $this->view->assign(array_merge_recursive(
                $fetchAll,
                array(
                    "foreignKey"    => $foreignKey,
                    "requestDelay"  => $this->getFinishRequestTime(),
                    "status"        => "ativo",
                    "countAtivos"   => $this->model->countAll(["status" => "ativo"]),
                    "countInativos" => $this->model->countAll(["status" => "inativo"]),
                    "countLixeira"  => $this->model->countAll(["status" => "lixeira"]),
                )
            ));

            $this->view->render("layout/header");
            $this->view->render("layout/sidebar");
            $this->view->render("usuarios/list");
            $this->view->render("layout/footer");
            return PHP_EOL;

        })->bind($this->uri . "_list");

        // List inativos
        $app->get("/" . $this->uri . "/inactive", function(\Silex\Application $app, Request $request) {
            // Obtem parametros GET
            $params = $request->query->all();

            $fetchAll   = $this->model->fetchAll($params,["status" => "inativo"], $request->get('page'), $this->paginationItemsPerPage);
            $foreignKey = $this->model->getForeignKey();

            $this->view->assign(array_merge_recursive(
                $fetchAll,
                array(
                    "foreignKey"   => $foreignKey,
                    "requestDelay" => $this->getFinishRequestTime(),
                    "status"       => "inativo",
                    "countAtivos"   => $this->model->countAll(["status" => "ativo"]),
                    "countInativos" => $this->model->countAll(["status" => "inativo"]),
                    "countLixeira"  => $this->model->countAll(["status" => "lixeira"]),
                )
            ));

            $this->view->render("layout/header");
            $this->view->render("layout/sidebar");
            $this->view->render("usuarios/list");
            $this->view->render("layout/footer");
            return PHP_EOL;

        })->bind($this->uri . "_inactive");

        // List lixeira
        $app->get("/" . $this->uri . "/trash", function(\Silex\Application $app, Request $request) {
            // Obtem parametros GET
            $params = $request->query->all();

            $fetchAll   = $this->model->fetchAll($params, ["status" => "lixeira"], $request->get('page'), $this->paginationItemsPerPage);
            $foreignKey = $this->model->getForeignKey();

            $this->view->assign(array_merge_recursive(
                $fetchAll,
                array(
                    "foreignKey"    => $foreignKey,
                    "requestDelay"  => $this->getFinishRequestTime(),
                    "status"        => "lixeira",
                    "countAtivos"   => $this->model->countAll(["status" => "ativo"]),
                    "countInativos" => $this->model->countAll(["status" => "inativo"]),
                    "countLixeira"  => $this->model->countAll(["status" => "lixeira"]),
                )
            ));

            $this->view->render("layout/header");
            $this->view->render("layout/sidebar");
            $this->view->render("usuarios/list");
            $this->view->render("layout/footer");
            return PHP_EOL;

        })->bind($this->uri . "_trash");

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

            if( !$this->session->get_flash("form_data") ) {
                $this->view->assign(array("form" => array("data" => (array) $fetchOne['data'])));
                $this->echo->setForm((array) $fetchOne['data']);
            }

            $this->view->render("layout/header");
            $this->view->render("layout/sidebar");
            $this->view->render("usuarios/form");
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
            $this->view->render("layout/sidebar");
            $this->view->render("usuarios/form");
            $this->view->render("layout/footer");
            return PHP_EOL;
        })->bind($this->uri . "_insert");

        /*
         * Manipulacão de dados
         */

        // Insert / Update
        $app->post("/" . $this->uri . "/save", function(\Silex\Application $app, Request $request) {
            $post = $request->request->all();
            $file = $_FILES;

            $validationReturn = $this->validation->run($post, $file);

            if( isset($validationReturn['error']) &&  $validationReturn['error']) {
                $this->session->set_flash("form_data", $validationReturn);
                $this->session->set_flash('alert', array(
                    "type"    => "error",
                    "info"    => $validationReturn['errorInfo'],
                    "message" => $validationReturn['errorDesc'],
                    "fields"  => $validationReturn['errorFields']
                ));
            }else {
                $saveReturn = $this->model->save($validationReturn);
                if( $saveReturn ) {
                    $this->session->set_flash('alert', array(
                        "type" => "success",
                        "info" => "Item salvo com sucesso",
                        "message" => isset($post['id']) ? "<a href='" . $app['url_generator']->generate($this->uri . "_insert") . "'>Clique aqui para inserir novo registro.</a>" : "<a href='" . $app['url_generator']->generate($this->uri . "_update", array("id" => $saveReturn['insertID'])) . "'>Clique aqui para ver/editar registro.</a>"
                    ));
                }else {
                    $this->session->set_flash("form_data", $validationReturn);
                    $this->session->set_flash('alert', array(
                        "type" => "error",
                        "info" => "Erro ao salvar item",
                        "message" => "Algo deu errado ao salvar seu registro. Entre em contato com a equipe de desenvolvimento."
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

        // Actions
        $app->get("/" . $this->uri . "/action_delete/{id}/{uri}", function(\Silex\Application $app, $id, $uri) {
            $deleteReturn = $this->model->delete($id);

            if( $deleteReturn ) {
                $this->session->set_flash('alert', array(
                    "type" => "success",
                    "info" => "Item deletado com sucesso",
                    "message" => "Esse registro não pode mais ser restaurado"
                ));
            }else {
                $this->session->set_flash('alert', array(
                    "type" => "error",
                    "info" => "Erro ao deletar item",
                    "message" => "Algo deu errado ao apagar o registro. Entre em contato com a equipe de desenvolvimento."
                ));
            }
            return $app->redirect($app['url_generator']->generate($uri));
        })->bind($this->uri . "_action_delete");

        $app->get("/" . $this->uri . "/action_trash/{id}/{uri}", function(\Silex\Application $app, $id, $uri) {
            $update = $this->model->update($id, array("status" => "lixeira"));

            if( $update ) {
                $this->session->set_flash('alert', array(
                    "type" => "success",
                    "info" => "Item movido para lixeira",
                    "message" => "O registro agora está na lixeira.<br><br>Escolha uma ação: <a href='" . $app['url_generator']->generate($this->uri . "_trash") . "'>ver lixeira</a> | <a href='" . $app['url_generator']->generate($this->uri . "_action_restore", array("id" => $id, "uri" => $uri)) . "'>desfazer</a>"
                ));
            }else {
                $this->session->set_flash('alert', array(
                    "type" => "error",
                    "info" => "Erro ao mover item para lixeira",
                    "message" => "Algo deu errado ao mover o registro para lixeira. Entre em contato com a equipe de desenvolvimento."
                ));
            }
            return $app->redirect($app['url_generator']->generate($uri));
        })->bind($this->uri . "_action_trash");

        $app->get("/" . $this->uri . "/action_inactive/{id}/{uri}", function(\Silex\Application $app, $id, $uri) {
            $update = $this->model->update($id, array("status" => "inativo"));

            if( $update ) {
                $this->session->set_flash('alert', array(
                    "type" => "success",
                    "info" => "Item desativado",
                    "message" => "O registro agora está na lista de inativos.<br><br>Escolha uma ação: <a href='" . $app['url_generator']->generate($this->uri . "_inactive") . "'>ver inativos</a> | <a href='" . $app['url_generator']->generate($this->uri . "_action_active", array("id" => $id, "uri" => $uri)) . "'>desfazer</a>"
                ));
            }else {
                $this->session->set_flash('alert', array(
                    "type" => "error",
                    "info" => "Erro ao desativar item",
                    "message" => "Algo deu errado ao desativar o registro. Entre em contato com a equipe de desenvolvimento."
                ));
            }
            return $app->redirect($app['url_generator']->generate($uri));
        })->bind($this->uri . "_action_inactive");

        $app->get("/" . $this->uri . "/action_active/{id}/{uri}", function(\Silex\Application $app, $id, $uri) {
            $update = $this->model->update($id, array("status" => "ativo"));

            if( $update ) {
                $this->session->set_flash('alert', array(
                    "type" => "success",
                    "info" => "Item ativado",
                    "message" => "O registro agora está na lista de ativos.<br><br>Escolha uma ação: <a href='" . $app['url_generator']->generate($this->uri . "_list") . "'>ver ativos</a> | <a href='" . $app['url_generator']->generate($this->uri . "_action_inactive", array("id" => $id, "uri" => $uri)) . "'>desfazer</a>"
                ));
            }else {
                $this->session->set_flash('alert', array(
                    "type" => "error",
                    "info" => "Erro ao desativar item",
                    "message" => "Algo deu errado ao ativar o registro. Entre em contato com a equipe de desenvolvimento."
                ));
            }
            return $app->redirect($app['url_generator']->generate($uri));
        })->bind($this->uri . "_action_active");

        $app->get("/" . $this->uri . "/action_restore/{id}/{uri}", function(\Silex\Application $app, $id, $uri) {
            $update = $this->model->update($id, array("status" => "ativo"));

            if( $update ) {
                $this->session->set_flash('alert', array(
                    "type" => "success",
                    "info" => "Item restaurado",
                    "message" => "O registro agora está na lista de ativados.<br><br>Escolha uma ação: <a href='" . $app['url_generator']->generate($this->uri . "_list") . "'>ver ativos</a> | <a href='" . $app['url_generator']->generate($this->uri . "_action_trash", array("id" => $id, "uri" => $uri)) . "'>desfazer</a>"
                ));
            }else {
                $this->session->set_flash('alert', array(
                    "type" => "error",
                    "info" => "Erro ao restaurar item",
                    "message" => "Algo deu errado ao restaurar o registro. Entre em contato com a equipe de desenvolvimento."
                ));
            }
            return $app->redirect($app['url_generator']->generate($uri));
        })->bind($this->uri . "_action_restore");

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