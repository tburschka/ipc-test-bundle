<?php

namespace IPC\TestBundle\Tests;

use Doctrine\Common\Persistence\ManagerRegistry;

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
     * @param int    $id        The identifier
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
     * @param string $className The class name
     * @param int    $id        The identifier
     *
     * @return self
     */
    protected function enqueueRemove($className, $id)
    {
        $remove = [$className, $id];
        array_unshift($this->entities, $remove);
        return $this;
    }


    /**
     * Remove an entity direct by class name and identifier
     *
     * @param string $className The class name
     * @param int    $id        The identifier
     *
     * @return self
     *
     * @throws \Exception
     */
    public function remove($className, $id)
    {
        $entity = $this->getFresh($className, $id);
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
     * @return self
     *
     * @throws \RuntimeException Throws exception, if a deadlock was detected
     */
    public function removeAll()
    {
        $this->manager->resetManager();
        $removedEntities = -1;
        while ($removedEntities != 0) {
            $removedEntities = 0;
            foreach ($this->entities as $key => $value) {
                try {
                    $this->remove($value[0], $value[1]);
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
}