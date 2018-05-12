<?php

namespace Home\Model;

class ShareRecordModel extends \Think\Model {

  public function addShareRecord( $relId, $type, $way ) {
    $data = array( 'type' => intval( $type ), 'rel_id' => intval( $relId ), 'way' => $way,
      'ip' => get_client_ip(), 'device' => ( string ) $_SERVER[ 'HTTP_USER_AGENT' ],
      'session_id' => session_id(), 'create_time' => NOW_TIME );
    $return = $this->add( $data );
    return $return;
  }

}
