<?php

namespace Hexlet\Code\Utils;

use function Functional\flat_map;
use function Functional\sort;
use function Hexlet\Code\Formatters\stylish;
use function Hexlet\Code\Formatters\plain;

function getNode($key, $type, $value = null, $chilren = null)
{
    $node = [
        "key" => $key,
        "type" => $type,
        "value" => $value,
        "children" => $chilren
    ];
    return $node;
}

function genDiff($before, $after)
{
    $keysDeleted = array_keys(array_diff_key($before, $after));
    $keysAdded = array_keys(array_diff_key($after, $before));
    $keysIntersected = array_keys(array_intersect_key($before, $after));
    $deletedElem = array_map(fn($key) => getNode($key, 'deleted', $before[$key]), $keysDeleted);
    $addedElem = array_map(fn($key) => getNode($key, 'added', $after[$key]), $keysAdded);
    $sameKeyElem = flat_map($keysIntersected, function ($key) use ($before, $after) {
        $valueBefore = $before[$key];
        $valueAfter = $after[$key];
        if ($valueBefore === $valueAfter) {
            return [getNode($key, 'unchanged', $valueBefore)];
        }
        if (!is_array($valueBefore) || !is_array($valueAfter)) {
            return [getNode($key, 'changedFrom', $valueBefore), getNode($key, 'changedTo', $valueAfter)];
        }
        $children = genDiff($valueBefore, $valueAfter);
        return [getNode($key, 'changedArray', null, $children)];
    });
    $result = array_merge($deletedElem, $addedElem, $sameKeyElem);
    $sorted = sort($result, function ($node1, $node2) {
        if ($node1["key"] !== $node2["key"]) {
            return $node1["key"] <=> $node2["key"];
        }
        if ($node1["type"] === 'changedFrom') {
            return -1;
        }
        if ($node1["type"] === 'changedTo') {
            return 1;
        }
        return 0;
    });
    return $sorted;
}

function chooseFormat($format, $diff)
{
    $format = strtolower($format);
    if ($format === "stylish") {
        return stylish($diff);
    }
    if ($format === "json") {
        return json_encode($diff);
    }
    return plain($diff);
}
