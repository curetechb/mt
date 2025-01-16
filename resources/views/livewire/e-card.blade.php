<div class="container my-3">
 
    <div class="checkout_checkoutsection">
        <div class="delivery-address-section mb-3">
            <div class="pt-3">
                <hr>
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="text-black">Delivery Address</h6>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 mx-auto py-4">
            
                            <div class="mb-3">
                                <select wire:change="updateOccasion" wire:model="occasion" name="occasion" id="occasion" class="form-select">                                
                                    @foreach ($images as $key => $imageName)
                                        <option value="{{$key}}">{{ $key }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">@error('occasion') {{ $message }} @enderror</div>
                            </div>
                            <div class="mb-3">
                                <input wire:keydown="updateGreetingCard" type="text" wire:model="to" name="to" class="form-control shadow-none @error('to') is-invalid @enderror" placeholder="To">
                                <div class="invalid-feedback">@error('to') {{ $message }} @enderror</div>
                            </div>
                           
                    </div>
                </div>

                <div id="capture" class="bday-card-container mx-auto">
                    <div class="bday-card">
  
                        <!-- Top part of the card: image + decorations -->
                        <div class="bday-decor--container">
                        
                            <div class="bday-pic "> 
                                <img class="img-fluid" src="{{$image}}">
                            </div>
                        
                            {{-- <p class="bday-decor bday-decor--top-right float">🎈</p>
                            <p class="bday-decor bday-decor--top-left spin">🌼</p> --}}
                        
                        </div> 
                        
                        
                        <!-- Banner --> 
                        <h1 class="bday-banner">
                            <span>Happy</span> <span>{{$occasion}}</span> <span>{{$to}}</span>
                        </h1> 
                    
                    </div>

                    
                </div>
                <div class="d-flex align-items-center justify-content-center my-4">
                    <div>
                        <a id="captureBtn" href="" class="btn btn-primary border-0 rounded-0">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01"/>
                            </svg>
                            <span>Download</span>
                        </a>
                    </div>
                </div>
                
                
                  

            </div>
        </div>
    </div>

 </div>

@push("scripts")
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.getElementById('captureBtn').addEventListener('click', function (e) {
    e.preventDefault();

    const element = document.getElementById('capture'); // The element to capture

    html2canvas(element).then((canvas) => {
      // Convert the canvas to an image
      const imageData = canvas.toDataURL('image/png');

      // Create a link to download the image
      const link = document.createElement('a');
      link.href = imageData;
      link.download = 'screenshot.png';
      link.click();
    });
  });
</script>
@endpush