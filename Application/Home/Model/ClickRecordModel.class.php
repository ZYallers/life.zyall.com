<?php

namespace Home\Model;

class ClickRecordModel extends \Think\Model {

  public function addClickRecord( $type, $relId ) {
    $data = array( 'type' => intval( $type ), 'rel_id' => intval( $relId ),
      'ip' => get_client_ip(), 'device' => ( string ) $_SERVER[ 'HTTP_USER_AGENT' ],
      'session_id' => session_id(), 'create_time' => NOW_TIME );
    $return = $this->add( $data );
    return $return;
  }

}
