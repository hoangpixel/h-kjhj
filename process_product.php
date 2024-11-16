<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "product_db";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['productName']) ? $_POST['productName'] : '';
    $price = isset($_POST['productPrice']) ? $_POST['productPrice'] : 0;
    $description = isset($_POST['productDescription']) ? $_POST['productDescription'] : '';
    $image = isset($_POST['productImage']) ? $_POST['productImage'] : '';

    // Thêm dữ liệu vào MySQL
    $sql = "INSERT INTO products (name, price, description, image) VALUES ('$name', '$price', '$description', '$image')";

    if ($conn->query($sql) === TRUE) {
        echo "Sản phẩm đã được thêm vào MySQL!";

        // Thêm dữ liệu vào file data.js
        $dataFile = 'data.js';

        // Tạo đối tượng sản phẩm mới
        $newProduct = [
            "name" => $name,
            "price" => $price,
            "description" => $description,
            "image" => $image
        ];

        // Kiểm tra và đọc dữ liệu hiện tại từ file
        if (file_exists($dataFile)) {
            $jsonData = file_get_contents($dataFile);
            $jsonData = str_replace('var products = ', '', $jsonData); // Loại bỏ `var products = `
            $jsonData = rtrim($jsonData, ';'); // Loại bỏ dấu `;` ở cuối
            $dataArray = json_decode($jsonData, true);
        } else {
            $dataArray = [];
        }

        // Kiểm tra nếu `dataArray` không phải là mảng
        if (!is_array($dataArray)) {
            $dataArray = [];
        }

        // Thêm sản phẩm mới vào danh sách
        $dataArray[] = $newProduct;

        // Chuyển mảng thành JSON và ghi lại vào file
        $jsonOutput = json_encode($dataArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($dataFile, "var products = " . $jsonOutput . ";");

        echo "<br>Dữ liệu đã được lưu vào file data.js!";
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
