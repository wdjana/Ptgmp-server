<?php
namespace Ptgmp;

use Pioc\App as BaseApp;
use Pioc\Http\Aceess;

use Pioc\Log\Access;
use Pioc\Log\DumpToFile;

class App extends BaseApp {

    protected function getLogDir() {
        $name = 'logdir';

        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = null;

            $config = $this->config;

            if (!empty($config['logdir'])) {
                $this->_m[$name] = $config['logdir'];
            }
        }
        return $this->_m[$name];
    }

}




function shutdownx()
{
    $app = App::$app;

    // var_dump($app);

    $logdir = $app->logdir;
    $timeStart = $app->timeStart;
    $accessLogFile = sprintf('%s/ptgmp.access_%s.txt', $logdir, date('Ymd_l'));
    // echo 'logdir=', $logdir, "\n";


    if (!is_dir($logdir)) {
        mkdir($logdir, 0755, true);
    }

    $access = new Access();
    $access->app = $app;
    $line = $access->line;
    DumpToFile::appendText($accessLogFile,  $line);


    // echo "timestart: $timeStart","\t",$logdir,",",$accessLogFile,"\n";
    // echo "line: $line\n";



    // $rootdir = '/home/hihi/workspace/www/html/ptgmp/logs';
    // $file = sprintf('%s/log_%s.txt', $rootdir, date('d_D'));
    //
    // echo $file, "\n";
    //
    // $log = new Logger('ptgmp');
    // $log->pushHandler(new StreamHandler($file, \Monolog\Logger::WARNING));
    //
    //
    // if (PHP_SAPI == 'cli') {
    //     $log->info('this is debug from cli');
    // }
    //
    // $response = App::$app->response;
    // $code = $response->code;
    //
    // $log->info("response code $code");
    // $log->warning("this is debug warning");

    // $log->info = "clientAppToken = " . App::$app->clientAppToken;
}

register_shutdown_function('Ptgmp\shutdownx');
