<?php

namespace Admin\Controller;

use THink\Controller;

class ArticleController extends Controller {

  protected function _initialize() {
    $this->isLogin();
  }

  public function listAction() {
    $form = I( 'get.form', array(), 'addslashes,trim' );
    $page = I( 'get.page', 1, 'intval' );
    $list = D( 'Article' )->pageList( $form, $page );
    $this->assign( 'form', $form );
    $this->assign( 'articles', $list );
    $this->assign( 'categorys', D( 'Category' )->findAll( true ) );
    $this->display();
  }

  public function editAction() {
    $code = I( 'get.code' );
    $article = array();
    if ( !empty( $code ) ) {
      $Hashids = new \Org\Hashids\Hashids();
      $ids = $Hashids->decode( $code );
      if ( !$ids[ 0 ] > 0 ) {
        $this->error( '必要参数不符合！' );
      }
      $article = D( 'Article' )->findArticleByPk( $ids[ 0 ] );
      if ( empty( $article ) ) {
        $this->error( '该文章已删除或不存在！' );
      }
    }
    $this->assign( 'article', $article );
    $this->assign( 'categorys', D( 'Category' )->findAll() );
    $this->display();
  }

  public function searchTagsAction() {
    $tag = trim( I( 'post.tag' ) );
    if ( empty( $tag ) ) {
      $this->ajaxReturn( array( 'status' => 0 ) );
    }
    $except = trim( I( 'post.except', '' ), ',' );
    $data = D( 'Tag' )->findAllByNameAndExcept( $tag, $except );
    $tags = array();
    foreach ( $data as $value ) {
      $tags[] = $value[ 'name' ];
    }
    $this->ajaxReturn( array( 'status' => 1, 'data' => $tags ) );
  }

  public function saveAction() {
    $form = I( 'post.form' );
    if ( empty( $form[ 'title' ] ) || $form[ 'category_id' ] == 0 || empty( $form[ 'tags' ] ) || empty( $form[ 'content' ] ) ) {
      $this->error( '提交信息不完整！' );
    }
    //内容不进行addslashes处理
    $content = $form[ 'content' ];
    unset( $form[ 'content' ] );
    cleanXSS( $form );
    $form[ 'content' ] = $content;
    $form[ 'alias' ] = strtolower( $form[ 'alias' ] );
    
    $Article = D( 'Article' );
    $Article->startTrans();
    $result = $Article->saveArticle( $form );
    if ( false !== $result ) {
      $Article->commit();
      if( 0 == intval( $form[ 'id' ] ) && !empty( $form[ 'alias' ] ) ) {
        $this->baiduLinkInitiativePush( 'http://www.zyall.com/' . $form[ 'alias' ] . '.html' );
      }
      $this->success( '保存成功。', 'javascript:history.go(-2);' );
    } else {
      $Article->rollback();
      $this->error( '保存失败！' );
    }
  }

  /**
   * 百度链接主动推送
   */
  private function baiduLinkInitiativePush( $urls ) {
    $ch = curl_init();
    $options =  array(
      CURLOPT_URL => 'http://data.zz.baidu.com/urls?site=www.zyall.com&token=so4pYlvac58hlPG6',
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POSTFIELDS => implode( "\n", ( array ) $urls ),
      CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
    );
    curl_setopt_array( $ch, $options );
    $result = curl_exec( $ch );
    return $result;
  }

  public function deleteAction() {
    $code = I( 'post.code' );
    if ( empty( $code ) ) {
      $this->ajaxReturn( array( 'status' => -1, 'msg' => '缺少必要参数！' ) );
    }
    $Hashids = new \Org\Hashids\Hashids();
    $ids = $Hashids->decode( $code );
    if ( !$ids[ 0 ] > 0 ) {
      $this->ajaxReturn( array( 'status' => -2, 'msg' => '必要参数不符合！' ) );
    }

    $Article = D( 'Article' );
    $Article->startTrans();
    $result = $Article->deleteArticle( $ids[ 0 ] );
    if ( $result ) {
      $Article->commit();
      $this->ajaxReturn( array( 'status' => 1 ) );
    } else {
      $Article->rollback();
      $this->ajaxReturn( array( 'status' => 0, 'msg' => '删除失败！' ) );
    }
  }

  public function chkTitleExistAction() {
    $title = I( 'post.title' );
    cleanXSS( $title );
    $except = I( 'post.except', 0, 'intval' );
    if ( empty( $title ) ) {
      $this->ajaxReturn( array( 'status' => -1, 'msg' => '缺少必要参数！' ) );
    }
    $article = D( 'Article' )->findByTitleAndExcept( $title, $except );
    if ( !empty( $article ) ) {
      $this->ajaxReturn( array( 'status' => 1, 'msg' => '标题已存在！' ) );
    }
    $this->ajaxReturn( array( 'status' => 0, 'msg' => '标题不存在！' ) );
  }

  public function chkAliasExistAction() {
    $alias = I( 'post.alias' );
    cleanXSS( $alias );
    if ( empty( $alias ) ) {
      $this->ajaxReturn( array( 'status' => -1, 'msg' => '缺少必要参数！' ) );
    }
    $except = I( 'post.except', 0, 'intval' );
    $article = D( 'Article' )->findByAliasAndExcept( $alias, $except );
    if ( !empty( $article ) ) {
      $this->ajaxReturn( array( 'status' => 1, 'msg' => '别名已存在！' ) );
    }
    $category = D( 'Category' )->findByAliasAndExcept( $alias );
    if ( !empty( $category ) ) {
      $this->ajaxReturn( array( 'status' => 1, 'msg' => '别名已存在！' ) );
    }
    $tag = D( 'Tag' )->findByNameAndExcept( $alias );
    if ( !empty( $tag ) ) {
      $this->ajaxReturn( array( 'status' => 1, 'msg' => '别名已存在！' ) );
    }
    $this->ajaxReturn( array( 'status' => 0, 'msg' => '标签不存在！' ) );
  }

}
