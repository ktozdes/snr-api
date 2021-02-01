<?php


namespace App\Interfaces;


interface ParserInterface
{
    public function message();
    public function get();
    public function post($url, $bodyJson = '{}');
}
