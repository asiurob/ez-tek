<?php
class QueryEngine {

  private $conn;
  private $server;
  private $user;
  private $pass;
  private $db;

  public function __construct() {
    $this->server = '67.227.206.106';
    $this->user   = 'eztekcom_illidan';
    $this->pass   = 'pzR?y.Y&JU1){a*n7C';
    $this->db     = 'eztekcom_sargeras';
    $this->conn = new mysqli( $this->server, $this->user, $this->pass, $this->db );
  }

  public function executeQuery( $query, $params = array(), $type = '', $meta = false ){
    //Preparamos dos arreglos que servirán para retornar y almacenar parametros
    $result    = array();
    $data      = array();

    //Se declara un arreglo con sentencias permitidas
    $allowed   = array('select','update','insert');
    //Separamos la consulta y los tipos de datos que se enviaran
    $order_arr = explode( ' ', $query );
    //La orden será la primera detección (select, update, insert)
    $order     = trim( strtolower( $order_arr[0] ) );

    //Validamos si la sentencia enviada está permitida
    if( !in_array( $order, $allowed ) ){
      $result['data']    = null;
      $result['message'] = "No es posible ejecutar esta acción";
      $result['code']    = 500;
      if( $meta ){
          $result['meta']['query']  = $query;
          $result['meta']['types']  = $type;
          $result['meta']['params'] = implode( ',', $params );
          $result['meta']['error']  = 'Sentencia ' . $order . ' no reconocida';
      }
    }

    else{
      //Validamos si hay errores en la conexión y creamos un arreglo asociativo
      //Si está la meta activa se añade información extra
      if( $this->conn->connect_errno ){
        $result['data']    = null;
        $result['message'] = "Falló la conexión a la base de datos";
        $result['code']    = 500;
        if( $meta ){
            $result['meta']['query']  = $query;
            $result['meta']['types']  = $type;
            $result['meta']['params'] = implode( ',', $params );
            $result['meta']['error']  = $this->conn->connect_errno . ' ' . '('.$this->conn->connect_error.')';
        }

      }

      else{

        //Añadimos la codificación de los datos
        $this->conn->set_charset( 'utf8' );
        //Validamos que se haya preparado bien la consulta y creamos el arreglo
        if( !$stmt = $this->conn->prepare( $query ) ){
          $result['data']    = null;
          $result['message'] = "Consulta mal formada";
          $result['code']    = 500;
          if( $meta ){
              $result['meta']['query']  = $query;
              $result['meta']['types']  = $type;
              $result['meta']['params'] = implode( ',', $params );
              $result['meta']['error']  =  $this->conn->errno . " " . $this->conn->error;
          }
        }

        else{
          $info = array();
          //Ciclamos los parámetros, los limpiamos y los agregamos al arreglo $data
          foreach( $params as $param ){
            array_push( $data, $this->conn->real_escape_string($param) );
          }

          $info[] = &$type;

          //Ligamos los parámetros y su respectivo tipo de dato al statement
          for ( $i = 0; $i < count( $data ); $i++ ) {
              $info[] = &$data[$i];
          }

          call_user_func_array(array( $stmt, 'bind_param' ), $info);
          //Validamos la ejecución de la consulta
          if( !$stmt->execute() ){
            $result['data']    = null;
            $result['message'] = "No fueron ligados los datos";
            $result['code']    = 500;
            if( $meta ){
                $result['meta']['query']  = $query;
                $result['meta']['types']  = $type;
                $result['meta']['params'] = implode( ',', $params );
                $result['meta']['error']  =  $this->conn->errno . " " . $this->conn->error;
            }
          }

          else{
            //Según cual sea la sentencia se ejecutarán diferentes acciones
            switch ( $order ) {

              case 'select':
                //Almacenamos la información
                $temp = self::get_result( $stmt );

                $result['data']    = $temp;
                $result['message'] = "Consulta exitosa";
                $result['code']    = 200;
                if( $meta ){
                    $result['meta']['query']  = $query;
                    $result['meta']['types']  = $type;
                    $result['meta']['params'] = implode( ',', $params );
                    $result['meta']['error']  = 'No error';
                }
                //Liberamos la memoria
                $stmt->free_result();
              break;

              case 'insert':
                //Retornamos el ID del registro insertado
                $result['data']    = $this->conn->insert_id;
                $result['message'] = 'Se insertó correctamente el registro';
                $result['code']    = 200;
                if( $meta ){
                    $result['meta']['query']  = $query;
                    $result['meta']['types']  = $type;
                    $result['meta']['params'] = implode( ',', $params );
                    $result['meta']['error']  = 'No error';
                }
              break;

              case 'update':
                //Retornamos cuantos registros fueron afectados
                $result['data']    = $this->conn->affected_rows;
                $result['message'] = $this->conn->affected_rows ?
                                    'Se actualizó correctamente el registro' :
                                    'No fue posible actualizar el campo';
                $result['code']    = 200;
                if( $meta ){
                    $result['meta']['query']  = $query;
                    $result['meta']['types']  = $type;
                    $result['meta']['params'] = implode( ',', $params );
                    $result['meta']['error']  = 'No error';
                }
              break;


              default:
                $result['data']    = null;
                $result['message'] = "No es posible ejecutar esta acción";
                $result['code']    = 405;
                if( $meta ){
                    $result['meta']['query']  = $query;
                    $result['meta']['types']  = $type;
                    $result['meta']['params'] = implode( ',', $params );
                    $result['meta']['error']  = 'Sentencia ' . $order . ' no reconocida';
                }
              break;
            }
          }
          //Cerramos la conexión
          $stmt->close();
        }
      }
    }
    //Retornamos el resultado
    return $result;
  }

  protected function get_result( $stmt ) {
    $arrResult = array();
    $stmt->store_result();
    for ( $i = 0; $i < $stmt->num_rows; $i++ ) {
        $metadata = $stmt->result_metadata();
        $arrParams = array();
        while ( $field = $metadata->fetch_field() ) {
            $arrParams[] = &$arrResult[ $i ][ $field->name ];
        }
        call_user_func_array( array( $stmt, 'bind_result' ), $arrParams );
        $stmt->fetch();
    }
    return $arrResult;
  }

  
}




?>
