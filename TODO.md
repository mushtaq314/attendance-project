# TODO: Implement Face Recognition Login System

## Tasks
- [x] Create public/api/save_face.php: PHP script to receive base64 image and save as PNG in storage/faces/
- [x] Edit public/auth/login.php: Add video, button, canvas, img elements and JavaScript for face capture in face-login-section
- [x] Edit public/auth/register.php: Add video, button, canvas, img elements and JavaScript for face capture in face-capture-section
- [x] Create public/api/save_descriptor.php: PHP script to save face descriptors to database
- [x] Update public/api/fetch_descriptors.php: Fetch face descriptors from database
- [x] Update public/api/accept_login.php: Handle face recognition login
- [x] Update login.php to use face recognition for authentication
- [x] Update register.php to capture and save face descriptors
- [x] Load face-api.js models from CDN instead of local files
- [x] Test camera access (requires HTTPS) and face recognition functionality
- [x] Ensure storage/faces/ directory is writable and created if needed

## Notes
- Adapt IDs to avoid conflicts: faceVideo, faceCaptureBtn, faceCanvas, faceSnapshot
- Fetch URL: /attendance-project/public/api/save_face.php
- Integrate with existing face-api.js without conflicts
- Face recognition uses Euclidean distance with threshold of 0.6 for matching
