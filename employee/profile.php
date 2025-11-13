<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
$u = current_user();
?>
<!doctype html>
<html>
<body>
<h3>Profile</h3>
<p>Name: <?=htmlspecialchars($u['name'])?></p>
<p>Email: <?=htmlspecialchars($u['email'])?></p>
<?php if (empty($u['face_descriptor'])): ?>
  <p>You haven't registered face yet. <button id="registerFaceBtn">Register Face</button></p>
  <script src="/attendance-project/public/assets/js/face-init.js"></script>
  <script>
    document.getElementById('registerFaceBtn').addEventListener('click', async ()=>{
      try{
        const r = await captureDescriptorAndSend(<?= $u['id'] ?>);
        if (r.ok) alert('Descriptor saved.'); else alert('Error');
        location.reload();
      } catch(e){alert(e.message)}
    });
  </script>
<?php else: ?>
  <p>Face registered ✓</p>
<?php endif; ?>
</body>
</html>
