{if $qinformer_is_enabled}
<link rel="stylesheet" href="templates/styles_qinformer.css" title="stylesheet" type="text/css">
{literal}

<SCRIPT type=text/javascript>
window.addEvent('domready', function(){
var href,url;	
function parseQuery ( query ) {
   var Params = new Object ();
   if ( ! query ) return Params; // return empty object
   var Pairs = query.split(/[;&]/);
   for ( var i = 0; i < Pairs.length; i++ ) {
      var KeyVal = Pairs[i].split('=');
      if ( ! KeyVal || KeyVal.length != 2 ) continue;
      var key = unescape( KeyVal[0] );
      var val = unescape( KeyVal[1] );
      val = val.replace(/\+/g, ' ');
      Params[key] = val;
   }
   return Params;
}
var myFirstElement  = new Element('div', {id: 'yyy'});	
myFirstElement.innerHTML='';
$$('.photo').store('tip:title', 'Loading...');	
$$('.recentaction_media').store('tip:title', 'Loading...');	
var quick_inf;
quick_inf = new Tips($$('.recentaction_media','.photo'));

$$('a').addEvent('mouseenter', function(event) {
url = this.href;
	queryString = url.replace(/^[^\?]+\??/,'');
	var params = parseQuery( queryString );
    href=params['user'];
});

quick_inf.addEvent('show', function(tip){
if (!href) {
name_user=url.split('/'); 

href=name_user[name_user.length-2]; 
}
        var r = new Request({
            method: 'get',
            url: 'qinformer.php?name='+href ,
             onSuccess: function(responseText) { 
                  $$('.tip').set('html',responseText); 
             },
             onFailure: function() { 
                  $$('.tip').set('html','Loading error!');
            }
        });
         r.send(); 
    tip.fade('in');
});
quick_inf.addEvent('hide', function(tip){
    tip.fade('out');
});
});
</SCRIPT>
{/literal}

{/if}