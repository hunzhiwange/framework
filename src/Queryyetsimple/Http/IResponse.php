<?php declare(strict_types=1);
/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Leevel\Http;

/**
 * HTTP 响应接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.27
 * @see https://baike.baidu.com/item/HTTP%E7%8A%B6%E6%80%81%E7%A0%81/5053660?fr=aladdin
 * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
 * @version 1.0
 */
interface IResponse
{

    /**
     * HTTP_CONTINUE
     * 
     * @var int
     */
    const HTTP_CONTINUE = 100;

    /**
     * HTTP_SWITCHING_PROTOCOLS
     * 
     * @var int
     */  
    const HTTP_SWITCHING_PROTOCOLS = 101;

    /**
     * HTTP_PROCESSING (RFC2518)
     * 
     * @var int
     */
    const HTTP_PROCESSING = 102;
    
    /**
     * HTTP_OK
     * 
     * @var int
     */  
    const HTTP_OK = 200;
    
    /**
     * HTTP_CREATED
     * 
     * @var int
     */  
    const HTTP_CREATED = 201;
    
    /**
     * HTTP_ACCEPTED
     * 
     * @var int
     */  
    const HTTP_ACCEPTED = 202;
    
    /**
     * HTTP_NON_AUTHORITATIVE_INFORMATION
     * 
     * @var int
     */  
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    
    /**
     * HTTP_NO_CONTENT
     * 
     * @var int
     */  
    const HTTP_NO_CONTENT = 204;
    
    /**
     * HTTP_RESET_CONTENT
     * 
     * @var int
     */  
    const HTTP_RESET_CONTENT = 205;
    
    /**
     * HTTP_PARTIAL_CONTENT
     * 
     * @var int
     */  
    const HTTP_PARTIAL_CONTENT = 206;
    
     /**
     * HTTP_MULTI_STATUS (RFC4918)
     * 
     * @var int
     */  
    const HTTP_MULTI_STATUS = 207;
    
     /**
     * HTTP_ALREADY_REPORTED (RFC5842)
     * 
     * @var int
     */  
    const HTTP_ALREADY_REPORTED = 208;
    
     /**
     * HTTP_IM_USED (RFC3229)
     * 
     * @var int
     */  
    const HTTP_IM_USED = 226;
    
    /**
     * HTTP_MULTIPLE_CHOICES
     * 
     * @var int
     */  
    const HTTP_MULTIPLE_CHOICES = 300;
    
    /**
     * HTTP_MOVED_PERMANENTLY
     * 
     * @var int
     */  
    const HTTP_MOVED_PERMANENTLY = 301;
    
    /**
     * HTTP_FOUND
     * 
     * @var int
     */  
    const HTTP_FOUND = 302;
    
    /**
     * HTTP_SEE_OTHER
     * 
     * @var int
     */  
    const HTTP_SEE_OTHER = 303;
    
    /**
     * HTTP_NOT_MODIFIED
     * 
     * @var int
     */  
    const HTTP_NOT_MODIFIED = 304;
    
    /**
     * HTTP_USE_PROXY
     * 
     * @var int
     */  
    const HTTP_USE_PROXY = 305;
    
    /**
     * HTTP_RESERVED
     * 
     * @var int
     */  
    const HTTP_RESERVED = 306;
    
    /**
     * HTTP_TEMPORARY_REDIRECT
     * 
     * @var int
     */  
    const HTTP_TEMPORARY_REDIRECT = 307;
    
     /**
     * HTTP_PERMANENTLY_REDIRECT (RFC7238)
     * 
     * @var int
     */  
    const HTTP_PERMANENTLY_REDIRECT = 308;
    
    /**
     * HTTP_BAD_REQUEST
     * 
     * @var int
     */  
    const HTTP_BAD_REQUEST = 400;
    
    /**
     * HTTP_UNAUTHORIZED
     * 
     * @var int
     */  
    const HTTP_UNAUTHORIZED = 401;
    
    /**
     * HTTP_PAYMENT_REQUIRED
     * 
     * @var int
     */  
    const HTTP_PAYMENT_REQUIRED = 402;
    
    /**
     * HTTP_FORBIDDEN
     * 
     * @var int
     */  
    const HTTP_FORBIDDEN = 403;
    
    /**
     * HTTP_NOT_FOUND
     * 
     * @var int
     */  
    const HTTP_NOT_FOUND = 404;
    
    /**
     * HTTP_METHOD_NOT_ALLOWED
     * 
     * @var int
     */  
    const HTTP_METHOD_NOT_ALLOWED = 405;
    
    /**
     * HTTP_NOT_ACCEPTABLE
     * 
     * @var int
     */  
    const HTTP_NOT_ACCEPTABLE = 406;
    
