<?php

namespace One\CheckJeHuis\Security;

use One\CheckJeHuis\Entity\City;
use One\CheckJeHuis\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CityVoter extends Voter
{
    const EDIT = 'CITY_EDIT';
    const DUPLICATE = 'CITY_DUPLICATE';
    const DELETE = 'CITY_DELETE';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::EDIT, self::DUPLICATE, self::DELETE))) {
            return false;
        }

        if (!$subject instanceof City) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var City $city */
        $city = $subject;
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($city, $user);
            case self::DUPLICATE:
                return $this->canDuplicate($city, $user);
            case self::DELETE:
                return $this->canDelete($city, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canDuplicate(City $post, User $user)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    private function canEdit(City $city, User $user)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user->hasRole('ROLE_CITY') && $user->getCity() === $city) {
            return true;
        }

        return false;
    }

    private function canDelete(City $post, User $user)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

}