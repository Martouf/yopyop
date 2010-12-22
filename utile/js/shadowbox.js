if(typeof jQuery=="undefined"){throw"Unable to load Shadowbox, jQuery library not found"}var Shadowbox={};Shadowbox.lib={adapter:"jquery",getStyle:function(B,A){return jQuery(B).css(A)},setStyle:function(C,B,D){if(typeof B!="object"){var A={};A[B]=D;B=A}jQuery(C).css(B)},get:function(A){return(typeof A=="string")?document.getElementById(A):A},remove:function(A){jQuery(A).remove()},getTarget:function(A){return A.target},getPageXY:function(A){return[A.pageX,A.pageY]},preventDefault:function(A){A.preventDefault()},keyCode:function(A){return A.keyCode},addEvent:function(C,A,B){jQuery(C).bind(A,B)},removeEvent:function(C,A,B){jQuery(C).unbind(A,B)},append:function(B,A){jQuery(B).append(A)}};(function(A){A.fn.shadowbox=function(B){return this.each(function(){var E=A(this);var D=A.extend({},B||{},A.metadata?E.metadata():A.meta?E.data():{});var C=this.className||"";D.width=parseInt((C.match(/w:(\d+)/)||[])[1])||D.width;D.height=parseInt((C.match(/h:(\d+)/)||[])[1])||D.height;Shadowbox.setup(E,D)})}})(jQuery);
if(typeof Shadowbox=="undefined"){throw"Unable to load Shadowbox, no base library adapter found"}(function(){var version="2.0";var options={animate:true,animateFade:true,animSequence:"wh",flvPlayer:"flvplayer.swf",modal:false,overlayColor:"#000",overlayOpacity:0.8,flashBgColor:"#000000",autoplayMovies:true,showMovieControls:true,slideshowDelay:0,resizeDuration:0.55,fadeDuration:0.35,displayNav:true,continuous:false,displayCounter:true,counterType:"default",counterLimit:10,viewportPadding:20,handleOversize:"resize",handleException:null,handleUnsupported:"link",initialHeight:160,initialWidth:320,enableKeys:true,onOpen:null,onFinish:null,onChange:null,onClose:null,skipSetup:false,errors:{fla:{name:"Flash",url:"http://www.adobe.com/products/flashplayer/"},qt:{name:"QuickTime",url:"http://www.apple.com/quicktime/download/"},wmp:{name:"Windows Media Player",url:"http://www.microsoft.com/windows/windowsmedia/"},f4m:{name:"Flip4Mac",url:"http://www.flip4mac.com/wmv_download.htm"}},ext:{img:["png","jpg","jpeg","gif","bmp"],swf:["swf"],flv:["flv"],qt:["dv","mov","moov","movie","mp4"],wmp:["asf","wm","wmv"],qtwmp:["avi","mpg","mpeg"],iframe:["asp","aspx","cgi","cfm","htm","html","pl","php","php3","php4","php5","phtml","rb","rhtml","shtml","txt","vbs"]}};var SB=Shadowbox;var SL=SB.lib;var default_options;var RE={domain:/:\/\/(.*?)[:\/]/,inline:/#(.+)$/,rel:/^(light|shadow)box/i,gallery:/^(light|shadow)box\[(.*?)\]/i,unsupported:/^unsupported-(\w+)/,param:/\s*([a-z_]*?)\s*=\s*(.+)\s*/,empty:/^(?:br|frame|hr|img|input|link|meta|range|spacer|wbr|area|param|col)$/i};var cache=[];var gallery;var current;var content;var content_id="shadowbox_content";var dims;var initialized=false;var activated=false;var slide_timer;var slide_start;var slide_delay=0;var ua=navigator.userAgent.toLowerCase();var client={isStrict:document.compatMode=="CSS1Compat",isOpera:ua.indexOf("opera")>-1,isIE:ua.indexOf("msie")>-1,isIE7:ua.indexOf("msie 7")>-1,isSafari:/webkit|khtml/.test(ua),isWindows:ua.indexOf("windows")!=-1||ua.indexOf("win32")!=-1,isMac:ua.indexOf("macintosh")!=-1||ua.indexOf("mac os x")!=-1,isLinux:ua.indexOf("linux")!=-1};client.isBorderBox=client.isIE&&!client.isStrict;client.isSafari3=client.isSafari&&!!(document.evaluate);client.isGecko=ua.indexOf("gecko")!=-1&&!client.isSafari;var ltIE7=client.isIE&&!client.isIE7;var plugins;if(navigator.plugins&&navigator.plugins.length){var detectPlugin=function(plugin_name){var detected=false;for(var i=0,len=navigator.plugins.length;i<len;++i){if(navigator.plugins[i].name.indexOf(plugin_name)>-1){detected=true;break}}return detected};var f4m=detectPlugin("Flip4Mac");plugins={fla:detectPlugin("Shockwave Flash"),qt:detectPlugin("QuickTime"),wmp:!f4m&&detectPlugin("Windows Media"),f4m:f4m}}else{var detectPlugin=function(plugin_name){var detected=false;try{var axo=new ActiveXObject(plugin_name);if(axo){detected=true}}catch(e){}return detected};plugins={fla:detectPlugin("ShockwaveFlash.ShockwaveFlash"),qt:detectPlugin("QuickTime.QuickTime"),wmp:detectPlugin("wmplayer.ocx"),f4m:false}}var apply=function(o,e){for(var p in e){o[p]=e[p]}return o};var isLink=function(el){return el&&typeof el.tagName=="string"&&(el.tagName.toUpperCase()=="A"||el.tagName.toUpperCase()=="AREA")};SL.getViewportHeight=function(){var h=window.innerHeight;var mode=document.compatMode;if((mode||client.isIE)&&!client.isOpera){h=client.isStrict?document.documentElement.clientHeight:document.body.clientHeight}return h};SL.getViewportWidth=function(){var w=window.innerWidth;var mode=document.compatMode;if(mode||client.isIE){w=client.isStrict?document.documentElement.clientWidth:document.body.clientWidth}return w};SL.createHTML=function(obj){var html="<"+obj.tag;for(var attr in obj){if(attr=="tag"||attr=="html"||attr=="children"){continue}if(attr=="cls"){html+=' class="'+obj.cls+'"'}else{html+=" "+attr+'="'+obj[attr]+'"'}}if(RE.empty.test(obj.tag)){html+="/>"}else{html+=">";var cn=obj.children;if(cn){for(var i=0,len=cn.length;i<len;++i){html+=this.createHTML(cn[i])}}if(obj.html){html+=obj.html}html+="</"+obj.tag+">"}return html};var ease=function(x){return 1+Math.pow(x-1,3)};var animate=function(el,p,to,d,cb){var from=parseFloat(SL.getStyle(el,p));if(isNaN(from)){from=0}if(from==to){if(typeof cb=="function"){cb()}return }var delta=to-from;var op=p=="opacity";var unit=op?"":"px";var fn=function(ease){SL.setStyle(el,p,from+ease*delta+unit)};if(!options.animate&&!op||op&&!options.animateFade){fn(1);if(typeof cb=="function"){cb()}return }d*=1000;var begin=new Date().getTime();var end=begin+d;var timer=setInterval(function(){var time=new Date().getTime();if(time>=end){clearInterval(timer);fn(1);if(typeof cb=="function"){cb()}}else{fn(ease((time-begin)/d))}},10)};var clearOpacity=function(el){var s=el.style;if(client.isIE){if(typeof s.filter=="string"&&(/alpha/i).test(s.filter)){s.filter=s.filter.replace(/[\w\.]*alpha\(.*?\);?/i,"")}}else{s.opacity="";s["-moz-opacity"]="";s["-khtml-opacity"]=""}};var getComputedHeight=function(el){var h=Math.max(el.offsetHeight,el.clientHeight);if(!h){h=parseInt(SL.getStyle(el,"height"),10)||0;if(!client.isBorderBox){h+=parseInt(SL.getStyle(el,"padding-top"),10)+parseInt(SL.getStyle(el,"padding-bottom"),10)+parseInt(SL.getStyle(el,"border-top-width"),10)+parseInt(SL.getStyle(el,"border-bottom-width"),10)}}return h};var getPlayer=function(url){var m=url.match(RE.domain);var d=m&&document.domain==m[1];if(url.indexOf("#")>-1&&d){return"inline"}var q=url.indexOf("?");if(q>-1){url=url.substring(0,q)}if(RE.img.test(url)){return"img"}if(RE.swf.test(url)){return plugins.fla?"swf":"unsupported-swf"}if(RE.flv.test(url)){return plugins.fla?"flv":"unsupported-flv"}if(RE.qt.test(url)){return plugins.qt?"qt":"unsupported-qt"}if(RE.wmp.test(url)){if(plugins.wmp){return"wmp"}if(plugins.f4m){return"qt"}if(client.isMac){return plugins.qt?"unsupported-f4m":"unsupported-qtf4m"}return"unsupported-wmp"}else{if(RE.qtwmp.test(url)){if(plugins.qt){return"qt"}if(plugins.wmp){return"wmp"}return client.isMac?"unsupported-qt":"unsupported-qtwmp"}else{if(!d||RE.iframe.test(url)){return"iframe"}}}return"unsupported"};var handleClick=function(ev){var link;if(isLink(this)){link=this}else{link=SL.getTarget(ev);while(!isLink(link)&&link.parentNode){link=link.parentNode}}if(link){SB.open(link);if(gallery.length){SL.preventDefault(ev)}}};var toggleNav=function(id,on){var el=SL.get("shadowbox_nav_"+id);if(el){el.style.display=on?"":"none"}};var buildBars=function(cb){var obj=gallery[current];var title_i=SL.get("shadowbox_title_inner");title_i.innerHTML=obj.title||"";var nav=SL.get("shadowbox_nav");if(nav){var c,n,pl,pa,p;if(options.displayNav){c=true;var len=gallery.length;if(len>1){if(options.continuous){n=p=true}else{n=(len-1)>current;p=current>0}}if(options.slideshowDelay>0&&hasNext()){pa=slide_timer!="paused";pl=!pa}}else{c=n=pl=pa=p=false}toggleNav("close",c);toggleNav("next",n);toggleNav("play",pl);toggleNav("pause",pa);toggleNav("previous",p)}var counter=SL.get("shadowbox_counter");if(counter){var co="";if(options.displayCounter&&gallery.length>1){if(options.counterType=="skip"){var i=0,len=gallery.length,end=len;var limit=parseInt(options.counterLimit);if(limit<len){var h=Math.round(limit/2);i=current-h;if(i<0){i+=len}end=current+(limit-h);if(end>len){end-=len}}while(i!=end){if(i==len){i=0}co+='<a onclick="Shadowbox.change('+i+');"';if(i==current){co+=' class="shadowbox_counter_current"'}co+=">"+(++i)+"</a>"}}else{co=(current+1)+" "+SB.LANG.of+" "+len}}counter.innerHTML=co}cb()};var hideBars=function(anim,cb){var obj=gallery[current];var title=SL.get("shadowbox_title");var info=SL.get("shadowbox_info");var title_i=SL.get("shadowbox_title_inner");var info_i=SL.get("shadowbox_info_inner");var fn=function(){buildBars(cb)};var title_h=getComputedHeight(title);var info_h=getComputedHeight(info)*-1;if(anim){animate(title_i,"margin-top",title_h,0.35);animate(info_i,"margin-top",info_h,0.35,fn)}else{SL.setStyle(title_i,"margin-top",title_h+"px");SL.setStyle(info_i,"margin-top",info_h+"px");fn()}};var showBars=function(cb){var title_i=SL.get("shadowbox_title_inner");var info_i=SL.get("shadowbox_info_inner");var t=title_i.innerHTML!="";if(t){animate(title_i,"margin-top",0,0.35)}animate(info_i,"margin-top",0,0.35,cb)};var loadContent=function(){var obj=gallery[current];if(!obj){return }var changing=false;if(content){content.remove();changing=true}var p=obj.player=="inline"?"html":obj.player;if(typeof SB[p]!="function"){SB.raise("Unknown player "+obj.player)}content=new SB[p](content_id,obj);listenKeys(false);toggleLoading(true);hideBars(changing,function(){if(!content){return }if(!changing){SL.get("shadowbox").style.display=""}var fn=function(){resizeContent(function(){if(!content){return }showBars(function(){if(!content){return }SL.get("shadowbox_body_inner").innerHTML=SL.createHTML(content.markup(dims));toggleLoading(false,function(){if(!content){return }if(typeof content.onLoad=="function"){content.onLoad()}if(options.onFinish&&typeof options.onFinish=="function"){options.onFinish(gallery[current])}if(slide_timer!="paused"){SB.play()}listenKeys(true)})})})};if(typeof content.ready!="undefined"){var id=setInterval(function(){if(content){if(content.ready){clearInterval(id);id=null;fn()}}else{clearInterval(id);id=null}},100)}else{fn()}});if(gallery.length>1){var next=gallery[current+1]||gallery[0];if(next.player=="img"){var a=new Image();a.src=next.content}var prev=gallery[current-1]||gallery[gallery.length-1];if(prev.player=="img"){var b=new Image();b.src=prev.content}}};var setDimensions=function(height,width,resizable){resizable=resizable||false;var sb=SL.get("shadowbox_body");var h=height=parseInt(height);var w=width=parseInt(width);var view_h=SL.getViewportHeight();var view_w=SL.getViewportWidth();var border_w=parseInt(SL.getStyle(sb,"border-left-width"),10)+parseInt(SL.getStyle(sb,"border-right-width"),10);var extra_w=border_w+2*options.viewportPadding;if(w+extra_w>=view_w){w=view_w-extra_w}var border_h=parseInt(SL.getStyle(sb,"border-top-width"),10)+parseInt(SL.getStyle(sb,"border-bottom-width"),10);var bar_h=getComputedHeight(SL.get("shadowbox_title"))+getComputedHeight(SL.get("shadowbox_info"));var extra_h=border_h+2*options.viewportPadding+bar_h;if(h+extra_h>=view_h){h=view_h-extra_h}var drag=false;var resize_h=height;var resize_w=width;var handle=options.handleOversize;if(resizable&&(handle=="resize"||handle=="drag")){var change_h=(height-h)/height;var change_w=(width-w)/width;if(handle=="resize"){if(change_h>change_w){w=Math.round((width/height)*h)}else{if(change_w>change_h){h=Math.round((height/width)*w)}}resize_w=w;resize_h=h}else{var link=gallery[current];if(link){drag=link.player=="img"&&(change_h>0||change_w>0)}}}dims={height:h+border_h+bar_h,width:w+border_w,inner_h:h,inner_w:w,top:(view_h-(h+extra_h))/2+options.viewportPadding,resize_h:resize_h,resize_w:resize_w,drag:drag}};var resizeContent=function(cb){if(!content){return }setDimensions(content.height,content.width,content.resizable);if(cb){switch(options.animSequence){case"hw":adjustHeight(dims.inner_h,dims.top,true,function(){adjustWidth(dims.width,true,cb)});break;case"wh":adjustWidth(dims.width,true,function(){adjustHeight(dims.inner_h,dims.top,true,cb)});break;case"sync":default:adjustWidth(dims.width,true);adjustHeight(dims.inner_h,dims.top,true,cb)}}else{adjustWidth(dims.width,false);adjustHeight(dims.inner_h,dims.top,false);var c=SL.get(content_id);if(c){if(content.resizable&&options.handleOversize=="resize"){c.height=dims.resize_h;c.width=dims.resize_w}if(gallery[current].player=="img"&&options.handleOversize=="drag"){var top=parseInt(SL.getStyle(c,"top"));if(top+content.height<dims.inner_h){SL.setStyle(c,"top",dims.inner_h-content.height+"px")}var left=parseInt(SL.getStyle(c,"left"));if(left+content.width<dims.inner_w){SL.setStyle(c,"left",dims.inner_w-content.width+"px")}}}}};var adjustHeight=function(height,top,anim,cb){height=parseInt(height);var sb=SL.get("shadowbox_body");if(anim){animate(sb,"height",height,options.resizeDuration)}else{SL.setStyle(sb,"height",height+"px")}var s=SL.get("shadowbox");if(anim){animate(s,"top",top,options.resizeDuration,cb)}else{SL.setStyle(s,"top",top+"px");if(typeof cb=="function"){cb()}}};var adjustWidth=function(width,anim,cb){width=parseInt(width);var s=SL.get("shadowbox");if(anim){animate(s,"width",width,options.resizeDuration,cb)}else{SL.setStyle(s,"width",width+"px");if(typeof cb=="function"){cb()}}};var listenKeys=function(on){if(!options.enableKeys){return }SL[(on?"add":"remove")+"Event"](document,"keydown",handleKey)};var handleKey=function(e){var code=SL.keyCode(e);SL.preventDefault(e);if(code==81||code==88||code==27){SB.close()}else{if(code==37){SB.previous()}else{if(code==39){SB.next()}else{if(code==32){SB[(typeof slide_timer=="number"?"pause":"play")]()}}}}};var toggleLoading=function(on,cb){var loading=SL.get("shadowbox_loading");if(on){loading.style.display="";if(typeof cb=="function"){cb()}}else{var p=gallery[current].player;var anim=(p=="img"||p=="html");var fn=function(){loading.style.display="none";clearOpacity(loading);if(typeof cb=="function"){cb()}};if(anim){animate(loading,"opacity",0,options.fadeDuration,fn)}else{fn()}}};var fixTop=function(){SL.get("shadowbox_container").style.top=document.documentElement.scrollTop+"px"};var fixHeight=function(){SL.get("shadowbox_overlay").style.height=SL.getViewportHeight()+"px"};var hasNext=function(){return gallery.length>1&&(current!=gallery.length-1||options.continuous)};var toggleVisible=function(cb){var els,v=(cb)?"hidden":"visible";var hide=["select","object","embed"];for(var i=0;i<hide.length;++i){els=document.getElementsByTagName(hide[i]);for(var j=0,len=els.length;j<len;++j){els[j].style.visibility=v}}var so=SL.get("shadowbox_overlay");var sc=SL.get("shadowbox_container");var sb=SL.get("shadowbox");if(cb){SL.setStyle(so,{backgroundColor:options.overlayColor,opacity:0});if(!options.modal){SL.addEvent(so,"click",SB.close)}if(ltIE7){fixTop();fixHeight();SL.addEvent(window,"scroll",fixTop)}sb.style.display="none";sc.style.visibility="visible";animate(so,"opacity",parseFloat(options.overlayOpacity),options.fadeDuration,cb)}else{SL.removeEvent(so,"click",SB.close);if(ltIE7){SL.removeEvent(window,"scroll",fixTop)}sb.style.display="none";animate(so,"opacity",0,options.fadeDuration,function(){sc.style.visibility="hidden";sb.style.display="";clearOpacity(so)})}};Shadowbox.init=function(opts){if(initialized){return }if(typeof SB.LANG=="undefined"){SB.raise("No Shadowbox language loaded");return }if(typeof SB.SKIN=="undefined"){SB.raise("No Shadowbox skin loaded");return }apply(options,opts||{});var markup=SB.SKIN.markup.replace(/\{(\w+)\}/g,function(m,p){return SB.LANG[p]});var bd=document.body||document.documentElement;SL.append(bd,markup);if(ltIE7){SL.setStyle(SL.get("shadowbox_container"),"position","absolute");SL.get("shadowbox_body").style.zoom=1;var png=SB.SKIN.png_fix;if(png&&png.constructor==Array){for(var i=0;i<png.length;++i){var el=SL.get(png[i]);if(el){var match=SL.getStyle(el,"background-image").match(/url\("(.*\.png)"\)/);if(match){SL.setStyle(el,{backgroundImage:"none",filter:"progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,src="+match[1]+",sizingMethod=scale);"})}}}}}for(var e in options.ext){RE[e]=new RegExp(".("+options.ext[e].join("|")+")s*$","i")}var id;SL.addEvent(window,"resize",function(){if(id){clearTimeout(id);id=null}id=setTimeout(function(){if(ltIE7){fixHeight()}resizeContent()},50)});if(!options.skipSetup){SB.setup()}initialized=true};Shadowbox.loadSkin=function(skin,dir){if(!(/\/$/.test(dir))){dir+="/"}skin=dir+skin+"/";document.write('<link rel="stylesheet" type="text/css" href="'+skin+'skin.css">');document.write('<script type="text/javascript" src="'+skin+'skin.js"><\/script>')};Shadowbox.loadLanguage=function(lang,dir){if(!(/\/$/.test(dir))){dir+="/"}document.write('<script type="text/javascript" src="'+dir+"shadowbox-"+lang+'.js"><\/script>')};Shadowbox.loadPlayer=function(players,dir){if(typeof players=="string"){players=[players]}if(!(/\/$/.test(dir))){dir+="/"}for(var i=0,len=players.length;i<len;++i){document.write('<script type="text/javascript" src="'+dir+"shadowbox-"+players[i]+'.js"><\/script>')}};Shadowbox.setup=function(links,opts){if(!links){var links=[];var a=document.getElementsByTagName("a"),rel;for(var i=0,len=a.length;i<len;++i){rel=a[i].getAttribute("rel");if(rel&&RE.rel.test(rel)){links[links.length]=a[i]}}}else{if(!links.length){links=[links]}}var link;for(var i=0,len=links.length;i<len;++i){link=links[i];if(typeof link.shadowboxCacheKey=="undefined"){link.shadowboxCacheKey=cache.length;SL.addEvent(link,"click",handleClick)}cache[link.shadowboxCacheKey]=this.buildCacheObj(link,opts)}};Shadowbox.buildCacheObj=function(link,opts){var href=link.href;var o={el:link,title:link.getAttribute("title"),player:getPlayer(href),options:apply({},opts||{}),content:href};var opt,l_opts=["player","title","height","width","gallery"];for(var i=0,len=l_opts.length;i<len;++i){opt=l_opts[i];if(typeof o.options[opt]!="undefined"){o[opt]=o.options[opt];delete o.options[opt]}}var rel=link.getAttribute("rel");if(rel){var match=rel.match(RE.gallery);if(match){o.gallery=escape(match[2])}var params=rel.split(";");for(var i=0,len=params.length;i<len;++i){match=params[i].match(RE.param);if(match){if(match[1]=="options"){eval("apply(o.options, "+match[2]+")")}else{o[match[1]]=match[2]}}}}return o};Shadowbox.applyOptions=function(opts){if(opts){default_options=apply({},options);options=apply(options,opts)}};Shadowbox.revertOptions=function(){if(default_options){options=default_options;default_options=null}};Shadowbox.open=function(obj,opts){this.revertOptions();if(isLink(obj)){if(typeof obj.shadowboxCacheKey=="undefined"||typeof cache[obj.shadowboxCacheKey]=="undefined"){obj=this.buildCacheObj(obj,opts)}else{obj=cache[obj.shadowboxCacheKey]}}if(obj.constructor==Array){gallery=obj;current=0}else{var copy=apply({},obj);if(!obj.gallery){gallery=[copy];current=0}else{current=null;gallery=[];var ci;for(var i=0,len=cache.length;i<len;++i){ci=cache[i];if(ci.gallery){if(ci.content==obj.content&&ci.gallery==obj.gallery&&ci.title==obj.title){current=gallery.length}if(ci.gallery==obj.gallery){gallery.push(apply({},ci))}}}if(current==null){gallery.unshift(copy);current=0}}}obj=gallery[current];if(obj.options||opts){this.applyOptions(apply(apply({},obj.options||{}),opts||{}))}var match,r;for(var i=0,len=gallery.length;i<len;++i){r=false;if(gallery[i].player=="unsupported"){r=true}else{if(match=RE.unsupported.exec(gallery[i].player)){if(options.handleUnsupported=="link"){gallery[i].player="html";var s,a,oe=options.errors;switch(match[1]){case"qtwmp":s="either";a=[oe.qt.url,oe.qt.name,oe.wmp.url,oe.wmp.name];break;case"qtf4m":s="shared";a=[oe.qt.url,oe.qt.name,oe.f4m.url,oe.f4m.name];break;default:s="single";if(match[1]=="swf"||match[1]=="flv"){match[1]="fla"}a=[oe[match[1]].url,oe[match[1]].name]}var msg=SB.LANG.errors[s].replace(/\{(\d+)\}/g,function(m,i){return a[i]});gallery[i].content='<div class="shadowbox_message">'+msg+"</div>"}else{r=true}}else{if(gallery[i].player=="inline"){var match=RE.inline.exec(gallery[i].content);if(match){var el;if(el=SL.get(match[1])){gallery[i].content=el.innerHTML}else{SB.raise("Cannot find element with id "+match[1])}}else{SB.raise("Cannot find element id for inline content")}}}}if(r){gallery.splice(i,1);if(i<current){--current}else{if(i==current){current=i>0?current-1:i}}--i;len=gallery.length}}if(gallery.length){if(options.onOpen&&typeof options.onOpen=="function"){options.onOpen(obj)}if(!activated){setDimensions(options.initialHeight,options.initialWidth);adjustHeight(dims.inner_h,dims.top,false);adjustWidth(dims.width,false);toggleVisible(loadContent)}else{loadContent()}activated=true}};Shadowbox.change=function(num){if(!gallery){return }if(!gallery[num]){if(!options.continuous){return }else{num=num<0?(gallery.length-1):0}}if(typeof slide_timer=="number"){clearTimeout(slide_timer);slide_timer=null;slide_delay=slide_start=0}current=num;if(options.onChange&&typeof options.onChange=="function"){options.onChange(gallery[current])}loadContent()};Shadowbox.next=function(){this.change(current+1)};Shadowbox.previous=function(){this.change(current-1)};Shadowbox.play=function(){if(!hasNext()){return }if(!slide_delay){slide_delay=options.slideshowDelay*1000}if(slide_delay){slide_start=new Date().getTime();slide_timer=setTimeout(function(){slide_delay=slide_start=0;SB.next()},slide_delay);toggleNav("play",false);toggleNav("pause",true)}};Shadowbox.pause=function(){if(typeof slide_timer=="number"){var time=new Date().getTime();slide_delay=Math.max(0,slide_delay-(time-slide_start));if(slide_delay){clearTimeout(slide_timer);slide_timer="paused"}toggleNav("pause",false);toggleNav("play",true)}};Shadowbox.close=function(){if(!activated){return }listenKeys(false);toggleVisible(false);if(content){content.remove();content=null}if(typeof slide_timer=="number"){clearTimeout(slide_timer)}slide_timer=null;slide_delay=0;if(options.onClose&&typeof options.onClose=="function"){options.onClose(gallery[current])}activated=false};Shadowbox.clearCache=function(){for(var i=0,len=cache.length;i<len;++i){if(cache[i].el){SL.removeEvent(cache[i].el,"click",handleClick);delete cache[i].el.shadowboxCacheKey}}cache=[]};Shadowbox.getPlugins=function(){return plugins};Shadowbox.getOptions=function(){return options};Shadowbox.getCurrent=function(){return gallery[current]};Shadowbox.getVersion=function(){return version};Shadowbox.getClient=function(){return client};Shadowbox.getContent=function(){return content};Shadowbox.getDimensions=function(){return dims};Shadowbox.raise=function(e){if(typeof options.handleException=="function"){options.handleException(e)}else{throw e}}})();
if(typeof Shadowbox=="undefined"){throw"Unable to load Shadowbox language file, base library not found."}Shadowbox.LANG={code:"fr",of:"de",loading:"chargement",cancel:"Annuler",next:"Suivant",previous:"Précédent",play:"Lire",pause:"Pause",close:"Fermer",errors:{single:'Vous devez installer le plugin <a href="{0}">{1}</a> pour afficher ce contenu.',shared:'Vous devez installer les plugins <a href="{0}">{1}</a> et <a href="{2}">{3}</a> pour afficher ce contenu.',either:'Vous devez installer le plugin <a href="{0}">{1}</a> ou <a href="{2}">{3}</a> pour afficher ce contenu.'}};
(function(){var F=Shadowbox;var L=F.lib;var A=F.getClient();var I;var M;var J="shadowbox_drag_layer";var K;var D=function(){I={x:0,y:0,start_x:null,start_y:null}};var E=function(N,O,C){if(N){D();var P=["position:absolute","height:"+O+"px","width:"+C+"px","cursor:"+(A.isGecko?"-moz-grab":"move"),"background-color:"+(A.isIE?"#fff;filter:alpha(opacity=0)":"transparent")];L.append(L.get("shadowbox_body_inner"),'<div id="'+J+'" style="'+P.join(";")+'"></div>');L.addEvent(L.get(J),"mousedown",H)}else{var Q=L.get(J);if(Q){L.removeEvent(Q,"mousedown",H);L.remove(Q)}}};var H=function(N){L.preventDefault(N);var C=L.getPageXY(N);I.start_x=C[0];I.start_y=C[1];M=L.get("shadowbox_content");L.addEvent(document,"mousemove",G);L.addEvent(document,"mouseup",B);if(A.isGecko){L.setStyle(L.get(J),"cursor","-moz-grabbing")}};var B=function(){L.removeEvent(document,"mousemove",G);L.removeEvent(document,"mouseup",B);if(A.isGecko){L.setStyle(L.get(J),"cursor","-moz-grab")}};var G=function(Q){var O=F.getContent();var R=F.getDimensions();var P=L.getPageXY(Q);var N=P[0]-I.start_x;I.start_x+=N;I.x=Math.max(Math.min(0,I.x+N),R.inner_w-O.width);L.setStyle(M,"left",I.x+"px");var C=P[1]-I.start_y;I.start_y+=C;I.y=Math.max(Math.min(0,I.y+C),R.inner_h-O.height);L.setStyle(M,"top",I.y+"px")};Shadowbox.img=function(O,N){this.id=O;this.obj=N;this.resizable=true;this.ready=false;var C=this;K=new Image();K.onload=function(){C.height=C.obj.height?parseInt(C.obj.height,10):K.height;C.width=C.obj.width?parseInt(C.obj.width,10):K.width;C.ready=true;K.onload="";K=null};K.src=N.content};Shadowbox.img.prototype={markup:function(C){return{tag:"img",id:this.id,height:C.resize_h,width:C.resize_w,src:this.obj.content,style:"position:absolute"}},onLoad:function(){var C=F.getDimensions();if(C.drag&&F.getOptions().handleOversize=="drag"){E(true,C.resize_h,C.resize_w)}},remove:function(){var C=L.get(this.id);if(C){L.remove(C)}E(false);if(K){K.onload="";K=null}}}})();
(function(){var A=Shadowbox;var B=A.lib;Shadowbox.html=function(D,C){this.id=D;this.obj=C;this.height=this.obj.height?parseInt(this.obj.height,10):300;this.width=this.obj.width?parseInt(this.obj.width,10):500};Shadowbox.html.prototype={markup:function(C){return{tag:"div",id:this.id,cls:"html",html:this.obj.content}},remove:function(){var C=B.get(this.id);if(C){B.remove(C)}}}})();
(function(){var A=Shadowbox;var B=A.lib;var D=A.getClient();Shadowbox.iframe=function(E,C){this.id=E;this.obj=C;this.height=this.obj.height?parseInt(this.obj.height,10):B.getViewportHeight();this.width=this.obj.width?parseInt(this.obj.width,10):B.getViewportWidth()};Shadowbox.iframe.prototype={markup:function(E){var C={tag:"iframe",id:this.id,name:this.id,height:"100%",width:"100%",frameborder:"0",marginwidth:"0",marginheight:"0",scrolling:"auto"};if(D.isIE){C.allowtransparency="true";if(!D.isIE7){C.src='javascript:false;document.write("");'}}return C},onLoad:function(){var C=(D.isIE)?B.get(this.id).contentWindow:window.frames[this.id];C.location=this.obj.content},remove:function(){var C=B.get(this.id);if(C){B.remove(C);if(D.isGecko){delete window.frames[this.id]}}}}})();
