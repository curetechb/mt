
<div class="my-3">
   <div>
      <main>


         <div class="pt-2 container">
            <div class="row">
               <div class="col-md-7 text-center mx-auto promo-text">
                  <h1 class="h2 main-title mb-2">Assalamualaikum,</h1>
                  <p class="mb-0">Muslim Town helps Muslim families to practice Islamic culture and Akida in daily life. Markets are full of western or Indian designed items. We bring Islamic design in our daily used items for families and children. Keep us in your Dua.</p>
               </div>
            </div>
         </div>

         {{-- <div class="py-4 container">
            <div class="styles_categorybox">
               <h4 class="text-center my-4 h4 fw-bold">Our Product Categories</h4>
               <ul class="undefined row">
                  @foreach ($categories as $category)
                     <li class="col-sm-6 col-md-4 mb-3">
                        <a class="d-flex align-items-center" href="">
                              <div class="text-center w-100"><span>{{ $category->name }}</span></div>
                              <img src="{{ api_asset($category->icon) }}" alt="" width="30"/>
                        </a>
                     </li>
                  @endforeach
               </ul>
            </div>
         </div> --}}

         <div class="py-4">
            <div class="container">
               @if (count($products) > 0)
                  <div class="product-grid-container">
                     @foreach ($products as $product)
                           <div class="product-grid-item" wire:key="{{ $product->id }}">
                              <div class="productcard_productbox">
                              <a class="productcard_productdetails">
                                 <div class="productcard_productimg">
                                       <img src="{{api_asset($product->thumbnail_img)}}" alt="Regular Daily Combo" class="img-fluid"></div>
                                 <div class="productcard_productinfo text-center p-2">
                                       <h3>{{ $product->name }}</h3>
                                       <div>{{ $product->unit_value." ".$product->unit }}</div>
                                       <div>{{ currency_symbol().$product->unit_price }}</div>
                                 </div>
                                 <div data-bs-toggle="modal" data-bs-target="#productModal{{$product->id}}" class="productcard_productview">
                                       <div class="d-flex align-items-center justify-content-center">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                          <path d="M21.92,11.6C19.9,6.91,16.1,4,12,4S4.1,6.91,2.08,11.6a1,1,0,0,0,0,.8C4.1,17.09,7.9,20,12,20s7.9-2.91,9.92-7.6A1,1,0,0,0,21.92,11.6ZM12,18c-3.17,0-6.17-2.29-7.9-6C5.83,8.29,8.83,6,12,6s6.17,2.29,7.9,6C18.17,15.71,15.17,18,12,18ZM12,8a4,4,0,1,0,4,4A4,4,0,0,0,12,8Zm0,6a2,2,0,1,1,2-2A2,2,0,0,1,12,14Z"></path>
                                          </svg>
                                          Details
                                       </div>
                                 </div>
                              </a>

                              @php
                                 $temp_id = \Cookie::get('temp_id');
                                 $in_cart = \App\Models\Cart::where('product_id', $product->id)->where("temp_user_id", $temp_id)->first();
                              @endphp

                              @if (!$in_cart)
                                 <div class="productcard_productboxbtn productcard_productbtninactive">
                                    <button wire:click="$dispatchTo('cart-sidebar','cart-updated', {product_id: {{$product->id}}, type: '+'})" type="button" class="d-block w-100">Add to Bag</button>
                                 </div>
                              @else
                                 <div class="productcard_productboxbtn productcard_productbtnactive">
                                    <button wire:click="$dispatchTo('cart-sidebar','cart-updated', {product_id: {{$product->id}}, type: '-'})" type="button">-</button>
                                    <button type="button" class="d-block w-100">{{ $in_cart->quantity }} in Box</button>
                                    <button wire:click="$dispatchTo('cart-sidebar','cart-updated', {product_id: {{$product->id}}, type: '+'})" type="button">+</button>
                                 </div>
                              @endif



                              </div>
                              <!-- Modal -->
                                 <div wire:ignore.self class="modal fade" id="productModal{{$product->id}}" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
                                    <div class="modal-dialog  modal-xl">
                                    <div class="modal-content">
                                       <div class="modal-header">
                                          <h6 class="modal-title" id="productModalLabel">Product Details</h6>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                       </div>
                                       <div class="modal-body">
                                          <div class="product_productcontainer">
                                             <div class="row">
                                                   <div class="col-lg-6 text-center">
                                                      <img src="{{api_asset($product->thumbnail_img)}}" alt="Regular Daily Combo" class="img-fluid" />
                                                   </div>
                                                   <div class="col-lg-6">
                                                      <div class="mt-3">
                                                         <h6 class="product_productname">{{ $product->name }}</h6>
                                                         <div class="product_productunit">{{ $product->unit_value." ".$product->unit }}</div>
                                                         <h1 class="product_productprice">{{ currency_symbol().$product->unit_price }}</h1>
                                                         <hr />
                                                         <div class="product_qtychanger">
                                                               <div>
                                                                  <button type="button" wire:click="$dispatchTo('cart-sidebar','cart-updated', {product_id: {{$product->id}}, type: '-'})">
                                                                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M19,11H5a1,1,0,0,0,0,2H19a1,1,0,0,0,0-2Z"></path></svg>
                                                                  </button>
                                                                  <span>{{ $in_cart ? $in_cart->quantity : 0 }} in Bag</span>
                                                                  <button type="button" wire:click="$dispatchTo('cart-sidebar','cart-updated', {product_id: {{$product->id}}, type: '+'})">
                                                                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M19,11H13V5a1,1,0,0,0-2,0v6H5a1,1,0,0,0,0,2h6v6a1,1,0,0,0,2,0V13h6a1,1,0,0,0,0-2Z"></path></svg>
                                                                  </button>
                                                               </div>
                                                               <button data-bs-dismiss="modal" aria-label="Close" wire:click="$dispatchTo('cart-sidebar','cart-updated', {product_id: {{$product->id}}, type: '+', open_sidebar: true})" type="button" class="btn btn-primary my-3 px-5 fw-bold">Buy Now</button>
                                                         </div>
                                                         <hr />
                                                         <div>
                                                               <p>{!! $product->description !!}</p>
                                                         </div>
                                                      </div>
                                                   </div>
                                             </div>
                                          </div>

                                       </div>

                                    </div>
                                    </div>
                                 </div>
                           </div>
                     @endforeach
                  </div> 
               @else
                  <div class="mb-5">
                     <div class="d-flex align-items-center justify-content-center">
                        <div class="text-center">
                           <img src="{{asset('livewire/no-product-found.png')}}" alt="No product found" class="my-3" style="max-width: 150px">
                           <h4>No Products Found</h4>
                           <p>Your search did not match any products. Please try again.</p>
                           <a href="/" wire:navigate class="btn btn-primary px-5 py-2 mt-3 fw-bold">Return Home</a>
                        </div>
                     </div>
                  </div>
               @endif
            </div>
         </div>

      </main>

   </div>
</div>
