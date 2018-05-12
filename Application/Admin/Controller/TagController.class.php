<?php

namespace Admin\Controller;

use THink\Controller;

class TagController extends Controller {

  protected function _initialize() {
    $this->isLogin();
  }

  public function listAction() {
    $form = I( 'get.form', array(), 'addslashes,trim' );
    $page = I( 'get.page', 1, 'intval' );
    $list = D( 'Tag' )->pageList( $form, $page );
    $this->assign( 'form', $form );
    $this->assign( 'tags', $list );
    $this->display();
  }

  public function editAction() {
    $code = I( 'get.code' );
    $tag = array();
    if ( empty( $code ) ) {
      $this->error( '缺少必要参数！' );
    }
    $Hashids = new \Org\Hashids\Hashids();
    $ids = $Hashids->decode( $code );
    if ( !$ids[ 0 ] > 0 ) {
      $this->error( '必要参数不符合！' );
    }
    $tag = D( 'Tag' )->getbyid( $ids[ 0 ] );
    if ( empty( $tag ) ) {
      $this->error( '该标签已删除或不存在！' );
    }
    $this->assign( 'tag', $tag );
    $this->display();
  }

  public function saveAction() {
    $form = I( 'post.form' );
    if ( empty( $form[ 'name' ] ) || !$form[ 'id' ] > 0 ) {
      $this->error( '提交信息不完整！' );
    }
    $Tag = D( 'Tag' );
    $Tag->startTrans();
    $result = $Tag->data( array_merge( $form, array( 'update_time' => NOW_TIME ) ) )->save();
    if ( false !== $result ) {
      $Tag->commit();
      $this->success( '保存成功。', '/Admin/Tag/list' );
    } else {
      $Tag->rollback();
      $this->error( '保存失败！' );
    }
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
    $Tag = D( 'Tag' );
    $Tag->startTrans();
    $result = $Tag->deleteTag( $ids[ 0 ] );
    if ( $result ) {
      $Tag->commit();
      $this->ajaxReturn( array( 'status' => 1 ) );
    } else {
      $Tag->rollback();
      $this->ajaxReturn( array( 'status' => 0, 'msg' => '删除失败！' ) );
    }
  }

  public function chkNameExistAction() {
    $name = I( 'post.name' );
    cleanXSS( $name );
    if ( empty( $name ) ) {
      $this->ajaxReturn( array( 'status' => -1, 'msg' => '缺少必要参数！' ) );
    }
    $article = D( 'Article' )->findByAliasAndExcept( $name );
    if ( !empty( $article ) ) {
      $this->ajaxReturn( array( 'status' => 1, 'msg' => '名称已存在！' ) );
    }
    $category = D( 'Category' )->findByAliasAndExcept( $name );
    if ( !empty( $category ) ) {
      $this->ajaxReturn( array( 'status' => 1, 'msg' => '名称已存在！' ) );
    }
    $except = I( 'post.except', 0, 'intval' );
    $tag = D( 'Tag' )->findByNameAndExcept( $name, $except );
    if ( !empty( $tag ) ) {
      $this->ajaxReturn( array( 'status' => 1, 'msg' => '名称已存在！' ) );
    }
    $this->ajaxReturn( array( 'status' => 0, 'msg' => '名称不存在！' ) );
  }

}
