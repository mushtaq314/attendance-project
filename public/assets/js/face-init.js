// face-init.js - uses face-api.js CDN to capture descriptor and either register or login
// Assumes face-api.min.js is loaded via CDN in the page using this script

// Define function immediately on window to avoid timing issues
window.captureDescriptorAndSend = async function(userId) {
  console.log('captureDescriptorAndSend called with userId:', userId);
  try {
    const descriptor = await getDescriptorFromCam();
    const body = { user_id: userId, descriptor: Array.from(descriptor) };
    const r = await fetch('/attendance-project/api/save_descriptor.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body)});
    const result = await r.json();
    console.log('Save descriptor result:', result);
    return result;
  } catch (e) {
    console.error('captureDescriptorAndSend error:', e);
    throw e;
  }
};

(async function(){
  // load models from CDN
  const MODEL_URL = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights';
  try {
    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
    console.log('Face API models loaded successfully');
  } catch (e) {
    console.error('Failed to load face API models:', e);
    return;
  }

  async function getDescriptorFromCam() {
    console.log('Opening camera modal...');
    // Create modal for camera display
    const modal = document.createElement('div');
    modal.style.cssText = `
      position: fixed; top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center;
      z-index: 9999;
    `;
    modal.innerHTML = `
      <div style="background: white; padding: 20px; border-radius: 10px; text-align: center;">
        <h5>Face Capture</h5>
        <p>Position your face in the camera and click Capture.</p>
        <video id="faceVideo" width="320" height="240" autoplay style="border: 1px solid #ccc;"></video>
        <br><br>
        <button id="captureBtn" style="margin-right: 10px;">Capture</button>
        <button id="cancelBtn">Cancel</button>
      </div>
    `;
    document.body.appendChild(modal);

    const video = document.getElementById('faceVideo');
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
    console.log('Camera stream started');

    return new Promise((resolve, reject) => {
      video.onloadedmetadata = () => {
        video.play();
        console.log('Video playing');
      };

      document.getElementById('captureBtn').onclick = async () => {
        console.log('Capture button clicked');
        try {
          const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
          console.log('Face detection result:', detection);
          stream.getTracks().forEach(t => t.stop());
          document.body.removeChild(modal);
          if (!detection) throw new Error('No face detected');
          resolve(detection.descriptor);
        } catch (e) {
          console.error('Capture error:', e);
          reject(e);
        }
      };

      document.getElementById('cancelBtn').onclick = () => {
        console.log('Cancel button clicked');
        stream.getTracks().forEach(t => t.stop());
        document.body.removeChild(modal);
        reject(new Error('Cancelled'));
      };
    });
  }

  // face login button on login page
  const faceLoginBtn = document.getElementById('faceLoginBtn');
  if (faceLoginBtn) {
    faceLoginBtn.addEventListener('click', async ()=>{
      console.log('Face login button clicked');
      try {
        const desc = await getDescriptorFromCam();
        console.log('Got descriptor for login:', desc.length);
        // send to server for match
        const res = await fetch('/attendance-project/api/match_descriptor.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({descriptor: Array.from(desc)})});
        const j = await res.json();
        console.log('Match result:', j);
        if (j.match) {
          // if matched, programmatically login
          // create a form post to set session
          const f = document.createElement('form');
          f.method = 'POST'; f.action = '/attendance-project/public/auth/verify_face.php';
          const i = document.createElement('input'); i.type='hidden'; i.name='user_id'; i.value=j.user_id; f.appendChild(i);
          document.body.appendChild(f); f.submit();
        } else {
          alert('Face not recognized');
        }
      } catch (e) { alert('Face login failed: ' + e.message); }
    });
  }

  // Update the function to use the loaded models
  window.captureDescriptorAndSend = async function(userId) {
    console.log('Updated captureDescriptorAndSend called with userId:', userId);
    const descriptor = await getDescriptorFromCam();
    const body = { user_id: userId, descriptor: Array.from(descriptor) };
    const r = await fetch('/attendance-project/api/save_descriptor.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body)});
    return await r.json();
  };
})();
