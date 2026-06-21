<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0 rounded-4">

                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0 fw-bold">🔑 Password Reset</h3>
                        <p class="mb-0 mt-1 opacity-75">E-Services Platform</p>
                    </div>

                    <div class="card-body p-5 text-center">
                        <p class="fs-5">Hello <strong>{{ $user->first_name }}</strong>,</p>
                        <p class="text-muted">Use this code to reset your password:</p>

                        <div class="my-4 p-4 bg-light rounded-3 border">
                            <h1 class="display-3 fw-bold text-primary letter-spacing-3">
                                {{ $code }}
                            </h1>
                        </div>

                        <p class="text-muted small">
                            ⏰ This code expires in <strong>10 minutes</strong>.
                        </p>
                        <p class="text-muted small">
                            If you did not request this, ignore this email.
                        </p>
                    </div>

                    <div class="card-footer text-center text-muted small py-3">
                        © {{ date('Y') }} E-Services Platform. All rights reserved.
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>