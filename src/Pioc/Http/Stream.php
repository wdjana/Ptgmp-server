<?php
namespace Pioc\Http;

class Stream {

    protected $path = "";

    protected $filename = "";

    protected $stream = "";

    protected $mimeType = "";

    protected $size = 0;

    protected $start = -1;

    protected $end = -1;

    protected $buffer = 102400;

    public $errors = array();

    public $app = null;

    public function __construct($filepath, $filename="")
    {
        $this->path = $filepath;
        if (empty($filename)) {
            $this->filename = basename($filepath);
        } else {
            $this->filename = $filename;
        }
    }

    public function start()
    {
        $this->open();
        $this->setHeader();
        $this->stream();
        $this->done();
    }

    protected function open()
    {
        if (!($this->stream = fopen($this->path, 'rb'))) {
            $this->errors[] = "could not open stream for reading";
            $this->done();
        } else {
            $this->mimeType = mime_content_type($this->path);
        }
    }

    protected function setHeader()
    {
        ob_get_clean();
        header("Content-Type: $this->mimeType");
        header('Content-Disposition: filename="'.$this->filename.'"');
        header('Cache-Control: max-age=259200, public');
        header('Expires: ' .  gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', @filemtime($this->path)) . ' GMT');
        $this->start = 0;
        // $this->size = Readsize::fromfile($this->path);

		// only work on php x64 if you use php
		$this->size = filesize($this->path);

		// $this->size = $this->getFilesize();
        $this->end = $this->size -1;
        // header("Accept-Ranges: 0-".$this->end);
        header("Accept-Ranges: bytes");

        if (isset($_SERVER['HTTP_RANGE'])) {
            $c_start = $this->start;
            $c_end = $this->end;

            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);

            if (strpos($range, ',') !== false) {
                http_response_code(416);
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit();
            }

            if ($range == '-') {
                $c_start = $this->size - substr($range, 1);
            } else {
                $range = explode('-', $range);
                $c_start = $range[0];
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
            }

            $c_end = ($c_end > $this->end) ? $this->end : $c_end;

            if ($c_start > $c_end || $c_start > $this->size - 1 || $c_end >= $this->size) {
                http_response_code(416);
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit();
            }

            http_response_code(206);
            $this->start = $c_start;
            $this->end = $c_end;
            $length = $this->end - $this->start + 1;
            fseek($this->stream, $this->start);
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: ".$length);
            header("Content-Range: bytes $this->start-$this->end/".$this->size);
        } else {
            http_response_code(200);
            header("Content-Length: " . $this->size);
        }

    }

    protected function stream()
    {
        $i = $this->start;
        set_time_limit(0);
        while(!feof($this->stream) && $i <= $this->end) {
            $bytesToRead = $this->buffer;
            if(($i+$bytesToRead) > $this->end) {
                $bytesToRead = $this->end - $i + 1;
            }
            // $data = fread($this->stream, $bytesToRead);
            // echo $data;
            echo fread($this->stream, $bytesToRead);
            flush();
            $i += $bytesToRead;
        }
    }

    protected function done()
    {
        if ($this->stream) {
            fclose($this->stream);
        }

        exit();
    }

}
