<?php

namespace IPC\TestBundle\Tests;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @var TestsEntityHandler
     */
    protected $testsEntityHandler;

    /**
     * Set up Symfony by boot kernel as well as get container and doctrine manager registry
     */
    public function setUp()
    {
        self::bootKernel();
        $this->container          = static::$kernel->getContainer();
        $this->manager            = $this->container->get('doctrine');
        $this->testsEntityHandler = new TestsEntityHandler($this->manager);
    }

    public function tearDown()
    {
        $this->testsEntityHandler->removeAll();
        parent::tearDown();
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
}
