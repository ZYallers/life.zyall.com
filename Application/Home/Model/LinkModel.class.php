<?php

namespace Home\Model;

class LinkModel extends \Think\Model {

  public function findAllLink() {
    $rows = $this->field( explode( ',', 'id,name,summary,href,click_count' ) )->select();
    return $rows;
  }

}
