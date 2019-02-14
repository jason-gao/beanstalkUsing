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
    /**
     * @node_name 权限节点名称
     * @link
     * @desc
     * reserve后不删除，可以继续取到下一个job
     * job到reserved状态后，put ttr时间到后，没有删除，就会回到ready状态，可能立马还会被取到
     *
     */
    public function reserveJob()
    {
        $beanstalk = BeanstalkClient::getInstance(['host' => 'test.yundun.com']);
        $beanstalk->connect();
        //watch多个 顺序消费多个tube
        $beanstalk->watch('test-pro2');
        $beanstalk->watch('test-pro3');
        while (true) {
            $job = $beanstalk->reserve(2); // Block until job is available.
            if (!$job) {
                var_dump("ready job empty");
                break;
            }
            var_dump($job);
            $id     = $job['id'];
            $status = $beanstalk->statsJob($id);
            var_dump('status', $status);

            //删除指定id job
            if ($id = 380590) {
                $beanstalk->delete($job['id']);
            }
        }

        $beanstalk->disconnect();
    }


    public function buryJob()
    {
        $beanstalk = BeanstalkClient::getInstance(['host' => 'test.yundun.com']);
        $beanstalk->connect();
        $beanstalk->watch('test-pro2');

        while (true) {
            $job = $beanstalk->reserve(2); // Block until job is available.
            if (!$job) {
                var_dump("ready job empty");
                break;
            }
            $id = $job['id'];
            var_dump($job);
            $buryRes = $beanstalk->bury($id, 1);
            var_dump($buryRes);
            $status = $beanstalk->statsJob($id);
            var_dump($status);

            //delete buried
            $beanstalk->useTube('test-pro2');
            $job = $beanstalk->peekBuried();
            $beanstalk->delete($job['id']);
        }

        $beanstalk->disconnect();
    }

    public function deleteBuriedJob()
    {
        $beanstalk = BeanstalkClient::getInstance(['host' => 'test.yundun.com']);
        $beanstalk->connect();
        $beanstalk->useTube('test-pro1');

        while (true) {
            //delete buried
            $job = $beanstalk->peekBuried();
            if (!$job) {
                var_dump("buried job empty");
                break;
            }
            var_dump($job);
            $beanstalk->delete($job['id']);
        }

        $beanstalk->disconnect();
    }


    public function deleteAllJob()
    {
        $beanstalk = BeanstalkClient::getInstance(['host' => 'test.yundun.com']);
        $beanstalk->connect();

        for ($i = 1; $i < 10; $i++) {
            $beanstalk->watch('test-pro' . $i);
            while (true) {
                $job = $beanstalk->reserve(2); // Block until job is available.
                if (!$job) {
                    var_dump("ready job empty");
                    break;
                }
                var_dump($job);
                $beanstalk->delete($job['id']);
            }
        }

        $beanstalk->disconnect();
    }


    public function listTubeStatus()
    {
        $beanstalk = BeanstalkClient::getInstance(['host' => 'test.yundun.com']);
        $beanstalk->connect();

        $tubes = $beanstalk->listTubes();
        foreach ((array)$tubes as $k => $name) {
            var_dump("tube:$name");
            $tubeStats = $beanstalk->statsTube($name);
            var_dump($tubeStats);
            var_dump("current-jobs-ready:".$tubeStats['current-jobs-ready']);
        }

        $beanstalk->disconnect();

        return $tubes;

    }

    public function stats(){
        $beanstalk = BeanstalkClient::getInstance(['host' => 'test.yundun.com']);
        $beanstalk->connect();
        var_dump("stats:");
        var_dump($beanstalk->stats());

        $beanstalk->disconnect();
    }

}