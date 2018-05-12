$( function () {
  //显示提示
  var showAlert = function ( msg ) {
    var $alert = $( '.j-alert' );
    if ( msg ) {
      var $span = $alert.children( 'span:last' );
      $span.text( msg );
      $alert.slideDown( function () {
        window.setTimeout( function () {
          $span.text( '' );
          $alert.slideUp();
        }, 3000 );
      } );
    }
  };

  //更新验证码
  var refreshVerify = function () {
    var $verify = $( '.j-verify' );
    var url = $verify.data( 'url' ).replace( /\./g, '/' );
    $verify.attr( 'src', '/' + url + '?_=' + new Date().getTime() );
  };

  //立即登录
  $( '.j-login' ).on( 'click', function () {
    var $form = $( '.j-form' );
    var account = $.trim( $form.find( 'input[name=account]' ).val() );
    var pwd = $.trim( $form.find( 'input[name=pwd]' ).val() );
    var verify = $.trim( $form.find( 'input[name=verify]' ).val() );
    if ( '' == account || '' == pwd || '' == verify ) {
      showAlert( '用户、密码或验证码存在未填写！' );
      refreshVerify();
      return false;
    }
    $.post( $form.attr( 'action' ), $form.serialize(), function ( res ) {
      if ( 0 > res.status ) {
        showAlert( res.msg );
        refreshVerify();
      } else {
        window.location.href = res.url;
      }
    }, 'json' );
  } );

  //更换验证码
  $( '.j-verify' ).on( 'click', function () {
    refreshVerify();
  } );

  $( '.j-form' ).find( 'input[name=account]' ).focus();

} );