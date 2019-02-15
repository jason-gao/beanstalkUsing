<?php
/**
 * Desc: 监控消息队列
 * Created by PhpStorm.
 * User: jasong
 * Date: 2017/12/15 11:32
 */

namespace beanstalkUsing;

class BeanstalkMonitorImpl extends BeanstalkMonitor
{

    public function __construct($options, $monitorConf)
    {
        parent::__construct($options, $monitorConf);
    }

    public function monitor()
    {
        $this->monitorTubeStatus();
    }


    public function sendNotice($params)
    {
        log::getInstance()->Info("impl sendNotice");
        log::getInstance()->Info("tube:" . $params['tube']);
        log::getInstance()->Info("current-jobs-ready0:" . $params['current-jobs-ready0']);
        log::getInstance()->Info("current-jobs-ready1:" . $params['current-jobs-ready1']);
    }

}