<?php

namespace IPC\TestBundle\Tests;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

class TestsEntityHandler
{

    /**
     * @var ManagerRegistry
     */
    protected $manager;

    /**
     * @var array
     */
    protected $entities = [];

    /**
     * @var array
     */
    protected $classMetaData = [];


    /**
     * TestsEntityHandler constructor.
     *
     * @param ManagerRegistry $manager
     */
    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get a fresh copy of an entity by class name and identifier
     *
     * @param string $className The class name
     * @param mixed  $id        The identifier(s)
     *
     * @return object|null      Returns entity or null, if entity does not exist
     */
    public function getFresh($className, $id)
    {
        $objectManager = $this->manager->getManagerForClass($className);
        $entity        = $objectManager->find($className, $id);
        return $entity;
    }

    /**
     * Enqueue entity to the list of entities for remove by removeAll() function
     *
     * @param mixed $entity The entity
     *
     * @return $this
     */
    public function enqueueRemove($entity)
    {
        $className = get_class($entity);
        $identifier = $this->getClassMetaData($className)->getIdentifier();
        array_unshift($this->entities, [$className, $identifier]);
        return $this;
    }

    /**
     * Remove an entity direct by entity
     *
     * @param mixed $entity The entity
     *
     * @return $this
     */
    public function remove($entity)
    {
        $className = get_class($entity);
        $identifier = $this->getClassMetaData($className)->getIdentifier();
        return $this->removeByClassAndIdentifier($className, $identifier);
    }

    /**
     * Remove an entity direct by class name and identifier
     *
     * @param string $className  The class name
     * @param mixed  $identifier The identifier(s)
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function removeByClassAndIdentifier($className, $identifier)
    {
        $entity = $this->getFresh($className, $identifier);
        if ($entity !== null) {
            $objectManager = $this->manager->getManagerForClass(get_class($entity));
            $objectManager->remove($entity);
            $objectManager->flush();
        } // no else
        return $this;
    }

    /**
     * Remove all enqueued entities
     *
     * @return $this
     *
     * @throws \RuntimeException Throws exception, if a deadlock was detected
     */
    public function removeAllEnqueued()
    {
        $this->manager->resetManager();
        $removedEntities = -1;
        while ($removedEntities != 0) {
            $removedEntities = 0;
            foreach ($this->entities as $key => $value) {
                try {
                    $this->removeByClassAndIdentifier($value[0], $value[1]);
                    unset($this->entities[$key]);
                    $removedEntities++;
                } catch (\Exception $e) {
                    // ignore exception $e since a foreign key constraint may exists, but have to reset manager
                    $this->manager->resetManager();
                }
            }
        }
        if (!empty($removedEntities)) {
            throw new \RuntimeException('Remaining entities after removeAll().');
        } // no else
        return $this;
    }

    /**
     * Get class metadata from doctrine using an local array cache
     *
     * @param string $className The class name
     *
     * @return ClassMetadata
     */
    protected function getClassMetaData($className)
    {
        if (!isset($this->classMetaData[$className])) {
            $this->classMetaData[$className] = $this->manager->getManagerForClass($className)->getClassMetadata($className);
        }
        return $this->classMetaData[$className];
    }
}