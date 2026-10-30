<?php
$home = home_all();
$banner = $home['banner'];
$clients = $home['clients']['logos'] ?? [];
$about = $home['about'];
$stats = $home['stats']['items'] ?? [];
$marquee = $home['marquee']['items'] ?? [];
$projects = $home['projects'];
$services = $home['services'];
$counters = $home['counters']['items'] ?? [];
$team = $home['team']['items'] ?? [];
$testi = $home['testimonials'];
$teamMargins = ['', 'mt-20px md-mt-0', 'mt-40px md-mt-0', 'mt-60px md-mt-0'];

function clients_row(array $clients): void { ?>
    <div class="row row-cols-1 row-cols-lg-5 row-cols-md-3 row-cols-sm-2 clients-style-06 justify-content-center"
        data-anime='{ "el": "childs", "translateY": [30, 0], "opacity": [0,1], "duration": 600, "delay":0, "staggervalue": 300, "easing": "easeOutQuad" }'>
        <div class="col client-box text-center md-mb-35px"></div>
        <?php foreach ($clients as $c): ?>
        <div class="col client-box text-center md-mb-35px">
            <a href="<?= e($c['link'] ?? '#') ?>"><img src="<?= e($c['image'] ?? '') ?>" class="h-35px" alt=""></a>
        </div>
        <?php endforeach; ?>
    </div>
<?php }
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>


<!-- start banner -->
<section class="p-0 full-screen ipad-top-space-margin position-relative overflow-hidden md-h-auto">
    <div class="container-fluid p-0 h-100 position-relative">
        <div class="row h-100 g-0">
            <div
                class="col-xl-5 col-lg-6 d-flex justify-content-center flex-column ps-10 xxl-ps-5 xl-ps-2 md-ps-0 position-relative order-2 order-lg-1">
                <div
                    class="vertical-title-center align-items-center w-75px justify-content-center position-absolute h-auto d-none d-md-flex">
                    <div class="title fs-16 alt-font text-dark-gray fw-700 text-uppercase ls-05px text-uppercase"
                        data-fancy-text='{ "opacity": [0, 1], "translateY": [50, 0], "filter": ["blur(20px)", "blur(0px)"], "string": ["<?= e(str_replace('"', '', $banner['vertical_text'])) ?>"], "duration": 400, "delay": 0, "speed": 50, "easing": "easeOutQuad" }'>
                    </div>
                </div>
                <div class="border-start border-color-extra-medium-gray ps-60px ms-100px lg-ps-30px lg-ms-70px position-relative z-index-9 sm-ps-30px sm-pe-30px sm-ms-0 border-0"
                    data-anime='{ "el": "childs", "translateY": [30, 0], "opacity": [0,1], "duration": 600, "delay":0, "staggervalue": 300, "easing": "easeOutQuad" }'>
                    <h1
                        class="text-dark-gray fw-600 alt-font outside-box-right-10 xl-outside-box-right-15 ls-minus-4px sm-ls-minus-2px md-me-0">
                        <?= e($banner['heading']) ?></h1>
                    <p class="w-75 mb-35px lg-w-90 sm-w-100"><?= e($banner['text']) ?></p>
                    <a href="<?= e(app_href($banner['btn_link'])) ?>" class="btn btn-extra-large btn-gradient-fuel-yellow-blue fw-400"><?= e($banner['btn_text']) ?></a>
                </div>
            </div>
            <div
                class="col-xl-7 col-lg-6 position-relative swiper-number-pagination-progress md-h-500px order-1 order-lg-2 md-mb-50px">
                <div class="swiper h-100 banner-slider magic-cursor drag-cursor"
                    data-slider-options='{ "slidesPerView": 1, "loop": true, "pagination": { "el": ".swiper-number-line-pagination", "clickable": true }, "navigation": { "nextEl": ".slider-one-slide-next-1", "prevEl": ".slider-one-slide-prev-1" }, "autoplay": { "delay": 4000, "stopOnLastSlide": true, "disableOnInteraction": false },"keyboard": { "enabled": true, "onlyInViewport": true }, "effect": "fade" }'
                    data-swiper-number-pagination-progress="true">
                    <div class="swiper-wrapper">
                        <?php foreach ($banner['slides'] as $slide): ?>
                        <div class="swiper-slide">
                            <div class="position-absolute left-0px top-0px w-100 h-100 cover-background background-position-center-top"
                                style="background-image:url('<?= e($slide['image'] ?? '') ?>');"></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div
                        class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets d-block d-sm-none">
                    </div>
                </div>
                <div
                    class="swiper-pagination-wrapper d-none d-lg-flex align-items-center justify-content-center position-absolute bottom-40px md-bottom-30px sm-bottom-20px left-minus-45 md-left-30px sm-left-20px z-index-9">
                    <div class="number-prev fs-14 fw-600 text-dark-gray"></div>
                    <div class="swiper-pagination-progress bg-extra-medium-gray">
                        <span class="swiper-progress"></span>
                    </div>
                    <div class="number-next fs-14 fw-600 text-dark-gray"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end banner -->



