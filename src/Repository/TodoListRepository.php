<?php

namespace App\Repository;

use App\Entity\TodoList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TodoListRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TodoList::class);
    }

    /**
     * Persist the entity in database.
     *
     * @param TodoList $todoList
     */
    public function save(TodoList $todoList): void
    {
        $this->_em->persist($todoList);
        $this->_em->flush();
    }
}
