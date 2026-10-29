<?php
/* Existing index.php content = default data for each section. */
$lorem = 'We strive to develop real-world web solutions that are ideal for small to large project.';
$step  = 'We create compelling web designs, which are the right-fit for your target groups.';
$ctr   = 'We have crafted beautiful and engaging web solutions.';
$proj  = 'Creating products with a strong identity. Provide brilliant ideas and adding the world called success brand. We deliver customized marketing campaign to use your audience to make a positive move.';
$plink = 'demo-branding-agency-single-project-slider.html';

return [
    'banner' => [
        'vertical_text' => 'advanced branding solutions',
        'heading'  => 'Provide branding solutions to grow your business.',
        'text'     => 'Creating products with a strong identity. We provide brilliant ideas and adding the world called success brand.',
        'btn_text' => "Let's talk - Send a message",
        'btn_link' => 'demo-branding-agency-contact.html',
        'slides'   => [
            ['image' => 'assets/images/demo-branding-agency-slider-01.jpg'],
            ['image' => 'assets/images/demo-branding-agency-slider-02.jpg'],
            ['image' => 'assets/images/demo-branding-agency-slider-03.jpg'],
        ],
    ],
    'clients' => [
        'logos' => [
            ['image' => 'assets/images/logo-logitech.svg', 'link' => '#'],
            ['image' => 'assets/images/logo-monday.svg',   'link' => '#'],
            ['image' => 'assets/images/logo-google.svg',   'link' => '#'],
            ['image' => 'assets/images/logo-paypal.svg',   'link' => '#'],
        ],
    ],
    'about' => [
        'tagline'   => 'About branding About us',
        'heading'   => 'We provide brilliant ideas and adding the world called success to your brand.',
        'text'      => 'Creating products with a strong identity. We provide brilliant ideas and adding the world called success brand. We deliver customized marketing campaign to use your audience to make a positive move.',
        'image1'    => 'assets/images/demo-branding-agency-home-01.jpg',
        'image2'    => 'assets/images/demo-branding-agency-home-02.jpg',
        'image3'    => 'assets/images/demo-branding-agency-home-03.jpg',
        'btn1_text' => 'Explore details',
        'btn1_link' => 'demo-branding-agency-about.html',
        'btn2_text' => 'More services',
        'btn2_link' => 'demo-branding-agency-services.html',
    ],
    'stats' => [
        'items' => [
            ['title' => 'Over 1336+ success worldwide projects.', 'text' => $step],
            ['title' => 'Over 756+ happy clients and counting.',   'text' => $step],
            ['title' => 'Our presence in over 11+ countries.',     'text' => $step],
        ],
    ],
    'marquee' => [
        'items' => [
            ['text' => 'Provide branding solutions to grow your business'],
            ['text' => 'Drive business growth with our customized branding solutions'],
        ],
    ],
    'projects' => [
        'label' => 'Latest projects',
        'items' => [
            ['tagline' => 'Branding and design', 'title' => 'Whiteline face beauty.', 'image' => 'assets/images/demo-branding-agency-home-04.jpg',
             'text' => $proj, 'btn_text' => 'Explore project', 'btn_link' => $plink],
            ['tagline' => 'Web development and design', 'title' => 'Rebounce force riders.', 'image' => 'assets/images/demo-branding-agency-home-05.jpg',
             'text' => 'We specialize in developing products with a distinct and compelling identity. Our team excels generating brilliant ideas that propel brands to success. Through customized marketing campaigns.',
             'btn_text' => 'Explore project', 'btn_link' => $plink],
            ['tagline' => 'Branding and design', 'title' => 'Decorator hard carpet.', 'image' => 'assets/images/demo-branding-agency-home-06.jpg',
             'text' => $proj, 'btn_text' => 'Explore project', 'btn_link' => $plink],
        ],
    ],
    'services' => [
        'tagline' => 'Branding services',
        'heading' => 'We create to brands and businesses to stand out in this digital era.',
        'items'   => [
            ['title' => 'Research and strategy',   'text' => $lorem],
            ['title' => 'Wireframes and design',   'text' => $lorem],
            ['title' => 'Maintenance and support', 'text' => $lorem],
        ],
    ],
    'counters' => [
        'items' => [
            ['title' => 'Days of experience.',     'text' => $ctr, 'number' => '3053'],
            ['title' => 'Valuable happy clients.', 'text' => $ctr, 'number' => '1750'],
            ['title' => 'Presence in countries.',  'text' => $ctr, 'number' => '50'],
            ['title' => 'Worldwide projects.',     'text' => $ctr, 'number' => '856'],
        ],
    ],
    'team' => [
        'items' => [
            ['name' => 'Jeremy dupont',  'role' => 'Designer', 'image' => 'assets/images/demo-branding-agency-team-01.jpg', 'hover_image' => 'assets/images/demo-branding-agency-team-01-hover.jpg', 'social_icon' => 'fa-brands fa-twitter',    'social_link' => 'https://www.twitter.com/'],
            ['name' => 'Matthew taylor', 'role' => 'Writer',   'image' => 'assets/images/demo-branding-agency-team-02.jpg', 'hover_image' => 'assets/images/demo-branding-agency-team-02-hover.jpg', 'social_icon' => 'fa-brands fa-facebook-f', 'social_link' => 'https://www.facebook.com/'],
            ['name' => 'Herman miller',  'role' => 'Manager',  'image' => 'assets/images/demo-branding-agency-team-03.jpg', 'hover_image' => 'assets/images/demo-branding-agency-team-03-hover.jpg', 'social_icon' => 'fa-brands fa-linkedin-in', 'social_link' => 'https://www.linkedin.com/'],
            ['name' => 'Jessica dover',  'role' => 'Designer', 'image' => 'assets/images/demo-branding-agency-team-04.jpg', 'hover_image' => 'assets/images/demo-branding-agency-team-04-hover.jpg', 'social_icon' => 'fa-brands fa-instagram',   'social_link' => 'https://www.instagram.com/'],
        ],
    ],
    'testimonials' => [
        'heading'  => 'We are trusted by our clients',
        'bg_image' => 'assets/images/demo-branding-agency-about-01.png',
        'items' => [
            ['text' => 'The *wonderful* services you offer locally are great for our community. People are tired of having to travel out of town for things.', 'name' => 'Herman miller'],
            ['text' => "Absolutely amazing theme and *awesome* design with possibilities. It's so very easy to use and to customize everything.", 'name' => 'Alexander Harad'],
            ['text' => 'There are design companies and then there are user *experience.* Simply the great designs and best theme for fast loading website.', 'name' => 'Jacob Kalling'],
        ],
    ],
];