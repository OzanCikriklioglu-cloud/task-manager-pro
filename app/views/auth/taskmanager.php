<?php
// C:\xampp\htdocs\Task_Manager_PRO\app\views\auth\taskmanager.php
session_start();

// Cache disabling
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Timezone
date_default_timezone_set('Europe/Warsaw');

// Auth check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /Task_Manager_PRO/public/index.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Params: filter, sort, order, category
$filter = $_GET['filter'] ?? 'all';
$categoryFilter = $_GET['category'] ?? 'all';
$allowedSorts = ['priority', 'due_date', 'created_at', 'completed_at'];
$sort  = in_array($_GET['sort'] ?? '', $allowedSorts) ? $_GET['sort'] : 'created_at';
$order = strtoupper($_GET['order'] ?? 'DESC');
if (!in_array($order, ['ASC','DESC'])) $order = 'DESC';

// DB
require 'C:\xampp\htdocs\Task_Manager_PRO\config\connection.php';
$pdo = new PDO($dsn, $dbUser, $dbPass, $options);

// Fetch categories for dropdown
$catStmt = $pdo->prepare("SELECT DISTINCT c.name FROM categories c
    JOIN tasks t ON t.category_id=c.id WHERE t.user_id=:user_id");
$catStmt->execute([':user_id'=>$user_id]);
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// Base SQL
$sql = "SELECT t.id,t.title,t.description,t.priority,t.due_date,t.status,t.created_at,t.completed_at,
           c.name AS category_name
    FROM tasks t
    LEFT JOIN categories c ON t.category_id=c.id
    WHERE t.user_id=:user_id";
// Status filter
if ($filter==='completed') $sql .= " AND t.status='completed'";
elseif ($filter==='incomplete') $sql .= " AND t.status='incomplete'";
// Category filter
if ($categoryFilter!=='all') $sql .= " AND c.name=:catName";
// Order
$sql .= " ORDER BY t.$sort $order";

$stmt = $pdo->prepare($sql);
$params = [':user_id'=>$user_id];
if ($categoryFilter!=='all') $params[':catName'] = $categoryFilter;
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark failed
$now = time();
foreach ($tasks as &$t) {
    if ($t['status']!=='completed' && $t['due_date'] && strtotime($t['due_date'])<$now) {
        $t['status']='failed';
    }
}
unset($t);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Task Manager</title>
<link rel="stylesheet" href="/Task_Manager_PRO/public/Appear/style.css">
<style>
body{font-family:Arial;margin:20px}
.datetime{text-align:center;color:#555;margin-bottom:10px}
.header{display:flex;justify-content:space-between;align-items:center}
.filters{margin:10px 0}
.filter-btn{padding:6px 12px;margin-right:8px;text-decoration:none;border:1px solid #ddd;border-radius:4px;background:#f4f4f4;color:#333}
.active-filter{background:#007bff;color:#fff;border-color:#007bff}
select{padding:6px;border:1px solid #ccc;border-radius:4px;margin-right:20px}
table{width:100%;border-collapse:collapse;margin-top:10px}
th,td{padding:8px;border:1px solid #ddd}
th{background:#f4f4f4}
.btn-danger{background:#dc3545;color:#fff;padding:6px 12px;text-decoration:none;border-radius:4px}
.btn-danger:hover{opacity:0.9}
</style>
</head>
<body>
<div class="datetime"><?=date('Y-m-d H:i')?></div>
<div class="header">
  <h1>Task Manager</h1>
  <a href="/Task_Manager_PRO/public/logout.php" class="btn-danger">Çıkış Yap</a>
</div>

<!-- Category Filter -->
<div class="filters">
  <label>Kategori:</label>
  <select onchange="location.href='?filter=<?=$filter?>&sort=<?=$sort?>&order=<?=$order?>&category='+this.value">
    <option value="all" <?=$categoryFilter==='all'?'selected':''?>>Tümü</option>
    <?php foreach($categories as $cat): ?>
      <option value="<?=htmlspecialchars($cat)?>" <?=$categoryFilter===$cat?'selected':''?>><?=htmlspecialchars($cat)?></option>
    <?php endforeach; ?>
  </select>

  <!-- Existing filters -->
  <a href="?filter=all&sort=<?=$sort?>&order=<?=$order?>&category=<?=$categoryFilter?>" class="filter-btn <?= $filter==='all'?'active-filter':''?>">Hepsi</a>
  <a href="?filter=completed&sort=<?=$sort?>&order=<?=$order?>&category=<?=$categoryFilter?>" class="filter-btn <?= $filter==='completed'?'active-filter':''?>">Bitirilen</a>
  <a href="?filter=incomplete&sort=<?=$sort?>&order=<?=$order?>&category=<?=$categoryFilter?>" class="filter-btn <?= $filter==='incomplete'?'active-filter':''?>">Bitirilmemiş</a>
</div>

<!-- Sort Buttons -->
<div class="filters">
  <span>Sırala:</span>
  <?php foreach(['priority'=>'Öncelik','created_at'=>'Oluşturma','due_date'=>'Bitiş'] as $col=>$label): ?>
    <a href="?filter=<?=$filter?>&sort=<?=$col?>&order=<?= $sort===$col&&$order==='ASC'?'DESC':'ASC'?>&category=<?=$categoryFilter?>"
       class="filter-btn <?= $sort===$col?'active-filter':''?>">
      <?=$label?> <?= $sort===$col?($order==='ASC'?'↑':'↓'):''?>
    </a>
  <?php endforeach; ?>
</div>

<a href="/Task_Manager_PRO/app/views/tasks/create.php">Yeni Görev Ekle</a>

<?php if(empty($tasks)): ?>
  <p>Henüz görev yok.</p>
<?php else: ?>
  <table>
    <thead>
      <tr><th>#</th><th>Başlık</th><th>Açıklama</th><th>Kategori</th><th>Öncelik</th><th>Durum</th><th>Son Tarih</th><th>İşlemler</th></tr>
    </thead>
    <tbody>
      <?php $i=1; foreach($tasks as $t): ?>
      <tr>
        <td><?=$i++?></td>
        <td><?=htmlspecialchars($t['title'])?></td>
        <td><?=nl2br(htmlspecialchars($t['description']))?></td>
        <td><?=htmlspecialchars($t['category_name']?:'Default')?></td>
        <td style="color: <?= $t['priority']==='low' ? 'green' : ($t['priority']==='medium'? 'orange':'red') ?>;">
            <?= ucfirst($t['priority']) ?>
        </td>
        <td><?=ucfirst(str_replace('_',' ',$t['status']))?></td>
        <td><?= $t['due_date']?date('Y-m-d H:i',strtotime($t['due_date'])):'-'?></td>
        <td>
          <a href="../tasks/view.php?id=<?=$t['id']?>&filter=<?=$filter?>">Görüntüle</a> |
          <a href="edit.php?id=<?=$t['id']?>">Düzenle</a> |
          <a href="delete.php?id=<?=$t['id']?>" onclick="return confirm('Silinsin mi?')">Sil</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
</body>
</html>
