<?php

function get_timezone() :string
{
    $ip = 'http://ip-api.com/json/'.request()->ip();

    if (env('APP_ENV') !== 'production') {
        return "America/New_York";
    }

    return json_decode(file_get_contents($ip), true)['timezone'];
}

function tz(): string
{
    return auth()->check() && auth()->user()->timezone ? auth()->user()->timezone : get_timezone();
}

/* disgusting hack to get high rez images from this endpoint */
function fix_box_art(string $string)
{
    return str_replace(['-52', 'x72'], ['-285', 'x380'], $string);
}