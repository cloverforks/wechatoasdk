<?php
/**
 * @param string $url
 * @return bool|array
 */
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

/**
 * @param string $url
 * @param array $params
 * @param bool $json
 * @return bool|mixed
 */
function wechat_post_request($url, $params, $json = true)
{
    $result = file_get_contents($url, false, stream_context_create([
        'http' => [
            'header' => 'Content-Type: application/' . ($json ? 'json' : 'x-www-form-urlencoded'),
            'content' => $json ? json_encode($params) : http_build_query($params),
            'method' => 'POST',
            'timeout' => 3
        ]
    ]));
    $data = json_decode($result, true);
    if (json_last_error() === 0)
        return $data;
    return false;
}

/**
 * @param string $url
 * @param bool|string $return
 * @return false|int|string
 */
function wechat_download_file($url, $return = true)
{
    $result = file_get_contents($url, false, stream_context_create([
        'http' => [
            'timeout' => 30
        ]
    ]));

    if (!empty($http_response_header)) {
        $headers = implode("", $http_response_header);
        if (stristr($headers, 'Content-disposition: attachment') === false)
            return false;
    } else
        return false;

    if ($return === true)
        return $result;
    return file_put_contents($return, $result);
}

/**
 * @param string $url
 * @param array $file
 * @param string $type
 * @return bool|array
 */
function wechat_upload_file($url, $file, $type = '')
{
    if (!isset($file['name'], $file['type'], $file['size'], $file['tmp_name']))
        return false;

    if (!$type) {
        if (stristr($file['type'], 'image') !== false)
            $type = $file['size'] > 64 * 1000 ? 'image' : 'thumb';
        elseif (stristr($file['type'], 'video') !== false)
            $type = 'video';
        elseif (stristr($file['type'], 'audio') !== false)

            $type = 'voice';
    }

    $boundary = '--------------------------' . microtime(true);
    $header = [
//        sprintf("Authorization: Basic %s", base64_encode($username . ':' . $password)),
        "Content-Type: multipart/form-data; boundary=" . $boundary,
    ];

    $file_contents = file_get_contents($file['tmp_name']);
    $content = "--" . $boundary . "\r\n" .
        "Content-Disposition: form-data; name=\"" . 'media' . "\"; filename=\"" . $file['name'] . "\"\r\n" .
        "Content-Type: {$file['type']}\r\n\r\n" .
        $file_contents . "\r\n";

    $content .= "--" . $boundary . "\r\n" .
        "Content-Disposition: form-data; name=\"type\"\r\n\r\n" .
        "$type\r\n";

    $content .= "--" . $boundary . "--\r\n";

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $header),
            'content' => $content,
        ]
    ]);

    $result = file_get_contents($url, false, $context);
    $data = json_decode($result, true);
    if (json_last_error() === 0)
        return $data;
    return false;
}