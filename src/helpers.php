<?php
// Helpers: response shortcuts, env reader
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $val = getenv($key);
        return $val === false ? $default : $val;
    }
}

function json_response($data, $status = 200)
{
    header('Content-Type: application/json', true, $status);
    echo json_encode($data);
}
