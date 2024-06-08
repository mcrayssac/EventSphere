<?php

namespace App\Security;

use App\Entity\Event;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EventVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['view'])
            && $subject instanceof Event;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Event $event */
        $event = $subject;

        switch ($attribute) {
            case 'view':
                if ($event->getIsPublic()) {
                    return true;
                }

                return $user instanceof UserInterface;
        }

        return false;
    }
}
