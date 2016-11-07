<?php

define("FILE_PATH", "views");

function makeView(string $fileName)
{
    return require_once FILE_PATH . DIRECTORY_SEPARATOR . $fileName.".php";
}