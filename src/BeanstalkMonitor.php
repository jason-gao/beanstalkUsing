<?php
/**
 * Desc: 监控消息队列
 * Created by PhpStorm.
 * User: jasong
 * Date: 2017/12/15 11:32
 */

namespace beanstalkUsing;

abstract class BeanstalkMonitor
{

    protected $options     = [
        'host' => 'test.yundun.com'
    ];
    protected $monitorConf = [
        'frequency'       => 3,
        'tubeMax'         => 100,
        'noticeFrequency' => 600,
        'monitor'         => ['default'],
        'log'             => true,
        'log_name'        => 'test',
        'log_file'        => '/tmp/monitor_beanstalk_tube.log'
    ];

    public function __construct($options, $monitorConf)
    {
        $this->options     = $options;
        $this->monitorConf = $monitorConf;
    }

    public function monitorTubeStatus()
    {
        $beanstalk = BeanstalkClient::getInstance($this->options);
        $beanstalk->connect();

        $status          = []; //监控结果
        $frequency       = $this->monitorConf['frequency']; //监控频率/s
        $buffer          = 2; //保留2次结果
        $tubeMax         = $this->monitorConf['tubeMax']; //tube最大数
        $dividingLine    = "\n\n==================\n\n";
        $noticeFrequency = $this->monitorConf['noticeFrequency']; //通知频率 /s
        $notice          = []; //记录每个tube最后一次通知时间
        $monitor         = $this->monitorConf['monitor']; //指定监控tube

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
                            $this->log('alert', $dividingLine);
                            $this->log('alert', "tube:$tube");
                            $this->log('alert', "0 current-jobs-ready:" . $ts[0]['current-jobs-ready']);
                            $this->log('alert', "1 current-jobs-ready:" . $ts[1]['current-jobs-ready']);
                            //发送通知
                            if (!isset($notice[$tube]) || (time() - $notice[$tube]) > $noticeFrequency) {
                                Log::getInstance()->alert("send notice");
                                $this->sendNotice([
                                    'tube'                => $tube,
                                    'current-jobs-ready0' => $ts[0]['current-jobs-ready'],
                                    'current-jobs-ready1' => $ts[1]['current-jobs-ready']
                                ]);
                                $notice[$tube] = time();
                            }
                            $this->log('alert', $dividingLine);
                        }
                    }
                }
            }
            sleep($frequency);
        }

        $beanstalk->disconnect();
    }

    protected function log($level, $content)
    {
        if ($this->monitorConf['log']) {
            Log::getInstance($this->monitorConf['log_name'], $this->monitorConf['log_file'])->$level($content);
        }
    }

    abstract protected function sendNotice($params);

}