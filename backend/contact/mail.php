<?php
session_start();
require_once '../core/QueryEngine.php';
$engine   = new QueryEngine();

$response = array();
$method   = strtolower( $_SERVER['REQUEST_METHOD'] );
$files    = json_decode( file_get_contents('php://input'), true );
$params   = array();
switch ( $method ) {

  case 'post':
    $query  = "";
    $types  = "";

    $result = $engine->executeQuery( $query, $params, $types );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
    $response['code']    = $result['code'];

    if( !empty( $result['meta'] ) ) {
      $response['meta'] = $result['meta'];
    }

    $response['code'] = 200;
    $response['message'] = 'Correcto';

  break;

  default:
    $response['success'] = false;
    $response['message'] = 'Método de petición no soportado';
    $response['code']    = 405;
  break;
}
$code = $response['code'];
unset( $response['code'] );

header('Content-type: application/json');
http_response_code( $code );
echo json_encode( $response );
?>
