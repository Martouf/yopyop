function startRichEditor(){
	tinyMCE.init({
		mode : "exact",
	//	mode : "textareas",
		elements : "texte",
		theme : "advanced",
		plugins : "safari,directionality,ecoimage", //table,emotions,media,
		theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,|,bullist,numlist,|,link,unlink,code,|,hr,image",  //tablecontrols,emotions,media,
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		verify_html : false,
		inline_styles : false,
//		browsers : "msie,gecko,opera",
		entity_encoding : "raw"
	});
}
function echo(str){
	try{
		console.log(str);
	}
	catch(e){alert(str)}
}