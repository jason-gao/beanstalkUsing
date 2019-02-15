<?php
/**
 * Desc: beanstalk client
 * Created by PhpStorm.
 * User: jasong
 * Date: 2017/12/15 11:33
 */

namespace beanstalkUsing;

use  Beanstalk\Client;

class BeanstalkClient
{
    private static $beanstalk;

    public static function getInstance($options)
    {
//        $options['logger'] = Log::getInstance();
        if (!isset(self::$beanstalk) || !is_object(self::$beanstalk)) {
            self::$beanstalk = new Client($options);
        }

        return self::$beanstalk;
    }


}