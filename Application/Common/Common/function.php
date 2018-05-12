<?php

/**
 * 公共模块公共函数文件
 */

/**
 * 函数名称:encrypt
 * 函数作用:加密解密字符串
 * 使用方法:
 * 加密     :encrypt('str','E','nowamagic');
 * 解密     :encrypt('被加密过的字符串','D','nowamagic');
 * 参数说明:
 * $str      :需要加密解密的字符串
 * $type     :判断是加密还是解密:E:加密   D:解密
 * $salt     :加密的钥匙(密匙);
 */
function encrypt( $str, $type = 'E', $salt = '' ) {
  $key = empty( $salt ) ? md5( session_id ) : md5( $salt );
  $keyLen = strlen( $key );
  $str = $type == 'D' ? base64_decode( $str ) : substr( md5( $str . $key ), 0, 8 ) . $str;
  $strlen = strlen( $str );
  $rndkey = $box = array();
  $result = '';
  for ( $i = 0; $i <= 255; $i++ ) {
    $rndkey[ $i ] = ord( $key[ $i % $keyLen ] );
    $box[ $i ] = $i;
  }
  for ( $j = $i = 0; $i < 256; $i++ ) {
    $j = ($j + $box[ $i ] + $rndkey[ $i ]) % 256;
    $tmp = $box[ $i ];
    $box[ $i ] = $box[ $j ];
    $box[ $j ] = $tmp;
  }
  for ( $a = $j = $i = 0; $i < $strlen; $i++ ) {
    $a = ($a + 1) % 256;
    $j = ($j + $box[ $a ]) % 256;
    $tmp = $box[ $a ];
    $box[ $a ] = $box[ $j ];
    $box[ $j ] = $tmp;
    $result .= chr( ord( $str[ $i ] ) ^ ($box[ ($box[ $a ] + $box[ $j ]) % 256 ]) );
  }
  if ( $type == 'D' ) {
    if ( substr( $result, 0, 8 ) == substr( md5( substr( $result, 8 ) . $key ), 0, 8 ) ) {
      return substr( $result, 8 );
    } else {
      return '';
    }
  } else {
    return str_replace( '=', '', base64_encode( $result ) );
  }
}

/**
 * @param String|Array $param
 * @param boolean $high 默认为false，安全级别高
 * @return boolean
 */
function cleanXSS( &$param, $high = false ) {
  if ( !is_array( $param ) ) {
    $param = trim( $param ); //清理空格
    $param = strip_tags( $param ); //过滤html标签
    $param = htmlspecialchars( $param ); //将字符内容转化为html实体
    $param = addslashes( $param );
    if ( !$high ) {
      return true;
    }
    $param = str_replace( array( '"', "\\", "'", "/", "..", "../", "./", "//" ), '', $param );
    $no = '/%0[0-8bcef]/';
    $param = preg_replace( $no, '', $param );
    $no = '/%1[0-9a-f]/';
    $param = preg_replace( $no, '', $param );
    $no = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
    $param = preg_replace( $no, '', $param );
    return true;
  }
  $keys = array_keys( $param );
  foreach ( $keys as $key ) {
    cleanXSS( $param [ $key ], $high );
  }
  return true;
}
