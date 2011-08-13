{"objets":[<!--{foreach from=$objets key=key item=aObjet}-->{
"latLieu":<!--{if $aObjet.latitude!=""}--><!--{$aObjet.latitude}--><!--{else}-->0<!--{/if}-->,
"longLieu":<!--{if $aObjet.longitude!=""}--><!--{$aObjet.longitude}--><!--{else}-->0<!--{/if}-->,
"lienMoyenne":"<!--{if $aObjet.image.externe != '1' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$aObjet.image.lienMoyenne}-->",
"lienVignette":"<!--{if $aObjet.image.externe != '1' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$aObjet.image.lienVignette}-->"
},<!--{/foreach}-->]}