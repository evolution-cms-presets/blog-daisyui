<?php

return [
    'theme' => [
        'enabled' => true, // Enables frontend theme handling at all.
        'show_toggle' => true, // Shows the sun/moon light-dark switch.
        'show_themes' => true, // Shows the themes menu with the lists below.
        'default_light' => 'evolight', // First light theme used by default and by the toggle.
        'default_dark' => 'evodark', // First dark theme used by default and by the toggle.
        'storage_key' => 'evo.blogDaisyui.theme', // localStorage key prefix for remembered choices.

        'light' => [ // Visible light themes. Remove entries here to hide them from the menu.
            'evolight' => 'EVO Light',
            'evolightness' => 'EVO Lightness',
            'light' => 'Light',
            'cupcake' => 'Cupcake',
            'bumblebee' => 'Bumblebee',
            'emerald' => 'Emerald',
            'corporate' => 'Corporate',
            'retro' => 'Retro',
            'cyberpunk' => 'Cyberpunk',
            'valentine' => 'Valentine',
            'garden' => 'Garden',
            'lofi' => 'Lofi',
            'pastel' => 'Pastel',
            'fantasy' => 'Fantasy',
            'wireframe' => 'Wireframe',
            'cmyk' => 'CMYK',
            'autumn' => 'Autumn',
            'acid' => 'Acid',
            'lemonade' => 'Lemonade',
            'winter' => 'Winter',
            'caramellatte' => 'Caramellatte',
            'nord' => 'Nord',
            'silk' => 'Silk',
        ],
        'dark' => [ // Visible dark themes. Custom names need matching tokens in themes.css.
            'evodark' => 'EVO Dark',
            'evodarkness' => 'EVO Darkness',
            'dark' => 'Dark',
            'synthwave' => 'Synthwave',
            'halloween' => 'Halloween',
            'forest' => 'Forest',
            'aqua' => 'Aqua',
            'black' => 'Black',
            'luxury' => 'Luxury',
            'dracula' => 'Dracula',
            'business' => 'Business',
            'night' => 'Night',
            'coffee' => 'Coffee',
            'dim' => 'Dim',
            'sunset' => 'Sunset',
            'abyss' => 'Abyss',
        ],
    ],
];
