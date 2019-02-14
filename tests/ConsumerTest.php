<?php

namespace beanstalkUsing;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{

    public function reserve()
    {
        $beanstalkConsumer = new BeanstalkConsumer();
        $beanstalkConsumer->reserveJob();
    }

    public function bury()
    {
        $beanstalkConsumer = new BeanstalkConsumer();
        $beanstalkConsumer->buryJob();
    }

    public function deleteBuried()
    {
        $beanstalkConsumer = new BeanstalkConsumer();
        $beanstalkConsumer->deleteBuriedJob();
    }

    public function deleteAllJob()
    {
        $beanstalkConsumer = new BeanstalkConsumer();
        $beanstalkConsumer->deleteAllJob();
    }


    public function listTubeStatus()
    {
        $beanstalkConsumer = new BeanstalkConsumer();
        $beanstalkConsumer->listTubeStatus();
    }


    public function stats()
    {
        $beanstalkConsumer = new BeanstalkConsumer();
        $beanstalkConsumer->stats();
    }

    /**
     * @test
     */
    public function monitorTube(){
        $beanstalkConsumer = new BeanstalkConsumer();
        $beanstalkConsumer->monitorTubeStatus();
    }
}

?>