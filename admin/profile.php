<?php
require_once __DIR__.'/../includes/auth.php'; require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$pdo->prepare("UPDATE users SET name=? WHERE id=?")->execute([trim($_POST['name']),current_user()['id']]);$_SESSION['user']['name']=trim($_POST['name']);flash('Profile updated.');redirect('profile.php');}
page_header('My Profile','admin');?>
<div class="panel"><form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><label class="form-label">Name</label><input class="form-control mb-3" name="name" value="<?=e(current_user()['name'])?>" required><label class="form-label">Email</label><input class="form-control mb-3" value="<?=e(current_user()['email'])?>" disabled><button class="btn btn-primary">Save profile</button></form></div><?php page_footer();?>
