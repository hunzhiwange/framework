<?php
namespace Queryyetsimple\Protocol\Thrift;

use Thrift\Transport\TFramedTransport;
use Thrift\Transport\Transport;
use Thrift\Factory\TTransportFactory;

class TFramedTransportFactory extends TTransportFactory{
  /**
   * @static
   * @param TTransport $transport
   * @return TTransport
   */
  public static function getTransport(\Thrift\Transport\TTransport $transport) {
    return new TFramedTransport($transport);
  }
}
