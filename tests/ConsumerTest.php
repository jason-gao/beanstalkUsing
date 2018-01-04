<?php

namespace beanstalkUsing;

class ConsumerTest extends \PHPUnit_Framework_TestCase {

	public function testReserve() {
        $beanstalkConsumer = new BeanstalkConsumer();
        $beanstalkConsumer->consumer();
	}
}

?>