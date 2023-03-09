<?php
namespace Pioc\Log;

class DumpToFile {

    public static function logToLines($logs) {
        $retval = array();

        if (!empty($logs)) {
            foreach($logs as $log) {
                $message = array();

                if (!empty($log['message'])) {
                    $message[] = $log['message'];
                }

                if (!empty($log['context'])) {
                    $context = json_encode($log['context'], JSON_PRETTY_PRINT |
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES |
                        JSON_NUMERIC_CHECK );

                    $context = str_replace('"', '', $context);
                    $message[] = $context;
                }

                if (!empty($message)) {
                    $retval[] = implode("\n", $message);
                }
            }
        }

        return empty($retval) ? '' : implode("\n", $retval);
    }

    public static function appendText($filepath, $text) {
        if (($fp = fopen($filepath, 'a+'))) {
            $text .= "\n";
            fwrite($fp, $text);
            fclose($fp);
        }
    }
}
