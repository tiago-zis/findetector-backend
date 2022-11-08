<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\UsageTrackingTokenStorage;

class JWTCreatedListener
{

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var User
     */
    private $user;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, UsageTrackingTokenStorage $token)
    {
        $this->requestStack = $requestStack;
        $this->user = $token->getToken()->getUser();
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $payload = $event->getData();

        $payload['username'] = $this->user ? $this->user->getName() : null;
        
        $event->setData($payload);

        //$event->setHeader($header);
    }
}
