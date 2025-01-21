<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Muslim Town' }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('livewire/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">

    <link href="{{ asset('livewire/bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('livewire/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-KTDTXR3W');</script>
    <!-- End Google Tag Manager -->
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KTDTXR3W"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div id="app">
        @livewire('header')
        <main class="main">

            {{ $slot }}

            <livewire:cart-sidebar>
        </main>

        <footer class="footer_main_footer container mb-3">
            <hr />
            <div class="row mt-5 text-center">
                <div class="col-md-6 mb-4">
                    <a class="d-block" href="/"><img src="{{ asset('livewire/logo.png') }}" alt="Muslim Town"
                            width="150" /></a>
                    <div class="footer_brand_text px-4 mt-3">
                        <span class="text-black">Assalamualaikum. Muslim Town helps Muslim families to practice Islamic culture and Akida
                            in daily life. Markets are full of western or Indian designed items. We bring Islamic design
                            in our daily used items for families and children. Keep us in your Dua</span>
                    </div>
                </div>
                <div class="col-md-3 mb-4 mt-5">
                    <div>
                        <h6 class="pb-3 text-black">Contact Info</h6>
                        <ul>
                            {{-- <li class="mb-2"><a href="tel:+8809642-778778">Phone: +8809642-778778</a></li> --}}
                            <li class="mb-2"><a href="mailto:support@muslim.town">Email: support@muslim.town</a></li>
                        </ul>
                        <ul class="d-flex align-items-center justify-content-center mt-3">
                            <li><a class="d-block w-35px" href="https://www.facebook.com/muslimstown" target="_blank" rel="noreferrer"><img
                                        src="{{ asset('livewire/facebook.png') }}" alt=""
                                        class="img-fluid" /></a></li>
                            <li class="mx-3"><a class="d-block w-35px" href="https://www.instagram.com/themuslim_town/" target="_blank"
                                    rel="noreferrer"><img src="{{ asset('livewire/instagram.png') }}" alt=""
                                        class="img-fluid" /></a></li>
                            <li><a class="d-block w-35px" href="https://www.youtube.com/@muslimstown" target="_blank" rel="noreferrer"><img
                                        src="{{ asset('livewire/youtube.png') }}" alt=""
                                        class="img-fluid" /></a></li>
                            <li class="mx-3"><a class="d-block w-35px" href="https://www.linkedin.com/company/muslimtown/" target="_blank"
                                    rel="noreferrer"><img src="{{ asset('livewire/linkedin.png') }}" alt=""
                                        class="img-fluid" /></a></li>
                            <li><a class="d-block w-35px" href="https://www.tiktok.com/@muslimstown" target="_blank" rel="noreferrer"><img
                                        src="{{ asset('livewire/tiktok.png') }}" alt=""
                                        class="img-fluid" /></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 mb-4 mt-5">
                    <div>
                        <h6 class="pb-3 text-black">Resources</h6>
                        <ul>
                            <li class="mb-2"><a wire:navigate href="/page/terms">Terms &amp; Conditions</a></li>
                            <li class="mb-2"><a wire:navigate href="/page/privacy-policy">Privacy Policy</a></li>
                            <li class="mb-2"><a wire:navigate href="/support">Support</a></li>
                            {{-- <li class="mb-2"><a wire:navigate href="/page/trade-license">Trade License</a></li>
                           <li class="mb-2"><a wire:navigate href="/page/disclaimer">Disclaimer</a></li>
                           <li class="mb-2"><a wire:navigate href="/page/about-us">About Us</a></li>
                           <li class="mb-2"><a wire:navigate href="/page/faq">FAQ</a></li> --}}
                        </ul>
                    </div>
                </div>
            </div>
            <hr />
            {{-- <div class="row">
                  <div class="col-md-6">
                     <div class="footer_copyright">
                        <span>© 2022 <a href="https://www.muslim.town">Muslim.town</a> | All Rights Reserved</span>
                        <p class="small my-0" style="font-size:12px">Muslim Town is a sister concern of CureTech Ltd.</p>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="d-flex align-items-center justify-content-end footer_payment_methods">
                        <div>
                              <img src="{{ asset('livewire/cod.png') }}" width="80"/>
                              <span class="mx-1"></span><img src="{{ asset('livewire/bkash.png') }}" width="80"/>
                        </div>
                     </div>
                  </div>
               </div> --}}
            <div class="footer_copyright text-center">
                <span>© {{ date('Y') }} <a href="https://www.muslim.town">Muslim.town</a> | All Rights
                    Reserved</span>
                <p class="small my-0" style="font-size:12px">Muslim Town is a sister concern of CureTech Ltd.</p>
            </div>
        </footer>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            $(".toggle-cart-sidebar").on("click", function() {
                if ($("#cartsidebar").hasClass("end-0")) {
                    $("#cartsidebar").removeClass("end-0");
                    $(".cart-close-btn").addClass("d-none");
                    $(".cart-close-btn").removeClass("d-flex");
                } else {
                    $("#cartsidebar").addClass("end-0");
                    $(".cart-close-btn").removeClass("d-none");
                    $(".cart-close-btn").addClass("d-flex");
                }
            });
        });
    </script>

    <script>
        document.addEventListener('livewire:init', () => {

            Livewire.on('product_error', (event) => {
                toastr.options.positionClass = 'toast-bottom-right';
                toastr.error(event[0]);
            });

        });
    </script>

    @stack("scripts")
</body>

</html>
