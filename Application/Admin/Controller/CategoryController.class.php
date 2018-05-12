<?php

namespace Admin\Controller;

use THink\Controller;

class CategoryController extends Controller {

  protected function _initialize() {
    $this->isLogin();
  }

  public function listAction() {
    $form = I( 'get.form', array(), 'addslashes,trim' );
    $page = I( 'get.page', 1, 'intval' );
    $list = D( 'Category' )->pageList( $form, $page );
    $this->assign( 'form', $form );
    $this->assign( 'categorys', $list );
    $this->display();
  }

  public function editAction() {
    $code = I( 'get.code' );
    $category = array();
    if ( !empty( $code ) ) {
      $Hashids = new \Org\Hashids\Hashids();
      $ids = $Hashids->decode( $code );
      if ( !$ids[ 0 ] > 0 ) {
        $this->error( '必要参数不符合！' );
      }
      $category = D( 'Category' )->getbyid( $ids[ 0 ] );
      if ( empty( $category ) ) {
        $this->error( '该分类已删除或不存在！' );
      }
    }
    $this->assign( 'category', $category );
    $this->display();
  }

  public function saveAction() {
    $form = I( 'post.form' );
    if ( empty( $form[ 'name' ] ) || !isset( $form[ 'menu' ] ) ) {
      $this->error( '提交信息不完整！' );
    }
    $Category = D( 'Category' );
    $Category->startTrans();
    $result = $Category->saveCategory( $form );
    if ( false !== $result ) {
      $Category->commit();
      $this->success( '保存成功。', '/Admin/Category/list' );
    } else {
      $Category->rollback();
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
    //检查分类下是否还有文章
    $row = D( 'Article' )->getbycategory_id( $ids[ 0 ] );
    if ( !empty( $row ) ) {
      $this->ajaxReturn( array( 'status' => -3, 'msg' => '该分类下还有文章，请先删除所有文章！' ) );
    }
    $Category = D( 'Category' );
    $Category->startTrans();
    $result = $Category->deleteCategory( $ids[ 0 ] );
    if ( $result ) {
      $Category->commit();
      $this->ajaxReturn( array( 'status' => 1 ) );
    } else {
      $Category->rollback();
      $this->ajaxReturn( array( 'status' => 0, 'msg' => '删除失败！' ) );
    }
  }

  public function chkNameExistAction() {
    $name = I( 'post.name' );
    cleanXSS( $name );
    $except = I( 'post.except', 0, 'intval' );
    if ( empty( $name ) ) {
      $this->ajaxReturn( array( 'status' => -1, 'msg' => '缺少必要参数！' ) );
    }
    $category = D( 'Category' )->findByNameAndExcept( $name, $except );
    if ( !empty( $category ) ) {
      $this->ajaxReturn( array( 'status' => 1, 'msg' => '名称已存在！' ) );
    }
    $this->ajaxReturn( array( 'status' => 0, 'msg' => '名称不存在！' ) );
  }

  public function chkAliasExistAction() {
    $alias = I( 'post.alias' );
    cleanXSS( $alias );
    if ( empty( $alias ) ) {
      $this->ajaxReturn( array( 'status' => -1, 'msg' => '缺少必要参数！' ) );
    }
    $article = D( 'Article' )->findByAliasAndExcept( $alias );
    if ( !empty( $article ) ) {
      $this->ajaxReturn( array( 'status' => 1, 'msg' => '别名已存在！' ) );
    }
    $except = I( 'post.except', 0, 'intval' );
    $category = D( 'Category' )->findByAliasAndExcept( $alias, $except );
    if ( !empty( $category ) ) {
      $this->ajaxReturn( array( 'status' => 1, 'msg' => '别名已存在！' ) );
    }
    $tag = D( 'Tag' )->findByNameAndExcept( $alias );
    if ( !empty( $tag ) ) {
      $this->ajaxReturn( array( 'status' => 1, 'msg' => '别名已存在！' ) );
    }
    $this->ajaxReturn( array( 'status' => 0, 'msg' => '别名不存在！' ) );
  }

}
