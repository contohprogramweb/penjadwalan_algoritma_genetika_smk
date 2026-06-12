<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – GAP Penjadwalan SMK</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,.1);
            padding: 40px;
            width: 420px;
            max-width: 95vw;
        }
        .card h2 {
            text-align: center;
            margin-bottom: 8px;
            color: #1a1a2e;
            font-size: 22px;
        }
        .card p.sub {
            text-align: center;
            color: #666;
            font-size: 13px;
            margin-bottom: 28px;
        }
        label { display: block; font-size: 13px; color: #444; margin-bottom: 5px; }
        input[type=text], input[type=password] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 18px;
            transition: border-color .2s;
        }
        input:focus { outline: none; border-color: #4f46e5; }
        button[type=submit] {
            width: 100%;
            padding: 11px;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            transition: background .2s;
        }
        button[type=submit]:hover { background: #4338ca; }
        button[type=submit]:disabled { background: #a5b4fc; cursor: not-allowed; }
        #alert-box {
            display: none;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        #alert-box.error   { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        #alert-box.success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
    </style>
</head>
<body>
<div class="card">
    <h2>GAP Penjadwalan SMK</h2>
    <p class="sub">Silakan masuk dengan akun Anda</p>

    <div id="alert-box"></div>

    <form id="form-login">
        <!-- CSRF token -->
        <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_token ?>">

        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password" required>

        <button type="submit" id="btn-login">Masuk</button>
    </form>
</div>

<script>
document.getElementById('form-login').addEventListener('submit', function(e) {
    e.preventDefault();

    var btn   = document.getElementById('btn-login');
    var alert = document.getElementById('alert-box');
    var form  = e.target;

    btn.disabled    = true;
    btn.textContent = 'Memproses...';
    alert.style.display = 'none';

    var data = new FormData(form);

    fetch('<?= site_url('auth/proses_login') ?>', {
        method : 'POST',
        body   : data,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(json) {
        if (json.success) {
            alert.className     = 'success';
            alert.textContent   = json.message;
            alert.style.display = 'block';
            setTimeout(function() {
                window.location.href = json.redirect_url;
            }, 800);
        } else {
            alert.className     = 'error';
            alert.textContent   = json.message || 'Login gagal.';
            alert.style.display = 'block';
            btn.disabled        = false;
            btn.textContent     = 'Masuk';
        }
    })
    .catch(function() {
        alert.className     = 'error';
        alert.textContent   = 'Koneksi ke server gagal. Coba lagi.';
        alert.style.display = 'block';
        btn.disabled        = false;
        btn.textContent     = 'Masuk';
    });
});
</script>
</body>
</html>
