<div class="container my-3">

    <div class="checkout_checkoutsection">
        <div class="delivery-address-section mb-3">
            <div class="pt-3">
                <hr>
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="text-black">Send Greeting Card</h6>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 mx-auto py-4">

                        <div class="mb-3">
                            <select wire:change="updateOccasion" wire:model="occasion" name="occasion" id="occasion"
                                class="form-select">
                                @foreach ($images as $key => $imageName)
                                    <option value="{{ $key }}">{{ $key }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                @error('occasion')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <input wire:keydown="updateGreetingCard" type="text" wire:model="to" name="to"
                                class="form-control shadow-none @error('to') is-invalid @enderror" placeholder="To">
                            <div class="invalid-feedback">
                                @error('to')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <input wire:keydown="updateGreetingCard" type="text" wire:model="from" name="from"
                                class="form-control shadow-none @error('from') is-invalid @enderror" placeholder="From">
                            <div class="invalid-feedback">
                                @error('from')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div id="capture" class="bday-card-container mx-auto">
                    <div class="bday-card">

                        <!-- Top part of the card: image + decorations -->
                        <div class="bday-decor--container">

                            <div class="bday-pic ">
                                <img class="img-fluid" src="{{ $image }}">
                            </div>

                            {{-- <p class="bday-decor bday-decor--top-right float">🎈</p>
                            <p class="bday-decor bday-decor--top-left spin">🌼</p> --}}

                        </div>


                        <!-- Banner -->
                        <h1 class="bday-banner">
                            <span>Happy</span> <span>{{ $occasion }}</span> <span>{{ $to }}</span>
                        </h1>

                    </div>


                </div>
                <div class="d-flex align-items-center justify-content-center my-4">
                    <div>
                        {{-- <a id="captureBtn" href="" class="btn btn-primary border-0">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01"/>
                            </svg>
                            <span>Download</span>
                        </a> --}}
                        {{-- <a id="captureBtn" href="" class="btn border-0 bg-email text-white px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 14l11 -11" />
                                <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                            </svg>
                            <span>E-Mail</span>
                        </a>
                        <a id="shareButton" href="https://www.facebook.com/sharer/sharer.php?u="
                            class="btn border-0 bg-facebook text-white px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-brand-facebook">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
                            </svg>
                            <span>Facebook</span>
                        </a> --}}
                        <a id="sendButton" href="" class="btn border-0 bg-greeting text-white px-5 py-2 rounded-pill">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-share"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M18 6m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M8.7 10.7l6.6 -3.4" /><path d="M8.7 13.3l6.6 3.4" /></svg>
                            <span>Share</span>
                        </a>
                    </div>
                </div>




            </div>
        </div>
    </div>

</div>

@push('scripts')

    {{-- html2canvas --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    {{-- Download --}}
    <script>
        document.getElementById('captureBtn').addEventListener('click', function(e) {
            e.preventDefault();

            const element = document.getElementById('capture'); // The element to capture

            html2canvas(element).then((canvas) => {
                // // Convert the canvas to an image
                // const imageData = canvas.toDataURL('image/png');

                // // Create a link to download the image
                // const link = document.createElement('a');
                // link.href = imageData;
                // link.download = 'greeting-card.png';
                // link.click();

                canvas.toBlob(function(blob) {
                    const reader = new FileReader();
                    reader.onloadend = () => {
                        const base64data = reader.result;
                        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(base64data));
                    }
                    reader.readAsDataURL(blob);
                });

            });
        });
    </script>

    {{-- Share to Facebook --}}
    <script>
        document.getElementById('shareButton').addEventListener('click', function(e) {
            e.preventDefault();
            const imageUrl = "{{ $image }}"; // Replace with your image URL
            const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(imageUrl)}`;
            window.open(shareUrl, '_blank');
        });
    </script>

    {{-- Send Through Messenger --}}
    <script>
        // Helper function to convert Base64 to Blob
        function base64ToBlob(base64Data) {
            const byteString = atob(base64Data.split(",")[1]); // Decode base64
            const mimeString = base64Data.split(",")[0].split(":")[1].split(";")[0]; // Extract MIME type
            const arrayBuffer = new ArrayBuffer(byteString.length);
            const uintArray = new Uint8Array(arrayBuffer);

            for (let i = 0; i < byteString.length; i++) {
                uintArray[i] = byteString.charCodeAt(i);
            }

            return new Blob([uintArray], {
                type: mimeString
            });
        }

        document.getElementById('sendButton').addEventListener('click', function(e) {

            e.preventDefault();

            const element = document.getElementById('capture'); // The element to capture

            html2canvas(element).then((canvas) => {

                // Step 1: Convert canvas to Base64 data URL
                const base64Image = canvas.toDataURL('image/png');

                // Step 2: Convert Base64 data URL to Blob
                const blob = base64ToBlob(base64Image);

                // Step 3: Create a File object
                const file = new File([blob], "greeting.png", {
                    type: "image/png"
                });

                // Step 4: Use navigator.share
                if (navigator.share) {
                    navigator
                        .share({
                            title: "Shared Canvas Image",
                            text: "Check out this canvas image!",
                            files: [file], // File object here
                        })
                        .then(() => console.log("Image shared successfully!"))
                        .catch((error) => console.error("Error sharing image:", error));
                } else {
                    console.error("Web Share API not supported in this browser.");
                }

            });

        });
    </script>
@endpush
