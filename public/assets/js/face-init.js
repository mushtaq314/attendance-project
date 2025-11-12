// face-init.js - uses face-api.js CDN to capture descriptor and either register or login
// Assumes face-api.min.js is loaded via CDN in the page using this script

(async function(){
  // load models from CDN
  const MODEL_URL = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights';
  await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
  await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
  await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);

  async function getDescriptorFromCam() {
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    const video = document.createElement('video');
    video.srcObject = stream;
    video.play();
    // wait for few frames
    await new Promise(r => setTimeout(r, 1000));
    const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
    stream.getTracks().forEach(t => t.stop());
    if (!detection) throw new Error('No face detected');
    return detection.descriptor;
  }

  // face login button on login page
  const faceLoginBtn = document.getElementById('faceLoginBtn');
  if (faceLoginBtn) {
    faceLoginBtn.addEventListener('click', async ()=>{
      try {
        const desc = await getDescriptorFromCam();
        // send to server for match
        const res = await fetch('/api/match_descriptor.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({descriptor: Array.from(desc)})});
        const j = await res.json();
        if (j.match) {
          // if matched, programmatically login
          // create a form post to set session
          const f = document.createElement('form');
          f.method = 'POST'; f.action = '/public/auth/verify_face.php';
          const i = document.createElement('input'); i.type='hidden'; i.name='user_id'; i.value=j.user_id; f.appendChild(i);
          document.body.appendChild(f); f.submit();
        } else {
          alert('Face not recognized');
        }
      } catch (e) { alert('Face login failed: ' + e.message); }
    });
  }

  // Export function to window so register page can use
  window.captureDescriptorAndSend = async function(userId) {
    const descriptor = await getDescriptorFromCam();
    const body = { user_id: userId, descriptor: Array.from(descriptor) };
    const r = await fetch('/api/save_descriptor.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body)});
    return await r.json();
  }
})();
