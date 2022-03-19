<?php

namespace core;

class Request{
    public function method(){
        return $_SERVER['REQUEST_METHOD'];
    }

    public function path(){
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $mark_position = strpos($path, '?');
        if ($mark_position !== False)
            $path = substr($path, 0, $mark_position);
        return $path;
    }

    public function isGet(){
        return $this->method() === 'GET';
    }

    public function isPost(){
        return $this->method() === 'POST';
    }

    private function filterRequestData($request_data): array
    {
        $body = [];
        foreach($request_data as $key => $value){
            if (is_array($value)){
                $body[$key] = $this->filterRequestData($value);
            }
            else
                $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return $body;
    }
    public function body(): array{
        $body = array();
        if ($this->isPost()){
            $body = $this->filterRequestData($_POST);
        }
        return array_merge_recursive($body, $this->filterRequestData($_GET));
    }
}