<?php

namespace Admin\Controller;

use THink\Controller;

class UserController extends Controller {

  protected function _initialize() {
    $this->isLogin( 'login,verify,chkLogin' );
  }

  public function verifyAction() {
    $Verify = new \Think\Verify( array( 'expire' => 120, 'useImgBg' => false,
      'length' => 4, 'bg' => array( 221, 221, 221 ), 'codeSet' => '0123456789' ) );
    $Verify->entry();
  }

  public function loginAction() {
    $user = session( 'user' );
    if ( !empty( $user ) && $user[ 'expires' ] - NOW_TIME <= 1800 ) {
      $user[ 'expires' ] = NOW_TIME + 1800;
      session( 'user', $user );
      $this->redirect( 'Admin/Article/list' );
    } else {
      $this->display();
    }
  }

  private function chkVerify( $verify ) {
    $Verify = new \Think\Verify();
    return $Verify->check( $verify );
  }

  public function chkLoginAction() {
    $acc = I( 'post.account' );
    $pwd = I( 'post.pwd' );
    $verify = I( 'post.verify' );
    if ( !$this->chkVerify( $verify ) ) {
      $this->ajaxReturn( array( 'status' => -1, 'msg' => '验证码过期或错误！' ) );
    }
    if ( 'ZYaller' !== $acc || '5ddc4db81a863f36f09996c016562cc6' !== md5( $pwd ) ) {
      $this->ajaxReturn( array( 'status' => -2, 'msg' => '用户或密码错误！' ) );
    }
    session( 'user', array( 'name' => 'ZYaller', 'expires' => NOW_TIME + 1800 ) );
    $this->ajaxReturn( array( 'status' => 1, 'url' => '/Admin/Article/list' ) );
  }

  public function logoutAction() {
    session( 'user', null );
    $this->redirect( '/Admin/User/login' );
  }

}
