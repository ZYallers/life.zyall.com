<?php

namespace Home\Model;

class ArticleModel extends \Think\Model {

  public function pageList( $filter, $page, $limit = 10 ) {
    $where = "`status`='1' AND `id`!=21 ";
    if ( !empty( $filter[ 'title' ] ) ) {
      $where .= "AND LOCATE('{$filter[ 'title' ]}',`title`)>0 ";
    }
    if ( $filter[ 'category_id' ] > 0 ) {
      $where .= "AND `category_id`='" . $filter[ 'category_id' ] . "' ";
    }
    if ( $filter[ 'tags' ] > 0 ) {
      $where .= "AND FIND_IN_SET({$filter[ 'tags' ]},`tags`) ";
    }
    $order = array();
    if ( 1 == $filter[ 'zan' ] ) {
      $order[ 'zan_count' ] = 'DESC';
    }
    if ( 1 == $filter[ 'cai' ] ) {
      $order[ 'cai_count' ] = 'DESC';
    }
    $order[ 'id' ] = 'DESC';

    $row = $this->field( array( 'COUNT(`id`)' => 'total' ) )->where( $where )->find();
    $rows = $this->field( array( 'id', 'title', 'alias', 'img', 'summary', 'tags', 'click_count', 'zan_count',
          'cai_count', 'create_time' ) )->where( $where )->order( $order )
        ->limit( abs( $page - 1 ) * $limit, $limit )->select();
    foreach ( $rows as $key => $value ) {
      $rows[ $key ][ 'tags' ] = D( 'Tag' )->tranIds2Name( $value[ 'tags' ] );
    }
    $list = array( 'rows' => $rows, 'pager' => array( 'url' => 'Home/Article/list', 'page' => $page,
        'limit' => $limit, 'total' => intval( $row[ 'total' ] ), 'query' => $filter ) );
    return $list;
  }

  public function findAllLatestRelease( $limit = 7 ) {
    $rows = $this->field( '`id`,`title`,`alias`,`img`' )->where( array( 'status' => 1 ) )
        ->order( '`id` DESC' )->limit( $limit )->select();
    return $rows;
  }

  public function findAllHotRecommend( $limit = 7 ) {
    $rows = $this->field( '`id`,`title`,`alias`,`img`' )->where( array( 'status' => 1 ) )
        ->order( '`click_count` DESC,`zan_count` DESC,`id` DESC' )->limit( $limit )->select();
    return $rows;
  }

  public function findAllRandRead( $limit = 7 ) {
    $row = $this->field( 'COUNT(`id`) AS `total`' )->where( array( 'status' => 1 ) )->find();
    $total = intval( $row[ 'total' ] );
    $offset = mt_rand( 0, $total - $limit );
    $rows = $this->field( '`id`,`title`,`alias`,`img`' )->where( array( 'status' => 1 ) )->limit( $offset, $limit )->select();
    return $rows;
  }

}
