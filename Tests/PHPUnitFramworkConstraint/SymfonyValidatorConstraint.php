<?php

namespace IPC\TestBundle\Tests\PHPUnitFrameworkConstraint;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class SymfonyValidatorConstraint extends \PHPUnit_Framework_Constraint
{

    /**
     * @var string
     */
    protected $propertyPath;

    /**
     * @var ConstraintViolationListInterface
     */
    protected $constraintViolationList;

    public function __construct(ConstraintViolationListInterface $constraintViolationList, $propertyPath)
    {
        parent::__construct();
        $this->constraintViolationList = $constraintViolationList;
        $this->propertyPath = $propertyPath;
    }

    /**
     * {@inheritdoc}
     */
    public function matches($other)
    {
        $map = [];
        foreach ($this->constraintViolationList as $constraintViolation) {
            $map[$constraintViolation->getPropertyPath()][] = $constraintViolation->getMessageTemplate();
        }
        return (array_key_exists($this->propertyPath, $map) && in_array($other, $map[$this->propertyPath]));
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return sprintf("for propertyPath '%s' exists", $this->propertyPath);
    }
}