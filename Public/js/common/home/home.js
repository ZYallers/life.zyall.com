$( function () {

  //改变窗口大小重置样式
  $( window ).on( 'resize', function () {
    $( '#left-sidbar,#toolbar,#mainer,#right-sidbar,#footer,#mini-logo' ).removeAttr( 'style' );
  } );

  //搜索提交
  $( '#ft-search-submit' ).on( 'click', function () {
    $( '#ft-search-form' ).get( 0 ).submit();
  });
  
  //切换栏数
  $( '#switchBarLink' ).on( 'click', function () {
    if ( $( '#left-sidbar' ).is( ':hidden' ) ) {
      $( '#left-sidbar,#toolbar' ).removeAttr( 'style' );
      if ( $( document ).width() > 1080 ) {
        $( '#mainer' ).css( {'margin': '40px 290px 0px 330px'} );
        $( '#footer' ).css( {'margin': '0px 290px 40px 330px'} );
      } else {
        $( '#mainer' ).css( {'margin': '40px 20px 0px 330px'} );
        $( '#right-sidbar' ).css( {'margin-left': '330px'} );
        $( '#footer' ).css( {'margin': '0px 20px 40px 330px'} );
      }
      $( this ).attr( 'title', '切换为两栏' ).removeClass( 'icon-expand' ).addClass( 'icon-compress' );
      $('#mini-logo').removeAttr( 'style' );
    } else {
      $( '#left-sidbar' ).css( 'display', 'none' );
      $( '#toolbar' ).css( 'left', '0px' );
      if ( $( document ).width() > 1080 ) {
        $( '#mainer' ).css( {'margin': '40px 290px 0px 80px'} );
        $( '#footer' ).css( {'margin': '0px 290px 40px 80px'} );
      } else {
        $( '#mainer' ).css( {'margin': '40px 20px 0px 80px'} );
        $( '#right-sidbar' ).css( {'margin-left': '80px'} );
        $( '#footer' ).css( {'margin': '0px 20px 40px 80px'} );
      }
      $( this ).attr( 'title', '切换为三栏' ).removeClass( 'icon-compress' ).addClass( 'icon-expand' );
      $('#mini-logo').css( { 'display': 'block' } );
    }
  } );

  //侧边栏栏目收缩
  $( '#right-sidbar' ).find( '.panel-head' ).on( 'click', function () {
    var $this = $( this ), $next = $this.next();
    if ( $next.is( ':hidden' ) ) {
      $next.slideDown( 'fast', function () {
        $this.css( 'border-radius', '4px 4px 0px 0px' );
        $this.children( 'span:last' ).removeClass( 'icon-chevron-up' ).addClass( 'icon-chevron-down' );
      } );
    } else {
      $next.slideUp( 'normal', function () {
        $this.css( 'border-radius', '4px' );
        $this.children( 'span:last' ).removeClass( 'icon-chevron-down' ).addClass( 'icon-chevron-up' );
      } );
    }
  } );

  //友链点击
  $( '#panel-links a.tips' ).on( 'click', function () {
    $.post( '/Home/Article/linkRecord', {linkid: parseInt( $( this ).data( 'id' ) )} );
  } );

  //显示手机搜索框
  $('#wap-switch-search').on( 'click', function(){
    if( $('#ft-search').is(':hidden') ){
      $('#left-sidbar').animate({'height':'285px'},300,function(){
        $('#ft-search').css({display: 'block'});
      });
    }else{
      $('#left-sidbar').animate({'height':'235px'},300,function(){
        $('#ft-search').removeAttr('style');
      });
    }
  } );

} );