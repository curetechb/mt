<div class="container-fluid my-3">
    <div>
       <div class="row flex justify-center">
          <div class="col-md-5 mx-auto">
             <div class="purchased_purchased">
                <div class="text-center pt-4 ">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M14.72,8.79l-4.29,4.3L8.78,11.44a1,1,0,1,0-1.41,1.41l2.35,2.36a1,1,0,0,0,.71.29,1,1,0,0,0,.7-.29l5-5a1,1,0,0,0,0-1.42A1,1,0,0,0,14.72,8.79ZM12,2A10,10,0,1,0,22,12,10,10,0,0,0,12,2Zm0,18a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"></path>
                   </svg>
                   <h1>Thank You for Your Order!</h1>
                </div>
                <div>
                   <div>
                      <div>
                         <div class="p-3" style="background: rgb(248, 248, 248);">
                            <h5 class="text-center">Order Details</h5>
                            <hr>
                            <table class="table">
                               <tbody>
                                  <tr>
                                     <td class="pe-2">Order Number:</td>
                                     <td>#{{ $order->code }}</td>
                                  </tr>
                                  <tr>
                                     <td class="pe-2">Total:</td>
                                     <td><span class="pe-2 fw-bold">{{ currency_symbol().$order->grand_total }}</span></td>
                                  </tr>
                                  {{-- <tr>
                                    <td class="pe-2">Payment Type:</td>
                                    <td><span class="pe-2 fw-bold">{{ ucwords(str_replace("_", " ", $order->payment_type)) }}</span></td>
                                 </tr> --}}
                                  <tr>
                                     <td class="pe-2">Payment Status:</td>
                                     <td><span class="text-danger fw-bold">{{ ucwords($order->payment_status) }}</span></td>
                                  </tr>
                                  <tr>
                                    <td class="pe-2">Delivery Status:</td>
                                    <td><span class="text-primary fw-bold">{{ ucwords(str_replace("_", " ", $order->delivery_status)) }}</span></td>
                                 </tr>
                                  @php
                                  $shipping_details = json_decode($order->shipping_address);
                                  @endphp
                                  <tr>
                                    <td class="pe-2">Name:</td>
                                    <td>{{ $shipping_details->name }}</td>
                                 </tr>
                                 <tr>
                                    <td class="pe-2">Phone Number:</td>
                                    <td>{{ $shipping_details->phone }}</td>
                                 </tr>
                                  <tr>
                                     <td class="pe-2">Address:</td>
                                     <td>{{ $shipping_details->address }}</td>
                                  </tr>
                               </tbody>
                            </table>
                         </div>
                         <a class="btn btn-primary w-100 mt-2 py-2s" href="/" wire:navigate>Back to shopping</a>
                      </div>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>