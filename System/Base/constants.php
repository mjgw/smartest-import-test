<?php

define("SM_ERROR_MISC", 100);
define("SM_ERROR_TMPL", 101);
define("SM_ERROR_FILES", 102);
define("SM_ERROR_PERMISSIONS", 102);
define("SM_ERROR_AUTH", 103);
define("SM_ERROR_DB", 104);
define("SM_ERROR_DATABASE", 104);
define("SM_ERROR_PHP", 105);
define("SM_ERROR_USER", 106);
define("SM_ERROR_MODEL", 107);
define("SM_ERROR_SMARTEST_INTERNAL", 108);
define("SM_ERROR_LINK_TARGET", 109);
define("SM_ERROR_LINK_TARGET_ITEM", 110);
define("SM_ERROR_LINK_INVALID_FORMAT", 111);
define("SM_ERROR_CONFIG", 112);
define("SM_ERROR_PAGE_NOT_FOUND", 113);
define("SM_ERROR_FILE_NOT_FOUND", 114);

define('SM_QUERY_ALL_DRAFT', 0);
define('SM_QUERY_ALL_DRAFT_ARCHIVED', 1);
define('SM_QUERY_ALL_DRAFT_CURRENT', 2);

define('SM_QUERY_ALL_LIVE', 3);
define('SM_QUERY_ALL_LIVE_ARCHIVED', 4);
define('SM_QUERY_ALL_LIVE_CURRENT', 5);

define('SM_QUERY_PUBLIC_DRAFT', 6);
define('SM_QUERY_PUBLIC_DRAFT_ARCHIVED', 7);
define('SM_QUERY_PUBLIC_DRAFT_CURRENT', 8);

define('SM_QUERY_PUBLIC_LIVE', 9);
define('SM_QUERY_PUBLIC_LIVE_ARCHIVED', 10);
define('SM_QUERY_PUBLIC_LIVE_CURRENT', 11);

define('SM_QUERY_EQUAL', 0);
define('SM_QUERY_EQUALS', 0);
define('SM_QUERY_NOT_EQUAL', 1);
define('SM_QUERY_NOTEQUAL', 1);

define('SM_QUERY_CONTAINS', 2);
define('SM_QUERY_NOTCONTAINS', 3);
define('SM_QUERY_NOT_CONTAINS', 3);
define('SM_QUERY_DOESNOTCONTAIN', 3);
define('SM_QUERY_DOES_NOT_CONTAIN', 3);

define('SM_QUERY_STARTSWITH', 4);
define('SM_QUERY_STARTS_WITH', 4);
define('SM_QUERY_ENDSWITH', 5);
define('SM_QUERY_ENDS_WITH', 5);

define('SM_QUERY_GREATERTHAN', 6);
define('SM_QUERY_GREATER_THAN', 6);
define('SM_QUERY_LESSTHAN', 7);
define('SM_QUERY_LESS_THAN', 7);

define('SM_QUERY_TAGGEDWITH',      8);
define('SM_QUERY_TAGGED_WITH',     8);
define('SM_QUERY_NOTTAGGEDWITH',   9);
define('SM_QUERY_NOT_TAGGED_WITH', 9);

define('SM_STATUS_ALL', 0);

define('SM_STATUS_HIDDEN',          1); // the default for draft pages/previews
define('SM_STATUS_HIDDEN_CHANGED',  2);
define('SM_STATUS_HIDDEN_APPROVED', 3);

define('SM_STATUS_LIVE',            4);
define('SM_STATUS_LIVE_CHANGED',    5);
define('SM_STATUS_LIVE_APPROVED',   6); // the default for published pages

define('SM_STATUS_CURRENT',         7);
define('SM_STATUS_ARCHIVED',        8);
define('SM_STATUS_CHANGED',         9); // all items that have been changed, regardless if public or hidden
define('SM_STATUS_APPROVED',        10); // all items that have been approved, regardless if public or hidden

define('SM_CONTEXT_GENERAL',              100);
define('SM_CONTEXT_SYSTEM_UI',            101);
define('SM_CONTEXT_CONTENT_PAGE',         102);
define('SM_CONTEXT_DYNAMIC_TEXTFRAGMENT', 103);
define('SM_CONTEXT_COMPLEX_ELEMENT',      104); // For rendering templates without containers
define('SM_CONTEXT_ITEMSPACE_TEMPLATE',   105);
define('SM_CONTEXT_INLINE_ASSET',         106);
define('SM_CONTEXT_HYPERLINK',            107);

define('SM_USER_MESSAGE_INFO',          1);
define('SM_USER_MESSAGE_SUCCESS',       2);
define('SM_USER_MESSAGE_WARNING',       4);
define('SM_USER_MESSAGE_ERROR',         8);
define('SM_USER_MESSAGE_FAIL',          8);
define('SM_USER_MESSAGE_ACCESSDENIED',  16);
define('SM_USER_MESSAGE_ACCESS_DENIED', 16);

