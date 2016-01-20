<?php

namespace Modules\Cms\Helpers;

use Modules\Base\Helper\UrlHelper;

class EchoHelper
{

    private $alert;

    private $form;

    public function __construct( $alert, $form )
    {
        $this->alert = $alert;
        $this->form  = $form;
    }

    public function setAlert($alert)
    {
        $this->alert = $alert;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }

    public function error( $field )
    {
        echo isset($this->alert['fields']) && in_array($field, $this->alert['fields']) ? "has-error" : "";
    }

    public function value_text( $field )
    {
        echo isset($this->form[$field]) ? $this->form[$field] : '';
    }

    public function value_select( $haystack, $needle )
    {
        echo isset($this->form[$haystack]) && $this->form[$haystack] == $needle ? 'selected' : '';
    }

    public function value_mult_select( $haystack, $needle )
    {
        echo isset($this->form[$haystack]) && (in_array($needle, explode(',', $this->form[$haystack])) || in_array($needle, $this->form[$haystack]))  ? 'selected' : '';
    }

    public function input_image( $field )
    {
        if( isset($this->form[$field]) && !empty($this->form[$field]) ) {
            $html = "";
            $html .= "<div class='form-group'>";
            $html .= "<input type='hidden' name='" . $field . "' value='" . $this->form[$field] . "'>";
            $html .= "<img src='" . UrlHelper::publicURL('uploads/') . $this->form[$field] . "' width='100px'>";
            $html .= "</div>";

            echo $html;
        }
    }

    public function input_hide( $field )
    {
        if( isset($this->form[$field]) && !empty($this->form[$field]) ) {
            echo "<input type='hidden' name='" . $field . "' value='" . $this->form[$field] . "'>";
        }
    }
}