<!-- start section -->
<section>
    <div class="container">
        <?php clients_row($clients); ?>
    </div>
</section>
<!-- end section -->



<!-- start section -->
<section class="pt-0">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 position-relative">
                <div class="row align-items-center position-relative md-mb-15"
                    data-anime='{ "opacity": [0,1], "duration": 600, "delay":0, "staggervalue": 300, "easing": "easeOutQuad" }'>
                    <div class="col-md-5 sm-mb-30px" data-bottom-top="transform: translateY(50px)"
                        data-top-bottom="transform: translateY(-50px)">
                        <img class="w-100" src="<?= e($about['image1']) ?>" alt="" />
                    </div>
                    <div class="col-lg-7 col-md-7 sm-mb-30px text-end"
                        data-bottom-top="transform: translateY(-30px)"
                        data-top-bottom="transform: translateY(30px)">
                        <img src="<?= e($about['image3']) ?>" alt=""
                            class="box-shadow-quadruple-large md-w-100" />
                    </div>
                    <div class="w-50 sm-w-100 overflow-hidden position-absolute sm-position-relative left-140px bottom-minus-200px sm-bottom-0px sm-left-0px p-0 sm-ps-15px sm-pe-15px"
                        data-shadow-animation="true" data-animation-delay="100"
                        data-bottom-top="transform: translateY(20px)"
                        data-top-bottom="transform: translateY(-20px)">
                        <img src="<?= e($about['image2']) ?>" alt=""
                            class="box-shadow-quadruple-large w-100" />
                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-6 offset-xl-1 md-mt-20 sm-mt-0"
                data-anime='{ "el": "childs", "translateY": [30, 0], "opacity": [0,1], "duration": 600, "delay":0, "staggervalue": 300, "easing": "easeOutQuad" }'>
                <div class="mb-10px">
                    <span class="w-25px h-1px d-inline-block bg-base-color me-5px align-middle"></span>
                    <span
                        class="text-gradient-base-color fs-15 alt-font fw-700 ls-05px text-uppercase d-inline-block align-middle"><?= e($about['tagline']) ?></span>
                </div>
                <h4 class="text-dark-gray alt-font fw-600 ls-minus-2px mb-20px"><?= e($about['heading']) ?></h4>
                <p class="w-90 md-w-100 mb-35px sm-mb-20px"><?= e($about['text']) ?></p>
                <a href="<?= e(app_href($about['btn1_link'])) ?>"
                    class="btn btn-large btn-dark-gray btn-switch-text btn-box-shadow border-1 left-icon me-10px sm-mb-15px sm-mt-15px">
                    <span>
                        <span><i class="feather icon-feather-edit"></i></span>
                        <span class="btn-double-text" data-text="<?= e($about['btn1_text']) ?>"><?= e($about['btn1_text']) ?></span>
                    </span>
                </a>
                <a href="<?= e(app_href($about['btn2_link'])) ?>"
                    class="btn btn-large btn-transparent-light-gray border-1 btn-switch-text left-icon sm-mb-15px sm-mt-15px">
                    <span>
                        <span><i class="feather icon-feather-briefcase"></i></span>
                        <span class="btn-double-text" data-text="<?= e($about['btn2_text']) ?>"><?= e($about['btn2_text']) ?></span>
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>
<!-- end section -->



