<?php

abstract class BaseController {

    protected $urlvalues;
    protected $action;

    public function __construct($action, $urlvalues) {
        $this->action = $action;
        $this->urlvalues = $urlvalues;
    }

    public function execute() {
        return $this->{$this->action}();
    }

    protected function view($viewData, $file) {
        $viewloc = APPPATH . 'views/' . strtolower(get_class($this)) . '/' . $file . '.php';
        require($viewloc);
    }

}