define('SM_INSTALLSTATUS_COMPLETE',           0);
define('SM_INSTALLSTATUS_NO_FILE_PERMS',      1);
define('SM_INSTALLSTATUS_NO_CONFIG',          2);
define('SM_INSTALLSTATUS_NO_DB_CONFIG',       4);
define('SM_INSTALLSTATUS_DB_NO_CONN',         8);
define('SM_INSTALLSTATUS_DB_NONE',            16);
define('SM_INSTALLSTATUS_DB_NOT_ALLOWED',     32);
define('SM_INSTALLSTATUS_DB_NO_CREATE_PERMS', 64);
define('SM_INSTALLSTATUS_DB_DATA_INVALID',    96);
define('SM_INSTALLSTATUS_NO_USERS',           128);
define('SM_INSTALLSTATUS_USER_DATA_INVALID',  256);
define('SM_INSTALLSTATUS_NO_SITES',           512);
define('SM_INSTALLSTATUS_SITE_DATA_INVALID',  1024);

define('SM_COMMENTSTATUS_APPROVED', 0);
define('SM_COMMENTSTATUS_PENDING',  1);
define('SM_COMMENTSTATUS_REJECTED', 2);

define('SM_LOG_DEBUG',         0);
define('SM_LOG_NOTICE',        1);
define('SM_LOG_WARNING',       2);
define('SM_LOG_ERROR',         4);
define('SM_LOG_PERMISSIONS',   8);
define('SM_LOG_ACCESS_DENIED', 8);
define('SM_LOG_ACCESSDENIED',  8);
define('SM_LOG_USER_ACTION',   16);

define('SM_LINK_TYPE_DUD',           0);
define('SM_LINK_TYPE_PAGE',          1);
define('SM_LINK_TYPE_METAPAGE',      2);
define('SM_LINK_TYPE_IMAGE',         4);
define('SM_LINK_TYPE_DOWNLOAD',      8);
define('SM_LINK_TYPE_TAG',           16);
define('SM_LINK_TYPE_AUTHOR',        32);
define('SM_LINK_TYPE_EXTERNAL',      256);
define('SM_LINK_TYPE_MAILTO',        512);
define('SM_LINK_TYPE_QUINCE_ROUTE',  1024);
define('SM_LINK_TYPE_INTERNAL_ITEM', 2048);

define('SM_LINK_SCOPE_NONE',     0);
define('SM_LINK_SCOPE_INTERNAL', 1);
define('SM_LINK_SCOPE_EXTERNAL', 2);

define('SM_LINK_FORMAT_AUTO',         1);
define('SM_LINK_FORMAT_USER',         2);
define('SM_LINK_FORMAT_URL',          4);
define('SM_LINK_FORMAT_QUINCE_ROUTE', 8);
define('SM_LINK_FORMAT_FORM',         16);

define('SM_DATETIME_RESOLUTION_SECONDS', 1);
define('SM_DATETIME_RESOLUTION_MINUTES', 2);
define('SM_DATETIME_RESOLUTION_HOURS',   4);
define('SM_DATETIME_RESOLUTION_DAYS',    8);
define('SM_DATETIME_RESOLUTION_MONTHS',  16);
define('SM_DATETIME_RESOLUTION_YEARS',   32);

define('SM_LINK_GET_TARGET_TITLE', 'SM_LINK_GET_TARGET_TITLE');

define('SM_MTM_SORT_GROUP_ORDER', 'ManyToManyLookups.mtmlookup_order_index');

define('SM_MTMLOOKUPSTATUS_ALL',   0);
define('SM_MTMLOOKUPSTATUS_DRAFT', 1);
define('SM_MTMLOOKUPSTATUS_LIVE',  2);
define('SM_MTMLOOKUPSTATUS_OLD',   4);

define('SM_MTMLOOKUPMODE_ALL',    0);
define('SM_MTMLOOKUPMODE_DRAFT',  1);
define('SM_MTMLOOKUPMODE_PUBLIC', 2);

define('SM_RANDOM_ALL', 1);
define('SM_RANDOM_NUMERIC', 2);
define('SM_RANDOM_HEX', 4);
define('SM_RANDOM_ALPHANUMERIC', 8);

// Different platforms
define('SM_USERAGENT_NORMAL',  1);
define('SM_USERAGENT_DESKTOP', 1);

define('SM_USERAGENT_UNSUPPORTED_BROWSER', 2);
define('SM_USERAGENT_OLD_BROWSER', 2);

define('SM_USERAGENT_MOBILE', 4);
define('SM_USERAGENT_SMALL_MOBILE', 4);
define('SM_USERAGENT_PHONE', 4);

define('SM_USERAGENT_LARGE_MOBILE', 8);
define('SM_USERAGENT_TABLET', 8);

$GLOBALS['reserved_keywords'] = array('__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'constant', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor');