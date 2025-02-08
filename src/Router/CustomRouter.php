<?php

namespace Advancedynamic\Codeigniter\Attributeroutes\Router;

use Advancedynamic\Codeigniter\Attributeroutes\Utilities\RouteScanner;
use CodeIgniter\Router\Router;
use CodeIgniter\Router\RouteCollectionInterface;
use CodeIgniter\HTTP\RequestInterface;

class CustomRouter extends Router {
    private array $controllerNamespaces;
    private string $modulesPath;

    public function __construct(
        RouteCollectionInterface $routes, 
        ?RequestInterface $request = null, 
        array $controllerNamespace = [],
        ?string $modulesPath = null
    ) {
        parent::__construct($routes, $request);
        $this->controllerNamespaces = $controllerNamespace;
        $this->modulesPath = $modulesPath ?? 'modules'; // default to 'modules' if not provided
        $this->collection->setDefaultNamespace('');
    }
        
    public function initialize() {
        $namespaces = [];
        foreach ($this->controllerNamespaces as $nsPatternOriginal) {
            if (strpos($nsPatternOriginal, '*') !== false) {
                $pos    = strpos($nsPatternOriginal, '*');
                $before = substr($nsPatternOriginal, 0, $pos);
                $after  = substr($nsPatternOriginal, $pos + 1);
    
                $beforePath = ROOTPATH . str_replace('\\', DIRECTORY_SEPARATOR, preg_replace('/^Modules/', $this->modulesPath, $before));
                
                $afterPath = str_replace('\\', DIRECTORY_SEPARATOR, $after);
    
                $filePattern = $beforePath . '*' . $afterPath;
                $moduleDirs  = glob($filePattern, GLOB_ONLYDIR);
                foreach ($moduleDirs as $dir) {
                    $moduleName = basename(dirname($dir));
                    $realNamespace = str_replace('*', $moduleName, $nsPatternOriginal);
                    $namespaces[] = $realNamespace;
                }
            } else {
                $namespaces[] = $nsPatternOriginal;
            }
        }
        
        foreach ($namespaces as $namespace) {
            $scanner = new RouteScanner();
            $routes  = $scanner->scan($namespace);
            foreach ($routes as $route) {
                if (strpos($route['action'], '\\') === false) {
                    // Ensure we use leading backslash for absolute namespace
                    $namespace = '\\' . trim($namespace, '\\');
                    $action = trim($route['action'], '\\');
                    $route['action'] = $namespace . '\\' . $action;
                }
                $this->addRouteWithPattern($route);
            }
        }
    }
    
    protected function addRouteWithPattern($routeInfo) {
        $method = strtolower($routeInfo['method']);
        if (!in_array($method, ['get', 'post', 'put', 'delete', 'patch', 'options', 'head', ''], true)) {
            throw new \Exception("Unsupported HTTP method $method attempted in routing.");
        }
        
        if (!empty($method)) {
            $path = $routeInfo['path'] . ($routeInfo['pattern'] ?? '');
            $filter = $routeInfo['filter'] ?? [];
            
            // Register the route with absolute namespace
            $this->collection->$method($path, $routeInfo['action'], $filter);
        }
    }
}