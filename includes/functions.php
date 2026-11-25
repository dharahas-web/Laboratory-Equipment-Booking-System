<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): never { header("Location: {$url}"); exit; }
function app_base_path(): string {
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $applicationRoot = realpath(__DIR__ . '/..');
    if ($documentRoot === false || $applicationRoot === false) return '';

    $documentRoot = rtrim($documentRoot, DIRECTORY_SEPARATOR);
    if (strcasecmp($applicationRoot, $documentRoot) === 0) return '';

    $documentRootPrefix = $documentRoot . DIRECTORY_SEPARATOR;
    if (strncasecmp($applicationRoot, $documentRootPrefix, strlen($documentRootPrefix)) !== 0) return '';

    return '/' . str_replace(DIRECTORY_SEPARATOR, '/', substr($applicationRoot, strlen($documentRootPrefix)));
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
            http_response_code(419); exit('Invalid form token. Please go back and retry.');
        }
    }
}
function flash(string $message, string $type='success'): void { $_SESSION['flash'] = [$message, $type]; }
function show_flash(): void {
    if (!empty($_SESSION['flash'])) {
        [$message, $type] = $_SESSION['flash']; unset($_SESSION['flash']);
        echo '<div class="alert alert-'.e($type).'">'.e($message).'</div>';
    }
}
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!current_user()) redirect(app_base_path() . '/login.php'); }
function require_role(string $role): void {
    require_login();
    if ((current_user()['role'] ?? '') !== $role) { http_response_code(403); exit('Access denied.'); }
}
function notify(PDO $pdo, int $userId, string $message): void {
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$userId, $message]);
}
function page_header(string $title, string $role): void {
    $user = current_user();
    $basePath = app_base_path();
    $prefix = $basePath . ($role === 'admin' ? '/admin/' : '/student/');
    $links = $role === 'admin'
      ? ['Dashboard'=>'dashboard.php','Equipment'=>'equipment.php','Bookings'=>'bookings.php','Students'=>'students.php','Laboratories'=>'laboratories.php','Maintenance'=>'maintenance.php','Reports'=>'reports.php','Notifications'=>'notifications.php','Profile'=>'profile.php']
      : ['Dashboard'=>'dashboard.php','Browse Equipment'=>'equipment.php','My Bookings'=>'my-bookings.php','Booking History'=>'booking-history.php','Report Issue'=>'report-issue.php','Notifications'=>'notifications.php','Profile'=>'profile.php'];
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' | LabDesk</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="'.e($basePath).'/assets/css/style.css" rel="stylesheet"><script src="https://cdn.jsdelivr.net/npm/chart.js"></script></head><body>';
    echo '<div class="app-shell"><aside class="sidebar"><a class="brand" href="'.$prefix.'dashboard.php">◈ LabDesk</a><div class="small muted mb-3">'.ucfirst($role).' Portal</div><nav>';
    foreach ($links as $label=>$file) echo '<a href="'.$prefix.$file.'">'.e($label).'</a>';
    echo '<a href="'.e($basePath).'/logout.php">Logout</a></nav></aside><main class="main"><header class="topbar"><div><span class="muted">Laboratory Equipment Booking System</span><h1>'.e($title).'</h1></div><div class="user-chip">'.e($user['name'] ?? 'User').'</div></header><section class="content">';
    show_flash();
}
function page_footer(): void {
    echo '</section><footer class="footer">LabDesk · Laboratory Equipment Booking and Maintenance System</footer></main></div><script src="'.e(app_base_path()).'/assets/js/main.js"></script></body></html>';
}
function status_badge(string $status): string {
    $class = strtolower(str_replace(' ', '-', $status));
    return '<span class="status '.$class.'">'.e($status).'</span>';
}
