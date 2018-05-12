<?php

namespace Home\Controller;

use THink\Controller;

class WeixinController extends Controller {

  const APPID = "wxb553dfd1291ac2f8";
  const APPSECRET = "d4624c36b6795d1d99dcf0547af5443d";

	public function indexAction(){
		echo MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
	}

	public function OAuthAction(){
		$appid = self::APPID;
		$redirectUri = urlencode( 'http://zyall.com/Home/Weixin/openid' );
		$scope = "snsapi_base";
		$state = sha1( uniqid( mt_rand(), true ) );
		$getCodeApi = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}"
		   ."&redirect_uri={$redirectUri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
    redirect( $getCodeApi );
	}

	public function openidAction(){
		$code = $_GET['code'];
		if(empty($code)){
			$this->error('用户授权失败');
		}else{
			$appid = self::APPID;
			$appsecret = self::APPSECRET;
			$getAccessTokenApi = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}".
					"&secret={$appsecret}&code={$code}&grant_type=authorization_code";
			$response = self::curlHttp($getAccessTokenApi,'POST');
	    $data = json_decode($response, true);
    	if(empty($data['openid'])){
  			$this->error('获取用户openid失败');
    	}else{
    		die("<h1>您的openid是：{$data['openid']}</h1>");
    	}
		}
	}

    /**
     * curl发送HTTP请求方法
     * @param $url
     * @param string $method
     * @param array $params
     * @param array $header
     * @param int $timeout
     * @param bool|false $multi
     * @return mixed
     * @throws Exception
     */
    public static function curlHttp($url, $method = 'GET', $params = array(),  $header = array(), $timeout = 30, $multi = false){
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_TIMEOUT, $timeout );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
        switch(strtoupper($method)){
            case 'GET':
                if( !empty( $params ) ){
                    $uri = parse_url( $url );
                    $url .= ( empty( $uri['query'] ) ? '?' : '&' ) . http_build_query( $params );
                }
                curl_setopt( $curl, CURLOPT_URL, $url );
                break;
            case 'POST':
                curl_setopt( $curl, CURLOPT_URL, $url );
                curl_setopt( $curl, CURLOPT_POST, true );
                $params = $multi ? $params : http_build_query($params);  //判断是否传输文件
                curl_setopt( $curl, CURLOPT_POSTFIELDS, $params );
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        $response  = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        if($error){
            throw new Exception('请求发生错误：' . $error);
        }
        return $response;
    }

	private function getAccessToken($appid, $appsecret){
		$now = time();
    $file = RUNTIME_PATH.'Data/weixin_access_token.jpg';
  	$data = json_decode( file_get_contents( $file ), true );
    if ( empty( $data['expire_time'] ) || $data['expire_time'] < $now || empty( $data[ 'access_token' ] ) ) {
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
      $response = self::curlHttp( $url );
      $data = json_decode(  $response, true );
      if ( !empty( $data['access_token'] ) ) {
        $data = array( 'expire_time' => $now + $data['expires_in'] - 60, 'access_token' => $data['access_token'] );
        $fp = fopen( $file, 'w' );
        fwrite( $fp, json_encode( $data ) );
        fclose( $fp );
      }
    }
    return $data;
	}

	public function sendMsgAction(){
		$openid = $_GET['touser'];
		if(empty($openid)){
			$this->error('缺乏必要参数，无法处理请求');
		}
		$data = $this->getAccessToken(self::APPID, self::APPSECRET);
		if(empty($data['access_token'])){
			$this->error('获取access_token失败');
		}
		$accessToken = $data['access_token'];
		$api = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
		$params = array(
			'touser' => $openid,
			'url' => 'http://buyer.mmcmmc.com/?a=product.productAppDetail&pid=28888&from=appItemList',
			'template_id' => 'wmKL7l_10_Hulifq6mC30HcY1D8l4pzxE2SEXOw8ji4',
			'data' => array(
				'buyer' => array(
					'value' => '咸蛋超人',
					'color' => '#173177'
				),
				'item_name' => array(
					'value' => '欧洲站2015秋冬新款茧型',
					'color' => '#173177'
				),
				'create_date' => array(
					'value' => '11月30日 14时20分',
					'color' => '#173177'
				)
			)
		);
		$response = self::curlHttp($api,'POST',json_encode( $params ), array(), 30, true);
		echo '<pre>'; print_r($response); exit;
	}
}