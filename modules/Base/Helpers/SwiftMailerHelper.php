<?php
/*
 * Essa classe depende da biblioteca Swiftmailer
 * Composer: "swiftmailer/swiftmailer": "~5.4.1"
 * Git: https://github.com/swiftmailer/swiftmailer
 */
namespace Modules\Base\Helper;

use Silex\Provider\SwiftmailerServiceProvider;

class SwiftMailerHelper
{
    private $app;

    private $host;

    private $port;

    private $username;

    private $password;

    private $from;

    private $encryption;

    private $auth_mode;

    private $subject;

    private $to = array();

    private $title;

    private $body;

    private $template = false;

    private $template_replaces = array();

    private $attach = false;

    /**
     * SwiftMailerHelper constructor.
     * @param $app
     */
    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function autoConfig()
    {
        if( !defined('SMTP_HOST') ||
            !defined('SMTP_PORT') ||
            !defined('SMTP_USERNAME') ||
            !defined('SMTP_PASSWORD') ||
            !defined('SMTP_FROM') ||
            !defined('SMTP_ENCRYPTION') ||
            !defined('SMTP_AUTH_MODE') ) {
                return false;
        }
        $this->host = SMTP_HOST;
        $this->port = SMTP_PORT;
        $this->username = SMTP_USERNAME;
        $this->password = SMTP_PASSWORD;
        $this->from = SMTP_FROM;
        $this->encryption = SMTP_ENCRYPTION;
        $this->auth_mode = SMTP_AUTH_MODE;
        return true;
    }

    /**
     * @param \Silex\Application $app
     */
    public function setApp($app)
    {
        $this->app = $app;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param mixed $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @param mixed $encryption
     */
    public function setEncryption($encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * @param mixed $auth_mode
     */
    public function setAuthMode($auth_mode)
    {
        $this->auth_mode = $auth_mode;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param array $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param array $template_replaces
     */
    public function setTemplateReplaces($template_replaces)
    {
        $this->template_replaces = $template_replaces;
    }

    /**
     * @param mixed $attach
     */
    public function setAttach($attach)
    {
        if( file_exists($attach) ) {
            $this->attach = \Swift_Attachment::fromPath($attach);
        }
    }



    public function send()
    {
        $this->app->register(new SwiftmailerServiceProvider());
        $this->app['swiftmailer.use_spool'] = false;
        $this->app['swiftmailer.options'] = array(
            'host'       => $this->host,
            'port'       => $this->port,
            'username'   => $this->username,
            'password'   => $this->password,
            'encryption' => $this->encryption,
            'auth_mode'  => $this->auth_mode
        );

        if( $this->template && file_exists( $this->template )) {
            $htmlTemplate = file_get_contents($this->template);
            foreach( $this->template_replaces as $key => $replace ) {
                $htmlTemplate = str_replace($key, $replace, $htmlTemplate);
            }
         }else {
            $htmlTemplate  = "<h3>" . $this->title . "</h3>";
            $htmlTemplate .= "<p>" . $this->body . "</p>";
        }

        $message = \Swift_Message::newInstance()
            ->setSubject( $this->subject )
            ->setFrom(array( $this->from ))
            ->setTo($this->to)
            ->setBody($htmlTemplate, 'text/html');
        if( $this->attach ) $message->attach( $this->attach );
        $this->app['mailer']->send($message);
    }

}