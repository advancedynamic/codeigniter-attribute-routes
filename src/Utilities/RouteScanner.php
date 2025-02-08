<?php
namespace Advancedynamic\Codeigniter\Attributeroutes\Utilities;

use DirectoryIterator;
use ReflectionClass;
use Advancedynamic\Codeigniter\Attributeroutes\Attributes\GetRoute;
use Advancedynamic\Codeigniter\Attributeroutes\Attributes\PostRoute;
use Advancedynamic\Codeigniter\Attributeroutes\Attributes\PutRoute;
use Advancedynamic\Codeigniter\Attributeroutes\Attributes\DeleteRoute;
use Advancedynamic\Codeigniter\Attributeroutes\Attributes\PatchRoute;
use Advancedynamic\Codeigniter\Attributeroutes\Attributes\HeadRoute;

class RouteScanner {
    
    public function scan(string $controllerNamespace) {
        $controllerClasses = $this->getControllerClasses($controllerNamespace);
        $routes = [];

        foreach ($controllerClasses as $class) {
            $reflectionClass = new ReflectionClass($class);
            foreach ($reflectionClass->getMethods() as $method) {
                $routes[] = $this->processAttributes($method);
            }
        }

        return $routes;
    }

    private function processAttributes($method) {
        $routeInfo = ['method' => '', 'path' => '', 'action' => '', 'filter' => []];

        foreach ($method->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof GetRoute) {
                $routeInfo['method'] = 'get';
                $routeInfo['path'] = $instance->path;
                $routeInfo['filter'] = $instance->filter ?? [];
            } elseif ($instance instanceof PostRoute) {
                $routeInfo['method'] = 'post';
                $routeInfo['path'] = $instance->path;
                $routeInfo['filter'] = $instance->filter ?? [];
            } elseif ($instance instanceof DeleteRoute) {
                $routeInfo['method'] = 'delete';
                $routeInfo['path'] = $instance->path;
                $routeInfo['filter'] = $instance->filter ?? [];
            } elseif ($instance instanceof PutRoute) {
                $routeInfo['method'] = 'put';
                $routeInfo['path'] = $instance->path;
                $routeInfo['filter'] = $instance->filter ?? [];
            } elseif ($instance instanceof PatchRoute) {
                $routeInfo['method'] = 'patch';
                $routeInfo['path'] = $instance->path;
                $routeInfo['filter'] = $instance->filter ?? [];
            } elseif ($instance instanceof HeadRoute) {
                $routeInfo['method'] = 'head';
                $routeInfo['path'] = $instance->path;
                $routeInfo['filter'] = $instance->filter ?? [];
            }
        }

        $routeInfo['action'] = $method->getDeclaringClass()->getName() . '::' . $method->getName();
        return $routeInfo;
    }
    private function getControllerClasses($namespace) {
        $controllerClasses = [];
        $namespaceDir = $this->convertNamespaceToPath($namespace);

        foreach (new DirectoryIterator($namespaceDir) as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
                $className = $namespace . '\\' . $fileInfo->getBasename('.php');

                if (class_exists($className)) {
                    $reflectionClass = new ReflectionClass($className);
                    if ($reflectionClass->isSubclassOf(\CodeIgniter\Controller::class) && !$reflectionClass->isAbstract()) {
                        $controllerClasses[] = $className;
                    }
                }
            }
        }

        return $controllerClasses;
    }

    private function convertNamespaceToPath($namespace) {
        $namespace = trim($namespace, '\\');

        // Assuming the base namespace is 'App' and it maps to APPPATH
        return ROOTPATH . str_replace('\\', DIRECTORY_SEPARATOR, lcfirst($namespace));
    }
}