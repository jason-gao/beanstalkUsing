<?php

namespace beanstalkUsing;

class ProducerTest extends \PHPUnit_Framework_TestCase
{

    public function testPut()
    {
        $beanstalkProducer = new BeanstalkProducer();
        $beanstalkProducer->producer();
    }
}

?>