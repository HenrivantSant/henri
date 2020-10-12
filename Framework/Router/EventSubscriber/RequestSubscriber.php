<?php


namespace Henri\Framework\Router\EventSubscriber;

use Henri\Framework\Kernel\Event\KernelRequestEvent;
use Henri\Framework\Router\Exceptions\BadRequestException;
use Henri\Framework\Router\Helper\Validator;
use Henri\Framework\Router\HTTPRequest;
use Henri\Framework\Router\Model\Request as RequestModel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RequestSubscriber implements EventSubscriberInterface {

    /**
     * @var Validator $requestValidator
     */
    private $requestValidator;

    /**
     * @var HTTPRequest $HTTPRequest
     */
    private $HTTPRequest;

    /**
     * @var RequestModel $modelRequest
     */
    private $modelRequest;

    /**
     * RequestSubscriber constructor.
     *
     * @param Validator $requestValidator
     * @param HTTPRequest $HTTPRequest
     * @param RequestModel $modelRequest
     */
    public function __construct(
        Validator $requestValidator,
        HTTPRequest $HTTPRequest,
        RequestModel $modelRequest
    ) {
        $this->requestValidator = $requestValidator;
        $this->HTTPRequest      = $HTTPRequest;
        $this->modelRequest     = $modelRequest;
    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * The code must not depend on runtime state as it will only be called at compile time.
     * All logic depending on runtime state must be put into the individual methods handling the events.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array {
        return array(
            KernelRequestEvent::class => 'onKernelRequest',
        );
    }

    /**
     * @param KernelRequestEvent $event
     * @param string $eventClassName
     * @param EventDispatcher $eventDispatcher
     *
     * @return void
     * @throws \Dibi\Exception
     */
    public function onKernelRequest( KernelRequestEvent $event, string $eventClassName, EventDispatcher $eventDispatcher ): void {
        $requestValid = $this->requestValidator->requestIsValid();

        $this->logRequest($requestValid);

        if (!$requestValid) {
            throw new BadRequestException('Invalid request');
        }
    }

    /**
     * Method to log a request
     *
     * @param bool $isValidRequest
     *
     * @throws \Dibi\Exception
     */
    private function logRequest(bool $isValidRequest): void {
        $ip         = $this->HTTPRequest->request->getRequest()['REMOTE_ADDR'];
        $origin     = $this->HTTPRequest->request->getRequest()['HTTP_HOST'] . $this->HTTPRequest->request->getUri();
        $time       = date('Y-m-d H:i:s', strtotime('now'));
        $method     = $this->HTTPRequest->request->getMethod();
        $headers    = $this->HTTPRequest->request->getHeaders();
        $body       = $this->HTTPRequest->request->input->getArray();
        $code       = $isValidRequest ? 200 : 400;

        $this->modelRequest->logRequest($ip, $origin, $time, $method, $headers, $body, $code);
    }

}