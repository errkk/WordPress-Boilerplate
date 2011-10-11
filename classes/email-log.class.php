<?php
class emailLog
{
    private static $maxlogsize=1048576;

    public static function setMaxLogSize($bytes) {
        self::$maxlogsize = @intval($bytes);
    }

    public static function write($message, $filename='default')
    {
        return log_message( $message, $filename );
    }
}


