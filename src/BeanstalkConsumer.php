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
        $tubes = (array)$tubes;
        sort($tubes);
        foreach ($tubes as $k => $name) {
            var_dump("tube:$name");
            $tubeStats = $beanstalk->statsTube($name);
            var_dump($tubeStats);
            var_dump("current-jobs-ready:" . $tubeStats['current-jobs-ready']);
        }

        $beanstalk->disconnect();

        return $tubes;
    }

    public function stats()
    {
        $beanstalk = BeanstalkClient::getInstance(['host' => 'test.yundun.com']);
        $beanstalk->connect();
        var_dump("stats:");
        var_dump($beanstalk->stats());

        $beanstalk->disconnect();
    }


    public function monitorTubeStatus($options = ['host' => 'test.yundun.com'])
    {
        $beanstalk = BeanstalkClient::getInstance($options);
        $beanstalk->connect();

        $status          = []; //监控结果
        $frequency       = 30; //监控频率/s
        $buffer          = 2; //保留2次结果
        $tubeMax         = 100; //tube最大数
        $dividingLine    = "\n\n==================\n\n";
        $noticeFrequency = 600; //通知频率 /s
        $notice          = []; //记录每个tube最后一次通知时间
        $monitor         = ['test-pro1', 'test-pro2']; //指定监控tube

        while (true) {
            $tubes = $beanstalk->listTubes();
            $tubes = (array)$tubes;
            if ($monitor) {
                $tubes = $monitor;
            }
            sort($tubes);
            foreach ($tubes as $k => $name) {
                $tubeStats = $beanstalk->statsTube($name);
                if (!isset($status[$name])) {
                    $status[$name] = [];
                }
                if (count($status[$name]) >= $buffer) {
                    array_shift($status[$name]);
                }
                array_push($status[$name], $tubeStats);
            }

            foreach ($status as $tube => $ts) {
                if (count($ts) == $buffer) {
                    if ($ts[1]['current-jobs-ready'] > $tubeMax) {
                        if ($ts[1]['current-jobs-ready'] >= $ts[0]['current-jobs-ready']) {
                            Log::getInstance()->alert($dividingLine);
                            Log::getInstance()->alert("tube:$tube");
                            Log::getInstance()->alert("0 current-jobs-ready:" . $ts[0]['current-jobs-ready']);
                            Log::getInstance()->alert("1 current-jobs-ready:" . $ts[1]['current-jobs-ready']);
                            //发送通知
                            if (!isset($notice[$tube]) || (time() - $notice[$tube]) > $noticeFrequency) {
                                Log::getInstance()->alert("send notice");
                                $notice[$tube] = time();
                                Log::getInstance()->Info("notice tube time:" . $notice[$tube]);
                            }
                            Log::getInstance()->alert($dividingLine);
                        }
                    }
                }
            }
            sleep($frequency);
        }

        $beanstalk->disconnect();
    }

}