<?php

namespace Modules\Base\Helper;

class ViewHelper
{
    /**
     * @var array
     */
    private $data = array();

    /**
     * @var
     */
    private $app;

    /**
     * ViewHelper constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @var bool
     */
    private $path = FALSE;

    /**
     * @var string
     */
    private $extension = ".phtml";

    /**
     * @var bool
     */
    private $render = FALSE;

    /**
     * @param $data
     */
    public function assign($data)
    {
        $this->data = array_merge_recursive($this->data, $data);
    }

    /**
     * @param $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        if( substr($extension, 0) != ".")  $this->extension = "." . $this->extension;
    }

    /**
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
        if( substr($path, -1) != "/" ) $this->path = $this->path . "/";
    }

    /**
     * @param $template
     */
    public function render($template)
    {
        if( !$this->path ) {
            $app = new \Silex\Application();
            $app->abort(500, "Path not defined!");
        }

        $file = $this->path . strtolower($template) . $this->extension;

        if (file_exists($file)) {
            $this->render = $file;
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            die('Template ' . $template . ' not found!');
        }

        $this->data['app'] = $this->app;
        extract($this->data);
        include($this->render);
    }
}