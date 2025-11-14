<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // Validation
    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Check if email already exists
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $existing_user = $stmt->fetch();
        if ($existing_user) {
            // Check if user has face descriptor set up
            if (!empty($existing_user['face_descriptor'])) {
                $error = "Email address is already registered and face recognition is set up.";
            } else {
                // Allow face setup for existing user without face
                $user_id = $existing_user['id'];
                $face_capture = true;
                $success = "Email already registered. Please set up your face recognition.";
            }
        } else {
            try {
                $stmt = db()->prepare('INSERT INTO users (name,email,password,role,approved) VALUES (?,?,?,?,?)');
                $stmt->execute([$name,$email,NULL,'employee',0]);
                $user_id = db()->lastInsertId();
                $face_capture = true;
                $success = "Registration successful! Now, please set up your face recognition.";
            } catch (Exception $e) {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Registration - Attendance System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            position: relative;
        }
        .register-header {
            background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%);
            color: white;
            padding: 2.5rem;
            text-align: center;
            position: relative;
        }
        .register-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="60" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.3;
        }
        .register-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
            position: relative;
            z-index: 1;
        }
        .register-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }
        .register-body {
            padding: 2.5rem;
        }
        .form-floating {
            margin-bottom: 1.5rem;
        }
        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 1rem 1.2rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #FF6B6B;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .btn-register {
            background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
            color: white;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
        }
        .btn-register:disabled {
            opacity: 0.7;
            transform: none;
        }
        .login-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }
        .login-link a {
            color: #FF6B6B;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .login-link a:hover {
            color: #4ECDC4;
        }
        .alert {
            border-radius: 12px;
            border: none;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            color: #6c757d;
        }
        .form-control:focus + .input-group-text,
        .input-group-text:focus-within {
            border-color: #FF6B6B;
        }
        .password-strength {
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        .password-strength.weak { color: #dc3545; }
        .password-strength.medium { color: #ffc107; }
        .password-strength.strong { color: #28a745; }
        @media (max-width: 576px) {
            .register-card {
                margin: 1rem;
                max-width: none;
            }
            .register-header,
            .register-body {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="register-card">
                    <div class="register-header">
                        <i class="fas fa-user-plus fa-3x mb-3"></i>
                        <h2>Join Our Team</h2>
                        <p>Create your employee account</p>
                    </div>
                    <div class="register-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= htmlspecialchars($success) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($face_capture) && $face_capture): ?>
                            <div class="face-capture-section text-center">
                                <h5 class="mb-3">Set Up Face Recognition</h5>
                                <p class="text-muted mb-4">Please look at the camera to capture your face for login.</p>
                                <button type="button" class="btn btn-primary btn-lg" id="captureFaceBtn">
                                    <i class="fas fa-camera me-2"></i>Capture Face
                                </button>
                                <div id="faceCaptureStatus" class="mt-3"></div>

                                <!-- Face Capture Elements -->
                                <div id="regFaceCaptureSection" style="display: none; text-align: center; margin-top: 1rem;">
                                    <video id="regFaceVideo" width="320" height="240" autoplay style="border: 2px solid #FF6B6B; border-radius: 10px;"></video>
                                    <br>
                                    <button id="regFaceCaptureBtn" class="btn btn-primary mt-3">
                                        <i class="fas fa-camera me-2"></i>Capture Face
                                    </button>
                                </div>
                                <canvas id="regFaceCanvas" width="640" height="480" style="display:none;"></canvas>
                                <img id="regFaceSnapshot" alt="snapshot" style="display:none;"/>
                            </div>
                        <?php endif; ?>

                        <?php if (!isset($face_capture) || !$face_capture): ?>
                            <div class="mb-4">
                                <p class="text-muted mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Face recognition will be set up after registration and admin approval.
                                </p>
                            </div>
                        <?php endif; ?>

                        <form method="post" novalidate id="registerForm">
                            <div class="form-floating mb-3">
                                <input type="text" name="name" id="name" class="form-control" placeholder="Full Name" required
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                <label for="name">
                                    <i class="fas fa-user me-2"></i>Full Name
                                </label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email Address" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                <label for="email">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                            </div>

                            <div class="mb-4">
                                <p class="text-muted mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Face recognition will be set up after registration.
                                </p>
                            </div>

                            <button type="submit" class="btn btn-register" id="registerBtn">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </button>
                        </form>

                        <div class="login-link">
                            <p class="mb-2 text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                After registration, set up your face recognition for login.
                                Your account will be reviewed and approved by an administrator.
                            </p>
                            <p class="mb-0">
                                Already have an account?
                                <a href="/attendance-project/public/auth/login.php">Login here</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('registerBtn');
            const inputs = this.querySelectorAll('input[required]');

            let isValid = true;
            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                return false;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';
        });

        // Remove invalid class on input
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                }
            });
        });

        // Face capture for registration
        const captureBtn = document.getElementById('captureFaceBtn');
        if (captureBtn) {
            captureBtn.addEventListener('click', function() {
                const captureSection = document.getElementById('regFaceCaptureSection');
                const status = document.getElementById('faceCaptureStatus');

                if (captureSection.style.display === 'none') {
                    captureSection.style.display = 'block';
                    this.innerHTML = '<i class="fas fa-times me-2"></i>Cancel Capture';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-secondary');

                    // Start camera when showing capture section
                    startRegCamera();
                } else {
                    captureSection.style.display = 'none';
                    this.innerHTML = '<i class="fas fa-camera me-2"></i>Capture Face';
                    this.classList.remove('btn-secondary');
                    this.classList.add('btn-primary');

                    // Stop camera when hiding capture section
                    stopRegCamera();
                }
            });
        }

        // Load face-api models
        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/gh/justadudewohacks/face-api.js@master/weights'),
            faceapi.nets.faceLandmark68Net.loadFromUri('https://cdn.jsdelivr.net/gh/justadudewohacks/face-api.js@master/weights'),
            faceapi.nets.faceRecognitionNet.loadFromUri('https://cdn.jsdelivr.net/gh/justadudewohacks/face-api.js@master/weights')
        ]).then(() => {
            console.log('Face API models loaded');
        }).catch(err => {
            console.error('Error loading face API models:', err);
        });

        // Additional face capture functionality
        let regStream = null;
        const regFaceVideo = document.getElementById('regFaceVideo');
        const regFaceCanvas = document.getElementById('regFaceCanvas');
        const regFaceSnapshotImg = document.getElementById('regFaceSnapshot');
        const regFaceCaptureBtn = document.getElementById('regFaceCaptureBtn');

        function startRegCamera() {
            navigator.mediaDevices.getUserMedia({ video: true })
            .then(newStream => {
                regStream = newStream;
                regFaceVideo.srcObject = regStream;
                regFaceVideo.play();
            })
            .catch(err => {
                console.error("Error accessing camera: ", err);
                alert("Cannot access camera. Please check permissions and that you are on HTTPS.");
            });
        }

        function stopRegCamera() {
            if (regStream) {
                regStream.getTracks().forEach(track => track.stop());
                regStream = null;
                regFaceVideo.srcObject = null;
            }
        }

        regFaceCaptureBtn.addEventListener('click', async () => {
            if (!regStream) {
                alert('Camera not active. Please try again.');
                return;
            }

            // Disable button during processing
            regFaceCaptureBtn.disabled = true;
            regFaceCaptureBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            try {
                // Detect face and get descriptor
                const detection = await faceapi.detectSingleFace(regFaceVideo, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (!detection) {
                    alert('No face detected. Please ensure your face is clearly visible and try again.');
                    regFaceCaptureBtn.disabled = false;
                    regFaceCaptureBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Capture Face';
                    return;
                }

                // Save descriptor to server
                const response = await fetch('/attendance-project/public/api/save_descriptor.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: <?php echo isset($user_id) ? $user_id : 'null'; ?>, descriptor: Array.from(detection.descriptor) })
                });

                const data = await response.json();

                if (data.ok) {
                    alert('Face captured and saved successfully! You can now login with face recognition.');
                    window.location.href = '/attendance-project/public/auth/login.php?registered=1';
                } else {
                    alert('Failed to save face descriptor. Please try again.');
                }

            } catch (err) {
                console.error('Face capture error:', err);
                alert('Face capture failed. Please try again.');
            } finally {
                regFaceCaptureBtn.disabled = false;
                regFaceCaptureBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Capture Face';
            }
        });
    </script>
</body>
</html>
