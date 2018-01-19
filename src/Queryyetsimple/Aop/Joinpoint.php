<?php

namespace queryyetsimple\aop;

// use ClosureAnalysisException;
// use ReflectionFunction;
// use SplFileObject;
// use RuntimeException;
//use ReflectionClass;

// https://github.com/AOP-PHP/AOP
use Closure;

class joinpoint
{

    protected $pointcut;
    protected $advices = [];

    public function __construct($pointcut, Closure $advice) {
        $this->pointcut = $pointcut;
        $this->advices[] = $advice;


        echo $this->pointcut;




        //echo $code = (new closures($advice))->parse();

        //$pointcut2 = new pointcut(app());
        //list($aopclass,$methods) = explode('->',$pointcut);



        //$pointcut2->parse($aopclass,$methods,$code);
        //foreach ($this->aops as $aopclass => $methods) {
            //echo $aopclass;
           //echo $this['psr4']->file($aopclass);;
           //$file = $this['psr4']->file($aopclass);
           //$aop->parse($aopclass,$file,$methods);
        //}
    }

    public function addAdvice(Closure $advice) {
        $this->advices[] = $advice;
    }

    public function getAdvice(){
        return $this->advices;
    }

    public function &getArguments() {}
    public function getPropertyName() {}
    public function getPropertyValue() {}
    public function setArguments(array $arguments) {}
    public function getKindOfAdvice() {}
    public function &getReturnedValue() {}
    public function &getAssignedValue() {}
    public function setReturnedValue($value) {}
    public function setAssignedValue($value) {}
    public function getPointcut() {}
    public function getObject() {}
    public function getClassName() {}
    public function getMethodName() {}
    public function getFunctionName() {}
    public function getException() {}
    public function process() {}

}