$( function () {

  // 显示侧边栏
  $( '#top-sidbar-menubtn a' ).on( 'click', function () {
    var $leftBar = $( '#left-sidbar' );
    $leftBar.css( {display: 'block'} ).animate( {'width': '60px'}, 200, function () {
      $leftBar.find( '.lsl' ).css( {display: 'block'} );
    } );
  } );

  // 隐藏侧边栏
  $( '#wap-hideleftbar' ).on( 'click', function () {
    var $leftBar = $( '#left-sidbar' );
    $leftBar.find( '.lsl' ).css( {display: 'none'} );
    $leftBar.animate( {'width': '0px'}, 200, function () {
      $leftBar.css( {display: 'none'} );
    } );
  } );

} );