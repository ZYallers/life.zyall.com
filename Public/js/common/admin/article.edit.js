$( function () {
  //百度编辑器
  window.editor = UE.getEditor( 'content' );
  // 标签
  $( '#tags' ).tags( {url: '/Admin/Article/searchTags', maxchoose: 6} );
  // 按钮点击
  var valiFormFunc = function () {
    var $form = $( '[rel=form]' );
    var $title = $form.find( '#title' );
    var $alias = $form.find( '#alias' );
    var $category_id = $form.find( '#category_id' );
    var $tags = $form.find( '#tags' );
    var $content = $form.find( '#content' );
    $content.val( window.editor.getContent() );
    if ( '' === $.trim( $title.val() ) ) {
      alert( '标题不能为空' );
      return false;
    }
    if ( '' === $.trim( $alias.val() ) ) {
      alert( '别名不能为空' );
      return false;
    }
    if ( !( $category_id.val() > 0 ) ) {
      alert( '请选择分类' );
      return false;
    }
    if ( '' === $.trim( $tags.val() ) ) {
      alert( '至少输入一个标签' );
      return false;
    }
    if ( '' === $.trim( $content.val() ) ) {
      alert( '内容不能为空' );
      return false;
    }
    return true;
  };
  var chkTitleExist = function ( callback ) {
    var title = $( '[rel=form]' ).find( '#title' ).val();
    var id = parseInt( $( '[rel=form]' ).find( '#id' ).val() );
    $.post( '/Admin/Article/chkTitleExist', {title: title, except: id}, function ( res ) {
      callback( res );
    }, 'json' );
  };
  var chkAliasExist = function ( callback ) {
    var alias = $( '[rel=form]' ).find( '#alias' ).val();
    var id = parseInt( $( '[rel=form]' ).find( '#id' ).val() );
    $.post( '/Admin/Article/chkAliasExist', {alias: alias, except: id}, function ( res ) {
      callback( res );
    }, 'json' );
  };
  $( '[rel=submit]' ).on( 'click', function () {
    if ( valiFormFunc() ) {
      chkTitleExist( function ( res ) {
        if ( 0 == res.status ) {
          chkAliasExist( function ( res ) {
            if ( 0 == res.status ) {
              $( '[rel=status]' ).val( 1 );
              $( '[rel=form]' ).get( 0 ).submit();
            } else {
              alert( res.msg );
            }
          } );
        } else {
          alert( res.msg );
        }
      } );
    }
  } );
  $( '[rel=draft]' ).on( 'click', function () {
    if ( valiFormFunc() ) {
      chkTitleExist( function ( res ) {
        if ( 0 == res.status ) {
          chkAliasExist( function ( res ) {
            if ( 0 == res.status ) {
              $( '[rel=status]' ).val( 2 );
              $( '[rel=form]' ).get( 0 ).submit();
            } else {
              alert( res.msg );
            }
          } );
        } else {
          alert( res.msg );
        }
      } );
    }
  } );
} );

