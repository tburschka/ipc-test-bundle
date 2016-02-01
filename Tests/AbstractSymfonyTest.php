<?php

namespace IPC\TestBundle\Tests;

use Doctrine\Common\Persistence\ManagerRegistry;
use IPC\TestBundle\Tests\PHPUnitFrameworkConstraint\SymfonyValidatorConstraint;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractSymfonyTest extends KernelTestCase
{
    /**
     * @var ManagerRegistry
     */
    protected $manager;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var TestsEntityHandler
     */
    protected $testsEntityHandler;

    /**
     * Set up Symfony by boot kernel as well as get container and doctrine manager registry
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->container          = static::$kernel->getContainer();
        $this->manager            = $this->container->get('doctrine');
        $this->validator          = $this->container->get('validator');
        $this->testsEntityHandler = new TestsEntityHandler($this->manager);
    }

    protected function tearDown()
    {
        $this->testsEntityHandler->removeAllEnqueued();
        parent::tearDown();
    }

    /**
     * Remove an entity
     *
     * @param mixed $entity
     * @param bool $now
     */
    protected function removeEntity($entity, $now = false)
    {
        if ($now) {
            $this->testsEntityHandler->remove($entity);
        } else {
            $this->testsEntityHandler->enqueueRemove($entity);
        }
    }

    /**
     * Creates a Client.
     *
     * WARNING:
     * per default this client runs in insulate-mode
     * change this behavior not here, but before you use this client
     *
     * @param array $server  An array of server parameters
     * @param array $services
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $server = [], $services = [])
    {
        $client = static::$kernel->getContainer()->get('test.client');
        $client->setServerParameters($server);
        foreach ($services as $key => $service) {
            $client->getContainer()->set($key, $service);
        }
        return $client;
    }

    /**
     * Assert that an violation exists in a ConstraintViolationList
     *
     * @param string                           $messageTemplate
     * @param string                           $propertyPath
     * @param ConstraintViolationListInterface $violationList
     * @param string                           $message
     */
    protected function assertViolationExists($messageTemplate, $propertyPath, ConstraintViolationListInterface $violationList, $message = '')
    {
        self::assertThat($messageTemplate, new SymfonyValidatorConstraint($violationList, $propertyPath), $message);
    }
}
