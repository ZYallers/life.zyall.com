$( function () {
  
  //内容过滤
  $( '#content' ).find('p,center,div').each( function () {
      $(this).removeAttr( 'style' ).removeAttr( 'class' );
  } );
  var title = $( 'title' ).text();
  $( '#content a' ).removeAttr( 'style' ).removeAttr( 'class' ).attr( 'title', title );
  $( '#content img' ).removeAttr( 'width' ).removeAttr( 'height' ).removeAttr( 'style' ).attr( {title: title, alt: title} );
  $('#content embed').each(function(){
     var self = $(this), dw = $(document).width();
     self.removeAttr('style').removeAttr('width').removeAttr('height').css({'width': '100%','height': dw > 760 ? '400px' : '250px'});
  });
  $('#content iframe').each(function(){
     var self = $(this), dw = $(document).width();
     self.removeAttr('style').removeAttr('width').removeAttr('height').attr({'frameborder': '0','allowfullscreen':'true'})
         .css({'width': '100%','height': dw > 760 ? '400px' : '250px'});
  });

  //赞一个、踩一下
  $( '#zan-btn,#cai-btn' ).on( 'click', function () {
    var self = $( this ), type = self.data( 'type' );
    $.post( '/Home/Article/review', {type: type, artid: parseInt( $( '#art_id' ).val() )}, function ( res ) {
      if ( 1 == res.status ) {
        var badge = self.children( '.badge' );
        var old = parseInt( badge.text() );
        badge.html( '&nbsp;' + ( old + 1 ) + '&nbsp;' );
      } else {
        if ( -2 == res.status ) {
          self.children( 'span:first' ).text( 4 == type ? '已点赞' : '已点踩' );
        }
      }
    }, 'json' );
  } );

  //生成二维码
  if( $(document).width() > 760 ){
    $.getScript(window.JS_ROOT + '/lib/jquery/jquery.qrcode.min.js', function(){
      $('#qrcode-cutline').removeAttr( 'style' );
      var isCanvasSupport = !!document.createElement('canvas').getContext;
      $("#qrcode").qrcode({
        render: isCanvasSupport ? 'canvas' : 'table', width: 150, height: 150,
        text: window.location.origin + '/' + $('#alias').val() + '.html'
      });
      !isCanvasSupport && $("#qrcode table").css( { 'margin': 'auto' } );
    });
  }

  //百度分享
  var webRoot = window.location.origin;
  var artAlias = $('#alias').val();
  var artImg = $( '#img' ).val();
  window._bd_share_config = {
    common: {
      bdText: $('title').text(), //自定义分享内容
      //bdDesc: $( '#summary' ).val(), //自定义分享摘要
      bdUrl: webRoot + '/' + artAlias + '.html', //自定义分享url地址
      bdPic: 'http' == artImg.substr(0,4).toLowerCase() ? artImg : webRoot + artImg, //自定义分享图片
      bdSign: 'on',
      onAfterClick: function ( cmd ) {
        $.post( '/Home/Article/shareRecord', {type: 1, artid: parseInt( $( '#art_id' ).val() ), cmd: cmd} );
        if ( 'weixin' == cmd ) {
          setWeixinFoot = window.setInterval( function () {
            var $weixinfoot = $( '.bd_weixin_popup_foot' );
            if ( $weixinfoot.length > 0 ) {
              $weixinfoot.parent().css({'height': 'auto','border-radius': '4px','box-shadow': '0px 2px 6px rgba(100,100,100,0.3)'});
              window.clearInterval( setWeixinFoot );
            }
          }, 500 );
        }
      }
    },
    share: [{"bdStyle": "1",bdSize: 32}]
  };
  $.getScript('http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='+~(-new Date()/36e5), function(){
    $('#article .bdsharebuttonbox').removeAttr('style');
  });

  //highlight代码高亮
  var preCode = $('#content pre[class*=brush]');
  if( preCode.length > 0 ){
    $.getScript('//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/highlight.min.js', function(){
      var styleArr = ['agate','androidstudio','atelier-dune.light','atelier-estuary.light',
                      'atelier-forest.light','atelier-heath.light','atelier-plateau.light',
                      'atelier-seaside.light','dark','foundation','github','kimbie.dark',
                      'monokai','monokai_sublime','obsidian','railscasts','school_book',
                      'solarized_light','tomorrow-night-eighties','tomorrow-night'];
      var style = styleArr[Math.floor( ( Math.random() * styleArr.length ) )];
      $('head').append( '<link href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/styles/'+style+'.min.css" rel="stylesheet"/>' );
      preCode.each(function(i, ele){
        $(ele).attr( 'class', $(ele).attr('class').split(';')[0].split(':').join(' '));
        hljs.highlightBlock(ele);
      });
    });
  }
  
  //畅言评论
  rfChanyanAdv = window.setInterval(function(){
    var advObj = $('div.section-service-w');
    if(advObj.length>0){
      advObj.remove();
      window.clearInterval(rfChanyanAdv);
    }
  },500);

});