<!-- start section -->
<section class="pt-0 border-top border-color-extra-medium-gray mt-6 md-mt-0 sm-border-top-0 process-steps-marquee">
    <div class="container">
        <div class="process-steps-viewport">
            <div class="row row-cols-1 row-cols-md-3 row-cols-sm-2 g-0 process-steps-track">
                <?php foreach ($stats as $i => $s): ?>
                <div class="col process-step-style-06 last-paragraph-no-margin hover-box<?= $i < count($stats) - 1 ? ' sm-mb-50px' : '' ?>">
                    <div class="process-step-icon-box position-relative top-minus-14px">
                        <span class="progress-step-separator bg-light-gray w-100 separator-line-1px opacity-1"></span>
                        <div
                            class="step-box d-flex align-items-center justify-content-center bg-white border-radius-100 w-25px h-25px position-relative border border-color-extra-medium-gray box-shadow-large">
                            <span class="w-7px h-7px bg-dark-gray border-radius-100"></span>
                        </div>
                    </div>
                    <span
                        class="d-block alt-font text-dark-gray fw-600 mb-10px mt-15px fs-22 lh-28 ls-minus-05px w-60 lg-w-75 md-w-100"><?= e($s['title'] ?? '') ?></span>
                    <p class="w-60 lg-w-75 md-w-100 sm-w-100"><?= e($s['text'] ?? '') ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<!-- end section -->







<!-- start section -->
<section class="overflow-hidden position-relative pt-0 lg-pb-0">
    <div class="container-fluid">
        <div class="row position-relative">
            <div class="col swiper swiper-width-auto feather-shadow text-center"
                data-slider-options='{ "slidesPerView": "auto", "spaceBetween":40, "speed": 20000, "loop": true, "allowTouchMove": false, "autoplay": { "delay":0, "disableOnInteraction": false, "reverseDirection": true }, "effect": "slide" }'>
                <div class="swiper-wrapper pb-30px marquee-slide">
                    <?php foreach ($marquee as $m): ?>
                    <div class="swiper-slide">
                        <div
                            class="fs-130 md-fs-90 sm-fs-70 alt-font text-dark-gray fw-600 ls-minus-6px sm-ls-minus-2px word-break-normal">
                            <?= e($m['text'] ?? '') ?> <span class="ms-20px">-</span></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end section -->


