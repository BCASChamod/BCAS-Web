<?php
function stateUpdate($key, $value) {
    $filepath = __DIR__ . "../../logs/state/default.json";

    if (!file_exists($filepath)) return false;

    $json = file_get_contents($filepath);
    $data = json_decode($json, true);

    if (isset($data[$key])) {
        $data[$key] = $value;
        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
        return true;
    }
    return false;
}

function errorlog($error) {
    $logFile = __DIR__ . "../../logs/debug/error_log.txt";
    $timestamp = date("Y-m-d H:i:s");
    $entry = "[$timestamp] $error\n";

    file_put_contents($logFile, $entry, FILE_APPEND);
}
