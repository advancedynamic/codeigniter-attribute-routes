# Custom Routing Library for CodeIgniter 4

This library enhances the routing system of CodeIgniter 4 by introducing PHP 8 Attributes. It allows defining routes directly in controller methods, thereby simplifying the routing process and reducing redundancy. The library also supports multiple controller namespaces and integrates middleware easily with routes.

## Features

- Define routes using PHP 8 Attributes.
- Support for GET, POST, PUT, DELETE, PATCH, and OPTIONS methods.
- Regular expression patterns in routes.
- Filter integration with routes.
- Multiple controller namespaces.

## Installation

1. **Install via Composer**

   Add the library to your CodeIgniter 4 project using Composer:

   ```bash
   composer require advancedynamic/codeigniter-attributeroutes
    ```

2. **Update the Bootstrap File**
    ```php
    use Advancedynamic\Attributeroutes\Router\CustomRouter;
    use Config\Services;

    $routes = Services::routes();
    $customRouter = new CustomRouter($routes, Services::request(), ['App\\Controllers']);
    $customRouter->initialize();

    Services::injectMock('router', $customRouter);
    ```
    
    Adjust the namespaces in the array as per your project structure.

## Usage

**Defining Routes**

Use attributes to define routes directly in the controller methods:

```php
namespace App\Controllers;

use Advancedynamic\Codeigniter\Attributeroutes\Attributes\GetRoute;
use Advancedynamic\Codeigniter\Attributeroutes\Attributes\PostRoute;

class MyController {
    #[GetRoute('/', ['filter'=> 'MyFilter'])]
    public function index() {
        return "GET method with for index";
    }

    #[GetRoute('/test-get/(\d+)')]
    public function testGetMethod($id) {
        return "GET method with ID: $id";
    }

    #[PostRoute('/test-post')]
    public function testPostMethod() {
        return "POST method";
    }

}
```

## Usage with different namespace



1. **Update the Bootstrap File**

    **Modify your app/Config/Routes.php to use the custom router:**

    ```php
    use Advancedynamic\Attributeroutes\Router\CustomRouter;
    use Config\Services;

    $routes = Services::routes();
    $customRouter = new CustomRouter($routes, Services::request(), ['App\\Controllers', 'Modules\\Dashboard\\Controllers']);
    $customRouter->initialize();

    Services::injectMock('router', $customRouter);
    ```

    **Modify your app/Config/Autoload.php to use the custom router, add this code to the class**
    ```php
    public function __construct() {
        parent::__construct();
        
        foreach(glob(ROOTPATH . 'modules/*', GLOB_ONLYDIR) as $item_dir) {
            $explode = explode(DIRECTORY_SEPARATOR, $item_dir);
            if (file_exists($item_dir)) {
                $this->psr4['Modules\\'.end($explode)] = ROOTPATH . 'modules/'.end($explode);
            }	
        }
    }
    ```
    
    To use the class you can build a class in the following folder

    ```
    └── Codeigniter project/
        └── Modules/
            ├── Controllers/
            │   └── Dashboard.php
            ├── Views
            ├── Config
            └── Models
    ```

    ```php
    <?php

    namespace Modules\Dashboard\Controllers;

    use Advancedynamic\Codeigniter\Attributeroutes\Attributes\GetRoute;

    class Dashboard extends BaseController {
        
        public function __construct() {
        }

        #[GetRoute('/test-dashboard')]
        public function index() {
            return view('Modules\Dashboard\Views\main');
        }
    }
    ```