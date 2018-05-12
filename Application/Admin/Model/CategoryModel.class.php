<?php

namespace Admin\Model;

use Think\Model;

class CategoryModel extends Model {

  public function findAll( $assc = false ) {
    $return = array();
    $rows = M( 'Category' )->field( array( 'id', 'name' ) )->select();
    if ( $assc ) {
      foreach ( $rows as $row ) {
        $return[ $row[ 'id' ] ] = $row[ 'name' ];
      }
    } else {
      $return = $rows;
    }
    return $return;
  }

  public function pageList( $filter, $page, $limit = 10 ) {
    $where = '1 ';
    if ( !empty( $filter[ 'name' ] ) ) {
      $where .= "AND LOCATE('{$filter[ 'name' ]}',`name`)>0 ";
    }
    $row = $this->field( array( 'COUNT(`id`)' => 'total' ) )->where( $where )->find();
    $rows = $this->where( $where )->order( array( 'id' => 'DESC' ) )
        ->limit( abs( $page - 1 ) * $limit, $limit )->select();
    $list = array( 'rows' => $rows, 'pager' => array( 'url' => 'Admin/Category/list', 'page' => $page,
        'limit' => $limit, 'total' => intval( $row[ 'total' ] ), 'query' => $filter ) );
    return $list;
  }

  public function saveCategory( array $data ) {
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

  public function deleteCategory( $id ) {
    $return = $this->where( array( 'id' => $id ) )->delete();
    if ( $return ) {
      M( 'ClickRecord' )->where( array( 'type' => 3, 'rel_id' => $id ) )->delete();
    }
    return $return;
  }

  public function findByNameAndExcept( $name, $except = 0 ) {
    $where = array( 'name' => $name );
    if ( $except > 0 ) {
      $where[ 'id' ] = array( 'NEQ', $except );
    }
    $row = $this->where( $where )->find();
    return $row;
  }

  public function findByAliasAndExcept( $alias, $except = 0 ) {
    $where = array( 'alias' => $alias );
    if ( $except > 0 ) {
      $where[ 'id' ] = array( 'NEQ', $except );
    }
    $row = $this->where( $where )->find();
    return $row;
  }

}
