<?
// PostgreSQL veritabanı bağlantı bilgileri
$pgHost = 'postgres';
$pgPort = '5432';
$pgDbname = 'dockerdb';
$pgUser = 'postgres';
$pgPassword = 'postgres';

// Redis bağlantı bilgileri
$redisHost = "redis";

session_start();

try {
    $pdo = new PDO("pgsql:host=$pgHost;dbname=$pgDbname;user=$pgUser;password=$pgPassword");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['settings'])) {
        $query = "SELECT st_name as name, st_value as value FROM settings;";
        $stmt = $pdo->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $_SESSION['settings'] = [];
        foreach ($rows as $row) {
            $_SESSION['settings'][$row['name']] = $row['value'];
        }
    }
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}

// Redis connection
$redisConn = new Redis();
$redisConn->connect($redisHost, 6379);
if (!$redisConn) {
    die("Redis connection failed");
}

require_once ("utils/funcs.php");
?>