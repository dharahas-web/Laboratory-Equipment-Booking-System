<?php
require_once __DIR__.'/../includes/auth.php'; require_role('admin');page_header('Settings','admin');?>
<div class="panel"><h2 class="section-title">Application settings</h2><p class="muted">Core application settings are configured in config/database.php and the database SQL file. Keep database credentials private.</p></div><?php page_footer();?>
