<?php
/**
 * Desc: BeanstalkConsumer
 * Created by PhpStorm.
 * User: jasong
 * Date: 2017/12/15 11:32
 */

namespace beanstalkUsing;

class BeanstalkConsumer
{
    public function consumer()
    {
        $beanstalk = BeanstalkClient::getInstance(['host' => 'test.yundun.com']);
        $beanstalk->connect();
        $beanstalk->watch('test-pro');

        for ($i = 0; $i < 2; $i++) {
            $job     = $beanstalk->reserve(2); // Block until job is available.
            $id      = $job['id'];
            $buryRes = $beanstalk->bury($id, 1);
            var_dump($buryRes);
            $status = $beanstalk->statsJob($id);
            var_dump($status);
        }

        //delete buried
        $beanstalk->useTube('test-pro');
        $job = $beanstalk->peekBuried();
        $beanstalk->delete($job['id']);

        $beanstalk->disconnect();
    }

}