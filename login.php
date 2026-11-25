<?php
require_once __DIR__.'/includes/auth.php';
if (current_user()) redirect(current_user()['role']==='admin'?'admin/dashboard.php':'student/dashboard.php');
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $stmt=$pdo->prepare("SELECT id,name,email,password_hash,role,is_active FROM users WHERE email=? LIMIT 1");
    $stmt->execute([trim($_POST['email']??'')]); $u=$stmt->fetch();
    if($u && $u['is_active'] && password_verify($_POST['password']??'', $u['password_hash'])){
        session_regenerate_id(true); unset($u['password_hash']); $_SESSION['user']=$u;
        redirect($u['role']==='admin'?'admin/dashboard.php':'student/dashboard.php');
    } else $error='Invalid login details or inactive account.';
}
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login | LabDesk</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="assets/css/style.css" rel="stylesheet"></head><body><div class="auth-wrap"><div class="auth-card"><a class="brand" href="index.php">◈ LabDesk</a><h1>Welcome back</h1><p class="muted mb-4">Sign in to your laboratory portal.</p><?php if($error):?><div class="alert alert-danger"><?=e($error)?></div><?php endif?><form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" required><label class="form-label">Password</label><div class="input-group mb-4"><input id="password" class="form-control" type="password" name="password" required><button class="btn btn-outline-light" type="button" data-toggle-password="password">Show</button></div><button class="btn btn-primary w-100 py-2">Sign in</button></form><p class="muted mt-4 mb-0">Student? <a href="register.php">Create an account</a></p></div></div><script src="assets/js/main.js"></script></body></html>
