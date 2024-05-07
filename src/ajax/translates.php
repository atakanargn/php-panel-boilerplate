<?
include ("../config.php");
include ("../models/i18n.php");

$i18n = new I18n($pdo, $redisConn);

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $languages = $i18n->getAllLanguages();
        $existingWords = $i18n->getExistingWords();
        $newWords = $i18n->getNewWords();
        echo json_encode(["languages" => $languages, "exist" => $existingWords, "new" => $newWords], JSON_UNESCAPED_UNICODE);
        break;
    case "POST":
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            $i18n->addTranslation($data['name'], $data['value'], $data['language']);
            http_response_code(201);
            echo json_encode(["message" => $i18n->_t("i18n_successfully_created")]);
        } catch (Exception $e) {
            http_response_code(400);
            if (strpos($e, "duplicate")) {
                echo json_encode(["error" => $i18n->_t("i18n_duplicate_key_error")]);
            } else {
                echo json_encode(["error" => (string) $e]);
            }
        }
        break;
    case "PUT":
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            $i18n->updateTranslation($data['id'], $data['value']);
            http_response_code(201);
            echo json_encode(["message" => $i18n->_t("i18n_successfully_updated")]);
        } catch (Exception $e) {
            http_response_code(400);
            if (strpos($e, "duplicate")) {
                echo json_encode(["error" => $i18n->_t("i18n_duplicate_key_error")]);
            } else {
                echo json_encode(["error" => (string) $e]);
            }
        }
        break;
    case "DELETE":
        try {
            $id = $_GET['id'] ?? null;
            if ($id !== null) {
                $i18n->deleteTranslation($id);
                echo json_encode(["message" => $i18n->_t("i18n_successfully_deleted")]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => $i18n->_t("invalid_id")]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => (string) $e]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => $i18n->_t("method_not_allowed")]);
        break;
}

?>