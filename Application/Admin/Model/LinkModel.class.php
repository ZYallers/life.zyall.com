<?php

namespace Admin\Model;

use Think\Model;

class LinkModel extends Model {

  public function pageList( $filter, $page, $limit = 10 ) {
    $where = '1 ';
    if ( !empty( $filter[ 'name' ] ) ) {
      $where .= "AND LOCATE('{$filter[ 'name' ]}',`name`)>0 ";
    }
    $row = $this->field( array( 'COUNT(`id`)' => 'total' ) )->where( $where )->find();
    $rows = $this->where( $where )->order( array( 'id' => 'DESC' ) )->limit( abs( $page - 1 ) * $limit, $limit )->select();
    $list = array( 'rows' => $rows, 'pager' => array( 'url' => 'Admin/Link/list', 'page' => $page,
        'limit' => $limit, 'total' => intval( $row[ 'total' ] ), 'query' => $filter ) );
    return $list;
  }

  public function saveLink( array $data ) {
    $return = false;
    if ( $data[ 'id' ] > 0 ) {
      $data[ 'update_time' ] = NOW_TIME;
      $return = $this->data( $data )->save();
    } else {
      unset( $data[ 'id' ] );
      $data[ 'create_time' ] = $data[ 'update_time' ] = NOW_TIME;
      $return = $this->data( $data )->add();
    }
    return $return;
  }

  public function deleteLink( $id ) {
    $return = $this->where( array( 'id' => $id ) )->delete();
    if ( $return ) {
      M( 'ClickRecord' )->where( array( 'type' => 6, 'rel_id' => $id ) )->delete();
    }
    return $return;
  }

}
