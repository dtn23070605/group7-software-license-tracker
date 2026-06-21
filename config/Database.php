<?php

/**
 * Singleton Pattern — Database Connection
 *
 * Vấn đề cần giải quyết: nếu mỗi model tự tạo 1 đối tượng PDO riêng
 * (new PDO(...) ở nhiều nơi), ứng dụng sẽ mở nhiều connection đến
 * MySQL không cần thiết, gây tốn tài nguyên và khó kiểm soát.
 *
 * Singleton Pattern giải quyết bằng cách đảm bảo toàn bộ ứng dụng
 * chỉ có DUY NHẤT 1 đối tượng Database (và do đó chỉ 1 kết nối PDO)
 * trong suốt vòng đời của request, dù có bao nhiêu model/controller
 * gọi đến Database::getInstance().
 *
 * 3 yếu tố bắt buộc để 1 class là Singleton đúng chuẩn:
 * 1. Constructor là `private` — không ai bên ngoài gọi `new Database()` được.
 * 2. Có 1 biến static `$instance` lưu lại đối tượng duy nhất đã tạo.
 * 3. Có 1 method static (getInstance) để truy cập đối tượng đó —
 *    nếu chưa tồn tại thì tạo mới, nếu đã tồn tại thì trả lại cái cũ.
 */
class Database {

    // Lưu đối tượng Database duy nhất của toàn ứng dụng
    private static $instance = null;

    // Đối tượng PDO thực sự dùng để query database
    private $pdo;

    private $host     = 'localhost';
    private $dbname   = 'license_tracker';
    private $username = 'root';
    private $password = '';
    private $charset  = 'utf8mb4';

    /**
     * Constructor PRIVATE — đây là điểm cốt lõi của Singleton.
     * Vì private, không có chỗ nào trong code gọi `new Database()`
     * được nữa, trừ chính class này (qua getInstance()).
     */
    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
    }

    /**
     * Chặn clone — nếu không chặn, ai đó có thể dùng `clone` để tạo
     * ra 1 bản sao thứ 2 của Database, phá vỡ nguyên tắc "chỉ 1 instance".
     */
    private function __clone() {}

    /**
     * Chặn unserialize — tương tự, ngăn việc tái tạo object qua
     * serialize/unserialize để tạo ra instance thứ 2 ngoài ý muốn.
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton.");
    }

    /**
     * Cổng DUY NHẤT để lấy đối tượng Database trong toàn bộ ứng dụng.
     *
     * Lần gọi đầu tiên: self::$instance còn null → tạo mới 1 lần duy nhất.
     * Mọi lần gọi sau: self::$instance đã có sẵn → trả lại chính
     * đối tượng đó, KHÔNG tạo connection PDO mới.
     *
     * Nhờ vậy, dù SoftwareTitle, User, LicensePool, LicenseAllocation...
     * (rất nhiều model khác nhau) đều gọi Database::getInstance(),
     * tất cả đều dùng chung 1 kết nối PDO duy nhất.
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Trả về đối tượng PDO thật để các model dùng để query.
     * Cách gọi chuẩn từ 1 model: Database::getInstance()->getConnection()
     */
    public function getConnection(): PDO {
        return $this->pdo;
    }
}
