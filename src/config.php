<?
// PostgreSQL veritabanı bağlantı bilgileri
$host = 'postgres';
// Veritabanı sunucusu
$dbname = 'dockerdb';
// Veritabanı adı
$user = 'postgres';
// Veritabanı kullanıcı adı
$password = 'postgres';
// Veritabanı şifresi

session_start();

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname;user=$user;password=$password");
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

require_once ("utils/funcs.php");
?>