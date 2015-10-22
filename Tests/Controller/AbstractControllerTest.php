<?php

namespace IPC\TestBundle\Tests\Controller;

use IPC\TestBundle\Tests\AbstractSymfonyTest;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractControllerTest extends AbstractSymfonyTest
{

    /**
     * @var Client
     */
    protected $client = null;

    /**
     * Generates a URL from the given parameters.
     *
     * @param string      $route         The name of the route
     * @param mixed       $parameters    An array of parameters
     * @param bool|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * Set up a default client
     */
    public function setUp()
    {
        parent::setUp();
        $this->client = $this->createClient($this->getDefaultServerParameter());
    }

    /**
     * Get default server parameter
     * @return array
     */
    protected function getDefaultServerParameter()
    {
        return [
            'PHP_AUTH_USER' => 'test',
            'PHP_AUTH_PW'   => 'test'
        ];
    }
}