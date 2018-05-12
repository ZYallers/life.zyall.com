<?php

namespace Home\Model;

use Think\Model;

class CategoryModel extends Model {

  public function findAllMenu() {
    $rows = $this->field( array( 'id', 'name', 'alias' ) )
        ->where( array( 'menu' => 1 ) )->order( array( 'id' ) )->select();
    return $rows;
  }
  
}
