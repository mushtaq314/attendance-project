// app.js - common utilities: location tracking & offline sync

// send location to server
async function sendLocation(userId, action) {
  if (!navigator.geolocation) return;
  navigator.geolocation.getCurrentPosition(async (pos)=>{
    const data = { user_id: userId, lat: pos.coords.latitude, lng: pos.coords.longitude, accuracy: pos.coords.accuracy, provider: 'geolocation', action: action };
    try {
      await fetch('/api/save_location.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)});
    } catch (e) {
      // offline: save to localStorage queue
      const q = JSON.parse(localStorage.getItem('offline_queue') || '[]');
      q.push(data); localStorage.setItem('offline_queue', JSON.stringify(q));
    }
  }, (err)=>{ console.warn('geo error', err); });
}

// sync offline queue when back online
window.addEventListener('online', async ()=>{
  const q = JSON.parse(localStorage.getItem('offline_queue') || '[]');
  while (q.length) {
    const item = q.shift();
    try { await fetch('/api/save_location.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(item)}); } catch(e) { q.unshift(item); break; }
  }
  localStorage.setItem('offline_queue', JSON.stringify(q));
});

// live location pinger while logged in
function startLivePing(userId) {
  if (!userId) return;
  sendLocation(userId, 'login');
  window._livePingInterval = setInterval(()=> sendLocation(userId, 'heartbeat'), 30*1000);
}
function stopLivePing(userId) {
  clearInterval(window._livePingInterval);
  sendLocation(userId, 'logout');
}
