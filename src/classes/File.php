<?php

namespace App\classes;

class File{
    protected $data;
    public $filename;

    public function __construct($filename){
        $this->initFilename($filename);
        if (filesize($this->filename) == 0) {
            $this->data = $this->initData();
        }else{
            $this->data = $this->getProcessedFileContent($this->filename);
        }
    }

    public function initFileName($fileName){
        $this->filename = $fileName;
    }

    public function initData(){
        return [];
    }

    public function getData(){
        return $this->data;
    }

    public function getFileName(){
        return $this->filename;
    }

    public function getProcessedFileContent($filename){
        $serializedFileContent = file_get_contents($filename);
        $unserializedFileContent = unserialize($serializedFileContent);
        return $unserializedFileContent;
    }

    public function putProcessedFileContent($filename,$data){
        $serializedFileContent = serialize($data);
        return file_put_contents($filename,$serializedFileContent);
    }
}