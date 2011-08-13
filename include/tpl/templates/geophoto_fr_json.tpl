{"photos":[<!--{foreach from=$photos key=key item=aPhoto}-->{
"latLieu":<!--{if $aPhoto.latitude!=""}--><!--{$aPhoto.latitude}--><!--{else}-->0<!--{/if}-->,
"longLieu":<!--{if $aPhoto.longitude!=""}--><!--{$aPhoto.longitude}--><!--{else}-->0<!--{/if}-->,
"lienMoyenne":"<!--{if $aPhoto.externe != '1' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$aPhoto.lienMoyenne}-->",
"lienVignette":"<!--{if $aPhoto.externe != '1' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$aPhoto.lienVignette}-->"
},<!--{/foreach}-->]}