# TODO: Add Face Capture Feature to Login and Registration

## Tasks
- [x] Create public/api/save_face.php: PHP script to receive base64 image and save as PNG in storage/faces/
- [x] Edit public/auth/login.php: Add video, button, canvas, img elements and JavaScript for face capture in face-login-section
- [x] Edit public/auth/register.php: Add video, button, canvas, img elements and JavaScript for face capture in face-capture-section
- [ ] Test camera access (requires HTTPS) and image upload functionality
- [x] Ensure storage/faces/ directory is writable and created if needed

## Notes
- Adapt IDs to avoid conflicts: faceVideo, faceCaptureBtn, faceCanvas, faceSnapshot
- Fetch URL: /attendance-project/public/api/save_face.php
- Integrate with existing face-api.js without conflicts
