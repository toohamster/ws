<?php namespace Ws\Debug;

use Ws\Env;
use Ws\SClassBase;
use Ws\Mvc\Request;
use Ws\Mvc\Container;

final class Tracks extends SClassBase
{

    const LEVEL_ALL = 'all';
    const LEVEL_ERROR = 'error';

    const TAG_INIT = 'sys:init';
    const TAG_START = 'app:start';
    const TAG_BEFORE = 'app:before';
    const TAG_INFO = 'app:info';
    const TAG_AFTER = 'app:after';
    const TAG_ERROR = 'app:error';
    const TAG_FINISH = 'app:finish';
    const TAG_SHUTDOWN = 'sys:shutdown';

    function __construct()
    {
        $this->sysTags = [
            self::TAG_INIT,
            self::TAG_START,
            self::TAG_BEFORE,
            self::TAG_AFTER,
            self::TAG_ERROR,
            self::TAG_FINISH,
            self::TAG_SHUTDOWN,
        ];

        $options = Container::$config->get('debug.tracks');
        $enable = empty($options['enable']) ? false : $options['enable'];

        $this->enable = (bool) $enable;
        if (!$this->enable) return;

        $qauth = empty($options['qauth']) ? 'tracks' : $options['qauth'];
        $authval = Request::get($qauth);
        $this->enable = $options['secret'] == $authval;
    }

    public function track($tag, $track=null)
    {
        if ($this->enable)
        {
            AsDebug::disable();// 两者只能同时出现1种

            $tag = strtolower(trim($tag));
            if (!in_array($tag, $this->sysTags)) {
                $track = [$tag, $track];
                $tag = self::TAG_INFO;
            }

            return $this->pipe($tag, $track);
        }

        AsDebug::dd($track, $tag);
    }

    private function pipe($tag, $track)
    {
        $msg = ['tag' => $tag];
        switch ($tag) {
            case self::TAG_INIT:
                $ms = microtime(true);
                $m = explode('.', (string)$ms);
                if ( !isset($m[1]) ) $m[1] = 0;
                $msg = [
                    'tag' => $tag,
                    'ms' => $ms,
                    'date' => date("Ymd H:i:s.{$m[1]}", $m[0]),
                    'cookies' => isset($_COOKIE) ? $_COOKIE : [],
                    'sessions' => isset($_SESSION) ? $_SESSION : [],
                ];
                break;
            case self::TAG_START:
                if (!empty($track)) {
                    $msg['track'] = $track;
                }
                break;
            case self::TAG_BEFORE:
                if (!empty($track)) {
                    $msg['track'] = $track;
                }
                break;
            case self::TAG_INFO:
                $msg['track'] = $track;
                break;
            case self::TAG_AFTER:
                if (!empty($track)) {
                    $msg['track'] = $track;
                }
                break;
            case self::TAG_ERROR:
                if (!empty($track)) {
                    $msg['track'] = $track;
                }

                break;
            case self::TAG_FINISH:
                if (!empty($track)) {
                    $msg['track'] = $track;
                }
                break;
            case self::TAG_SHUTDOWN:
                $msg = [
                    'tag' => $tag,
                ];
                break;
        }

        Env::dump($msg);
    }

}
