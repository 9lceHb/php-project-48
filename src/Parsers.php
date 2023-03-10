<?php

namespace Hexlet\Code\Parsers;

use Symfony\Component\Yaml\Yaml;

function getExtention(string $path)
{
    $pathList = explode(".", $path);
    return end($pathList);
}

function getFixtureFullPath(string $fixtureName)
{
    // $parts = [__DIR__, 'tests', 'fixtures', $fixtureName];
    $parts = ['tests', 'fixtures', $fixtureName];
    return realpath(implode('/', $parts));
}

function render(string $path)
{
    $fullPath = realpath($path);
    if ($fullPath === false) {
        return false;
    }
    $text = file_get_contents($fullPath);
    if ($text === false) {
        return false;
    }
    $extention = getExtention($path);
    if ($extention === 'json') {
        return json_decode($text, true);
    }
    return Yaml::parseFile($fullPath);
}
