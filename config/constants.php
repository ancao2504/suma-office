<?php

return [
    'app' => [
        'url' => [
            'base'  => 'https://suma-honda.id',
            'api'   => 'http://124.158.154.66/suma-office/public/api',
            'images' => 'https://suma-honda.id/images',
        ], 'key' => [
            'username'  => 'suma-honda-sby',
            'password'  => 'Password200%'
        ],
        'access' => [
            'disabled'  => 'NOT_ALLOWED',
        ],
        'tokopedia' => [
            'kode_lokasi'   => 'OL',
            'kode_dealer'   => 'TP4A',
            'url' => [
                'base_url'  => 'https://fs.tokopedia.net',
                'account'   => 'https://accounts.tokopedia.com'
            ],
            'token'     => 'NDA5ZWRiOGRmZTc1NDkyOTliNThlNGYzZjI4YWE0MjU6ZjlkNDM0ZWVkOGNkNGI5MWFlYmRlOWYyMWJhYzRkMWU=',
            'fs_id'     => '15586',
            'shop_id'   => '5207422'
        ],
        'shopee' => [
            'kode_lokasi'   => 'OS',
            'kode_dealer'   => 'TP4C',
            'url' => [
                'host'      => 'https://partner.shopeemobile.com'
            ],
            'access_token'  => 'NDA5ZWRiOGRmZTc1NDkyOTliNThlNGYzZjI4YWE0MjU6ZjlkNDM0ZWVkOGNkNGI5MWFlYmRlOWYyMWJhYzRkMWU=',
            'partner_id'    => '15586',
            'shop_id'       => '306648863'
        ]
    ],
    'api' => [
        'url' => [
            'images' => 'https://suma-honda.id/images/parts',
        ],
        'access' => [
            'disabled'  => 'NOT_ALLOWED',
        ],
        'key' => [
            'username'  => 'suma-honda-sby',
            'password'  => 'Password200%'
        ],
        'data' => [
            'discount_default' => 19,
        ],
        'tokopedia' => [
            'kode_lokasi'   => 'OL',
            'kode_sales'    => 'G32',
            'kode_dealer'   => 'TP4A',
            'kode_beli'     => 'OL',
            'url'           => [
                'base_url'  => 'https://fs.tokopedia.net',
                'account'   => 'https://accounts.tokopedia.com'
            ],
            'token'     => 'NDA5ZWRiOGRmZTc1NDkyOTliNThlNGYzZjI4YWE0MjU6ZjlkNDM0ZWVkOGNkNGI5MWFlYmRlOWYyMWJhYzRkMWU=',
            'fs_id'     => '15586',
            'shop_id'   => '5207422'
        ],
        'shopee' => [
            'kode_lokasi'   => 'OS',
            'url'           => [
                'host'  => 'https://partner.shopeemobile.com',
            ],
            'kode_lokasi'   => 'OS',
            'kode_sales'    => 'G32',
            'kode_dealer'   => 'TP4C',
            'kode_beli'     => 'OL',
            'partner_id'    =>  2005563,
            'partner_key'   => '564c465379756967554e714b4e796d6142576c595652554e4b597a6f66444367',
            'shop_id'       => 306648863
        ]
    ]
];
