<?php

namespace App\EventListener;

use App\Entity\TermsOfUse;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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

    private $em;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, UsageTrackingTokenStorage $token, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->user = $token->getToken()->getUser();
        $this->em = $em;
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
        $payload['id'] = $this->user ? $this->user->getId() : null;
        $file = $this->user ? $this->user->getImage() : null;
        $terms = $this->checkTermsOfUse();

        if ($file) {
            $payload['image'] = [
                "id" => $file->getId(),
                "name" => $file->getName(),
            ];
        }

        if ($terms) {
            $payload['termsOfUse'] = [
                'id' => $terms->getId(),
                'content' => $terms->getContent(),
                'version' => $terms->getVersion()
            ];
        }

        $event->setData($payload);

        //$event->setHeader($header);
    }

    public function checkTermsOfUse(): ?TermsOfUse
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from(TermsOfUse::class, 'r')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();
        $terms = null;

        if (!empty($result)) {
            $terms = $result[0];
            $list = $this->user->getTermsOfUseList();

            foreach ($list as $item) {
                if ($terms->getId() === $item->getTerms()->getId() && $item->isAccepted() === true) {
                    $terms = null;
                }
            }
        }

        return $terms;
    }
}
