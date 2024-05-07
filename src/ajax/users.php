<?
include ("../config.php");
include ("../models/i18n.php");

$i18n = new I18n($pdo, $redisConn);

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $query = "SELECT
        us_id as id,
        us_email as email,
        us_phone as phone,
        us_fullname as fullname,
        us_status as status,
        us_role as role,
        (case us_permissions when null then '' else us_permissions end) as permissions,
        created_at,
        updated_at FROM users ORDER BY updated_at DESC;";
        $stmt = $pdo->query($query);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["users" => $users, "count" => count($users)], JSON_UNESCAPED_UNICODE);
        break;
    case "POST":
        $query = "INSERT INTO users (us_email,us_phone,us_fullname,us_password,us_status,us_role,us_permissions)VALUES(:email,:phone,:fullname,:password,:status,:role,:permissions)";
        $stmt = $pdo->prepare($query);
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);
        try {

            $stmt->execute([
                'email' => $data['email'],
                'phone' => $data['phone'],
                'fullname' => $data['fullname'],
                'password' => $data['password'],
                'status' => $data['status'],
                'role' => $data['role'],
                'permissions' => $data['permissions'],
            ]);
            http_response_code(201);
            echo json_encode(["message" => $i18n->_t("user_successfully_created")]);
        } catch (PDOException $e) {
            http_response_code(400);
            if (strpos($e, "duplicate")) {
                echo json_encode(["error" => $i18n->_t("user_duplicate_key_error")]);
            } else {
                echo json_encode(["error" => (string) $e]);
            }
        }
        break;
    case "PUT":
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);
        $query = "SELECT * FROM users WHERE us_id=:id;";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'id' => $data['id']
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            switch ($data["action"]) {
                case "update_email":
                    $query = "UPDATE users SET us_email=:email WHERE us_id=:id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
                    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
                    break;
                case "update_phone":
                    $query = "UPDATE users SET us_phone=:phone WHERE us_id=:id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
                    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
                    break;
                case "update_fullname":
                    $query = "UPDATE users SET us_fullname=:fullname WHERE us_id=:id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':fullname', $data['fullname'], PDO::PARAM_STR);
                    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
                    break;
                case "update_password":
                    $query = "UPDATE users SET us_password=:us_password WHERE us_id=:id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':password', $data['password'], PDO::PARAM_STR);
                    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
                    break;
                case "update_status":
                    $query = "UPDATE users SET us_status=:status WHERE us_id=:id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
                    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
                    break;
                case "update_role":
                    $query = "UPDATE users SET us_role=:role WHERE us_id=:id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':role', $data['role'], PDO::PARAM_STR);
                    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
                    break;
                case "update_permissions":
                    $query = "UPDATE users SET us_permissions=:permissions WHERE us_id=:id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':permissions', $data['permissions'], PDO::PARAM_STR);
                    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(["error" => $i18n->_t("method_not_allowed")]);
                    exit();
            }

            try {
                $stmt->execute([
                    'id' => $data['id'],
                    'value' => $data['value']
                ]);
                http_response_code(201);
                echo json_encode(["message" => $i18n->_t("user_successfully_updated")]);
            } catch (PDOException $e) {
                http_response_code(400);
                if (strpos($e, "duplicate")) {
                    echo json_encode(["error" => $i18n->_t("user_duplicate_key_error")]);
                } else {
                    echo json_encode(["error" => (string) $e]);
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => $i18n->_t("user_not_found")]);
        }
        break;
    case "DELETE":
        $id = $_GET['id'] ?? null;
        if ($id !== null) {
            $query = "SELECT * FROM users WHERE us_id=:id;";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'id' => $id
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $query = "DELETE FROM users WHERE us_id=:id;";
                $stmt = $pdo->prepare($query);
                try {
                    $stmt->execute([
                        'id' => $id
                    ]);
                    echo json_encode(["message" => $i18n->_t("user_successfully_deleted")]);
                } catch (PDOException $e) {
                    http_response_code(400);
                    echo json_encode(["error" => (string) $e]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => $i18n->_t("user_not_found")]);
            }

        } else {
            http_response_code(400);
            echo json_encode(["error" => $i18n->_t("invalid_id")]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => $i18n->_t("method_not_allowed")]);
        break;
}

?>