<!-- start section -->
<section class="stack-box py-0 z-index-99">
    <div class="stack-box-contain">
        <?php foreach ($projects['items'] as $i => $p):
            $n = $i + 1;
            $num = str_pad((string)$n, 2, '0', STR_PAD_LEFT);
            $odd = $n % 2 === 1;
            $bg = $odd ? 'bg-white' : 'bg-linen';
            $spacing = $odd ? 'lg-pt-8 ' . ($n === 1 ? 'lg-pb-8 md-pb-0' : 'md-pb-0 md-pt-0') : 'md-pt-0 md-pb-0';
        ?>
        <div class="stack-item stack-item-<?= $num ?> <?= $bg ?> <?= $spacing ?>">
            <div class="stack-item-wrapper">
                <div class="container-fluid">
                    <div class="row align-items-center full-screen md-h-auto">
                        <div class="col-lg-6 cover-background overflow-visible h-100 md-h-500px"
                            style="background-image: url(<?= e($p['image'] ?? '') ?>)">
                            <div
                                class="position-absolute right-minus-130px top-60px md-top-auto md-bottom-minus-50px fs-170 lg-fs-120 lg-right-minus-80px md-right-0px md-left-0px text-center text-lg-start alt-font z-index-9 fw-600 text-dark-gray opacity-3">
                                <?= $num ?></div>
                            <div class="position-absolute right-0px bottom-minus-1px">
                                <div class="vertical-title-center">
                                    <div
                                        class="title fw-700 fs-15 alt-font text-uppercase text-dark-gray <?= $bg ?> pt-30px pb-30px ps-10px pe-10px">
                                        <span class="d-inline-block"><?= e($projects['label']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-lg-6 ps-12 pe-14 xxl-ps-10 xxl-pe-10 xl-pe-8 lg-ps-6 lg-pe-4 md-p-50px sm-ps-30px sm-pe-30px position-relative align-self-center text-md-start text-center">
                            <div class="mb-15px">
                                <span class="w-25px h-1px d-inline-block bg-base-color me-5px align-middle"></span>
                                <span
                                    class="text-gradient-base-color fs-15 alt-font fw-700 ls-05px text-uppercase d-inline-block align-middle"><?= e($p['tagline'] ?? '') ?></span>
                            </div>
                            <h1 class="text-dark-gray alt-font fw-600 ls-minus-4px mb-25px"><?= e($p['title'] ?? '') ?></h1>
                            <p class="w-95 md-w-100 mb-35px"><?= e($p['text'] ?? '') ?></p>
                            <a href="<?= e(app_href($p['btn_link'] ?? '#')) ?>"
                                class="btn btn-large btn-dark-gray btn-switch-text btn-box-shadow fw-400">
                                <span>
                                    <span class="btn-double-text" data-text="<?= e($p['btn_text'] ?? '') ?>"><?= e($p['btn_text'] ?? '') ?></span>
                                    <span><i class="feather icon-feather-arrow-right"></i></span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<!-- end section -->


<!-- start section -->
<section class="pb-0">
    <div class="container">
        <div class="row mb-6">
            <div class="col-xl-5 col-md-6"
                data-anime='{ "el": "childs", "translateY": [30, 0], "opacity": [0,1], "duration": 600, "delay":0, "staggervalue": 300, "easing": "easeOutQuad" }'>
                <div class="mb-10px">
                    <span class="w-25px h-1px d-inline-block bg-base-color me-5px align-middle"></span>
                    <span
                        class="text-gradient-base-color fs-15 alt-font fw-700 ls-05px text-uppercase d-inline-block align-middle"><?= e($services['tagline']) ?></span>
                </div>
                <h4 class="text-dark-gray alt-font fw-600 w-90 ls-minus-2px"><?= e($services['heading']) ?></h4>
            </div>
            <div class="offset-xl-1 col-xl-5 col-md-6">
                <div class="accordion accordion-style-02" id="accordion-style-02"
                    data-active-icon="icon-feather-minus" data-inactive-icon="icon-feather-plus"
                    data-anime='{ "el": "childs", "translateY": [30, 0], "opacity": [0,1], "duration": 600, "delay":0, "staggervalue": 300, "easing": "easeOutQuad" }'>
                    <?php $last = count($services['items']) - 1; foreach ($services['items'] as $i => $s):
                        $first = $i === 0;
                        $border = $i === $last ? 'border-color-transparent' : 'border-color-dark-gray';
                        $id = 'accordion-style-02-' . str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
                    ?>
                    <div class="accordion-item<?= $first ? ' active-accordion' : '' ?>">
                        <div class="accordion-header border-bottom <?= $border ?>">
                            <a href="#" data-bs-toggle="collapse" data-bs-target="#<?= $id ?>"
                                aria-expanded="<?= $first ? 'true' : 'false' ?>" data-bs-parent="#accordion-style-02">
                                <div class="accordion-title mb-0 position-relative text-dark-gray">
                                    <i class="feather icon-feather-<?= $first ? 'minus' : 'plus' ?>"></i><span
                                        class="fw-600 alt-font fs-18"><?= e($s['title'] ?? '') ?></span>
                                </div>
                            </a>
                        </div>
                        <div id="<?= $id ?>" class="accordion-collapse collapse<?= $first ? ' show' : '' ?>"
                            data-bs-parent="#accordion-style-02">
                            <div
                                class="accordion-body last-paragraph-no-margin border-bottom <?= $border ?>">
                                <p><?= e($s['text'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-lg-4 row-cols-md-2 counter-style-04"
            data-anime='{ "el": "childs", "translateX": [30, 0], "opacity": [0,1], "duration": 600, "delay":0, "staggervalue": 300, "easing": "easeOutQuad" }'>
            <?php foreach ($counters as $i => $c): ?>
            <div class="col last-paragraph-no-margin <?= $i < count($counters) - 1 ? 'md-mb-30px' : '' ?> text-center text-md-start">
                <span class="alt-font fw-600 d-block mb-5px text-dark-gray fs-18"><?= e($c['title'] ?? '') ?></span>
                <p class="w-90 sm-w-100 sm-ps-15 sm-pe-15"><?= e($c['text'] ?? '') ?></p>
                <div class="separator-line-1px bg-extra-medium-gray w-90 mt-25px mb-25px sm-w-100"></div>
                <h3 class="vertical-counter d-inline-flex alt-font text-dark-gray fw-700 ls-minus-2px sm-ls-minus-1px sm-mb-0"
                    data-text="+" data-to="<?= (int)preg_replace('/\D/', '', (string)($c['number'] ?? '0')) ?>"></h3>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- end section -->


<!-- start section -->
<section class="pb-0">
    <div class="container-fluid p-0">
        <div class="team-carousel-viewport" aria-label="Team members">
            <div class="row row-cols-1 row-cols-lg-4 row-cols-sm-2 g-0 team-carousel-track">
                <?php foreach ($team as $i => $m): ?>
                <div class="col team-style-10 <?= $teamMargins[$i % 4] ?> md-ps-15px md-pe-15px md-mb-30px">
                    <figure class="mb-0 position-relative overflow-hidden">
                        <img src="<?= e($m['image'] ?? '') ?>" class="w-100" alt="" />
                        <img src="<?= e($m['hover_image'] ?? '') ?>" class="hover-switch-image" alt="" />
                        <figcaption class="w-100 h-100 d-flex flex-wrap">
                            <div class="social-icon d-flex flex-column flex-shrink-1 mb-auto p-30px ms-auto">
                                <a href="<?= e($m['social_link'] ?? '#') ?>" target="_blank" class="text-white bg-dark-gray"><i
                                        class="<?= e($m['social_icon'] ?? 'fa-brands fa-instagram') ?> icon-small"></i></a>
                            </div>
                            <div
                                class="team-member-strip w-100 mt-auto d-flex align-items-center pt-15px pb-15px ps-30px pe-30px bg-white">
                                <span class="team-member-name fw-600 alt-font text-dark-gray fs-18 ls-minus-05px"><?= e($m['name'] ?? '') ?></span>
                                <span class="member-designation fs-15 lh-20 ms-auto alt-font"><?= e($m['role'] ?? '') ?></span>
                            </div>
                        </figcaption>
                    </figure>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<!-- end section -->





<!-- start section -->
<section class="big-section position-relative">
    <div class="container position-relative">
        <div class="row justify-content-center mb-8"
            data-anime='{ "opacity": [0,1], "duration": 600, "delay":0, "staggervalue": 300, "easing": "easeOutQuad" }'>
            <div class="col-xl-4 col-md-5">
                <h2 class="alt-font text-dark-gray mb-30px fw-600 ls-minus-3px"><?= e($testi['heading']) ?><i
                        class="bi bi-heart-fill d-inline-block align-top ms-10px animation-zoom icon-very-medium text-red"></i>
                </h2>
                <div class="d-flex md-mb-25px">
                    <div
                        class="slider-one-slide-prev-1 text-dark-gray swiper-button-prev slider-navigation-style-04 border border-1 border-color-extra-medium-gray bg-white">
                        <i class="fa-solid fa-arrow-left"></i>
                    </div>
                    <div
                        class="slider-one-slide-next-1 text-dark-gray swiper-button-next slider-navigation-style-04 border border-1 border-color-extra-medium-gray bg-white">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-7 overflow-hidden offset-xl-2">
                <div class="swiper"
                    data-slider-options='{ "slidesPerView": 1, "spaceBetween": 40, "loop": true, "autoplay": { "delay": 8000, "disableOnInteraction": false }, "navigation": { "nextEl": ".slider-one-slide-next-1", "prevEl": ".slider-one-slide-prev-1" }, "keyboard": { "enabled": true, "onlyInViewport": true }, "breakpoints": { "992": { "slidesPerView": 1 }, "768": { "slidesPerView":1 }, "320": { "slidesPerView": 1 } }, "effect": "slide" }'>
                    <div class="swiper-wrapper">
                        <?php foreach ($testi['items'] as $t): ?>
                        <div class="swiper-slide">
                            <h5 class="text-dark-gray mb-15px w-95 xl-w-100 lh-40 ls-minus-1px alt-font"><?= hl($t['text'] ?? '') ?></h5>
                            <span class="text-gradient-base-color fw-700 text-uppercase ls-1px">@<?= e($t['name'] ?? '') ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php clients_row($clients); ?>
    </div>
</section>
<!-- end section -->


<?php include __DIR__ . '/../../includes/footer.php'; ?>