<?php

namespace App\Security;

use App\Entity\Event;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EventVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
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
            case self::VIEW:
                return $this->canView($event, $user);
            case self::EDIT:
            case self::DELETE:
                return $this->canEditOrDelete($event, $user);
        }

        return false;
    }

    private function canView(Event $event, UserInterface $user): bool
    {
        if ($event->getIsPublic()) {
            return true;
        }

        return $user instanceof UserInterface;
    }

    private function canEditOrDelete(Event $event, UserInterface $user): bool
    {
        return $event->getCreator() === $user;
    }
}
