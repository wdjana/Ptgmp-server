<?php
namespace Pioc\Http;

use Pioc\Base\Model;

class Response extends Model {

    public $defaultCode = 404;

    protected function getType()
    {
        $name = 'type';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = ResponseType::CODE;
        }

        return $this->_m[$name];
    }

    protected function setCode($code=200)
    {
        $this->_m['code'] = $code;
    }

    protected function getCode()
    {
        $name = 'code';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = $this->defaultCode;
        }
        return $this->_m[$name];
    }

    protected function setJson($json)
    {
        $this->_m['type'] = ResponseType::JSON;
        $this->_m['message'] = $json;
    }

    protected function setBuffer($buffer)
    {
        $this->_m['type'] = ResponseType::BUFFER;
        $this->_m['message'] = $buffer;
    }

    protected function setFile($file)
    {
        if (!empty($file) && is_file($file)) {
            $this->_m['type'] = ResponseType::FILE;
            $this->_m['message'] = $file;
        } else {
            $this->_m['type'] = ResponseType::CODE;
            $this->_m['code'] = 404;
        }
    }

    protected function setStream($file)
    {
        if (!empty($file) && is_file($file)) {
            $this->_m['type'] = ResponseType::STREAM;
            $this->_m['message'] = $file;
        } else {
            $this->_m['type'] = ResponseType::CODE;
            $this->_m['code'] = 404;
        }
    }

    protected function setAlias($alias)
    {
        $this->_m['alias'] = $alias;
    }

    protected function getAlias()
    {
        $name = 'alias';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = null;
        }
        return $this->_m[$name];
    }

    protected function sendCode($code=200)
    {
        http_response_code($code);
    }

    protected function sendJson($json)
    {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($json);
    }

    protected function sendBuffer($buffer)
    {
        http_response_code(200);
        header('Content-Type: text/html');
        echo $buffer;
    }

    protected function sendFile($file, $alias)
    {
        if (!empty($file) && is_file($file)) {
            empty($alias) && $alias = basename($file);
            $mime = mime_content_type($file);
            $size = filesize($file);

            http_response_code(200);
            header('Content-Description: File Transfer');
            header('Content-Type: '.$mime);
            header('Content-Disposition: attachment; filename="'.$alias.'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . $size);
            readfile($file);
        }
    }

    protected function sendStream($file, $alias)
    {
        $stream = new Stream($file, $alias);
        $stream->start();
    }

    public function send()
    {
        $type = $this->type;
        $code = $this->code;
        $message = $this->message;

        switch ($type) {
            case ResponseType::JSON:
                $this->sendJson($message);
            break;

            case ResponseType::BUFFER:
                $this->sendBuffer($message);
            break;

            case ResponseType::FILE:
                $alias = $this->alias;
                $this->sendFile($message, $alias);
            break;

            case ResponseType::STREAM:
                $alias = $this->alias;
                $this->sendStream($message, $alias);
            break;

            default:
                $this->sendCode($code);
                break;
        }
    }


}
