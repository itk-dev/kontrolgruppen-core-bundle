<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Security;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager implements UserManagerInterface
{
    /** @var \Doctrine\ORM\EntityManagerInterface */
    protected $objectManager;

    /** @var string */
    protected $class;

    /**
     * LogManager constructor.
     */
    public function __construct(ObjectManager $objectManager, string $class)
    {
        $this->objectManager = $objectManager;
        $this->class = $class;
    }

    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class();

        return $user;
    }

    public function deleteUser(UserInterface $user)
    {
        throw new \RuntimeException('Lazy programmer exception: '.__METHOD__.' not implemented!');
    }

    public function findUserBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function findUserByUsername($username)
    {
        return $this->findUserBy(['username' => $username]);
    }

    public function findUsers()
    {
        throw new \RuntimeException('Lazy programmer exception: '.__METHOD__.' not implemented!');
    }

    public function reloadUser(UserInterface $user)
    {
        throw new \RuntimeException('Lazy programmer exception: '.__METHOD__.' not implemented!');
    }

    public function updateUser(UserInterface $user, bool $andFlush = true)
    {
        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        if (false !== strpos($this->class, ':')) {
            $metadata = $this->objectManager->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

        return $this->class;
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository()
    {
        return $this->objectManager->getRepository($this->getClass());
    }
}
