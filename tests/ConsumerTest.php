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

    /**
     * @test
     */
    public function deleteAllJob()
    {
        $beanstalkConsumer = new BeanstalkConsumer();
        $tubes = [
            'new_mailsend',
            'new_smssend'
        ];
        $beanstalkConsumer->deleteAllJob($tubes);
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
     * tailf /tmp/testLog.log
     */
    public function monitorTube()
    {
        $beanstalkConsumer = new BeanstalkConsumer();
        $beanstalkConsumer->monitorTubeStatus();
    }

    /**
     *
     */
    public function monitorImpl()
    {
        $options           = [
            'host' => 'test.yundun.com'
        ];
        $monitorConf       = [
            'frequency'       => 10,
            'tubeMax'         => 100,
            'noticeFrequency' => 600,
            'monitor'         => ['default', 'test-pro1', 'test-pro2'],
            'log'             => true,
            'log_name'        => 'test',
            'log_file'        => '/tmp/monitor_beanstalk_tube.log'
        ];
        $beanstalkConsumer = new BeanstalkMonitorImpl($options, $monitorConf);
        $beanstalkConsumer->monitor();
    }

}

?>