<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Muslim Town</title>
  </head>
  <body>

    <header id="header">
        <div class="container">
            <nav class="navbar">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                            {{-- <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" viewBox="0 0 23 19"><rect y="16" width="23" height="3" rx="1.5" fill="#555"></rect><rect width="23" height="3" rx="1.5" fill="#555"></rect><rect y="8" width="23" height="3" rx="1.5" fill="#555"></rect></svg> --}}
                        </button>
                       <a class="navbar-brand py-0" href="{{ url('/') }}">
                          <img src="{{ asset('assets/images/logo.png') }}" alt="thl-logo" title="Lewis & Jordan Private Investigator" class="img-fluid" width="150">
                       </a>
                      <a href=""></a>
                    </div>
                </div>
            </nav>
            {{-- <form action="{{ url('/')}}" method="POST">
                @csrf
                <h1 class="text-center">Contact Us</h1>
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success')}}</div>
                @endif
                <div class="mb-3">
                  <label for="name" class="form-label">Name</label>
                  <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" required>
                  <span class="text-danger">
                    @error('name')
                        {{ $message }}
                    @enderror
                  </span>
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelp" required>
                  <span class="text-danger">
                    @error('email')
                        {{ $message }}
                    @enderror
                  </span>
                </div>
                <div class="mb-3">
                    <label for="area" class="form-label">Description</label>
                    <textarea class="form-control" id="area" rows="3"></textarea>
                  </div>
                <button type="submit" class="btn btn-primary">Submit</button>
              </form> --}}
        </div>
    </header>

    <div class="container">
        <div class="row">
            <div class="com-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ url('/')}}" method="POST">
                            @csrf
                            <h1 class="text-center">Contact Us</h1>
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success')}}</div>
                            @endif
                            <div class="mb-3">
                              <label for="name" class="form-label">Name</label>
                              <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" required>
                              <span class="text-danger">
                                @error('name')
                                    {{ $message }}
                                @enderror
                              </span>
                            </div>
                            <div class="mb-3">
                              <label for="email" class="form-label">Email</label>
                              <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelp" required>
                              <span class="text-danger">
                                @error('email')
                                    {{ $message }}
                                @enderror
                              </span>
                            </div>
                            <div class="mb-3">
                                <label for="area" class="form-label">Description</label>
                                <textarea class="form-control" id="area" rows="3"></textarea>
                              </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                          </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


  </body>
</html>
