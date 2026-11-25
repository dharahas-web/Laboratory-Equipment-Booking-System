<?php
require_once __DIR__.'/../includes/auth.php'; require_role('admin');
$stats=[
'Total Equipment'=>(int)$pdo->query("SELECT COUNT(*) FROM equipment")->fetchColumn(),
'Available Equipment'=>(int)$pdo->query("SELECT COALESCE(SUM(available_quantity),0) FROM equipment")->fetchColumn(),
'Pending Requests'=>(int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status='Pending'")->fetchColumn(),
'Approved Bookings'=>(int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status='Approved'")->fetchColumn(),
'Under Maintenance'=>(int)$pdo->query("SELECT COUNT(*) FROM equipment WHERE condition_status='Under Maintenance'")->fetchColumn(),
'Total Students'=>(int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn(),
'Total Laboratories'=>(int)$pdo->query("SELECT COUNT(*) FROM laboratories")->fetchColumn(),
];
$recent=$pdo->query("SELECT b.id,u.name,e.name equipment_name,b.booking_date,b.status FROM bookings b JOIN users u ON u.id=b.student_user_id JOIN equipment e ON e.id=b.equipment_id ORDER BY b.created_at DESC LIMIT 7")->fetchAll();
page_header('Admin Dashboard','admin');?>
<div class="grid-stats"><?php foreach($stats as $label=>$value):?><div class="card-dark"><div class="stat-label"><?=e($label)?></div><div class="stat"><?=$value?></div></div><?php endforeach?></div>
<div class="panel mb-4"><h2 class="section-title">Monthly booking activity</h2><canvas id="bookingChart" height="90"></canvas></div>
<div class="panel"><h2 class="section-title">Recent booking requests</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>ID</th><th>Student</th><th>Equipment</th><th>Date</th><th>Status</th></tr></thead><tbody><?php foreach($recent as $r):?><tr><td>#<?=$r['id']?></td><td><?=e($r['name'])?></td><td><?=e($r['equipment_name'])?></td><td><?=e($r['booking_date'])?></td><td><?=status_badge($r['status'])?></td></tr><?php endforeach?></tbody></table></div></div>
<script>new Chart(document.getElementById('bookingChart'),{type:'bar',data:{labels:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],datasets:[{label:'Bookings',data:<?=json_encode(array_map('intval',$pdo->query("SELECT COUNT(*) c FROM bookings WHERE YEAR(booking_date)=YEAR(CURDATE()) GROUP BY MONTH(booking_date)")->fetchAll(PDO::FETCH_COLUMN)))?>,backgroundColor:'#8B5CF6'}]},options:{plugins:{legend:{labels:{color:'#cbd5e1'}}},scales:{x:{ticks:{color:'#94a3b8'}},y:{ticks:{color:'#94a3b8'}}}}});</script>
<?php page_footer();?>
