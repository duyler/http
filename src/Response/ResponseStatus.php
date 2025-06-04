<?php

declare(strict_types=1);

namespace Duyler\Http\Response;

class ResponseStatus
{
    public const int STATUS_CONTINUE = 100;
    public const int STATUS_SWITCHING_PROTOCOLS = 101;
    public const int STATUS_PROCESSING = 102;
    public const int STATUS_EARLY_HINTS = 103;
    public const int STATUS_OK = 200;
    public const int STATUS_CREATED = 201;
    public const int STATUS_ACCEPTED = 202;
    public const int STATUS_NON_AUTHORITATIVE_INFORMATION = 203;
    public const int STATUS_NO_CONTENT = 204;
    public const int STATUS_RESET_CONTENT = 205;
    public const int STATUS_PARTIAL_CONTENT = 206;
    public const int STATUS_MULTI_STATUS = 207;
    public const int STATUS_ALREADY_REPORTED = 208;
    public const int STATUS_IM_USED = 226;
    public const int STATUS_MULTIPLE_CHOICES = 300;
    public const int STATUS_MOVED_PERMANENTLY = 301;
    public const int STATUS_FOUND = 302;
    public const int STATUS_SEE_OTHER = 303;
    public const int STATUS_NOT_MODIFIED = 304;
    public const int STATUS_USE_PROXY = 305;
    public const int STATUS_TEMPORARY_REDIRECT = 307;
    public const int STATUS_PERMANENT_REDIRECT = 308;
    public const int STATUS_BAD_REQUEST = 400;
    public const int STATUS_UNAUTHORIZED = 401;
    public const int STATUS_PAYMENT_REQUIRED = 402;
    public const int STATUS_FORBIDDEN = 403;
    public const int STATUS_NOT_FOUND = 404;
    public const int STATUS_METHOD_NOT_ALLOWED = 405;
    public const int STATUS_NOT_ACCEPTABLE = 406;
    public const int STATUS_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const int STATUS_REQUEST_TIMEOUT = 408;
    public const int STATUS_CONFLICT = 409;
    public const int STATUS_GONE = 410;
    public const int STATUS_LENGTH_REQUIRED = 411;
    public const int STATUS_PRECONDITION_FAILED = 412;
    public const int STATUS_PAYLOAD_TOO_LARGE = 413;
    public const int STATUS_URI_TOO_LONG = 414;
    public const int STATUS_UNSUPPORTED_MEDIA_TYPE = 415;
    public const int STATUS_RANGE_NOT_SATISFIABLE = 416;
    public const int STATUS_EXPECTATION_FAILED = 417;
    public const int STATUS_IM_A_TEAPOT = 418;
    public const int STATUS_MISDIRECTED_REQUEST = 421;
    public const int STATUS_UNPROCESSABLE_ENTITY = 422;
    public const int STATUS_LOCKED = 423;
    public const int STATUS_FAILED_DEPENDENCY = 424;
    public const int STATUS_TOO_EARLY = 425;
    public const int STATUS_UPGRADE_REQUIRED = 426;
    public const int STATUS_PRECONDITION_REQUIRED = 428;
    public const int STATUS_TOO_MANY_REQUESTS = 429;
    public const int STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public const int STATUS_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    public const int STATUS_INTERNAL_SERVER_ERROR = 500;
    public const int STATUS_NOT_IMPLEMENTED = 501;
    public const int STATUS_BAD_GATEWAY = 502;
    public const int STATUS_SERVICE_UNAVAILABLE = 503;
    public const int STATUS_GATEWAY_TIMEOUT = 504;
    public const int STATUS_VERSION_NOT_SUPPORTED = 505;
    public const int STATUS_VARIANT_ALSO_NEGOTIATES = 506;
    public const int STATUS_INSUFFICIENT_STORAGE = 507;
    public const int STATUS_LOOP_DETECTED = 508;
    public const int STATUS_NOT_EXTENDED = 510;
    public const int STATUS_NETWORK_AUTHENTICATION_REQUIRED = 511;

    /** @var array<int, string>  */
    public const array ERROR_PHRASES = [
        self::STATUS_BAD_REQUEST => 'Bad Request',
        self::STATUS_UNAUTHORIZED => 'Unauthorized',
        self::STATUS_PAYMENT_REQUIRED => 'Payment Required',
        self::STATUS_FORBIDDEN => 'Forbidden',
        self::STATUS_NOT_FOUND => 'Not Found',
        self::STATUS_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::STATUS_NOT_ACCEPTABLE => 'Not Acceptable',
        self::STATUS_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::STATUS_REQUEST_TIMEOUT => 'Request Timeout',
        self::STATUS_CONFLICT => 'Conflict',
        self::STATUS_GONE => 'Gone',
        self::STATUS_LENGTH_REQUIRED => 'Length Required',
        self::STATUS_PRECONDITION_FAILED => 'Precondition Failed',
        self::STATUS_PAYLOAD_TOO_LARGE => 'Payload Too Large',
        self::STATUS_URI_TOO_LONG => 'URI Too Long',
        self::STATUS_UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        self::STATUS_RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable',
        self::STATUS_EXPECTATION_FAILED => 'Expectation Failed',
        self::STATUS_IM_A_TEAPOT => 'I\'m a teapot',
        self::STATUS_MISDIRECTED_REQUEST => 'Misdirected Request',
        self::STATUS_UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
        self::STATUS_LOCKED => 'Locked',
        self::STATUS_FAILED_DEPENDENCY => 'Failed Dependency',
        self::STATUS_TOO_EARLY => 'Too Early',
        self::STATUS_UPGRADE_REQUIRED => 'Upgrade Required',
        self::STATUS_PRECONDITION_REQUIRED => 'Precondition Required',
        self::STATUS_TOO_MANY_REQUESTS => 'Too Many Requests',
        self::STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
        self::STATUS_UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',
        self::STATUS_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::STATUS_NOT_IMPLEMENTED => 'Not Implemented',
        self::STATUS_BAD_GATEWAY => 'Bad Gateway',
        self::STATUS_SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::STATUS_GATEWAY_TIMEOUT => 'Gateway Timeout',
        self::STATUS_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
        self::STATUS_VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
        self::STATUS_INSUFFICIENT_STORAGE => 'Insufficient Storage',
        self::STATUS_LOOP_DETECTED => 'Loop Detected',
        self::STATUS_NOT_EXTENDED => 'Not Extended',
        self::STATUS_NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required',
    ];
}
