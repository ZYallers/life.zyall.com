<?php

namespace Admin\Model;

use Think\Model;

class TagModel extends Model {

  public function findAllByNameAndExcept( $name, $except ) {
    $where = "LOCATE('{$name}',`name`)>0";
    if ( !empty( $except ) ) {
      foreach ( explode( ',', $except ) as $value ) {
        $where .= " AND LOCATE('{$value}',`name`)=0 ";
      }
    }
    $rows = M( 'Tag' )->field( array( 'name' ) )->where( $where )->select();
    return $rows;
  }

  public function getIdByName( $name ) {
    $row = $this->field( array( 'id' ) )->getbyname( $name );
    return empty( $row ) ? 0 : intval( $row[ 'id' ] );
  }

  public function getNameById( $id ) {
    $row = $this->field( array( 'name' ) )->getbyid( $id );
    return empty( $row ) ? '' : trim( $row[ 'name' ] );
  }

  public function saveTag( array $data ) {
    $return = false;
    if ( $data[ 'id' ] > 0 ) {
      $data[ 'update_time' ] = NOW_TIME;
      $return = M( 'tag' )->data( $data )->save();
    } else {
      unset( $data[ 'id' ] );
      $data[ 'create_time' ] = $data[ 'update_time' ] = NOW_TIME;
      $return = M( 'Tag' )->data( $data )->add();
    }
    return $return;
  }

  public function tranName2Ids( $tags ) {
    $return = array();
    foreach ( explode( ',', trim( $tags, ',' ) ) as $name ) {
      $tagId = $this->getIdByName( $name );
      if ( $tagId > 0 ) {
        $return[] = $tagId;
      } else {
        $_id = $this->saveTag( array( 'name' => $name ) );
        $_id > 0 && $return[] = $_id;
      }
    }
    $return = implode( ',', $return );
    return $return;
  }

  public function tranIds2Name( $ids ) {
    $return = array();
    foreach ( explode( ',', trim( $ids, ',' ) ) as $id ) {
      $name = $this->getNameById( $id );
      !empty( $name ) && $return[] = $name;
    }
    $return = implode( ',', $return );
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
    $list = array( 'rows' => $rows, 'pager' => array( 'url' => 'Admin/Tag/list', 'page' => $page,
        'limit' => $limit, 'total' => intval( $row[ 'total' ] ), 'query' => $filter ) );
    return $list;
  }

  public function deleteTag( $id ) {
    $return = false;
    $tag = $this->getbyid( $id );
    if ( !empty( $tag ) ) {
      $return = $this->where( array( 'id' => $id ) )->delete();
      if ( $return ) {
        //删除文章中记录的标签
        $sql = "UPDATE `__ARTICLE__` SET `tags`=TRIM(BOTH ',' FROM REPLACE(CONCAT(',',`tags`,','),',{$id},','')) WHERE FIND_IN_SET({$id},`tags`)";
        D( 'Article' )->execute( $sql );
        //删除点击记录
        D( 'ClickRecord' )->where( array( 'type' => 2, 'rel_id' => $id ) )->delete();
      }
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

}
