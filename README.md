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

NOTE: Multiple types can be exposed using a |, so GET|POST|PATCH would match all three types. This is not recommended on methods, but can be useful on the constructor. Of the contructor will only allow GET, a method with POST will never resolve as the route prefix will only allow GET.

### Route annotation
The router will 'harvest' all methods in the controllers classes with a `@Route` annotation, and map those as routes. If a route annotation is used on the contructor (highly recommended) this will be used as a prefix for all methods in this specific controller as explained in the example above.

The annotations comes with the following settings:
- type = Allowed HTTP methods to call this route (e.g. GET, POST, PATCH, PUT, etc.). There is no filter on this, so you're free to use custom methods as well. Multiple methods can be provided by piping them together like `@Route(type="GET|POST|PUT", route="/bar/", authRequired=false)`. Usually this should only be necessary on the constructor. There is no wildcard to allow all methods as you should normally not direct a GET request for data to the same functionality as a POST request.

This means you could have the same route for different HTTP Methods if you would desire this. You can have an endpoint `/article/[i:id]/`, where the a GET would lead to method which would return the value of the article with the given id, and where PATCH for example to this same endpoint would update the given article. Makes sense to split this into different methods right?
- route = The route for this method with a leading and closing slash
- authRequired = Whether any form of authentication is necessary to access this method. When authentication failed, this method will not be reached and the request will be denied access (more on this the chapter Authentication)
- authLevel = Which level of authentication is needed to access this method (more on this the chapter Authentication)
```php
    /**
     * @param array $params
     *
     * @Route(type="GET", route="/bar/[i:articleid]/", authRequired=false)
     *
     * @return JSONResponse
     */
    public function getBar( array $params): JSONResponse {
        // Let's return the article here
        return new JSONResponse(array('foo bar'));
    }

    /**
     * @param array $params
     *
     * @Route(type="PATCH", route="/bar/[i:articleid]/", authRequired=false)
     *
     * @return JSONResponse
     */
    public function patchBar( array $params): JSONResponse {
        // Let's update the article here
        return new JSONResponse(array('foo bar'));
    }
```

#### Variables in urls
Note: This principle is fork of [AltoRouter](https://github.com/dannyvankooten/AltoRouter).
As you can see in the previous example there some weird syntax going on in the route parameter in the annotation. This a route 'variable' with the name 'articleid'. Each route can have multiple variables which allows for the url to be for (like in this example) '/bar/123'.

Variables always follow the syntax `[variable_type:variable_name]`. Variable types are predefined and the variable is up to yourself, you will need the variable name to extract it's value later (123 in this case).

Variable types:
```php
*                    // Match all request URIs
[i]                  // Match an integer
[i:id]               // Match an integer as 'id'
[a:action]           // Match alphanumeric characters as 'action'
[h:key]              // Match hexadecimal characters as 'key'
[:action]            // Match anything up to the next / or end of the URI as 'action'
[create|edit:action] // Match either 'create' or 'edit' as 'action'
[*]                  // Catch all (lazy, stops at the next trailing slash)
[*:trailing]         // Catch all as 'trailing' (lazy)
[**:trailing]        // Catch all (possessive - will match the rest of the URI)
.[:format]?          // Match an optional parameter 'format' - a / or . before the block is also optional
```
Each of those variable types results in a regex
```php
'i'  => '[0-9]++'
'a'  => '[0-9A-Za-z]++'
'h'  => '[0-9A-Fa-f]++'
'*'  => '.+?'
'**' => '.++'
''   => '[^/\.]++'
```
It is possible to register your custom variable types to router if you wish to match a specific pattern. More on this in the chapter 'Hooking in to the router'.

#### Reading url variables
Okay so we can use several variables, convenient! How to read this? Easy! The variables which be passed into your function as an array. See the example below.
```php
	/**
	* @param array $params
	*
	* @Route(type="GET", route="/bar/[i:articleid]/", authRequired=false)
	*
	* @return JSONResponse
	*/
	public function getBar( array $params): JSONResponse {
		// Let's return the article here

		$articleID = $params['articleid'];

		return new JSONResponse(array('foo bar'));
	}
```
See how the variable name comes in to play now?

### Hooking in to the router (Route Events)
#### Register your own variable types
#### Adding/modifying routes

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
The system comes with a default Request module for making curl requests. This is powered by [Unirest](https://github.com/Kong/unirest-php). It's as easy passing the `Henri\Framework\Http\Request\Request` class an argument in the constructor of whereever you wish to make a request (this would usually be Service).

MORE DOCUMENTATION ON THIS WILL FOLLOW

## 6. Command line interface
The command line presents a clean implementation of the [Symfony Command Line](https://symfony.com/doc/current/components/console.html). See their documentation on how to use the command line general.

### Setup
It's requires a small setup to get to command line running.
1. Create a folder 'bin' in your project root
2. Create a file (without extension) called 'console' with the content from below
```php
#!/usr/bin/env php
<?php
if (PHP_SAPI !== 'cli') {
    echo 'bin/console must be run as a CLI application';
    exit(1);
}

try {
    define('INCLUDE_DIR', dirname(__DIR__));

    require INCLUDE_DIR . '/vendor/autoload.php';

    // set up autoloading
    include_once INCLUDE_DIR . '/vendor/henrivantsant/henri/Application/Bootstrap/Autoloading/Autoload.php';

    // set up DI
    include_once INCLUDE_DIR . '/vendor/henrivantsant/henri/Application/Bootstrap/DependencyInjection/DependencyInjection.php';
} catch (\Exception $e) {
    echo 'Autoload error: ' . $e->getMessage();
    exit(1);
}


try {
    // Build to application
    global $containerBuilder;
    $app = $containerBuilder->get('Henri\Framework\Console\Console');
    $app->run();
} catch (Exception $e) {
    while($e) {
        echo $e->getMessage();
        echo $e->getTraceAsString();
        echo "\n\n";
        $e->getPrevious();
    }
    exit(0);
}
```
This should be all.

### Default commands
The system comes with a batch of useful commands. Get a list of all available commands by running `php bin/console list` from the command line in the root of your project. The specifics of each command will be explained in their respective chapters.

### Make your command
It is very easy to add your own command for running tasks, changing settings, creating cron commands, etc.
```php
namespace Foo\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FooCommand extends Command {

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'foo:bar';

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...

        return 0;
    }

}
```

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
