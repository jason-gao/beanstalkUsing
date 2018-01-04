<?php
/**
 * Desc: BeanstalkProducer
 * Created by PhpStorm.
 * User: jasong
 * Date: 2017/12/15 11:32
 */

namespace beanstalkUsing;

class BeanstalkProducer
{

    public function producer()
    {
        $beanstalk = BeanstalkClient::getInstance(['host' => 'test.yundun.com']);
        $beanstalk->connect();
        $beanstalk->useTube('test-pro');
        for ($i = 0; $i < 100; $i++) {
            $beanstalk->put(
                23, // Give the job a priority of 23.
                0,  // Do not wait to put job into the ready queue.
                2, // Give the job 1 minute to run.
                $i // The job's body.
            );
        }

        $beanstalk->useTube('test-pro1');
        for ($i = 0; $i < 100; $i++) {
            $beanstalk->put(
                23, // Give the job a priority of 23.
                0,  // Do not wait to put job into the ready queue.
                60, // Give the job 1 minute to run.
                $i // The job's body.
            );
        }

        $beanstalk->disconnect();
    }
}