<?php
/**
 * Desc: BeanstalkProducer
 * Created by PhpStorm.
 * User: jasong
 * Date: 2017/12/15 11:32
 */

namespace beanstalkUsing;

use Library\CalcTime;

class BeanstalkProducer
{

    public function producer()
    {
        $beanstalk = BeanstalkClient::getInstance(['host' => 'test.yundun.com']);
        $beanstalk->connect();

        for ($i = 1; $i < 10; $i++) {
            $beanstalk->useTube('test-pro' . $i);
            CalcTime::start();
            for ($j = 0; $j < 10000; $j++) {
                $beanstalk->put(
                    23, // Give the job a priority of 23.
                    0,  // Do not wait to put job into the ready queue.
                    30, // Give the job 1 minute to run.
                    $j // The job's body.
                );
            }
            CalcTime::end();
            CalcTime::echoUseTime();
        }

        $beanstalk->disconnect();
    }
}