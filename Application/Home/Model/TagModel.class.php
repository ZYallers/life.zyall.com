<?php

namespace Home\Model;

use Think\Model;

class TagModel extends Model {

  public function getNameById( $id ) {
    $row = $this->field( array( 'name' ) )->getbyid( $id );
    return empty( $row ) ? '' : trim( $row[ 'name' ] );
  }

  public function tranIds2Name( $ids, $implode = false ) {
    $return = array();
    foreach ( explode( ',', trim( $ids, ',' ) ) as $id ) {
      $name = $this->getNameById( $id );
      !empty( $name ) && $return[] = $name;
    }
    if ( $implode ) {
      $return = implode( ',', $return );
    }
    return $return;
  }

  public function findAllHotTag( $limit = 30 ) {
    $sql = "SELECT A.`name`,A.`click_count`,COUNT(A.`id`) AS `article_count` FROM `life_tag` A " .
      "LEFT JOIN `life_article` B ON FIND_IN_SET(A.`id`,B.`tags`) " .
      "GROUP BY A.`id` ORDER BY `article_count` DESC,A.`click_count` DESC LIMIT {$limit}";
    $rows = $this->query( $sql );
    return $rows;
  }

}
