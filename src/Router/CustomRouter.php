<?php

namespace Advancedynamic\Codeigniter\Attributeroutes\Router;

use Advancedynamic\Codeigniter\Attributeroutes\Utilities\RouteScanner;
use CodeIgniter\Router\Router;
use CodeIgniter\Router\RouteCollectionInterface;
use CodeIgniter\HTTP\RequestInterface;

class CustomRouter extends Router {

    private $controllerNamespaces;

    public function __construct(RouteCollectionInterface $routes, RequestInterface $request = null, array $controllerNamespace = []) {
        parent::__construct($routes, $request);

        $this->controllerNamespaces = $controllerNamespace;
    }

    public function initialize() {
        foreach ($this->controllerNamespaces as $namespace) {
            $scanner = new RouteScanner();
            $routes = $scanner->scan($namespace);

            foreach ($routes as $route) {
                $this->addRouteWithPattern($route);
            }
        }
    }    

    protected function addRouteWithPattern($routeInfo) {
        $actionexplode = array();
        $actionArray = array();
        // Check if 'method' key is set and is a non-empty string
        $method = strtolower($routeInfo['method']);
        if (!in_array($method, ['get', 'post', 'put', 'delete', 'patch', 'options', 'head', ''], true)) {
            throw new \Exception("Unsupported HTTP method  $method attempted in routing.");
        }
        $method = strtolower($routeInfo['method']);
        if(!empty($method)) {
            $action = $routeInfo['action'];
            $path = $routeInfo['path'] . ($routeInfo['pattern'] ?? '');
            $filter = $routeInfo['filter'] ?? [];

            // Add the route
            // Dynamically call the method on the RouteCollection
            preg_match_all('/\([^()]+\)/', $path, $match);

            if((isset($match[0][0]))) {
                $actionArray[] = $action;
                for($i=1; $i<=count($match[0]);$i++) {
                    $actionArray[] = '$'.$i;
                }

                $action = implode('/', $actionArray);
            }

            $actionexplode = explode('\\', $action);
            $action = end($actionexplode);
            array_pop($actionexplode);
            $actionnamespace = implode('\\', $actionexplode);

            if(count($filter)>0)
                array_push($filter, array('namespace' => $actionnamespace));

            $this->collection->$method($path, $action, $filter);
        }
    }
}