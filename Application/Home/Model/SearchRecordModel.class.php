<?php

namespace Home\Model;

class SearchRecordModel extends \Think\Model {

  public function addSearchRecord( $keyword ) {
    $data = array( 'keyword' => $keyword, 'ip' => get_client_ip(), 'session_id' => session_id(),
      'device' => ( string ) $_SERVER[ 'HTTP_USER_AGENT' ], 'create_time' => NOW_TIME );
    $return = $this->add( $data );
    return $return;
  }

}
