# henri

## Looking for contributors!

## Intro
This is a simple, fast and basic PHP framework meant to write API's or simple programs in a fast and easy way. This is a not meant as a replacement for other frameworks,
under the hood it uses a lot of the magic from [Symfony](https://symfony.com/), [Dibi](https://github.com/dg/dibi), [Unirest](https://github.com/Kong/unirest-php) and [Firebase](https://github.com/firebase/php-jwt).

### Purpose  
This framework is not intended for building websites or big applications. The purpose of this framework is to provide a simple set of basic tools to build simple services, like: 
- Simple webservice
- API proxy to bundle several APIs endpoints into one
- Data caching layer
- API endpoint for React/Vue front-ends
- Logging service
- CDN

## Table of content
1. Routing
	1. Controllers
	1. Route annotation
	1. Responses
	1. Hooking in to the router (route events)
1. Dependency Injection
1. Configuration
	1. Yaml
	1. Reading the configuration
	1. Writing the configuration
1. Database handling
	1. Database layer
	1. Entities
	1. Entity Manager
	1. Entity Manager List
	1. Command line interface
1. Making (curl) requests
	1. Request service
1. Command Line
	1. Intro
	1. Default commands
	1. Make your own commands
1. Annotations
	1. What & why annotations
	1. How use your own annotations
1. Events & subscribers
	1. Default system events
	1. How to subscribe to events
	1. How to create your events
1. Authentication
	1. Authentication levels
	1. API Key
	1. JWT
	1. User logins
	1. Add your level and/or authentication
1. Users
	1. User management
	1. Create a user
	1. Update user
	1. User authentication
1. What's next!
	1. Native logging interface (status: in development)
	Logging implementation using [Monolog](https://github.com/Seldaek/monolog)
	1. Out of the box GraphQL support (status: expected early 2021)
	1. Overriding framework classes by setting preferences to the container (status: no expection yet)
	1. Influence DI behaviour using Annotations (status: no expectation yet)

## 1. Routing
When your application receives a request, it calls a controller action to generate the response. The routing configuration defines which action to run for each incoming URL. It also provides other useful features, like generating SEO-friendly URLs (e.g. /read/intro-to-henri instead of index.php?article_id=57).
### Controllers
Controllers are classes in which availables routes are defined and in which actions based on the route will be executed. Controllers must be created in the app directory in your app. For example 'app/Foo/Controller/' and should extent 'Henri\Framework\Controller\Controller'. This way the Router will know this is a controller class.
```php
namespace Foo\Controller;


use Henri\Framework\Controller\Controller;

use Henri\Framework\Http\Response\JSONResponse;
use Henri\Framework\Router\HTTPRequest;
use Henri\Framework\Annotations\Annotation\Route;

class Foo extends Controller {


    /**
     * Foo constructor.
     *
     * @param HTTPRequest $HTTPRequest
     *
     * @Route(type="GET", route="/foo/", authRequired=false)
     */
    public function __construct(HTTPRequest $HTTPRequest) {

        parent::__construct($HTTPRequest);
    }

    /**
     * @param array $params
     *
     * @Route(type="GET", route="/bar/", authRequired=false)
     *
     * @return JSONResponse
     */
    public function Bar( array $params): JSONResponse {
        return new JSONResponse(array('foo bar'));
    }
}
```

Don't worry to much about what's going on above, but here is a basic example of a route. In this example we expose the route 'foo/bar' for all GET requests. How come? On the constructor of the controller you can provide a prefix for all controllers routes. This is highly recommended to force you to group specific routes and use different controllers for different kinds of stuff.

On the method Bar we expose the route '/bar'/ which will be prefixed with the constructor route. So this results in '/foo/bar'. The moment this route is matched this method will be called. This is the first point entry for the actual app logic. All the data from the request can be found in the HTTPRequest which is passed in the contructor. All the request data is accesible through `$this->HTTPRequest`.

But what if you want a variable like an id? More on the specifics of building the Route Annotation in the chapter 'Route annotation'. 

NOTE: Multiple types can be exposed using a |, so GET|POST|PATCH would match all types. This is not recommended on methods, but can be useful on the constructor. Of the contructor will only allow GET, a method with POST will never resolve as the route prefix will only allow GET.

### Route annotation

### Hooking in to the router (Route Events)

## 2. Dependency Injection
DI is in the core of the system. It requires barely any configuration. Under the hood [Symfony Dependency Injection](https://symfony.com/doc/current/components/dependency_injection.html) is used to provide this. See the Symfony documentation for more detailed information on the specifics of DI.
```yaml
imports:
  - { resource: vendor/henrivantsant/henri/services.yaml }

parameters:
  # ...

services:
  _defaults:
    autowire: true
    autoconfigure: true
    public: true

  Foo\:
    resource: 'app/Foo/*'
```
This will tell the DI component to enable DI for all files in the app/Foo directory. In the top of the file the framework configuration for DI is included. Make sure to put this services.yaml file in the root of your project. Feel free to split your services.yaml in different files if it grows too big. This can easily be done by using a import statement to the additional services.yaml file, just like framework services.yaml is imported.

### How to inject
Injection happens in the contructor. Add the classes you wish to inject as arguments to the contructor and they will be automatically provided as an instance. How convenient! 
```php
namespace Foo\Controller;


use Henri\Framework\Configuration\Configuration;
use Henri\Framework\Controller\Controller;
use Henri\Framework\Http\Response\JSONResponse;
use Henri\Framework\Model\Entity\EntityManager;
use Henri\Framework\Router\HTTPRequest;
use Henri\Framework\Annotations\Annotation\Route;

class Foo extends Controller {

    /**
     * @var Configuration $configuration
     */
    private $configuration;

    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * Foo constructor.
     *
     * @param HTTPRequest $HTTPRequest
     * @param Configuration $configuration
     * @param EntityManager $entityManager
     * @param string $notAutowired
     *
     * @Route(type="GET", route="/foo/", authRequired=false)
     */
    public function __construct(
        HTTPRequest $HTTPRequest,
        Configuration $configuration,
        EntityManager $entityManager,
	string $notAutowired = null
    ) {
        $this->configuration = $configuration;
        $this->entityManager = $entityManager;

        parent::__construct( $HTTPRequest );
    }
}
``` 
NOTE: Because the container will automatically try to autowire all constructor arguments, this could lead to problems if you do not want them autowired. The easiest way around this is by giving the argument a default value like the `$notAutowired` example.

## 3. Configuration
### Yaml
### Reading the configuration
### Writing the configuration

## 4. Database handling
### Database layer
### Entities
### Entity Manager
### Entity Manager List
### Command line interface

## 5. Making (curl) requests

## 6. Command line interface
### Default commands
### Make your command

## 7. Annotations
### What & why annotations
### How use your own annotations

## 8. Events & Subscribers
### Default system events
### How to subscribe to events
### How to create your events

## Authentication
### Authentication levels
### Authentication clients
### API Key
### JWT
### User logins
### Add your level and/or authentication

## Users
### User management
### Create a user
### Update user
### User authentication
