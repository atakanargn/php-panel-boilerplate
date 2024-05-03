<?
include ("config.php");
$view_route = 'views/';

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($request) {
  case "":
  case "/":
    $sayfa = "index";
    include ($view_route . "index.php");
    break;
  case "/users":
    $sayfa = "users";
    include ($view_route . "users.php");
    break;
  case "/translate":
    $sayfa = "translates";
    include ($view_route . "translates.php");
    break;
  default:
    include ($view_route . "404.php");
}
?>