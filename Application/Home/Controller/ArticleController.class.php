<?php

namespace Home\Controller;

use THink\Controller;

class ArticleController extends Controller {

  protected function _initialize() {
    
  }

  private function getListFilter() {
    $filter = array();
    $navHead = '';
    $alias = I( 'get.alias' );
    $form = I( 'get.form' );
    cleanXSS( $alias );
    cleanXSS( $form );
    if( !\Org\Util\Waf::check( $alias ) ){
      $this->error( '您的提交带有不合法参数！' );
    }
    foreach ( $form as $value ) {
      if( !\Org\Util\Waf::check( $value ) ){
        $this->error( '您的提交带有不合法参数！' );
      }
    }
    
    if ( !empty( $alias ) ) {
      //检查是否为文章别名
      $article = M( 'Article' )->getbyAlias( $alias );
      if ( !empty( $article ) ) {
        R( 'Home/Article/show', array( 'id' => $article[ 'id' ] ) );
        exit;
      }
      //检查是否为标签别名
      $filter[ 'tags' ] = 0;
      $tag = M( 'Tag' )->getbyName( $alias );
      if ( !empty( $tag ) ) {
        M( 'Tag' )->where( array( 'id' => $tag[ 'id' ] ) )->setInc( 'click_count' );
        D( 'ClickRecord' )->addClickRecord( 2, $tag[ 'id' ] );
        $filter[ 'tags' ] = $tag[ 'id' ];
        $navHead = '标签 - ' . $alias;
      }
      //检查是否为分类别名
      $filter[ 'category_id' ] = 0;
      $category = M( 'Category' )->getbyAlias( $alias );
      if ( !empty( $category ) ) {
        M( 'Category' )->where( array( 'id' => $category[ 'id' ] ) )->setInc( 'click_count' );
        D( 'ClickRecord' )->addClickRecord( 3, $category[ 'id' ] );
        $filter[ 'category_id' ] = $category[ 'id' ];
        $navHead = '分类 - ' . $category[ 'name' ];
      }
    }

    if( is_array( $form ) && count( $form ) > 0 ){
      $form[ 'title' ] = \Org\Util\String::msubstr( $form[ 'title' ], 0, 10, 'utf-8', false );
      if ( !empty( $form[ 'title' ] ) ) {
        D( 'SearchRecord' )->addSearchRecord( $form[ 'title' ] );
        $navHead = '搜索 - ' . $form[ 'title' ];
      }
      if( $form['tags'] > 0 ){
      	$tag = M('Tag')->field( array( 'name' ) )->getbyId( $form['tags'] );
      	$navHead = '标签 - ' . $tag[ 'name' ];
      }
      if( $form['category_id'] > 0 ){
      	$category = M('Category')->field( array( 'name' ) )->getbyId( $form['category_id'] );
      	$navHead = '分类 - ' . $category[ 'name' ];
      }
      $filter = array_merge( $filter, $form );
    }
    $this->assign( 'listFilter', $filter );
    $this->assign( 'navHead', $navHead );
    return $filter;
  }

  public function listAction() {
    $filter = $this->getListFilter();
    $page = I( 'get.page', 1, 'intval' );
    $list = D( 'Article' )->pageList( $filter, $page );
    $this->assign( 'articles', $list );
    //右侧栏数据
    $this->assign( 'menus', D( 'Category' )->findAllMenu() );
    $this->assign( 'lasestRel', D( 'Article' )->findAllLatestRelease() );
    $this->assign( 'hotRec', D( 'Article' )->findAllHotRecommend() );
    $this->assign( 'randRead', D( 'Article' )->findAllRandRead() );
    $this->assign( 'hotTag', D( 'Tag' )->findAllHotTag() );
    $this->assign( 'friendlyLinks', D( 'Link' )->findAllLink() );
    $this->display();
  }

  /*public function test123Action(){
    phpinfo();
    exit;
    echo '<pre>';
    print_r($_SERVER);
    exit;
  }*/

  public function showAction( $id ) {
    $_id = I( 'get.id', 0, 'intval' );
    $_id > 0 && $id = $_id;
    $art = M( 'Article' )->getbyId( $id );
    if ( empty( $art ) ) {
      $this->error( '文章不存在或已删除！' );
    }
    if ( 1 != $art[ 'status' ] && 'aboutme' != $art[ 'alias' ]) {
      $this->error( '文章还未发布，不能访问！' );
    }
    M( 'Article' )->where( array( 'id' => $id ) )->setInc( 'click_count' );
    D( 'ClickRecord' )->addClickRecord( 1, $id );

    $artCon = M( 'ArticleContent' )->getbyArticle_id( $id );
    $art[ 'content' ] = $artCon[ 'content' ];
    $art[ 'tags' ] = D( 'Tag' )->tranIds2Name( $art[ 'tags' ] );

    $this->assign( 'art', $art );
    //右侧栏数据
    $this->assign( 'listFilter', array( 'category_id'=> $art[ 'category_id' ] ) );
    $this->assign( 'menus', D( 'Category' )->findAllMenu() );
    $this->assign( 'lasestRel', D( 'Article' )->findAllLatestRelease() );
    $this->assign( 'hotRec', D( 'Article' )->findAllHotRecommend() );
    $this->assign( 'randRead', D( 'Article' )->findAllRandRead() );
    $this->assign( 'hotTag', D( 'Tag' )->findAllHotTag() );
    $this->assign( 'friendlyLinks', D( 'Link' )->findAllLink() );
    $this->display( 'show' );
  }

  public function shareRecordAction() {
    $artId = I( 'post.artid', 0, 'intval' );
    $type = I( 'post.type', 0, 'intval' );
    $cmd = I( 'post.cmd', '', 'addslashes,trim' );
    if ( !$artId > 0 || !$type > 0 || empty( $cmd ) ) {
      $this->ajaxReturn( array( 'status' => -1 ) );
    }
    $result = D( 'ShareRecord' )->addShareRecord( $artId, $type, $cmd );
    $this->ajaxReturn( array( 'status' => $result ? 1 : 0 ) );
  }

  public function reviewAction() {
    $artId = I( 'post.artid', 0, 'intval' );
    $type = I( 'post.type', 0, 'intval' );
    if ( !$artId > 0 || !$type > 0 ) {
      $this->ajaxReturn( array( 'status' => -1 ) );
    }
    $data = M( 'ClickRecord' )->where( array( 'rel_id' => $artId,
        'session_id' => session_id(), 'type' => $type ) )->find();
    if ( !empty( $data ) ) {
      $this->ajaxReturn( array( 'status' => -2 ) );
    }
    $field = array( 4 => 'zan_count', 5 => 'cai_count' );
    M( 'Article' )->where( array( 'id' => $artId ) )->setInc( ( string ) $field[ $type ] );
    $result = D( 'ClickRecord' )->addClickRecord( $type, $artId );
    $this->ajaxReturn( array( 'status' => $result ? 1 : 0 ) );
  }

  public function linkRecordAction() {
    $linkId = I( 'post.linkid', 0, 'intval' );
    if ( !$linkId > 0 ) {
      $this->ajaxReturn( array( 'status' => -1 ) );
    }
    $result = D( 'ClickRecord' )->addClickRecord( 6, $linkId );
    if( false !== $result ){
      M('Link')->where( array( 'id'=>$linkId ) )->setInc( 'click_count' );
    }
    $this->ajaxReturn( array( 'status' => $result ? 1 : 0 ) );
  }

}
