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

### Reponses
A controller action must always return a `Henri\Framework\Http\Response` instance. This class itself is abstract and can not be used directly. By default de `Henri\Framework\Http\JSONResponse` is available. This will output the given payload as JSON. You can easily add different Responses as long as they extend from `Henri\Framework\Http\Response`.

TODO: ADD AN EXAMPLE HERE

### Exceptions 
For some 'simple' responses it's not necessary to give a response. You can simple throw an Exception and the system will catch it, and deal with it accordingly. Those are available right now:
- `Henri\Framework\Router\Exceptions\BadRequestException` => Will return a 400 status code
- `Henri\Framework\Router\Exceptions\InternalErrorException` => Will return a 500 status code
- `Henri\Framework\Router\Exceptions\NotAuthorizedException` => Will return a 401 status code
- `Henri\Framework\Router\Exceptions\NotFoundException` => Will return a 404 status code

### Hooking in to the router (Route Events)
#### Register your own variable types
#### Adding/modifying routes