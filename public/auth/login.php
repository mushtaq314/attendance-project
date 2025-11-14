<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();

$success = '';
$error = '';





// Check for registration success message
if (isset($_GET['registered'])) {
    $success = 'Registration successful! Your account is pending admin approval. You will be notified once approved.';
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Face Recognition Login - Attendance System</title>
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
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            position: relative;
        }
        .login-header {
            background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%);
            color: white;
            padding: 2.5rem;
            text-align: center;
            position: relative;
        }
        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="60" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.3;
        }
        .login-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
            position: relative;
            z-index: 1;
        }
        .login-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }
        .login-body {
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
        .btn-login {
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
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
        }
        .btn-login:disabled {
            opacity: 0.7;
            transform: none;
        }
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }
        .divider span {
            background: white;
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .face-login-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        .btn-face-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-face-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        .btn-face-login:disabled {
            opacity: 0.7;
            transform: none;
        }
        .register-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }
        .register-link a {
            color: #FF6B6B;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .register-link a:hover {
            color: #4ECDC4;
        }
        .alert {
            border-radius: 12px;
            border: none;
        }
        .face-instructions {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
            text-align: left;
        }
        .face-instructions h6 {
            color: #495057;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .face-instructions ul {
            margin: 0;
            padding-left: 1.2rem;
        }
        .face-instructions li {
            margin-bottom: 0.5rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .face-instructions i {
            color: #28a745;
            margin-right: 0.5rem;
        }
        @media (max-width: 576px) {
            .login-card {
                margin: 1rem;
                max-width: none;
            }
            .login-header,
            .login-body {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-sign-in-alt fa-3x mb-3"></i>
                        <h2>Face Recognition Login</h2>
                        <p>Use face recognition to sign in</p>
                    </div>
                    <div class="login-body">
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



                        <div class="face-login-section">
                            <button type="button" class="btn btn-face-login" id="faceLoginBtn">
                                <i class="fas fa-camera me-2"></i>Login with Face Recognition
                            </button>

                            <div class="face-instructions" id="faceInstructions" style="display: none;">
                                <h6>
                                    <i class="fas fa-info-circle"></i>Face Recognition Instructions
                                </h6>
                                <ul>
                                    <li><i class="fas fa-check"></i>Ensure good lighting on your face</li>
                                    <li><i class="fas fa-check"></i>Look directly at the camera</li>
                                    <li><i class="fas fa-check"></i>Keep your eyes open and natural</li>
                                    <li><i class="fas fa-check"></i>Remove glasses if they interfere</li>
                                    <li><i class="fas fa-check"></i>Stay still during capture</li>
                                    <li><i class="fas fa-check"></i>Smile naturally for better recognition</li>
                                </ul>
                            </div>

                            <!-- Face Capture Elements -->
                            <div id="faceCaptureSection" style="display: none; text-align: center; margin-top: 1rem;">
                                <video id="faceVideo" width="320" height="240" autoplay style="border: 2px solid #FF6B6B; border-radius: 10px;"></video>
                                <br>
                                <button id="faceCaptureBtn" class="btn btn-primary mt-3">
                                    <i class="fas fa-camera me-2"></i>Capture Face
                                </button>
                            </div>
                            <canvas id="faceCanvas" width="640" height="480" style="display:none;"></canvas>
                            <img id="faceSnapshot" alt="snapshot" style="display:none;"/>
                        </div>

                        <div class="register-link">
                            <p class="mb-0">
                                Don't have an account?
                                <a href="/attendance-project/public/auth/register.php">Register here</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load face-api models
        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights'),
            faceapi.nets.faceLandmark68Net.loadFromUri('https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights'),
            faceapi.nets.faceRecognitionNet.loadFromUri('https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights')
        ]).then(() => {
            console.log('Face API models loaded');
        }).catch(err => {
            console.error('Error loading face API models:', err);
        });

        // Face login functionality
        document.getElementById('faceLoginBtn').addEventListener('click', function() {
            const instructions = document.getElementById('faceInstructions');
            const captureSection = document.getElementById('faceCaptureSection');
            const btn = this;

            if (instructions.style.display === 'none') {
                instructions.style.display = 'block';
                captureSection.style.display = 'block';
                btn.innerHTML = '<i class="fas fa-times me-2"></i>Cancel Face Login';
                btn.classList.remove('btn-face-login');
                btn.classList.add('btn-secondary');

                // Start camera when showing capture section
                startCamera();
            } else {
                instructions.style.display = 'none';
                captureSection.style.display = 'none';
                btn.innerHTML = '<i class="fas fa-camera me-2"></i>Login with Face Recognition';
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-face-login');

                // Stop camera when hiding capture section
                stopCamera();
            }
        });

        // Auto-hide success message after 5 seconds
        setTimeout(function() {
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                successAlert.style.display = 'none';
            }
        }, 5000);

        // Face capture functionality
        let stream = null;
        const faceVideo = document.getElementById('faceVideo');
        const faceCanvas = document.getElementById('faceCanvas');
        const faceSnapshotImg = document.getElementById('faceSnapshot');
        const faceCaptureBtn = document.getElementById('faceCaptureBtn');

        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: true })
            .then(newStream => {
                stream = newStream;
                faceVideo.srcObject = stream;
                faceVideo.play();
            })
            .catch(err => {
                console.error("Error accessing camera: ", err);
                alert("Cannot access camera. Please check permissions and that you are on HTTPS.");
            });
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
                faceVideo.srcObject = null;
            }
        }

        faceCaptureBtn.addEventListener('click', async () => {
            if (!stream) {
                alert('Camera not active. Please try again.');
                return;
            }

            // Disable button during processing
            faceCaptureBtn.disabled = true;
            faceCaptureBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            try {
                // Detect face and get descriptor
                const detection = await faceapi.detectSingleFace(faceVideo, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (!detection) {
                    alert('No face detected. Please ensure your face is clearly visible and try again.');
                    faceCaptureBtn.disabled = false;
                    faceCaptureBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Capture Face';
                    return;
                }

                // Fetch stored face descriptors
                const response = await fetch('/attendance-project/public/api/fetch_descriptors.php');
                const users = await response.json();

                // Find matching user
                let matchedUser = null;
                let minDistance = Infinity;

                for (const user of users) {
                    if (user.descriptor) {
                        const distance = faceapi.euclideanDistance(detection.descriptor, user.descriptor);
                        if (distance < 0.6 && distance < minDistance) { // Threshold for matching
                            minDistance = distance;
                            matchedUser = user;
                        }
                    }
                }

                if (matchedUser) {
                    // Login the user
                    const loginResponse = await fetch('/attendance-project/public/api/accept_login.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: matchedUser.id })
                    });

                    const loginData = await loginResponse.json();

                    if (loginData.success) {
                        alert(`Welcome back, ${loginData.name}!`);
                        window.location.href = '/attendance-project/employee/index.php';
                    } else {
                        alert('Login failed. Please try again.');
                    }
                } else {
                    alert('Face not recognized. Please ensure you are registered or try again.');
                }

            } catch (err) {
                console.error('Face recognition error:', err);
                alert('Face recognition failed. Please try again.');
            } finally {
                faceCaptureBtn.disabled = false;
                faceCaptureBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Capture Face';
            }
        });
    </script>
</body>
</html>
