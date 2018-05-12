( function () {

  $.tags = {
    delayEvent: function ( obj, func, wait ) {
      var id = $( obj ).data( 'delayEventId' );
      if ( !id ) {
        id = Math.random() * 999999 + 1;
        $( obj ).data( 'delayEventId', id );
      }
      wait = wait || 200;
      window.delayEventLock = window.delayEventLock || {};
      if ( window.delayEventLock[id] ) {
        window.clearTimeout( window.delayEventLock[id] );
        delete window.delayEventLock[id];
      }
      window.delayEventLock[id] = window.setTimeout( function () {
        func.apply( obj, arguments );
        delete window.delayEventLock[id];
      }, wait );
      return window.delayEventLock[id];
    },
    loadStyle: function () {
      var $css = $( 'head' ).find( '#tags-style' );
      if ( 0 === $css.length ) {
        var style = '' +
                '.tags-hide{position:absolute;display:none;}\n' +
                '.tags-show>div{padding: 5px;cursor: pointer;}\n' +
                '.tags-show{border: 1px solid #ccc;position: absolute;z-index: 9999;border-radius: 4px;background: #fff;width: 100%}\n' +
                '.tags-show>div:hover,.tags-mouseover{background: #0a8;color: #fff;}\n' +
                '.tags-showdiv{border: 1px solid #ccc;border-radius: 4px;padding: 2px 0;}\n' +
                '.tags-showdiv>.tags-selected{display: inline-block}\n'+
                '.tags-showdiv>.tags-selected>div{cursor: default;display: inline-block;margin: 1px;border: 1px solid #0a8;background: #0a8;padding: 0px 5px 2px 5px;border-radius: 4px;}\n' +
                '.tags-showdiv>.tags-selected>div>txt{font-size: 14px;color: #fff;}\n' +
                '.tags-showdiv>.tags-selected>div>icon{font-size: 14px;margin-left: 5px;cursor: pointer;color: #fff;line-height: 1em;}\n' +
                '.tags-showdiv>.tags-selected>div>icon:hover{color: #0ab;}\n'+
                '.tags-showdiv>input{width: 100px;height: auto;padding: 0;display: inline-block;margin: 3px 2px;border: none;box-shadow: none;}';
        $( 'head' ).append( '<style type="text/css" id="tags-style">\n' + style + '\n</style>' );
      }
    },
    init: function ( ele, opts ) {
    	$( ele ).after( '<input rel="tags-hideinput" type="hidden" id="' + $( ele ).attr( 'id' ) + '" name="' + $( ele ).attr( 'name' ) + '"/>\n' );
      $( ele ).attr( {rel: 'tags-input'} ).removeAttr('id').removeAttr('name');
      $( ele ).data( 'opts', opts );
      $( ele ).attr( 'index', -1 );
      $( ele ).parent().append('<div rel="showtags" class="tags-showdiv"></div>\n');
      $('[rel=showtags]').append('<div rel="tags-selected" class="tags-selected"></div>\n')
      	.append( $( ele ) ).append( $('[rel=tags-hideinput]') );
      $('[rel=showtags]').after( '<div rel="tags-cands" class="tags-hide"></div>\n' );
    },
    delChooses: function ( obj ) { //删除自动完成需要的所有DIV
      $('[rel=tags-cands]').html( '' ).attr({class:'tags-hide'});
    },
    getData: function ( ele, func ) {
      var opts = $( ele ).data( 'opts' );
      $.post( opts.url, {tag: ele.value, limit: opts.showlimit, except: $('[rel=tags-hideinput]').val()}, function ( res ) {
        func.call( ele, res );
      }, 'json' );
    },
    pressKey: function ( e, obj ) { //响应键盘
      var $cands = $( '[rel=tags-cands]' );
      var index = obj.getAttribute( 'index' );
      var length = $cands.children( 'div' ).length;
      switch ( e.keyCode ) {
        case 40: //光标键"↓"
          index++;
          obj.setAttribute( 'index', index );
          if ( index > length ) {
            obj.setAttribute( 'index', 0 );
          } else if ( index < length ) {
            var $choose = $cands.children( 'div[index=' + index + ']' );
            $choose.get( 0 ).className = 'tags-mouseover';
            obj.value = $choose.attr( 'seq' );
            $choose.siblings().removeClass();
          } else {
            obj.setAttribute( 'index', -1 );
            obj.value = obj.getAttribute( 'search' );
            $cands.children().removeClass();
          }
          break;
        case 38: //光标键"↑"
          index--;
          obj.setAttribute( 'index', index );
          if ( index < -1 ) {
            index = length - 1;
            obj.setAttribute( 'index', index );
          } else if ( index == -1 ) {
            obj.value = obj.getAttribute( 'search' );
            $cands.children().removeClass();
          }
          if ( index > -1 ) {
            var $choose = $cands.children( 'div[index=' + index + ']' );
            $choose.get( 0 ).className = 'tags-mouseover';
            obj.value = $choose.attr( 'seq' );
            $choose.siblings().removeClass();
          }
          break;
        case 13: //回车键
          var $selected = $cands.children( 'div[index=' + index + ']' );
          if ( $selected.length > 0 ) {
            $.tags.selectDiv( $selected.get( 0 ), 1 );
          } else {
          	if(!$.trim(obj.value)){
          		return ;
          	}
            $.tags.selectDiv( $cands, 2 );
          }
          $cands.get( 0 ).className = 'tags-hide';
          obj.setAttribute( 'index', -1 );
          break;
        default:
          obj.setAttribute( 'index', -1 );
          break;
      }
    },
    selectDiv: function ( obj, type ) {
      var $input = $( '[rel=tags-input]' );
      var opts = $input.data( 'opts' );
      var $selected = $( '[rel=tags-selected]' );
      if ( opts.maxchoose <= $selected.children().length ) {
        alert( 'Max can choose：' + opts.maxchoose );
        return;
      }
      var $cands = $( '[rel=tags-cands]' );
      var $hide = $( '[rel=tags-hideinput]' );
      if ( 1 == type ) {
        $hide.val( $hide.val() + ',' + $( obj ).attr( 'seq' ) );
        $cands.attr( {class:'tags-hide'} );
        $.tags.showSelectedTags( $input.get( 0 ), $( obj ).attr( 'seq' ) );
      } else if ( 2 == type ) {
        $hide.val( $hide.val() + ',' + $input.val() );
        obj.attr( {class:'tags-hide'} );
        $.tags.showSelectedTags( $input.get( 0 ), $input.val() );
      }
      $input.val( '' );
    },
    showSelectedTags: function ( obj, text ) {
      var $div = $( '[rel=tags-selected]' );
      $div.append( '<div><txt>' + text + '</txt><icon class="icon-times"></icon></div>' );
      $div.children( 'div:last' ).children( 'icon' ).on( 'click', function () {
        $.tags.delSelectedTag( $( this ).parent().get( 0 ) );
      } );
      ;
    },
    delSelectedTag: function ( obj ) {
      var txt = $( obj ).children( 'txt' ).text();
      var $hide = $( '[rel=tags-hideinput]' );
      var oldVal = $hide.val();
      $hide.val( oldVal.replace( ',' + txt, '' ) );
      obj.remove();
    }
  };

  $.fn.tags = function ( options ) {
    this.defaults = {url: '', maxchoose: 5, showlimit: 10};
    var opts = $.extend( this.defaults, options );
    $.tags.loadStyle();
    this.each( function ( i, ele ) {
      $.tags.init( ele, opts );
      if ( $( ele ).val() ) {
        $( '[rel=tags-hideinput]' ).val( ',' + $( ele ).val() );
        var arr = $( ele ).val().split( ',' );
        for ( var i = 0, len = arr.length; i < len; i++ ) {
          $.tags.showSelectedTags( ele, arr[i] );
        }
        $( ele ).val( '' );
      }
      $( ele ).on( 'keyup', function ( e ) {
        if ( e.keyCode != 13 && e.keyCode != 38 && e.keyCode != 40 ) {
          $.tags.delayEvent( this, function () {
            if ( !$.trim( $( this ).val() ) ) { //值为空，退出
            	$( '[rel=tags-cands]' ).attr({class:'tags-hide'});
              return;
            }
            this.setAttribute( 'search', this.value );
            $.tags.delChooses( this );
            $.tags.getData( this, function ( res ) {
              if ( 1 !== parseInt( res.status ) || !$.isArray( res.data ) || 0 === res.data.length ) {
                return;
              }
              try {
                var reg = new RegExp( "(" + this.value + ")", "i" );
              } catch ( e ) {
                return;
              }
              var chooses = res.data;
              var $cands = $( '[rel=tags-cands]' );
              var index = 0;
              for ( var i = 0; i < chooses.length; i++ ) {
                if ( !reg.test( chooses[i] ) ) {
                  continue;
                }
                var text = chooses[i].replace( reg, '<strong>$1</strong>' );
                $cands.append( '<div index="' + index + '" seq="' + chooses[i] + '">' + text + '</div>\n' );
                index++;
              }
              $cands.children( 'div' ).on( 'click', function () {
                $.tags.selectDiv( this, 1 );
              } );
              if ( $cands.children( 'div' ).length > 0 ) {
                $cands.attr({class:'tags-show'});
              }
            } );
          } );
        }
        $.tags.pressKey( e, this );
      } );
    } );
  };
  
} )();