<header class="top-header">
    <div class="container d-flex align-items-center justify-content-between w-100">
        <a href="/" wire:navigate>
            <img src="{{ asset('livewire/logo.png') }}" width="100"
                alt="Muslim Town Logo" />
        </a>

        <div class="main-searchbox-container mx-3">
            <form wire:submit="submitSearchForm" action="" method="GET">
                <div class="main-searchbox">
                    <input type="text" name="search" wire:model="search"
                        placeholder="Search products here..." value="">
                    <button type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M21.71,20.29,18,16.61A9,9,0,1,0,16.61,18l3.68,3.68a1,1,0,0,0,1.42,0A1,1,0,0,0,21.71,20.29ZM11,18a7,7,0,1,1,7-7A7,7,0,0,1,11,18Z">
                            </path>
                        </svg>
                    </button>
                </div>
            </form>
            <div class="search-suggestions">
                <ul></ul>
            </div>
        </div>
 
        <div class="mobile-search-form">
            <button type="button" class="sidebar-controller" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                   <path d="M3,8H21a1,1,0,0,0,0-2H3A1,1,0,0,0,3,8Zm18,8H3a1,1,0,0,0,0,2H21a1,1,0,0,0,0-2Zm0-5H3a1,1,0,0,0,0,2H21a1,1,0,0,0,0-2Z"></path>
                </svg>
            </button>
        </div>
    </div>
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasExampleLabel"><a href="/" wire:navigate>
            <img src="{{ asset('livewire/logo.png') }}" width="100"
                alt="Muslim Town Logo" />
        </a></h5>
          
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul>
                <li class="mb-2 px-2 py-1"><a wire:navigate href="/page/terms">Terms &amp; Conditions</a></li>
                <li class="mb-2 px-2 py-1"><a wire:navigate href="/page/privacy-policy">Privacy Policy</a></li>
                <li class="mb-2 px-2 py-1"><a wire:navigate href="/page/return-policy">Return & Refund Policy</a></li>
                <li class="mb-2 px-2 py-1"><a wire:navigate href="/support">Support</a></li>
            </ul>
        </div>
    </div>
</header>

