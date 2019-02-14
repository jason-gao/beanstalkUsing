<?php


namespace beanstalkUsing;

class ConnectTest extends \PHPUnit_Framework_TestCase {

	public $subject;

	//https://phpunit.readthedocs.io/zh_CN/latest/fixtures.html#setup-teardown
	protected function setUp() {
		$host = 'test.yundun.com';
		$port = '11300';

		$this->subject = BeanstalkClient::getInstance(compact('host', 'port'));

		if (!$this->subject->connect()) {
			$message  = "Need a running beanstalkd server at {$host}:{$port}.";
			$this->markTestSkipped($message);
		}
	}

	public function testConnection() {
		$this->subject->disconnect();

		$result = $this->subject->connect();
		$this->assertTrue($result);

		$result = $this->subject->connected;
		$this->assertTrue($result);

		$result = $this->subject->disconnect();
		$this->assertTrue($result);

		$result = $this->subject->connected;
		$this->assertFalse($result);
	}
}

?>