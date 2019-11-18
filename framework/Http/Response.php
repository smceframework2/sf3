<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework 2
 */

namespace SF3\Http;


class Response
{

    public $header;

    public $fileName;

    
    public function type($mimeType = "")
    {
        $this->header =  "Content-Type: " . $mimeType;
        
        return $this;
    }
    
   
    public function name($fileName)
    {
        $this->fileName = $fileName;
        
        return $this;
    }
    

    public function put($data)
    {
        
        if(!empty($this->fileName)) {
            header("Content-disposition: attachment; filename=" . $this->fileName);
        }
        header($this->header);
        echo $data;
    }
    
   
    public function putFile($file)
    {
        
        if(!empty($this->fileName)){
            header("Content-disposition: attachment; filename=" . $this->fileName);
        }
        header($this->header);
        readfile($file);
    }
    
 
    public function getHeader()
    {
        
        return $this->header;
    }

    public function json($data)
    {
        header("application/json");
        $this->putFormat(json_encode($data));
    }
    

    private function putFormat($data)
    {
        
        if(!empty($this->fileName)) {
            header("Content-disposition: attachment; filename=" . $this->fileName);
        }
        header($this->header);
        echo $data;
    }
}