<?php

namespace queryyetsimple\aop;

// Aspect（切面）：横向切面关系被成组的放进一个类中。
// Advice（通知）：用于调用切面，定义某种情况下做什么和什么时间做这件事情。通知又分为：前通知、返回后通知、抛出后通知和周边通知。
// Joinpoint（接入点）：创建通知的位置。
// Pointcut（点切割）：定义了一种把通知匹配到某些接入点的方式。

class aop
{

    protected static $singleton;

    protected $joinpoints;

    public function __construct() {

    }

    public static function singleton () {
        if(static::$singleton) {
            return static::$singleton;
        }

        return static::$singleton = new self();
    }

    public function before($pointcut, $advice) {



        if ( !isset($this->joinpoints['before'] [$pointcut] ) ) {
            $this->joinpoints['before'][$pointcut] = new joinpoint($pointcut, $advice);
        } else {
            $this->joinpoints['before'][$pointcut]->addAdvice($advice);
        }

        //print_r( $this->joinpoints['before'][$pointcut]->getAdvice());

        //print_r($this->joinpoints['before'][$pointcut]);


 
    }

    public function after($pointcut, $advice) {

    }

    public function around($pointcut, $advice) {

    }


    protected function parsePointcut() {

    }

}