    /**
     * HTTP_PROXY_AUTHENTICATION_REQUIRED
     * 
     * @var int
     */  
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    
    /**
     * HTTP_REQUEST_TIMEOUT
     * 
     * @var int
     */  
    const HTTP_REQUEST_TIMEOUT = 408;
    
    /**
     * HTTP_CONFLICT
     * 
     * @var int
     */  
    const HTTP_CONFLICT = 409;
    
    /**
     * HTTP_GONE
     * 
     * @var int
     */  
    const HTTP_GONE = 410;
    
    /**
     * HTTP_LENGTH_REQUIRED
     * 
     * @var int
     */  
    const HTTP_LENGTH_REQUIRED = 411;
    
    /**
     * HTTP_PRECONDITION_FAILED
     * 
     * @var int
     */  
    const HTTP_PRECONDITION_FAILED = 412;
    
    /**
     * HTTP_REQUEST_ENTITY_TOO_LARGE
     * 
     * @var int
     */  
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    
    /**
     * HTTP_REQUEST_URI_TOO_LONG
     * 
     * @var int
     */  
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    
    /**
     * HTTP_UNSUPPORTED_MEDIA_TYPE
     * 
     * @var int
     */  
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    
    /**
     * HTTP_REQUESTED_RANGE_NOT_SATISFIABLE
     * 
     * @var int
     */  
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    
    /**
     * HTTP_EXPECTATION_FAILED
     * 
     * @var int
     */  
    const HTTP_EXPECTATION_FAILED = 417;
    
    /**
     * HTTP_I_AM_A_TEAPOT (RFC2324)
     * 
     * @var int
     */  
    const HTTP_I_AM_A_TEAPOT = 418;
    
    /**
     * HTTP_MISDIRECTED_REQUEST (RFC7540)
     * 
     * @var int
     */  
    const HTTP_MISDIRECTED_REQUEST = 421;
    
    /**
     * HTTP_UNPROCESSABLE_ENTITY (RFC4918)
     * 
     * @var int
     */  
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    
    /**
     * HTTP_LOCKED (RFC4918)
     * 
     * @var int
     */  
    const HTTP_LOCKED = 423;
    
    /**
     * HTTP_FAILED_DEPENDENCY (RFC4918)
     * 
     * @var int
     */  
    const HTTP_FAILED_DEPENDENCY = 424;
    
    /**
     * HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL (RFC2817)
     * 
     * @var int
     */  
    const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;
    
    /**
     * HTTP_UPGRADE_REQUIRED (RFC2817)
     * 
     * @var int
     */  
    const HTTP_UPGRADE_REQUIRED = 426;
    
    /**
     * HTTP_PRECONDITION_REQUIRED (RFC6585)
     * 
     * @var int
     */  
    const HTTP_PRECONDITION_REQUIRED = 428;
    
    /**
     * HTTP_TOO_MANY_REQUESTS (RFC6585)
     * 
     * @var int
     */  
    const HTTP_TOO_MANY_REQUESTS = 429;
    
    /**
     * HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE (RFC6585)
     * 
     * @var int
     */  
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    
    /**
     * HTTP_UNAVAILABLE_FOR_LEGAL_REASONS
     * 
     * @var int
     */  
    const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    
    /**
     * HTTP_INTERNAL_SERVER_ERROR
     * 
     * @var int
     */  
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    
    /**
     * HTTP_NOT_IMPLEMENTED
     * 
     * @var int
     */  
    const HTTP_NOT_IMPLEMENTED = 501;
    
    /**
     * HTTP_BAD_GATEWAY
     * 
     * @var int
     */  
    const HTTP_BAD_GATEWAY = 502;
    
    /**
     * HTTP_SERVICE_UNAVAILABLE
     * 
     * @var int
     */  
    const HTTP_SERVICE_UNAVAILABLE = 503;
    
    /**
     * HTTP_GATEWAY_TIMEOUT
     * 
     * @var int
     */  
    const HTTP_GATEWAY_TIMEOUT = 504;
    
    /**
     * HTTP_VERSION_NOT_SUPPORTED
     * 
     * @var int
     */  
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    
    /**
     * HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL (RFC2295)
     * 
     * @var int
     */  
    const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;
    
    /**
     * HTTP_INSUFFICIENT_STORAGE (RFC4918)
     * 
     * @var int
     */  
    const HTTP_INSUFFICIENT_STORAGE = 507;
    
    /**
     * HTTP_LOOP_DETECTED (RFC5842)
     * 
     * @var int
     */  
    const HTTP_LOOP_DETECTED = 508;
    
    /**
     * HTTP_NOT_EXTENDED (RFC2774)
     * 
     * @var int
     */  
    const HTTP_NOT_EXTENDED = 510;
    
    /**
     * HTTP_NETWORK_AUTHENTICATION_REQUIRED (RFC6585)
     * 
     * @var int
     */  
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;
}
