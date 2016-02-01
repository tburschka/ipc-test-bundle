<?php

namespace IPC\TestBundle\Tests\Controller;

use IPC\TestBundle\Tests\AbstractSymfonyTest;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractControllerTest extends AbstractSymfonyTest
{

    /**
     * @var Client
     */
    protected $client = null;

    /**
     * @inheritdoc
     * Set up a default client
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = self::createClient($this->getDefaultServerParameter());
    }

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
    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * Assert for \DOMElements (and their count) based on given selector
     *
     * @param Crawler $crawler   The Crawler
     * @param array   $selectors The selectors to check for \DOMElements
     */
    protected function assertDOMElements($crawler, $selectors)
    {
        foreach ($selectors as $key => $value) {
            $count    = 1;
            $selector = $value;
            if (is_string($key) && is_int($value)) {
                $count    = $value;
                $selector = $key;
            }
            $this->assertCount(
                $count,
                $crawler->filter($selector),
                sprintf('Element "%s" not matching the expected count.', $selector)
            );
        }
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