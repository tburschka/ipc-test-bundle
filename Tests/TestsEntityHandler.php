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


    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string $className
     * @param int    $id
     *
     * @return object|null
     */
    public function refresh($className, $id)
    {
        $objectManager = $this->manager->getManagerForClass($className);
        $entity        = $objectManager->find($className, $id);
        return $entity;
    }

    /**
     *
     * Default LiFo, FiFo if parameter $isLiFo == false
     *
     * @param string $className
     * @param int    $id
     *
     * @return self
     */
    protected function addForRemove($className, $id)
    {
        $remove = [$className, $id];
        array_unshift($this->entities, $remove);
        return $this;
    }


    /**
     * @param string $className
     * @param int $entityId
     *
     * @return self
     *
     * @throws \Exception
     */
    public function remove($className, $entityId)
    {
        $entity = $this->refresh($className, $entityId);
        if ($entity !== null) {
            $objectManager = $this->manager->getManagerForClass(get_class($entity));
            $objectManager->remove($entity);
            $objectManager->flush();
        } // no else
        return $this;
    }

    /**
     * @return self
     *
     * @throws \RuntimeException
     */
    public function removeAll()
    {
        $removedEntities = -1;
        while ($removedEntities != 0) {
            $removedEntities = 0;
            foreach ($this->entities as $key => $value) {
                try {
                    $this->remove($value[0], $value[1]);
                    unset($this->entities[$key]);
                    $removedEntities++;
                } catch (\Exception $e) {
                    // ignore exception $e since a foreign key constraint may exists
                }
            }
        }
        if (!empty($removedEntities)) {
            throw new \RuntimeException('Remaining entities after removeAll().');
        } // no else
        return $this;
    }
}