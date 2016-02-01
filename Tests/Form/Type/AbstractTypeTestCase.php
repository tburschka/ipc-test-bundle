<?php

namespace IPC\TestBundle\Tests\Form\Type;

use IPC\TestBundle\Tests\AbstractSymfonyTest;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

abstract class AbstractTypeTestCase extends AbstractSymfonyTest
{

    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @var FormBuilder
     */
    protected $builder;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = $this->container->get('form.factory');
        $this->dispatcher = $this->container->get('event_dispatcher');
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    public static function assertDateTimeEquals(\DateTime $expected, \DateTime $actual)
    {
        self::assertEquals($expected->format('c'), $actual->format('c'));
    }

    protected function getExtensions()
    {
        return array();
    }
}