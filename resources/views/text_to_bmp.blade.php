<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text to BMP Converter</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <h1 class="text-center mb-4">Convert Text to Image</h1>

                <!-- Form to input text -->
                <form action="{{ route('convert-to-bmp') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="text" class="form-label">Enter Text</label>
                        <input type="text" name="text" id="text" class="form-control" required placeholder="Enter text to be converted to Image">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS (Optional for components like modals, tooltips, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
