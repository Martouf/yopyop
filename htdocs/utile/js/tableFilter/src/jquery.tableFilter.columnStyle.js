/**
* @author daemach
*/
// Aggregator Constructor
jQuery.tf_columnStyle = function(col){
    this.name = "ColumnStyle";
    this.col = col;
	this.lastAlign = null;
};
jQuery.extend(jQuery.tf_columnStyle,{
    prototype : {
        init: function(){
			
			if (this.col.root.browserSucks && this.col.root.tbody.rows.length > 500){
				return;
			};

            /* Plugin Initialization:
            * This function gets called when the plugin is added to a column. Here are some functions you can use:
            *
            * this.addRow(location (string),clone (string)): Location = "header" || "footer". Clone = "blank" || "sortRow" || "pageRow".
            * 		By default addRow clones the first row in the tbody (blank) and prepends it to the footer. You can choose
            *  		to clone the paging or sorting rows for styling purposes if you wish. This function only adds the row once.
            *  		Successive calls will just return the row as a jQuery object.
            *  
            * this.getMainMenuRoot(): This function creates a node for this plugin in the main menu, located in the paging row
            *   	It only adds this root once, returning the root element on successive calls.
            *   
            *   
            * this.addMenu(id, base, container, direction, trigger, inEvent, outEvent): Returns the new menu's root element.
            * 		id: the id of the new menu.  The id is also used for the element's classname for css purposes
            * 		base: a containing div with relative positioning to which to anchor the menu.
            * 		container: the div's container element in case you want to anchor to that element's height.
            * 		direction: menu opens up or down
            * 		trigger: the element that is going to trigger an open.  This element uses slideToggle to open the menu
            * 		inEvent: the trigger event you want to use to open the menu.  Normally click or mouseover. Defaults to click
            * 		outEvent: the event you want to use to close the menu - this is triggered on the menu itself.  Defaults to mouseout
            * 
            * this. addSubMenu(text, target):  Returns the submenu as an element.  
            * 		text: the text for the menu
            * 		target: the parent element for this submenu
            * 
            * this.addMenuItem(text,target,fn): This function adds an item to the menu (as you might expect). 
            * 		text: the text for this node
            * 		target: the menu to which to append this node
            * 		fn: function that gets called when someone clicks on that node.
            *
            * this.getHeaderText(): Gets the header text.  Generally used when creating submenus;
            */
			
			
            // initialize some variables
            var plugin = this;
            var root = this.col.root;
            var ci = this.col.index;
            var homeRow = this.addRow("header","sortRow");
            var homeCell = homeRow.children().eq(this.col.index);
            var homeCellColor = $d.cw.rgb2hex($d.cm.getComputedStyle(homeRow[0],"backgroundColor"));
			
            // I abhor ie... it actually prepends a #!!! wth?
            homeCellColor = homeCellColor.substring(homeCellColor.length-6);
			
            var enabledColor = $d.cw.ccComplementary(homeCellColor);
            var ip = root.settings.imagePath;

            var buttons = '<img style="border: 1px solid #'+homeCellColor+';" id="cs_b_'+ci+'" src="'+ip+'/text_bold.png">&nbsp;' +
            '<img style="border: 1px solid #'+homeCellColor+';" id="cs_i_'+ci+'" src="'+ip+'/text_italic.png">&nbsp;' +
            '<img style="border: 1px solid #'+homeCellColor+';" id="cs_u_'+ci+'" src="'+ip+'/text_underline.png">&nbsp;' +
            '&nbsp;&nbsp;' +
            '<img style="border: 1px solid #'+homeCellColor+';" id="cs_al_'+ci+'" src="'+ip+'/text_align_left.png">&nbsp;' +
            '<img style="border: 1px solid #'+homeCellColor+';" id="cs_ac_'+ci+'" src="'+ip+'/text_align_center.png">&nbsp;' +
            '<img style="border: 1px solid #'+homeCellColor+';" id="cs_ar_'+ci+'" src="'+ip+'/text_align_right.png">';
			
            var base = jQuery('<div></div>').appendTo(homeCell);
            var trigger = jQuery('<a title="Column Styles" href="javascript:void;">Click Me</a>').appendTo(base);
			
            var rootMenu = this.addMenu("cs_menu"+ci, base[0], homeCell[0], "down", trigger,"click","click");
            this.addMenuItem("ColumnStyles",rootMenu);
            this.addMenuItem(buttons,rootMenu);
			
            if (this.col.dataType == "number" || this.col.dataType == "date"){
                $d.cm.setRule('.c'+ci,'text-align: right;');
				var img = jQuery('#cs_ar_'+ci)[0];
				this.lastAlign = img;
				img.style.borderColor = "#"+enabledColor;
            }
            // wire'em up
            jQuery('#cs_b_'+ci).click(function(){
				var hc =homeCellColor ,en = enabledColor;  // testing - did I mention IE sucks?
                var b = $d.cm.getRule('.c'+ci,'font-weight')[0],text;
                if (typeof b !== "undefined" && b[3] == 'bold'){
                    text = '""';
					this.style.borderColor = "#"+homeCellColor;
                    } else {
                    text = 'bold';
					this.style.borderColor = "#"+enabledColor;
                }
                $d.cm.setRule('.c'+ci,'font-weight: '+text+';');
            });
            jQuery('#cs_i_'+ci).click(function(){
                var b = $d.cm.getRule('.c'+ci,'font-style')[0],text;
                if (typeof b !== "undefined" && b[3] == 'italic'){
                    text = '""';
                    this.style.borderColor = "#"+homeCellColor;
                    } else {
                    text = 'italic';
                    this.style.borderColor = "#"+enabledColor;
                }
                $d.cm.setRule('.c'+ci,'font-style: '+text+';');
            });
            jQuery('#cs_u_'+ci).click(function(){
                var b = $d.cm.getRule('.c'+ci,'text-decoration')[0],text;
                if (typeof b !== "undefined" && b[3] == 'underline'){
                    text = '""';
                    this.style.borderColor = "#"+homeCellColor;
                    } else {
                    text = 'underline';
                    this.style.borderColor = "#"+enabledColor;
                }
                $d.cm.setRule('.c'+ci,'text-decoration: '+text+';');
            });
            jQuery('#cs_al_'+ci).click(function(){
                var b = $d.cm.getRule('.c'+ci,'text-align')[0];
                $d.cm.setRule('.c'+ci,'text-align: left;');
				this.style.borderColor = "#"+enabledColor;
				if (plugin.lastAlign != this && plugin.lastAlign !== null){
					plugin.lastAlign.style.borderColor = "#"+homeCellColor;
				}
				plugin.lastAlign = this;
            });
            jQuery('#cs_ac_'+ci).click(function(){
                var b = $d.cm.getRule('.c'+ci,'text-align')[0];
                $d.cm.setRule('.c'+ci,'text-align: center;');
				this.style.borderColor = "#"+enabledColor;
				if (plugin.lastAlign != this && plugin.lastAlign !== null){
					plugin.lastAlign.style.borderColor = "#"+homeCellColor;
				}
				plugin.lastAlign = this;
            });
            jQuery('#cs_ar_'+ci).click(function(){
				var p = plugin;
                var b = $d.cm.getRule('.c'+ci,'text-align')[0];
                $d.cm.setRule('.c'+ci,'text-align: right;');
				this.style.borderColor = "#"+enabledColor;
				if (plugin.lastAlign != this && plugin.lastAlign !== null){
					plugin.lastAlign.style.borderColor = "#"+homeCellColor;
				}
				plugin.lastAlign = this;
            });

        },
        start: function(f,p,s){
            return
        },
        process: function(vr,pr,a){
            return
        },
        finish: function(){
            return
        }
    }
});
jQuery.tblFilter.addPlugin(jQuery.tf_columnStyle);