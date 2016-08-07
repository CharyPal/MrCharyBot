<?php

namespace Tests\AppBundle;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class AppTestCase extends \PHPUnit_Framework_TestCase
{
    use MockTrait;
    
    /**
     * Call protected method of the object via reflection
     *
     * @param string|\stdClass $object
     * @param $method
     * @param array $args
     * @return mixed
     */
    protected function callProtected($object, $method, array $args = [])
    {
        if (is_string($object))
            $methodObject = new \ReflectionMethod($object, $method);
        else
            $methodObject = new \ReflectionMethod(get_class($object), $method);
        $methodObject->setAccessible(true);
        if (is_string($object))
            return $methodObject->invokeArgs(null, $args);
        else
            return $methodObject->invokeArgs($object, $args);
    }
}
