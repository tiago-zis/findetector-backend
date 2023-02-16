<?php

namespace App\Security;

use App\Entity\User;
use App\Helper\Constants;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const ROLE_USER_EDIT = 'ROLE_USER_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        
        if (!in_array($attribute, [self::ROLE_USER_EDIT])) {
            return false;
        }

        if ($subject && !$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var User $object */
        $object = $subject;
        
        if (in_array(Constants::ROLE_ADMIN, $user->getRoles()) && !$object->getPlainPassword()) {
            return true;
        } else if ($object->getId() === $user->getId()) {
            return true;
        }
        
        return false;
    }

}