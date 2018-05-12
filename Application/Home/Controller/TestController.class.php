<?php

namespace Home\Controller;

use THink\Controller;

class TestController extends Controller {

	public function indexAction(){
		echo MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
	}

}