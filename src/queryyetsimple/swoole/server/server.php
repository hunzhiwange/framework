<?php
namespace queryyetsimple\swoole\server;

use RuntimeException;

class server{

  protected $objServer;

  public function __construct(){
    $this->checkEnvironment();
  }


  protected function checkEnvironment(){
    $this->checkPhpVersion();
    $this->checkSwooleInstalled();
    $this->checkSwooleInstalled();
  }

  protected function checkSwooleInstalled(){
    if(!class_exists('swoole_server'))
    {
        throw new RuntimeException('Swoole is not installed.');
    }
  }

  protected function checkPhpVersion(){
      if(version_compare(PHP_VERSION, '7.1.0', '<')){
        throw new RuntimeException("PHP 7.1.0 OR Higher");
    }
  }  

  protected function checkSwooleVersion(){
    if(version_compare(phpversion('swoole'), '2.0', '<')){
        throw new RuntimeException("Swoole 2.0 OR Higher");
    }
  }

}

