<?php
namespace queryyetsimple\swoole\server;

use Swoole\Http\Server as Servers;

class http_server extends server{


    public function __construct() {
      parent::__construct();


        $this->objServer = new Servers("127.0.0.1", 9501);
        $this->objServer->set(array(
            'worker_num' => 8,
            'daemonize' => false,
        ));



        $this->objServer->on("start", function ($server) {
            echo "Swoole http server is started at http://127.0.0.1:9501\n";
        });


        $this->objServer->on('request', function($request, $response) {
            ob_start();
            var_dump($request->get);
            var_dump($request->post);
            var_dump($request->cookie);
            var_dump($request->files);
            var_dump($request->header);
            var_dump($request->server);

            $x = ob_get_contents();

            ob_end_clean();


        //$request2 = Request::getInstance($request);
       // $response2 = Response::getInstance($response);
        try{
            //Event::getInstance()->onRequest($request2,$response2);
           // Dispatcher::getInstance()->dispatch();
           // Event::getInstance()->onResponse($request2,$response2);
        }catch (\Exception $exception){
           // $handler = Di::getInstance()->get(SysConst::HTTP_EXCEPTION_HANDLER);
           // if($handler instanceof HttpExceptionHandlerInterface){
                $handler->handler($exception,$request2,$response2);
           // }else{
              //  Trigger::exception($exception);
            //}
        }
        //$response2->end(true);
            

            $response->cookie("User", "Swoole");
            $response->header("X-Server", "Swoole");
            $response->end($x."<h1>Hello Swoole3222222222!</h1>");
        });

       // $this->objServer->on('Start', array($this, 'onStart'));
        //$this->objServer->on('Connect', array($this, 'onConnect'));
      //  $this->objServer->on('Receive', array($this, 'onReceive'));
        //$this->objServer->on('Close', array($this, 'onClose'));

        $this->objServer->start();
    }

    public function onStart( Servers $serv ) {
        echo "Start\n";
    }

    public function onConnect( Servers $serv, $fd, $from_id ) {
        $serv->send( $fd, "Hello {$fd}!" );
    }

  //  public function onReceive(  $serv, $fd, $from_id, $data ) {
      //  echo 'xxx';
      //  exit();
        //echo "Get Message From Client {$fd}:{$data}\n";
        //$this->objServer->send($fd, $data);
  //  }

    public function onClose( Servers $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }
}