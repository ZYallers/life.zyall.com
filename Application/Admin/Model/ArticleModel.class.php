<?php

namespace Admin\Model;

use Think\Model;

class ArticleModel extends Model {

  public function saveArticle( array $data ) {
    $return = false;
    $content = $data[ 'content' ];
    unset( $data[ 'content' ] );
    if ( $data[ 'id' ] > 0 ) {
      $data[ 'update_time' ] = NOW_TIME;
      $data[ 'tags' ] = D( 'Tag' )->tranName2Ids( $data[ 'tags' ] );
      preg_match_all( '/<[img|IMG].*?src=["|\'|\s]?(.*?)(?="|\'|\s)/', $content, $matchs );
      $data[ 'img' ] = empty( $matchs ) ? '' : $matchs[ 1 ][ 0 ];
      $text = str_replace( PHP_EOL, '', trim( strip_tags( $content ) ) );
      $data[ 'summary' ] = \Org\Util\String::msubstr( $text, 0, 320 );
      $return = $this->data( $data )->save();
      if ( false !== $return ) {
        $return = M( 'ArticleContent' )->data( array( 'article_id' => $data[ 'id' ], 'content' => $content ) )->save();
      }
    } else {
      unset( $data[ 'id' ] );
      $data[ 'create_time' ] = $data[ 'update_time' ] = NOW_TIME;
      $data[ 'tags' ] = D( 'Tag' )->tranName2Ids( $data[ 'tags' ] );
      preg_match_all( '/<[img|IMG].*?src=["|\'|\s]?(.*?)(?="|\'|\s)/', $content, $matchs );
      $data[ 'img' ] = empty( $matchs ) ? '' : $matchs[ 1 ][ 0 ];
      $text = str_replace( '/\s/g', '', trim( strip_tags( $content ) ) );
      $data[ 'summary' ] = \Org\Util\String::msubstr( $text, 0, 320 );
      $return = $this->data( $data )->add();
      if ( false !== $return ) {
        M( 'ArticleContent' )->data( array( 'article_id' => $return, 'content' => $content ) )->add();
        M( 'Category' )->where( array( 'id' => $data[ 'category_id' ] ) )->setInc( 'article_count' );
      }
    }
    return $return;
  }

  public function pageList( $filter, $page, $limit = 10 ) {
    $where = '1 ';
    if ( !empty( $filter[ 'title' ] ) ) {
      $where .= "AND LOCATE('{$filter[ 'title' ]}',`title`)>0 ";
    }
    if ( $filter[ 'status' ] > 0 ) {
      $where .= "AND `status`='" . intval( $filter[ 'status' ] ) . "' ";
    }
    if ( $filter[ 'category_id' ] > 0 ) {
      $where .= "AND `category_id`='" . intval( $filter[ 'category_id' ] ) . "' ";
    }
    $row = $this->field( array( 'COUNT(`id`)' => 'total' ) )->where( $where )->find();
    $rows = $this->field( array( 'id', 'title', 'alias', 'category_id', 'status', 'click_count', 'zan_count',
          'cai_count', 'create_time', 'update_time' ) )->where( $where )->order( array( 'id' => 'DESC' ) )
        ->limit( abs( $page - 1 ) * $limit, $limit )->select();
    $list = array( 'rows' => $rows, 'pager' => array( 'url' => 'Admin/Article/list', 'page' => $page,
        'limit' => $limit, 'total' => intval( $row[ 'total' ] ), 'query' => $filter ) );
    return $list;
  }

  public function findArticleByPk( $id ) {
    $return = array();
    $row = $this->getbyid( $id );
    if ( !empty( $row ) ) {
      $return = $row;
      $return[ 'tags' ] = D( 'Tag' )->tranIds2Name( $return[ 'tags' ] );
      $content = M( 'ArticleContent' )->getbyarticle_id( $id );
      $return[ 'content' ] = empty( $content ) ? '' : $content[ 'content' ];
    }
    return $return;
  }

  public function deleteArticle( $id ) {
    $result = false;
    $row = M( 'Article' )->getbyid( $id );
    if ( !empty( $row ) ) {
      $result = M( 'Article' )->where( array( 'id' => $id ) )->delete();
      if ( $result ) {
        //删除内容
        $result = M( 'ArticleContent' )->where( array( 'article_id' => $id ) )->delete();
        if ( $result ) {
          //分类统计-1
          $result = M( 'Category' )->where( array( 'id' => $row[ 'category_id' ] ) )->setDec( 'article_count' );
          if ( $result ) {
            //删除点击记录
            M( 'ClickRecord' )->where( "`rel_id`={$id} AND `type` IN(1,4,5)" )->delete();
          }
        }
      }
    }
    return $result;
  }

  public function findByTitleAndExcept( $title, $except = 0 ) {
    $where = array( 'title' => $title );
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
