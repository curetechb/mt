<div class="container py-3">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0">
                <div class="card-body">
                    <form wire:submit="sendMail" method="POST">
                        @csrf
                        <h4 class="text-center mb-3">Contact Us</h4>
                        @if ($success)
                            <div class="alert alert-success">Message sent successfully!</div>
                        @endif
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input wire:model="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name"
                                aria-describedby="nameHelp" required value="{{ old('name') }}">
                                @error('name')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input wire:model="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone"
                                aria-describedby="phoneHelp" required value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email"
                                aria-describedby="emailHelp" required value="{{ old('email') }}">
                                @error('email')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                        </div>
                        <div class="mb-3">
                            <label for="area" class="form-label">Description</label>
                            <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
