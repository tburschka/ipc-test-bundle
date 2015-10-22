<?php

namespace IPC\TestBundle\Tests;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractSymfonyTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    protected $entityManager = null;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Set up Symfony by boot kernel as well as get container and doctrine entity manager
     */
    public function setUp()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();
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
        /**
         * @var Client $client
         */
        $client = static::$kernel->getContainer()->get('test.client');
        $client->setServerParameters($server);

        foreach ($services as $key => $service) {
            $client->getContainer()->set($key, $service);
        }

        return $client;
    }
}
