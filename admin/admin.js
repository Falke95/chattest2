globx=0; mobile=0;

if(typeof window.orientation !== 'undefined' && 'ontouchstart' in document.documentElement){mobile=1}

function de(x){return document.getElementById(x)}

// ---

function show_hide(){ 
if(mpr.style.display=='block'){close_preview();return}
document.body.style.marginLeft='0px'
if(men.style.display=='none'){men.style.display='block'; hsc.style.display='block'
}else{men.style.display='none'; hsc.style.display='none' }}

// ---

function preview(x,y){
sty='<textarea id="impexp" class="x_bcolor_bg" style="width:90%;height:150px;margin:auto" onclick="this.select()"></textarea>';
the='<div id="themelist" class="x_bcolor_bg" style="width:90%;padding:10px;margin:auto;overflow:auto"></div>';
switch(y){
case 6 : hst.style.display='block';mpr.style.display='block'; mpr.style.backgroundColor='#283439'; mpr.innerHTML=sty; break;
case 7 : hst.style.display='block';mpr.style.display='block'; mpr.style.backgroundColor='#283439'; mpr.innerHTML=the; break;
default: break;}}

// ---

function ttouch1(e){tj = e.changedTouches[0]; globx = parseInt(tj.clientX)}
function ttouch2(e){tj = e.changedTouches[0]; currx = parseInt(tj.clientX); if(globx<50 && (currx-globx)>50){show_hide()}}

function close_preview(){mpr.innerHTML='';hst.style.display='none';mpr.style.display='none'}

// ----------

function b64e(str) {
return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,function(match, p1){
return String.fromCharCode(parseInt(p1,16))}))}

function b64d(str){
return decodeURIComponent(Array.prototype.map.call(atob(str),function(c){
return '%'+('00'+c.charCodeAt(0).toString(16)).slice(-2)}).join(''))}

// ----------

function rand_str(l){
if(typeof Uint32Array!='function' || typeof window.crypto.getRandomValues!='function'){
return 'Your browser does not support generating random values...';}
str='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
randstr=''; m=str.length-1; rb=new Uint32Array(l); window.crypto.getRandomValues(rb);
for(i=0;i<l;i++){rn=rb[i]/(0xffffffff+1); s=Math.floor(rn*(m+1)); randstr+=str.charAt(s)}
return randstr}

// ----------

function escape_html(cd){
rp ={'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
return cd.replace(/[&<>"']/g, function(y){return rp[y];})}

// ----------

bencoded=0

function eform(el){
if(bencoded<1){
el=el.split(',')
for(i in el){
em=el[i].toString()
de(em).style.opacity=0
de(em).value=b64e(de(em).value)
} bencoded=1 } return true }

// ----------

lstorage=true; storkey='blabaxacpbookmarks';
try{lstored=localStorage.getItem(storkey); if(!lstored){lstored=''}}catch(e){lstorage=false}

function bookmarks(){
if(!lstorage){console.log('localStorage disabled!');return}
de('bmrk_menu').style.display='block'
disbm='';
arr=lstored.split("\n")
for(i=0;i<arr.length;i++){
ent=arr[i].split(':');if(ent[0] && ent[1] && !ent[2]){
disbm+='<div class="svg_star" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.8" onclick="self.location.href=\''+ent[1].trim()+'\'">'+ent[0].trim()+'</div>'
}} de('bmrk_box').innerHTML=disbm }

function add_bookmark(){
ly=self.location.href.toString().split('?q=')
if(ly[1] && ly[1].length>0){ly='admin.php?q='+ly[1]}else{ly='admin.php'}
ly="\n"+document.title.replace('ACP: ','').replace(':',' ')+'  :  '+ly; lstored+=ly
localStorage.setItem(storkey,lstored)
de('bmrk_menu').style.display='none'
de('bmrk_button').className='x_accent_bg pointer x_circle svg_star'
setTimeout("de('bmrk_button').className='x_bcolor_bg pointer x_circle svg_star'",800)}

function edit_bookmarks(){
hst.style.display='block';mpr.style.display='block'
de('bmrk_menu').style.display='none'
mpr.innerHTML='<textarea id="editb" style="height:300px"></textarea>'
mpr.innerHTML+='<input type="button" style="width:100%;height:40px;font-weight:bold" onclick="save_bookmarks()" value="OKAY">'
de('editb').value=lstored.trim()}

function save_bookmarks(){
lstored=de('editb').value.trim()
localStorage.setItem(storkey,lstored)
hst.style.display='none';mpr.style.display='none'
mpr.innerHTML=''}

function cmode(x){
cu=self.location.href.toString().replace('&dmode=1','').replace('&dmode=0','')
if(cu.indexOf('?q=')>0){
	self.location.href=cu+'&dmode='+x
return}
self.location.href='admin.php?dmode='+x
}