<?php

return [
    'email' => [
        'format' => 'mailto: %s',
        'input_group' => null,
    ],
    'tel'=> [
        'format' => 'tel: %s',
        'input_group' => null,
    ],
    'whatsapp'=> [
        'format' => 'https://api.whatsapp.com/send?phone=%s',
        'input_group' => null,
    ],
    'facebook'=> [
        'format' => 'https://facebook.com/%s',
        'input_group' => true,
    ],
    'facebook-messenger'=> [
        'format' => 'https://m.me/%s',
        'input_group' => true,
    ],
    'instagram'=> [
        'format' => 'https://instagram.com/%s',
        'input_group' => true,
    ],
    'twitter'=> [
        'format' => 'https://twitter.com/%s',
        'input_group' => true,
    ],
    'tiktok'=> [
        'format' => 'https://tiktok.com/@%s',
        'input_group' => true,
    ],
    'youtube'=> [
        'format' => 'https://youtube.com/channel/%s',
        'input_group' => true,
    ],
    'soundcloud'=> [
        'format' => 'https://soundcloud.com/%s',
        'input_group' => true,
    ],
    'linkedin'=> [
        'format' => 'https://linkedin.com/in/%s',
        'input_group' => true,
    ],
    'spotify' => [
        'format' => 'https://open.spotify.com/artist/%s',
        'input_group' => true,
    ],
    'pinterest' => [
        'format' => 'https://pinterest.com/%s',
        'input_group' => true,
    ]
];
