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