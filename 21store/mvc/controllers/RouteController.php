<?php

class RouteController
{
    private $_url;
    private $_dispath;
    private $_is_footer;

    function __construct($url)
    {
        $this->_url = $url;
        $this->_is_footer = 1;

        self::parsingURL();
    }

    function parsingURL()
    {
        // call home page if path is '/'
        if (strcmp($this->_url, "/") == 0) {
            require_once ROOT . DS . 'mvc' . DS . 'controllers' . DS . 'HomeController.php';
            $this->_dispath = new HomeController();
            require_once ROOT . DS . 'mvc' . DS . 'views' . DS . 'home.php';
            return;
        }

        $urlArray = explode("/", $this->_url);
        $controller = $urlArray[0];
        array_shift($urlArray);
        $id = -1;

        // check if admin -> no footer
        if (
            strcmp($controller, "admin") == 0
            || strcmp($controller, "add-product") == 0
            || strcmp($controller, "product-management") == 0
            || strcmp($controller, "account-management") == 0
            || strcmp($controller, "login-admin") === 0
            || strcmp($controller, "login") === 0
            || strcmp($controller, "register") === 0

        ) {
            $this->_is_footer = 0;
        }

        $controller = str_replace('-', ' ', $controller);
        $controller = ucwords($controller);
        $controller = str_replace(' ', '', $controller);
        $controller .= "Controller";


        require_once ROOT . DS . 'mvc' . DS . 'controllers' . DS . $controller . '.php';
        if ($id == -1) {
            $this->_dispath = new $controller();
        } else {
            $this->_dispath = new $controller($id);
        }
    }

    function show()
    {
        $this->_dispath->__render();
        if ($this->_is_footer == 1) $this->_dispath->__footer();
    }
}
