<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public contact + location
    |--------------------------------------------------------------------------
    |
    | Shown across the storefront — the WhatsApp buttons, the phone links, the
    | footer and the "come and see us" panel all read from here. Change any of
    | these in .env (SITE_PHONE, SITE_AREA, …) with no code deploy.
    |
    | Store the phone in the display format you want people to read; the tel:
    | and wa.me links strip it down to digits themselves.
    |
    */

    'phone' => env('SITE_PHONE', '+255 672 066 470'),

    // Short place name used in headings and the hero pill.
    'area' => env('SITE_AREA', 'Mbeya'),

    // Full address line for the footer and the location panel.
    'address' => env('SITE_ADDRESS', 'Mbeya, Tanzania'),

    // One line of directions. Keep it truthful — buyers use it to find you.
    'directions' => env('SITE_DIRECTIONS', 'In the centre of Mbeya — call or WhatsApp and we will guide you in.'),

    'hours' => env('SITE_HOURS', 'Mon–Sat, 08:00–18:00'),

];
