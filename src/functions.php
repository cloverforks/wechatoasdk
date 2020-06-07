<?php
function wechat_get_request($url)
{
    $result = file_get_contents($url, false, stream_context_create([
        'http' => [
            'timeout' => 3
        ]
    ]));

    $data = json_decode($result, true);
    if (json_last_error() === 0)
        return $data;
    return false;
}