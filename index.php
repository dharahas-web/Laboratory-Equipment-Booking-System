<?php
require_once __DIR__.'/includes/auth.php';
if (current_user()) redirect(current_user()['role']==='admin'?'admin/dashboard.php':'student/dashboard.php');
redirect('login.php');
