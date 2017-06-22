<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\queue\console;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use Exception;
use PHPQueue\Base;
use queryyetsimple\console\option;
use queryyetsimple\console\command;
use queryyetsimple\console\argument;

/**
 * 导入消息队列配置
 */
require dirname ( __DIR__ ) . '/config.php';

/**
 * 添加新的任务
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.11
 * @version 1.0
 */
class job extends command {
    
    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'queue:job';
    
    /**
     * 命令行描述
     *
     * @var string
     */
    protected $strDescription = 'Add a new job on a queue';
    
    /**
     * 响应命令
     *
     * @return void
     */
    public function handle() {
        $this->line ( $this->time ( 'Adding job, please wating...' ) );
        
        $booStatus = false;
        try {
            // 任务名字
            $arrPayload = [ 
                    'job' => $this->argument ( 'job' ) 
            ];
            
            // 附加参数
            $arrPayload ['data'] = $this->option ( 'data' ) ?  : [ ];
            $arrPayload ['attempts'] = 1;
            
            // 注册处理的队列
            $strConnect = 'queryyetsimple\queue\queues\\' . $this->argument ( 'connect' );
            if (! class_exists ( $strConnect )) {
                $this->error ( $this->time ( sprintf ( 'Connect %s not exits.', $strConnect ) ) );
                return;
            }
            call_user_func_array ( [ 
                    $strConnect,
                    'setQueue' 
            ], [ 
                    $this->option ( 'queue' ) 
            ] );
            
            // 添加任务
            $objQueue = Base::getQueue ( $this->argument ( 'connect' ) );
            $booStatus = Base::addJob ( $objQueue, $arrPayload );
        } catch ( Exception $oE ) {
            $this->error ( $this->time ( sprintf ( "Job add error: %s\n", $oE->getMessage () ) ) );
            throw $oE;
        }
        
        if ($booStatus) {
            $this->info ( $this->time ( sprintf ( "%s add succeed.", $this->option ( 'queue' ) . ':' . $this->argument ( 'job' ) ) ) );
        } else {
            $this->error ( $this->time ( sprintf ( "%s add failed.", $this->option ( 'queue' ) . ':' . $this->argument ( 'job' ) ) ) );
        }
    }
    
    /**
     * 命令参数
     *
     * @return array
     */
    protected function getArguments() {
        return [ 
                [ 
                        'job',
                        argument::REQUIRED,
                        'The job name to add.',
                        null 
                ],
                [ 
                        'connect',
                        argument::OPTIONAL,
                        'The name of connect. ',
                        option ( 'quque\default', 'redis' ) 
                ] 
        ];
    }
    
    /**
     * 命令配置
     *
     * @return array
     */
    protected function getOptions() {
        return [ 
                [ 
                        'queue',
                        null,
                        option::VALUE_OPTIONAL,
                        'The queue to listen on.',
                        'default' 
                ],
                [ 
                        'data',
                        null,
                        option::VALUE_OPTIONAL | option::VALUE_IS_ARRAY,
                        'The job json args.' 
                ] 
        ];
    }
}
