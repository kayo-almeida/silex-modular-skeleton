<?php

namespace Modules\Controller\Exemplo;

use Modules\Base\Helper\DataBaseHelper;
use Modules\Base\Helper\SwiftMailerHelper;
use Modules\Base\Helper\ViewHelper;
use Modules\Controller\Base\Base;
use Symfony\Component\HttpFoundation\Request;

class Exemplo implements Base
{
    public function __construct(\Silex\Application $app)
    {
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

        $conn = new DataBaseHelper();
        $db   = $conn->autoConnect();

        $view = new ViewHelper( $app );
        $view->setPath(dirname(__DIR__) . "/Views");

        // A query está sendo feita aqui porque é utilizadas em todos os métodos
        $headings = $db::table("exemplos")->get();

        // Essa variavel é adicionada aqui pois ela será usada em todos os métodos
        $view->assign(array(
            "headings" => $headings
        ));

        // Aqui ficarão as rotas/controllers desse módulo
        //=========== Home ===========//
        $app->get('/exemplo', function() use ($app, $view) {
            $view->render("exemplo");
            return PHP_EOL;
        })->bind("exemplo_home");

        //=========== Heading ===========//
        $app->get('/exemplo/heading/{id}', function(Request $request) use ($app, $view, $db) {

            // Pega o Heading atual
            $actual = $db::table("exemplos")->find( $request->get("id") );

            $view->assign(array("actual" => $actual));
            $view->render("exemplo");
            return PHP_EOL;
        })->bind("exemplo_heading");

        //=========== Login (GET) ===========//
        $app->get('/exemplo/login', function(Request $request) use ($app, $view) {
            return $app->redirect( $app['url_generator']->generate('exemplo_home') );
        })->bind("exemplo_login_get");

        //=========== Login (POST) ===========//
        $app->post('/exemplo/login', function(Request $request) use ($app, $view) {
            $view->assign(array(
                "nome" => $request->get("name")
            ));

            $mail = new SwiftMailerHelper($app);
            $mail->autoConfig();
            $mail->setSubject("Teste de Helper [Silex]");
            $mail->setTo(array("kayo@theauberginepanda.com"));
            $mail->setTitle("Testando 1, 2, 3...");
            $mail->setBody("Ola mundo :)");
            $mail->setAttach(__DIR__ . "/Exemplo.php");
            $mail->send();

            $view->render("exemplo");
            return PHP_EOL;
        })->bind("exemplo_login");
    }
}