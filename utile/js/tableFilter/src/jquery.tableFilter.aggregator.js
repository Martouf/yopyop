/**
* @author daemach
*/
// Aggregator Constructor
jQuery.tf_aggregator = function(col){
    this.name = "Aggregator";
    this.col = col;
};
jQuery.extend(jQuery.tf_aggregator,{
    prototype : {
        init: function(){
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
            var homeRow = this.addRow();
            var op = ["sum","avg","min","max"];
			
            // creates root menu for this plugin within the main menu
            var rootMenu = this.getMainMenuRoot();
            jQuery.each(op,function(i,v){
                plugin.addMenuItem("Show all "+v+"s",rootMenu,function(){
                    jQuery('#'+plugin.name+'_row div:visible')
					.fadeOut("fast",function(){ jQuery('#'+plugin.name+'_row table.ag_tbl tr td:first-child').text(v+':');
						jQuery('#'+plugin.name+'_row .ag_'+v).fadeIn("fast"); 
					});
                });
            });
            var colMenu = this.addSubMenu(this.getHeaderText(),rootMenu);
			
            // if you want something to happen in this column only, use the "ci" variable in the function as the column index
            jQuery.each(op,function(i,v){
                plugin.addMenuItem("Show "+v+"s", colMenu, function(){
                    jQuery('#ag_cell_'+ci+' div:visible').fadeOut("fast",function(){ jQuery('#ag_cell_'+ci+' table.ag_tbl tr td:first-child').text(v+':'); jQuery('#ag_cell_'+ci+' .ag_'+v).fadeIn("fast")});
                });
            });
			
            // this is the holder for aggregator's output
            var tbl = '<table class="ag_tbl" align="right"><tr title="Aggregates for this page" class="ag_p"><td id="ag_p_'+ci+'" align="right">sum:</td><td><div class="ag_sum">00000000</div><div class="ag_avg"></div><div class="ag_min"></div><div class="ag_max"></div></td></tr><tr  title="Aggregates for all filtered rows" class="ag_v"><td id="ag_v_'+ci+'" align="right">sum:</td><td><div class="ag_sum">00000000</div><div class="ag_avg"></div><div class="ag_min"></div><div class="ag_max"></div></td></tr></table>';
			
            // append this table to the cell in the correct column
            homeRow.children().eq(this.col.index).append(tbl).attr("id","ag_cell_"+ci).addClass("ag_cell ag_cell_"+ci).find('div:not(".ag_sum")').hide();
        },
        start: function(f,p,s){
			
            /* Normally a sort doesn't trigger calculation. Since the table is sorted once on build by default, I want to make sure
            * the aggregate holders are ready so I'm faking it into thinking we're doing a filter operation so they will initialize.
            */
            this.f = (!this.col.root.buildComplete)? true : f;
            this.s = s;
            this.p = p;
            if (this.f) {
                this.vCount = 0;
                this.vsum = 0;
                this.vavg = 0;
                this.vmin = 0;
                this.vmax = 0;
            }
            if (this.p || this.f) {
                this.pCount = 0;
                this.psum = 0;
                this.pavg = 0;
                this.pmin = 0;
                this.pmax = 0;
            }
        },
        process: function(vr,pr,a){
            var n = a[2];
            if (vr && this.f){
                this.vCount ++;
                this.vsum += n;
                this.vavg = this.vsum/this.vCount;
                this.vmin = (this.vmin<=n)?this.vmin:n;
                this.vmax = (this.vmax>=n)?this.vmax:n;
            };
            if (pr && (this.p || this.f)){
                this.pCount ++;
                this.psum += n;
                this.pavg = this.psum/this.pCount;
                this.pmin = (this.pmin<=a)?this.pmin:n;
                this.pmax = (this.pmax>=a)?this.pmax:n;
            };
        },
        finish: function(){
            var d = this.col.decimals, ci = this.col.index;
			var nFormat = function (num,s){
				s=s||"";
				num = num.toString().split(".");
				var o,t,i=3,n = num[0].split("").reverse();
				while(i < n.length){
					n.splice(i,0,",");
					i+=4;
				}
				num[0] = n.reverse().join("");
				return s+num.join(".");
			};
            $("#ag_cell_"+ci+" .ag_p .ag_sum").text((!isNaN(this.psum))?((d) ? nFormat(this.psum.toFixed(2)) : nFormat(Math.round(this.psum).toString())):"N/A");
            $("#ag_cell_"+ci+" .ag_p .ag_avg").text((!isNaN(this.pavg))?((d) ? nFormat(this.pavg.toFixed(2)) : nFormat(Math.round(this.pavg).toString())):"N/A");
            $("#ag_cell_"+ci+" .ag_p .ag_min").text((!isNaN(this.pmin))?((d) ? nFormat(this.pmin.toFixed(2)) : nFormat(Math.round(this.pmin).toString())):"N/A");
            $("#ag_cell_"+ci+" .ag_p .ag_max").text((!isNaN(this.pmax))?((d) ? nFormat(this.pmax.toFixed(2)) : nFormat(Math.round(this.pmax).toString())):"N/A");
            $("#ag_cell_"+ci+" .ag_v .ag_sum").text((!isNaN(this.vsum))?((d) ? nFormat(this.vsum.toFixed(2)) : nFormat(Math.round(this.vsum).toString())):"N/A");
            $("#ag_cell_"+ci+" .ag_v .ag_avg").text((!isNaN(this.vavg))?((d) ? nFormat(this.vavg.toFixed(2)) : nFormat(Math.round(this.vavg).toString())):"N/A");
            $("#ag_cell_"+ci+" .ag_v .ag_min").text((!isNaN(this.vmin))?((d) ? nFormat(this.vmin.toFixed(2)) : nFormat(Math.round(this.vmin).toString())):"N/A");
            $("#ag_cell_"+ci+" .ag_v .ag_max").text((!isNaN(this.vmax))?((d) ? nFormat(this.vmax.toFixed(2)) : nFormat(Math.round(this.vmax).toString())):"N/A");
        }
    }
});


jQuery.tblFilter.addPlugin(jQuery.tf_aggregator,"number");