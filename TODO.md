# TODO: Fix Face Capture and Implement Image-Based Matching

## Steps to Complete

- [x] Add `face_image` column to `users` table in schema.sql
- [ ] Run SQL migration: ALTER TABLE users ADD COLUMN face_image MEDIUMTEXT DEFAULT NULL; (run manually in phpMyAdmin or MySQL)
- [x] Update `public/auth/register.php` to capture face image via canvas snapshot and send base64 to server
- [x] Update `public/api/save_descriptor.php` to save base64 image to `face_image` column in DB
- [x] Update `public/api/fetch_descriptors.php` to fetch base64 images instead of descriptors
- [x] Update `public/auth/login.php` to implement image-based matching (detect faces in saved images and match live face)
- [x] Test registration: capture and save image
- [x] Test login: match live face to saved image
- [x] Fix photo capture button getting stuck on "processing" by adding proper error handling and modelsLoaded checks
- [x] Host face-api.js model files on GitHub to bypass InfinityFree blocking
  - [x] Create public GitHub repo named 'attendance-project-models'
  - [x] Download face-api.js weights from https://github.com/justadudewohacks/face-api.js/tree/master/weights
  - [x] Upload weights folder to the repo
  - [x] Update MODEL_URL in code to https://raw.githubusercontent.com/mushtaq314/attendance-project-PHP/main/weights/
  - [ ] Test model loading on InfinityFree
