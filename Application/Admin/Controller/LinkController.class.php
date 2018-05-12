<?php

namespace Admin\Controller;

use THink\Controller;

class LinkController extends Controller {

  protected function _initialize() {
    $this->isLogin();
  }

  public function listAction() {
    $form = I( 'get.form', array(), 'addslashes,trim' );
    $page = I( 'get.page', 1, 'intval' );
    $list = D( 'Link' )->pageList( $form, $page );
    $this->assign( 'form', $form );
    $this->assign( 'links', $list );
    $this->display();
  }

  public function editAction() {
    $code = I( 'get.code' );
    $link = array();
    if ( !empty( $code ) ) {
      $Hashids = new \Org\Hashids\Hashids();
      $ids = $Hashids->decode( $code );
      if ( !$ids[ 0 ] > 0 ) {
        $this->error( '必要参数不符合！' );
      }
      $link = D( 'Link' )->getbyid( $ids[ 0 ] );
      if ( empty( $link ) ) {
        $this->error( '该友链已删除或不存在！' );
      }
    }
    $this->assign( 'link', $link );
    $this->display();
  }

  public function saveAction() {
    $form = I( 'post.form' );
    if ( empty( $form[ 'name' ] ) || empty( $form[ 'href' ] ) ) {
      $this->error( '提交信息不完整！' );
    }
    $Link = D( 'Link' );
    $Link->startTrans();
    $result = $Link->saveLink( $form );
    if ( false !== $result ) {
      $Link->commit();
      $this->success( '保存成功。', '/Admin/Link/list' );
    } else {
      $Link->rollback();
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
    $Link = D( 'Link' );
    $Link->startTrans();
    $result = $Link->deleteLink( $ids[ 0 ] );
    if ( $result ) {
      $Link->commit();
      $this->ajaxReturn( array( 'status' => 1 ) );
    } else {
      $Link->rollback();
      $this->ajaxReturn( array( 'status' => 0, 'msg' => '删除失败！' ) );
    }
  }

}
