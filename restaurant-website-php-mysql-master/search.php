<?php
$pdo = new PDO('mysql:host=localhost;dbname=restaurant_website;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = $_GET['query'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM menus 
                       WHERE menu_name LIKE ? OR menu_description LIKE ?");
$stmt->execute(["%$query%", "%$query%"]);
$results = $stmt->fetchAll();

if (!$results) {
    $results = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .search-header {
            margin: 40px 0 20px;
            text-align: center;
            font-size: 1.6rem;
            color: #333;
        }

        .btn-back {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #ff6b6b;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #e74c3c;
            color: #fff;
        }

        .card {
            border-radius: 16px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            background-color: #fff;
            padding: 15px;
            text-align: center;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .price {
            font-weight: bold;
            color: #e74c3c;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .card-text {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .no-results {
            text-align: center;
            color: #777;
            margin-top: 30px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn-back">⬅ Quay lại trang chính</a>

        <h2 class="search-header">
            Kết quả tìm kiếm cho: <strong><?= htmlspecialchars($query) ?></strong>
        </h2>

        <?php if (count($results) > 0): ?>
            <div class="row">
                <?php foreach ($results as $food): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="admin/Uploads/images/<?= htmlspecialchars($food['menu_image']) ?>" 
                                 alt="<?= htmlspecialchars($food['menu_name']) ?>" 
                                 class="card-img-top">

                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($food['menu_name']) ?></h5>
                                <p class="price"><?= number_format($food['menu_price'], 0, ',', '.') ?> đ</p>
                                <p class="card-text"><?= htmlspecialchars(substr($food['menu_description'], 0, 80)) ?>...</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>  
        <?php else: ?>
            <p class="no-results">Không tìm thấy món ăn nào phù hợp.</p>
        <?php endif; ?>
    </div>
</body>
</html>
