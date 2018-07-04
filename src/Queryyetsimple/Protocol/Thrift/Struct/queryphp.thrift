namespace php Leevel.Protocol.Thrift.Service

/**
 * ---------------------------------------------------------------
 * 定义一个请求包结构
 * ---------------------------------------------------------------
 *
 * 约定请求数据包，方便只定义一个结构全自动调用 MVC 服务
 */
struct Request
{
  // call 为字符串类型，是指 Service 接口的名称
  // 例如：home://blog/info:get 为调用 mvc 接口中的数据
  1: required string call;

  // params 为 list 类型数据，一个元素可重复的有序列表，C++ 之 vector，Java 之 ArrayList，PHP 之 array
  // 在 PHP 服务端开发中相当于 call_user_func_array($call, $params)
  2: list<string> params;

  // 服务端客户端共享自定义共享数据
  // 相当于 PHP 中的关联数组
  3: map<string,string> metas;
}

/**
 * ---------------------------------------------------------------
 * 定义一个响应包结构
 * ---------------------------------------------------------------
 *
 * 通用响应接口，数据以 JSON 进行交互
 */
struct Response
{
  // status 为响应状态，200 表示成功，其他参考 HTTP 状态
  1: required i16 status;

  // code 为 JSON 字符串，客户端自主进行解析
  2: required string data;
}

/**
 * ---------------------------------------------------------------
 * 定义一个通用的服务
 * ---------------------------------------------------------------
 *
 * 通用调用服务，通过一个 call
 */
service Thrift
{
    Response call(1: Request request)
}
