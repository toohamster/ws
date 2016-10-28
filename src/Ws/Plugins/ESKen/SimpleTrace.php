<?php namespace Ws\Plugins\ESKen;

use Ws\Debug\Tracks;

/**
 * ESKen Trace 打印
 *
 * 使用 asdebug 服务打印调用信息
 */
final class SimpleTrace extends \Psr\Log\AbstractLogger
{

    public function log($level, $message, array $context = [])
    {
        if (empty($context)) {
            Tracks::instance()->track('es:query', $message);
        } else {
            Tracks::instance()->track('es:response', $context);
        }
    }

}