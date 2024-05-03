<?
include ("../config.php");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case "GET":
        $query = "SELECT i18n_id as id, i18n_name as block, i18n_value as value, i18n_language as language FROM i18n_words WHERE i18n_new=0 ORDER BY updated_at DESC;";
        $stmt = $pdo->query($query);
        $notNew = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $query = "SELECT i18n_id as id, i18n_name as block, i18n_value as value, i18n_language as language FROM i18n_words WHERE i18n_new=1 ORDER BY updated_at DESC;";
        $stmt = $pdo->query($query);
        $new = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $query = "SELECT i18n_language as language FROM i18n_words GROUP BY i18n_language;";
        $stmt = $pdo->query($query);
        $_languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $languages = [];
        foreach ($_languages as $row) {
            $languages[] = $row["language"];
        }

        echo json_encode(["languages" => $languages, "exist" => $notNew, "new" => $new], JSON_UNESCAPED_UNICODE);
        break;
    case "POST":
        $query = "INSERT INTO i18n_words (i18n_name,i18n_value,i18n_language)VALUES(:name,:value,:language)";
        $stmt = $pdo->prepare($query);
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);
        try {

            $stmt->execute([
                'name' => $data['name'],
                'value' => $data['value'],
                'language' => $data['language']
            ]);
            http_response_code(201);
            echo json_encode(["message" => _t("i18n_successfully_created")]);
        } catch (PDOException $e) {
            http_response_code(400);
            if (strpos($e, "duplicate")) {
                echo json_encode(["error" => _t("i18n_duplicate_key_error")]);
            } else {
                echo json_encode(["error" => (string) $e]);
            }

        }

        break;
    case "PUT":

        break;
    case "DELETE":
        $id = $_GET['id'] ?? null;
        if ($id !== null) {

        } else {
            http_response_code(400);
            echo json_encode(["error" => _t("invalid_id")]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => _t("method_not_allowed")]);
        break;
}

?>