<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Security;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Class ProcessVoter.
 */
class ProcessVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $security;

    /**
     * ProcessVoter constructor.
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Process) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // ADMIN can do anything! The power!
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // you know $subject is a Process object, thanks to supports
        /** @var Process $process */
        $process = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($process, $user);
            case self::EDIT:
                return $this->canEdit($process, $user);
            case self::DELETE:
                return $this->canDelete($process, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Process $process
     * @param User    $user
     *
     * @return bool
     */
    private function canView(Process $process, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($process, $user)) {
            return true;
        }

        if ($this->security->isGranted('ROLE_PROCESS_VIEW')) {
            return true;
        }

        return false;
    }

    /**
     * @param Process $post
     * @param User    $user
     *
     * @return bool
     */
    private function canEdit(Process $post, User $user)
    {
        return $this->security->isGranted('ROLE_SAGSBEHANDLER');
    }

    /**
     * Checks if the provided User entity has the rights to delete a Process.
     *
     * @param Process $process
     * @param User    $user
     *
     * @return bool
     */
    private function canDelete(Process $process, User $user)
    {
        return $this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN']);
    }
}
