<?php

namespace Controllers;

use PDO;
use Pimple\Container;
use Twig\Environment;

class AbstractController {

    protected Container $container;

    public function setContainer(Container $container) : void {
        $this->container = $container;
    }

    protected function permanentlyRedirect(string $location) : void {
        http_response_code(301);
        header('Location: ' . $location);
    }

    protected function db() : PDO {
        return $this->container['db'];
    }

    protected function twig() : Environment {
        return $this->container['twig'];
    }

    protected function render(string $view, array $data = []) : void {
        echo $this->twig()->render($view, $data);
    }
}
