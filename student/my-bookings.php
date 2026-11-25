<?php
require_once __DIR__.'/../includes/auth.php'; require_role('student');$uid=current_user()['id'];
if(($_GET['export']??'')==='csv'){
    $export=$pdo->prepare("SELECT b.id,e.equipment_code,e.name,ec.name,l.name,b.quantity,b.booking_date,b.start_time,b.end_time,b.purpose,b.status FROM bookings b JOIN equipment e ON e.id=b.equipment_id LEFT JOIN equipment_categories ec ON ec.id=e.category_id JOIN laboratories l ON l.id=e.laboratory_id WHERE b.student_user_id=? ORDER BY b.created_at DESC");
    $export->execute([$uid]);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="my-booked-equipment.csv"');
    $output=fopen('php://output','w');
    if($output===false) throw new RuntimeException('Unable to create the booking CSV export.');
    fwrite($output,"\xEF\xBB\xBF");
    fputcsv($output,['Booking ID','Equipment Code','Equipment','Category','Laboratory','Quantity','Booking Date','Start Time','End Time','Purpose','Status'],',','"','');
    while($row=$export->fetch(PDO::FETCH_NUM)){
        foreach($row as &$value){
            $value=(string)$value;
            if(preg_match('/^[\x00-\x20]*[=+\-@]/',$value)===1) $value="'".$value;
        }
        unset($value);
        fputcsv($output,$row,',','"','');
    }
    fclose($output);
    exit;
}
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$id=(int)$_POST['id'];$s=$pdo->prepare("UPDATE bookings SET status='Cancelled' WHERE id=? AND student_user_id=? AND status='Pending'");$s->execute([$id,$uid]);flash($s->rowCount()?'Booking cancelled.':'Only pending bookings can be cancelled.');redirect('my-bookings.php');}
$s=$pdo->prepare("SELECT b.*,e.name equipment_name FROM bookings b JOIN equipment e ON e.id=b.equipment_id WHERE b.student_user_id=? ORDER BY b.created_at DESC");$s->execute([$uid]);$rows=$s->fetchAll();page_header('My Bookings','student');?>
<div class="panel"><div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="?export=csv">Download booked equipment CSV</a></div><div class="table-responsive"><table class="table"><thead><tr><th>ID</th><th>Equipment</th><th>Date</th><th>Time</th><th>Qty</th><th>Purpose</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td>#<?=$r['id']?></td><td><?=e($r['equipment_name'])?></td><td><?=e($r['booking_date'])?></td><td><?=e(substr($r['start_time'],0,5))?>–<?=e(substr($r['end_time'],0,5))?></td><td><?=$r['quantity']?></td><td><?=e($r['purpose'])?></td><td><?=status_badge($r['status'])?></td><td><?php if($r['status']==='Pending'):?><form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm btn-outline-light">Cancel</button></form><?php endif?></td></tr><?php endforeach?></tbody></table></div></div><?php page_footer();?>
