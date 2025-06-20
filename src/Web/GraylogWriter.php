<?php
/**
 * @copyright 2020-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web;

use Gelf\Publisher;
use Gelf\Message;
use Gelf\Transport\UdpTransport;
use Psr\Log\LogLevel;

class GraylogWriter
{
    public static function doWrite(array $event)
    {
        $transport = new UdpTransport(GRAYLOG_DOMAIN, GRAYLOG_PORT, UdpTransport::CHUNK_SIZE_LAN);
        $publisher = new Publisher();
        $publisher->addTransport($transport);

        $message = new Message();
        $message->setLevel(LogLevel::ERROR);
        if (!empty($event['message'])) { $message->setShortMessage($event['message']); }
        if (!empty($event['file'   ])) { $message->setFile        ($event['file'   ]); }
        if (!empty($event['line'   ])) { $message->setLine        ($event['line'   ]); }

        $message->setAdditional('base_uri', BASE_URI);
        if (!empty($_SERVER['REQUEST_URI'])) {
            $message->setAdditional('request_uri', $_SERVER['REQUEST_URI']);
        }
        $message->setFullMessage(print_r($event, true));

        $publisher->publish($message);
    }

    public static function error(int $error, string $message, string $file, int $line)
    {
        $e = [
            'errno'   => $error,
            'message' => $message,
            'file'    => $file,
            'line'    => $line
        ];
        self::doWrite($e);
    }

    public static function exception($e)
    {
        $e = [
            'errno'   => $e->getCode(),
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTrace()
        ];
        self::doWrite($e);

    }

    public static function shutdown()
    {
        $e = error_get_last();
        if ($e) { self::doWrite($e); }
    }
}
