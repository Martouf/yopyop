/**
* @author daemach
*/
jQuery.fn.tableFilter = function(tblOptions,colOptions) {
    return this.each(function(){
        var tableFilter = new jQuery.tblFilter(this,tblOptions,colOptions);
        if ($daemach.debug) {
            window.tableFilter = tableFilter;
        }
    });
};
// Constructor
jQuery.tblFilter = function(tbl,tblOptions,colOptions){
    if ($("#tableFilter_header",tbl).size()){return};
	var root = this;
	
    this.version = "1.0 b2";
	this.userSettings = (jQuery.cookie) ? (JSON.parse($.cookie('daemach.tf.userSettings')) || {}):{};
    this.settings = jQuery.extend({},jQuery.tblFilter.defaults,tblOptions,this.userSettings);
	this.settings = jQuery.extend(this.settings,this.userSettings);
    this.getText = this.getBrowserTextField();
    this.browserSucks = jQuery.browser.msie;
    this.table = tbl;
    this.allRows = [];
    this.cols = [];
    this.headerRows = [];
    this.footerRows = [];
	this.lastCell = null;
    this.currentPage = 1;
    this.maxPage = 1;
    this.colSettings = colOptions;
    this.totalElements = 0;
    this.lastFilterElement = null;
    this.lastFilterCtrl = false;
    this.buildComplete = false;
    this.filterList = [];
    this.sortList = [];
    this.uniqueLength = 10;
    this.plugins = {};
    this.pluginList = [];

	$d.debug = this.settings.showDebug;
	
	$d.time("Total Build Time");

	var row;

    this.parseTable();

	// To increase speed by 1000% in ie (literally)	
	if (this.browserSucks){
		$d.cm.setRule("td","display:none");
	}

	this.parseColumns();

	this.buildColumns();

	// To increase speed by 1000% in ie (literally)
	if (this.browserSucks){
		$d.cm.setRule("td","display:''");
	}

	this.lockColumns();
	
	$d.timeEnd("Total Build Time");
	$d.log(" ");
	
	if (this.cols.length && !(typeof this.settings.sortOnLoad == "boolean")){
	this.cols[this.settings.sortOnLoad].sortColumn(null);
	};
	
	// we need to relock the columns and reset the menu whenever the window size changes
	jQuery(window).bind("resize",function(e){ root.lockColumns(true); $('#'+root.menu.className+'_trigger')[0].offsetLeft+"px" });
	
	// and on the initial load
	jQuery(window).bind("onLoad",function(e){ $('#'+root.menu.className).css("left",function(){return $('#'+root.menu.className+'_trigger')[0].offsetLeft+"px"}); });
	this.buildComplete = true;

};
jQuery.extend(jQuery.tblFilter,{
    defaults: {
        stripeClass: "offColor",
        pageLength: "25",
        paging: true,
        sort: true,
        sortOnLoad: 0,
        decimalPlaces: 2,
        loadPlugins: false,
		showDebug: false,
        imagePath: "images/icons"
    },
    prototype: {
        filterTable: function(op){
            var sort = (op === "sort") ? true:false;
            var page = (op === "page") ? true:false;
            var filter = (op === "filter") ? true:false;
            var root = this;
            var fl = this.filterList;
            var pl = this.pluginList;
            var ar = this.allRows;
            var show = false;
            var vr = 0; // visible record count
            var pr = 0; // page record count
            var fc = 0; // filter count
            var sc = this.settings.stripeClass;
            var minRow = (((this.currentPage - 1) * this.settings.pageLength) + 1);
            var maxRow = (this.currentPage * this.settings.pageLength);
            var fltChk = false;
            var row,r,i;
            $d.time("Filtering " + fl.length + " column" + ((fl.length==1)?"":"s"));
            if (this.settings.loadPlugins){
                // init plugins
                for (i=0;i<pl.length;i++) {
                    pl[i].startPlugins(filter,page,sort);
                };
            };
            // go to work
            for (var r=0; r<ar.length; r++) {
                row = ar[r][1];
                // sort early to try and buy %^$@^%$@^%$ IE some time
                if (sort) {

                  this.tbody.appendChild(row);

                }
                // filter first - if there is nothing in the filter list, show everything
                fltChk = (fl.length)? false:true;
                for (i=0;i<fl.length;i++) {
                    fltChk = fl[i].cFilter(ar[r][0][fl[i].index]);
                    if (!fltChk) { break; }; // break after first failure
                };
                if (fltChk) {
                    vr++;
                    show = (vr >= minRow && vr <= maxRow);
                    } else {
                    show = false;
                };
                ar[r][2] = show;
                if (this.settings.loadPlugins){
                    // process filter plugins
                    for (i=0;i<pl.length;i++) {
                        pl[i].processPlugins(fltChk,show,ar[r][0][pl[i].index],r);
                    };
                };
                // stripe and hide
                if (show && !this.browserSucks) {
                    row.style.display = "";
                    if (!(fc++%2)) {
                        $(row).addClass(sc);
                        } else {
                        $(row).removeClass(sc);
                    };
                } else {
                    row.style.display = "none";
                };
            };
            fc = 0; // grumble...
            // I despise IE...
            if (this.browserSucks){
                for (var i=0;i<ar.length;i++){
                    e = ar[i][1];
                    if (ar[i][2]){
                        e.style.display = "";
                        if (!(fc++%2))
                        $(e).addClass(sc);
                        else
                        $(e).removeClass(sc);
                        } else {
                        e.style.display = "none";
                    };
                };
            }; // IE sucks
            if (this.settings.loadPlugins){
                // process filter plugins
                for (i=0;i<pl.length;i++) {
                    pl[i].finishPlugins();
                };
            };
            $d.timeEnd("Filtering " + fl.length + " column" + ((fl.length==1)?"":"s"));
			
            this.maxPage = Math.floor(vr / this.settings.pageLength) + ((vr % this.settings.pageLength)? 1:0);
            this.updatePageNav(vr,ar.length,this.settings.pageLength); // currently visible, total records
        },
        sortTable: function(){
            var sl = this.sortList;
            var tb = this.tbody;
            var a = "var sortProxy = function (a,b){ return alphaSort(";
                var b = "";
                var c = ",";
                var d = "";
            var e = ")}";
            $d.time("Sorting " + sl.length + " column" + ((sl.length==1)?"":"s"));
            for (var i=0;i<sl.length;i++){
                // a[0][c][1-2]
                b += ((i)?"+":"") + ((sl[i].sortAsc)? "a":"b") + "[0]["+sl[i].index+"][0]";
                c += ((i)?"+":"") + ((sl[i].sortAsc)? "b":"a") + "[0]["+sl[i].index+"][0]";
            }
            eval(a+b+c+d+e);
            var alphaSort = function(a,b) {
                if (a==b) { return 0; };
                if (a<b) { return -1; };
                return 1;
            };
            // sort it
            this.allRows.sort(sortProxy);
            $d.timeEnd("Sorting " + sl.length + " column" + ((sl.length==1)?"":"s"));
            this.filterTable("sort");
        },
        pageNav: function(e){
            var action = e.target.id.substr(4,e.target.id.length-4);
            var curPage = this.currentPage;
            var maxPage = this.maxPage;
            var val;
            switch (action){
                case "First" :
                if (curPage !== 1){
                    this.currentPage = 1;
                };
                break;
                case "Prev":
                if (curPage > 1 ){
                    this.currentPage = this.currentPage - 1;
                };
                break;
                case "Current":
                val = parseInt($('#pageCurrent').val());
                if (val != this.currentPage){
                    this.currentPage = (val > maxPage) ? maxPage : val;
                };
                break;
                case "Next":
                if (curPage < maxPage){
                    this.currentPage = this.currentPage + 1;
                };
                break;
                case "Last":
                if (curPage !== maxPage){
                    this.currentPage = maxPage;
                };
                break;
                case "Records":
                val = parseInt($('#pageRecords').val());
                if (val != this.settings.pageLength){
                    this.currentPage = 1;
                    this.settings.pageLength = val;
					
					this.saveUserSettings("pageLength", this.settings.pageLength);
                };
                break;
            };
            this.filterTable("filter");
        },
        updatePageNav: function (v,t,l){
            var root = this;
            var re = /(\-disabled\.gif)/;
            var disable = function (str){
                var tmp;
                if (re.test(str)) {
                    return str;
                    } else {
                    tmp = str.split(".");
                    tmp.pop();
                    return tmp.join(".") + "-disabled.gif"
                };
            };
            var enable = function (str){
                var tmp;
                if (re.test(str)) {
                    return str.replace(re,".gif");
                    } else {
                    return str;
                };
            };
            $("#pageFirst").add("#pagePrev").attr("src",function(){ return (root.maxPage === 1 || root.currentPage == 1) ? disable(this.src) : enable(this.src); });
            $('#pageCurrent').val(this.currentPage.toString());
            $("#pageLast").add("#pageNext").attr("src",function(){ return (root.maxPage === 1 || root.currentPage == root.maxPage) ? disable(this.src) : enable(this.src); });
            $('#pageCount').text(this.maxPage.toString());
            $('#pageVisible').text(v.toString());
            $('#pageTotal').text(t.toString());
            $('#p_menu').css("left",function(){return $('#p_menu_trigger')[0].offsetLeft+"px"});
        },
		menu: {
			count: 0,
			isAnimating: false,
			enablePlugins: function(root){
				if (confirm("Are you sure you want to "+((root.settings.loadPlugins)?"disable":"enable")+" plugins?")){
					root.saveUserSettings("loadPlugins",((root.settings.loadPlugins)?false:true));
					window.location.reload();
				};
			},
			showDebug: function(root){
				$d.debug = (root.settings.showDebug)?false:true;
				root.saveUserSettings("showDebug",((root.settings.showDebug)?false:true));
				if (confirm("Debug "+((root.settings.showDebug)?"disabled.":"enabled.")+ " Reload? " + ((root.settings.showDebug)? "":"(You're going to need firebug to see this...)"))){
					window.location.reload();
				};
			}
		},
		saveUserSettings: function(key,value){
			var setting = {};
            if (jQuery.cookie){
				setting[key]=value;
				this.userSettings = JSON.toJSONString(jQuery.extend({},this.userSettings,setting));
				jQuery.cookie('daemach.tf.userSettings', this.userSettings, {expires: 10, path: '/'});
            };
		},
        parseTable: function(){
			$d.time("Parse Table");
            var headRows = []; //temp storage in case we find no thead
            var footRows = []; //temp storage in case we find no tfoot
            var cellsLength = [];
            var allRows = this.table.rows;
            var lengthChange = false;
            var footDone = false;
            var thisRow;
            var root = this;
            var ip = this.settings.imagePath;
			
            // halfway through the table we're going to look for header rows
            var half = allRows.length - Math.round((allRows.length / 2),0);
			
            for (var r=allRows.length-1;r>=0;r--){
                thisRow = allRows[r];
                if (r == allRows.length-1){
                    cellsLength.push([allRows[r].cells.length,r]); // first pass - set initial value
                };
                lengthChange = allRows[r].cells.length !== cellsLength[cellsLength.length-1][0];
                if (headRows.length || ( (r < half) && lengthChange) || r==0){
                    // found a candidate - push the rest of the rows
                    headRows.push(allRows[r]);
                };
                if((r > half) && !footDone){
                    if (lengthChange){
                        footDone = true;
                        } else {
                        footRows.push(allRows[r]);
                    };
                };
                if (lengthChange){
                    cellsLength.push([allRows[r].cells.length,r]);
                    lengthChange = false;
                }; //store the length and row number
            };
            this.cellsLength = cellsLength;
            // deal with heaers/footers
            var head = jQuery('thead',this.table);
            var foot = jQuery('tfoot',this.table);
            if(!head.size()){
                //make a header block
                head = jQuery('<thead />').prependTo(this.table);
                var cl = this.cellsLength.reverse();
                if (cl.length == 2 && (cl[1][1] - cl[0][1]) > half){ // 2 size changes, last one occurred after the halfway point
                    if (cl[0][0] < cl[1][0]){ // if there are less cells in the caught header than in the rest of the table, grab a new one.
                        headRows.reverse().push(allRows[cl[0][1]+1]);
                        headRows.reverse();
                    };
                };
                jQuery(headRows).each(function(){ jQuery(this).prependTo(head); });
            };
            if(!foot.size()){
                //make a footer block
                foot = jQuery('<tfoot />').prependTo(this.table);
                if (footRows.length < 3){
                    // jQuery(footRows).each(function(){ jQuery(this).prependTo(foot); }); //take a shot...
                };
            };
            this.headerRows[0] = jQuery('tr:last-child',head)[0]; // sorting row
            this.headerRows[1] = jQuery(this.headerRows[0]).clone().attr("id","tableFilter_header").appendTo(head).children().empty().end()[0]; // filtering row
            // set tbody after creating thead and tfoot
            this.tbody = jQuery('tbody',this.table)[0];
			//this.lastCell = this.tbody.rows[this.tbody.rows.length-1].cells[this.tbody.rows[0].cells.length-1];
            var tbodyWidth = 0;
            // determine cell counts - for future code that deals with spanned columns
            var cellCount = jQuery('tr',this.tbody)[0];
            cellCount= jQuery(cellCount).children();
            cellCount.each(function(){
                var tmp = jQuery(this).attr('colspan');
                tbodyWidth += (tmp)? tmp : 1;
            });
            // make it so
            this.actualCellCount = tbodyWidth;
            this.cellCount = cellCount.size();
			
            //make sure on initial parse that the saved/default value is not larger than the actual number of rows or there is no way to get to the paging bar
            this.settings.pageLength = (this.settings.pageLength > this.tbody.rows.length) ? this.tbody.rows.length : this.settings.pageLength;
            if (this.settings.paging && this.tbody.rows.length){
                this.maxPage = Math.floor(this.tbody.rows.length / this.settings.pageLength) + ((this.tbody.rows.length % this.settings.pageLength)? 1:0);
                this.pagingRow = jQuery(this.headerRows[1]).clone().removeAttr("id").empty().appendTo(foot); // paging row
                
                this.pagingRow.append('<td colspan="'+this.cellCount+'" align="left" nowrap="nowrap" valign="middle"><div id="p_base"> <img id="pageFirst" src="'+ip+'/page-first-disabled.gif" align="absmiddle" alt=""> <img id="pagePrev" src="'+ip+'/page-prev-disabled.gif" align="absmiddle" alt="">  Page <input id="pageCurrent" size="2" value="1" type="text"> of <span id="pageCount">'+this.maxPage+'</span> <img id="pageNext" src="'+ip+'/page-next.gif" align="absmiddle" alt=""> <img id="pageLast" src="'+ip+'/page-last.gif" align="absmiddle" alt=""> <img src="'+ip+'/toolbar.gif" align="absmiddle" alt=""> Showing: <input id="pageRecords" size="2" value="'+this.settings.pageLength+'" type="text"> rows per page - (<span id="pageVisible"></span> visible/<span id="pageTotal"></span> total) <img src="'+ip+'/toolbar.gif" align="absmiddle" alt=""> <a id="p_menu_trigger">&nbsp;Menu</a></div></td>')
                .find("img").css("cursor","pointer").bind("click",this,function(e){ e.data.pageNav(e)}).end()
                .find("input").addClass("filter").bind("change",this,function(e){ e.data.pageNav(e) })
                .bind("blur",this,function(e){ e.data.pageNav(e) })
                .bind("keydown",function(e){ if ((e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 47 & e.keyCode != 8) return false;});
				
                // create menu passing either elements or jQuery selectors for base and trigger. id must be a valid id string.
                this.mainMenu = this.createMenu("p_menu","#p_base",this.pagingRow[0],"up", "#p_menu_trigger","click");
                this.createMenuItem("TableFilter "+this.version,this.mainMenu);
				this.createSeparator(this.mainMenu);
                this.createMenuItem(((this.settings.loadPlugins)?"Disable":"Enable")+" plugins",this.mainMenu,function(){root.menu.enablePlugins(root);});
				this.createMenuItem(((this.settings.showDebug)?"Hide":"Show")+" debug info",this.mainMenu,function(){root.menu.showDebug(root);});
				this.createSeparator(this.mainMenu);
            }
			
            // set up colgoup for column locking
            this.colgroup = jQuery('colgroup',this.table);
            if (!this.colgroup.size()) {
                this.colgroup = jQuery('<colgroup>'+ "<col/>".repeat(this.cellCount) + '</colgroup>').prependTo(this.table);
                } else {
                this.colgroup = this.colgroup[0];
            };
			$d.timeEnd("Parse Table");
        },
        createMenu: function(id, base, container, direction, trigger, inEvent, outEvent,menuClass){
            var menu, mColor, lbColor, dbColor,root=this, setClass = false;
			base = $(base).css("position","relative");
			id = id || "menu_"+this.menu.count++;
			
			if (!this.menu.className){
				this.menu.className = id;
				setClass = true;
			}
			
			if (menuClass ){
				setClass = true;
			} else {
				menuClass =this.menu.className;
			}
			if (setClass){	
				// we're going to modify the menu colors to match the paging row
	            mColor = $d.cw.rgb2hex($d.cm.getRootStyle(base[0],"backgroundColor"));
				// ie... it actually prepends a #!!!  wth?
				mColor = mColor.substring(mColor.length-6);
				
	            lbColor = $d.cw.ccLighter(mColor,.15);
	            dbColor = $d.cw.ccDarker(mColor,.15);
	            // create CSS class specific to this menu
	            $d.cm.setRule("."+menuClass+" li","border: 1px solid; border-color: #"+lbColor+" #"+dbColor+" #"+dbColor+" #"+lbColor+"; background-color: #"+mColor+";");
	            $d.cm.setRule("."+menuClass+" li:hover","border: 1px solid; border-color: #"+dbColor+ " #"+lbColor+" #"+lbColor+" #"+dbColor+";");
				$d.cm.setRule("."+menuClass+" li.separator","height:3px; border: 1px solid; border-color: #"+dbColor+ " #"+lbColor+" #"+lbColor+" #"+dbColor+";");
			}
			
            menu = $('<div id="'+id+'" class="menu '+menuClass+'"><ul id="'+id+'_root"></ul></div>').appendTo(base);
			
            var cont = (container === null)? base:container;
			var rh = parseInt(cont.offsetHeight)+parseInt(cont.style.padding || 0)+parseInt(cont.style.borderBottomWidth || 0)+parseInt(cont.style.borderTopWidth || 0);
			var dh = parseInt(base[0].offsetHeight)+parseInt(base[0].style.padding || 0)+parseInt(base[0].style.borderBottomWidth || 0)+parseInt(base[0].style.borderTopWidth || 0);
			
            switch (direction){
                case "up" :
					menu[0].style.bottom = (dh + ((rh-dh)/2)-1)+"px";
                break;
                case "down" :
					menu[0].style.top = (dh + ((rh-dh)/2)-1)+"px";
                break;
            };
            if (container.nodeName.toLowerCase() == "td" && container.cellIndex == this.cellCount - 1){
                menu[0].style.right = "-2px";
            } else {
                menu[0].style.left = "-2px";
            }

			if (trigger){
                inEvent = inEvent || "mouseover";
                outEvent = outEvent || "mouseout";
                // bind open/close triggers
                jQuery(trigger).bind(inEvent,function(){
					if (!root.menu.isAnimating){
						// yeah - slideDown?  What's that about..
						$('#'+id).slideToggle("fast",function(){root.menu.isAnimating = false;});
						root.menu.isAnimating = true;
					}
					return false;
				});
                jQuery('#'+id).truemouseout(function(){
					if (!root.menu.isAnimating){
						$(this).slideUp("fast",function(){root.menu.isAnimating = false;});
						root.menu.isAnimating = true;
					}
					return false;
				});
            };
            return menu;
        },
        createSubMenu: function(text,target){
            target = jQuery(target).children('ul').eq(0);
            var sm = jQuery('<li class="submenu"></li>')
            .appendTo(target).hover(function(){$(this).addClass("over")},function(){$(this).removeClass("over")})
            .append('<table cellspacing="0" cellpadding="0" style="background-color: transparent;" width="100%"><tr style="background-color: transparent;"><td>'+text+'</td><td align="right">>>></td></tr></table><ul class="submenu" id="'+text+'_submenu"></ul>');
            return sm;
        },
        createMenuItem: function(text,target,fn,id){
            target = jQuery(target).children('ul').eq(0);
            var mi = jQuery('<li>'+text+'</li>').appendTo(target);
            if (fn){
                mi.bind('click',fn);
            };
            if (id){
                mi.attr("id",id);
            };
            return mi;
        },
        createSeparator: function(target){
            target = jQuery(target).children('ul').eq(0);
            var mi = jQuery('<li class="separator">&nbsp;</li>').appendTo(target);
        },
        guessDataType: function(rows,c){ // null rows suck. To avoid reparsing, test 10 rows
            var dt1, dt2,chk, cell, lc = 0, same = 0, tc = 0;
            if (rows.length > 100){
                chk = Math.floor(rows.length * .1);
                } else {
                chk = Math.floor(rows.length * .2) || 1;
            };
            for (var r=0;r<rows.length;r++){ // get first non-null cell
                cell = rows[r].cells[c];
                dt1 = this.getText(cell).trim();
                if (dt1.length){
                    dt1 = this.getDataType(dt1);
                    break;
                };
            };
            lc = r+1;
            while(lc < rows.length){
                dt2 = this.getText(rows[lc].cells[c]).trim();
                dt2 = this.getDataType(dt2);
                if (!dt2.length || dt1 == dt2){ // if no length, go with it. This is best-guess...
                    same++;
                };
                lc += chk;
                tc++;
            };
            if ((same/tc) < .75) {
                return "string";
                } else {
                return dt1;
            };
        },
        makeTempColumn: function(dataType,c){
            this.tmpCol = {};
            var tc = this.tmpCol;
            tc.dataType = dataType;
            tc.uniqueCount=0;
            tc.root = this;
            tc.index = c;
            switch (tc.dataType){
                case "string" :
                tc.uniqueText = [];
                break;
                case "date" :
                tc.uniqueYears = [];
                tc.uniqueMonths = [];
                break;
                case "number" :
                tc.decimals = false;
                tc.lengthChange = false;
                tc.uniqueText = [];
                break;
            };
            tc.sortAsc = null;
            tc.defaultSortAsc = true;
            tc.maxLen = null;
            tc.reparse = false;
        },
        parseColumns: function(){
			$d.time("Parse Column Data");
			//var col = $(this.colgroup).children().get();
            var rows = this.tbody.rows;
            var rLen = rows.length;
            var cLen = this.cellCount;
            var row,cell,tCell,nCell,tmp,text,html,cn,textLength,cols = {};

            for (var c=0;c<cLen;c++){
				//col[c].style.display = "none";
                dataType = this.guessDataType(rows,c);
                this.makeTempColumn(dataType,c);
				textLength=0;
				
				$d.time("Parsing Column "+c);
                for (var r=0;r<rLen;r++){
                    this.totalElements++;
					row = rows[r];
					cell = row.cells[c];
					
                    // init the row on the first column only
                    if (!c){
                        this.allRows[r]= [[],rows[r],true];
                    };
					
					cn = "c"+c+" r"+r+ " " + cell.className;
					text = (!this.browserSucks)? cell.textContent.trim() : cell.innerText.trim();

					if (!this.browserSucks || rLen < 500){
						cell.className = cn;					
					}

                    this.allRows[r][0][c] = this.parseText(text);
					
					if (text.length) {
                        textLength += text.length;
                    };
                };
				$d.timeEnd("Parsing Column "+c);
				
                if (this.tmpCol.reparse){
                    $d.time("Reparsing column "+ c);
                    for (r=0;r<rLen;r++){
                        text = this.allRows[r][0][c][1];
                        if (text.length) {
                            this.allRows[r][0][c] = this.parseText(text);
                        };
                    };
					$d.timeEnd("Reparsing column "+ c);
                };
				
                if (textLength) {
                    switch (this.tmpCol.dataType){
                        case "string" :
                        this.tmpCol.uniqueText.sort();
                        break;
                        case "date" :
                        this.tmpCol.uniqueMonths.sort();
                        this.tmpCol.uniqueYears.sort();
                        break;
                        case "number" :
                        this.tmpCol.uniqueText.sort();
                        break;
                    };
					
                    this.cols[c] = new jQuery.colFilter(this.tmpCol);
                    if (this.settings.loadPlugins){
                        this.addPlugins(this.cols[c]);
                    }
                };

            }//for column
			$d.timeEnd("Parse Column Data");

        },
        addPlugins: function (col){

            var tfp = jQuery.tblFilter.tfPlugins,name;
            if (tfp[col.dataType].length){
                for (var i=0;i<tfp[col.dataType].length;i++){
					
                    col.pluginList.push(new tfp[col.dataType][i](col));
					
                    //get plugin name
                    name = col.pluginList[col.pluginList.length-1].name;
                    if (!this.plugins[name]){
                        this.plugins[name]={};
                    };
                };
											
                this.pluginList.push(col);
                col.initPlugins();
            };
        },
        parseText: function(text){
            var arr, tc=this.tmpCol;
            if (text.length) {
                arr = this.parse[tc.dataType](tc,text);
                } else {
                switch (tc.dataType){
                    case "date":
                    arr = ["00000101","01/01/0000"];
                    break;
                    case "number":
                    arr = ["0".repeat(tc.maxLen),0,0];
                    break;
                    case "string":
                    arr = ["",""];
                    break;
                };
            };
            return arr;
        },
        parse:{
            string: function(tmpCol,text){
                var arr=[];
                arr[0] = text.toLowerCase().replace(/[^\w]*/g,"");
                arr[1] = text;
                if (tmpCol.uniqueCount <= tmpCol.root.uniqueLength && tmpCol.uniqueText.indexOf(text) < 0){
                    tmpCol.uniqueText.push(text);
                    tmpCol.uniqueCount++;
                };
                if (tmpCol.maxLen === null){
                    tmpCol.maxLen = arr[1].length;
                };
                if (tmpCol.maxLen < arr[1].length){
                    tmpCol.maxLen = arr[1].length;
                };
                return arr;
            },
            date: function(tmpCol,text){
                var arr=[],s,tmp,month,year,day;
                var _zeroPad = function(num) {
                    s = '0'+num;
                    return s.substring(s.length-2);
                };
                var _unique = function (month,year){
                    if (tmpCol.uniqueMonths.length < 12 && tmpCol.uniqueMonths.indexOf(month) < 0){
                        tmpCol.uniqueMonths.push(month);
                    };
                    if (tmpCol.uniqueCount <= tmpCol.root.uniqueLength && tmpCol.uniqueYears.indexOf(year) < 0){
                        tmpCol.uniqueYears.push(year);
                        tmpCol.uniqueCount++;
                    };
                };
                var _getYear = function(d){
                    return d.getFullYear().toString();
                };
                var _getMonth = function(d){
                    return _zeroPad(d.getMonth()+1);
                };
                var _getDay = function(d){
                    return _zeroPad(d.getDate());
                };
                tmp = Date.parse(text);
                if (isNaN(tmp)){
                    tmpCol.dataType="string";
                    tmpCol.reparse=true; // bah - bad guess
                };
                if (!tmpCol.reparse){
                    tmp = new Date(tmp);
                    year = _getYear(tmp);
                    month = _getMonth(tmp);
                    day =_getDay(tmp);
                    _unique(month,year);
                    arr[0] = year + month + day;
                    } else {
                    arr[0] = "00000000";
                };
                arr[1] = text;
                return arr;
            },
            number: function(tmpCol,text){
                var asc,tmp,dp,num,dec = false, arr=[];
				var pad = ["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"];
                dp = tmpCol.root.settings.decimalPlaces;
                num = Number(text.replace(/[$£¥€,~+]?/g, ''));
                if (isNaN(num)){
                    tmpCol.dataType="string";
                    tmpCol.reparse=true; // bah - bad guess
                };
				asc = num.toFixed(dp).split(".");
				
                if (!tmpCol.reparse){
					tmpCol.decimals = (text.indexOf(".") >= 0);
					
	                if (tmpCol.maxLen === null){
	                    tmpCol.maxLen = 8;
	                };

                    if (tmpCol.maxLen < asc[0].length){
                        tmpCol.reparse = true;
                        tmpCol.maxLen = asc[0].length;
                        } else if (!tmpCol.reparse){
						// padding majik
						asc[0] = pad.slice(0,tmpCol.maxLen-asc[0].length).concat(asc[0].split("")).join("");

                    };
                    if (tmpCol.uniqueCount <= tmpCol.root.uniqueLength && tmpCol.uniqueText.indexOf(text) < 0){
                        tmpCol.uniqueText.push(text);
                        tmpCol.uniqueCount++;
                    };
                    arr[0] = asc.join(".");
                    arr[1] = text;
                    arr[2] = num;
                    } else {
	                    if (tmpCol.maxLen < asc[0].length){
	                        tmpCol.maxLen = asc[0].length;
	                    };
                    arr[0] = 0;
                    arr[1] = text;
                    arr[2] = 0;
                }
                return arr;
            }
	
        },
        lockColumns: function(resize){
			$d.time("Lock Columns");
			var col = 	jQuery(this.colgroup).children().get();
			var tbl = this.table;

			tbl.style.width = ""; tbl.style.tableLayout = "";
			for (i=0;i<col.length;i++){
				col[i].style.width = ""
			}

			for (i=0;i<col.length;i++){
				col[i].style.width = col[i].offsetWidth+ "px";
			}
			tbl.style.width = tbl.offsetWidth+ "px";
			
            if (!this.browserSucks){
		//		this.table.style.tableLayout = "fixed";
            };
			$d.timeEnd("Lock Columns");
        },
        buildColumns: function() {
			$d.time("Build Headers");

            var root = this;
            var col = jQuery(this.colgroup).children();
            var sRow = jQuery(this.headerRows[0]).children();
            var hRow = jQuery(this.headerRows[1]).children();
            jQuery.each(root.cols,function(i,v){
                jQuery(col[i]).addClass("sortable-col");
				
                if (v ){
					v.buildSort(sRow[i]);
					if ((v.dataType == "date" && v.uniqueMonths.length > 1) || (v.dataType != "date" && v.uniqueText.length > 1)){
                  	  v.buildFilter(hRow[i]);
					}
                };
            });
			$d.timeEnd("Build Headers");
        },
        getDataType : function(str) {
            if (!isNaN(Date.parse(str))) {
                return "date";
                } else if (!isNaN(Number(str.replace(/[$£¥€,~+]?/g, '')))){
                return "number";
                } else {
                return "string";
            };
        },
        getBrowserTextField: function(){
            return (document.body.innerText) ? function(ele){ return ele.innerText } : function(ele){ return ele.textContent };
        },
        clearSortList: function(){
            var sl = this.sortList;
            for (var c=sl.length-1;c>=0;c--){
                sl[c].clearSort();
                sl.pop();
            }
        },
        clearFilterList: function(){
            var fl = this.filterList;
            for (var c=fl.length-1;c>=0;c--){
                fl[c].clearFilter();
                fl.pop();
            }
        },
        filterFocus: function(e,ele){
            var fl = this.filterList;
            if (fl.indexOf(e.data) >= 0 && e.ctrlKey){
                fl.splice(fl.indexOf(e.data),1);
                e.data.clearFilter();
                } else {
                this.lastFilterElement = e.target;
                this.lastFilterCtrl = e.ctrlKey;
            };
        }
    },
    tfPlugins: {
        date: [],
        number: [],
        string: []
    },
    addPlugin: function(constructor, dataType){
        if (typeof dataType == "undefined" || dataType == "all"){
            jQuery.tblFilter.tfPlugins["date"].push(constructor);
            jQuery.tblFilter.tfPlugins["string"].push(constructor);
            jQuery.tblFilter.tfPlugins["number"].push(constructor);
            } else {
            jQuery.tblFilter.tfPlugins[dataType].push(constructor);
        };
        // add init methods
        jQuery.extend(constructor.prototype,{
            addRow: function(location,clone){
                var root = this.col.root, newRow;
                if (typeof location == "undefined") {location = "footer";};
                if (typeof clone == "undefined") {clone = "blank";};
                if (root.plugins[this.name].row){
                    newRow = jQuery(root.plugins[this.name].row);
                    } else {
                    switch (clone){
                        case "blank":
                        newRow = root.plugins[this.name].row = jQuery(root.allRows[0][1]).clone().removeClass(root.settings.stripeClass);
                        break;
                        case "sortRow":
                        newRow = root.plugins[this.name].row = jQuery(root.headerRows[1]).clone();
                        break;
                        case "pageRow":
                        newRow = root.plugins[this.name].row = jQuery(root.headerRows[1]).clone();
                        break;
                    };
                    switch (location){
                        case "header":
                        newRow.attr("id",this.name+"_row").addClass(this.name+"_row").appendTo("thead").children().empty().end();
                        break;
                        case "footer":
                        newRow.attr("id",this.name+"_row").addClass(this.name+"_row").prependTo("tfoot").children().empty().end();
                        break;
                    };
                };
                return newRow;
            },
            getHeaderText: function(){
                var ele = jQuery(this.col.root.headerRows[0]).children().eq(this.col.index);
                return jQuery(ele).text();
            },
            preInit: function(){
                this.cmi = 0;
            },
            postInit: function(){
                this.cmi = 0;
            },
            getMainMenuRoot: function(){
                var root = this.col.root;
                if (!root.plugins[this.name].menu){
                    root.plugins[this.name].menu = root.createSubMenu(this.name,root.mainMenu);
                }
                return root.plugins[this.name].menu;
            },
            addMenu: function(id, base, container, direction, trigger, inEvent, outEvent){
                var root = this.col.root, ci = this.col.index;
                return root.createMenu(id, base, container, direction, trigger, inEvent, outEvent);
            },
            addSubMenu: function(text, target){
                var root = this.col.root;
                return root.createSubMenu(text,target);
            },
            addMenuItem: function(text,target,fn){
                var root = this.col.root, ci = this.col.index, mi, id, smid;
                smid = jQuery(target).children('ul').eq(0).attr("id");
                id = smid+'_mi_'+this.cmi;
                mi = jQuery('#'+id);
                if (!mi.size()){
                    root.createMenuItem(text,target,fn,id);
                }
                this.cmi++
            }
        });
    }
});
// Constructor
jQuery.colFilter = function(col){
    jQuery.extend(this,jQuery.colFilter.defaults,col);
    this.pluginList = [];
	this.lastFilterVal = "";
};
jQuery.extend(jQuery.colFilter,{
    defaults: {
        dataType: "text", //text/date/number
        filterType: "search", // search/auto
        filter: true,
        sort: true
    },
    prototype : {
        sortColumn: function(e){
            var sl = this.root.sortList;
            var ele = this.sortParentEle;
            if ((sl.indexOf(this) >= 0)){
                if (e.ctrlKey){
                    this.clearSort();
                    sl.splice(sl.indexOf(this),1);
                    } else {
                    var oClass = (this.sortAsc) ? "asc":"desc";
                    var nClass = (!this.sortAsc) ? "asc":"desc";
                    jQuery(ele).removeClass("sorted-" + oClass).addClass("sorted-" + nClass);
                    this.sortAsc = !this.sortAsc;
                };
                } else {
                if (sl.length && !e.ctrlKey){
                    this.root.clearSortList();
                };
                sl.push(this);
                if (this.defaultSortAsc){
                    jQuery(ele).addClass("sorted-asc");
                    } else {
                    jQuery(ele).addClass("sorted-desc");
                };
                jQuery(this.parentCol).addClass("sorted-col");
                this.sortAsc = this.defaultSortAsc;
            };
            $d.time("Total sort time");
            if (sl.length){
                this.root.sortTable(e);
            };
            $d.timeEnd("Total sort time");
            $d.log(" ");
        },
        clearSort: function(){
            var tClass = (this.sortAsc) ? "asc":"desc";
            jQuery(this.sortParentEle).removeClass("sorted-" + tClass);
            jQuery(this.parentCol).removeClass("sorted-col");
            this.sortAsc = this.defaultSortAsc;
        },
        buildSort: function(ele){
            this.sortParentEle = ele;
            this.parentCol = jQuery(this.root.colgroup).children()[this.index];
            jQuery(ele).addClass("sortable").css("cursor","pointer").bind("click",this,function(e){ e.data.sortColumn(e,this); });
        },
        filterColumn: function(e){
            var fl = this.root.filterList;
			var ff = this.filterField;
            var svl = (ff.length > 1) ? ff[0].value.length + ff[1].value.length : ff[0].value.length;
			var col = this;
			var _numCheck = function (){
				var ffv = ff[0].value;
				if (col.dataType == "number") {
					if ( !ffv.match(/(^[\.><=-])(\d+)/) && !ffv.match(/(^[><]=)(\d*\.*\d+)/) && !ffv.match(/(\d*\.*\d+)\s*-\s*(\d*\.*\d+)/) && !ffv.match(/(^\d*\.*\d+$)/) ){
						return false;
					};
				};
				return true;
			};

			if (e.type != "keyup" || (e.type == "keyup" && !ff[0].value.length) || (e.type == "keyup" && ff[0].value != this.lastFilterVal && _numCheck())){
	            if (fl.indexOf(this) < 0 && svl){
	                if (fl.length && this.root.lastFilterElement == e.target && !this.root.lastFilterCtrl && !(e.type == "keyup" && (e.ctrlKey || e.shiftKey || e.altKey))){
	                    this.root.clearFilterList();
	                };
	                fl.push(this);
	                jQuery(this.filterParentEle).addClass("filtered");
					
                } else if ((!svl && fl.indexOf(this) >= 0)) {
	                fl.splice(fl.indexOf(this),1);
	                this.clearFilter();
	            };
				this.lastFilterVal = ff[0].value;
	            this.root.currentPage = 1;

				// get a new filter for the current value
				this.cFilter = this.getFilter();

	            $d.time("Total filter time");
	            
				this.root.filterTable("filter");
	            
				$d.timeEnd("Total filter time");
	            $d.log(" ");
			}
        },
        clearFilter: function(){
            jQuery(this.filterParentEle).removeClass("filtered");
            jQuery(this.filterField).val("");
        },
        getFilter: function(){
            switch(this.dataType){
                case "string":
                return function(arg){
                    return (arg[0].length) ? (arg[0].indexOf(this.filterField[0].value.toLowerCase().replace(/[^\w]*/g,"")) >=0): false;
                };
                break;
                case "date":
                return function(arg){
                    var ff, ffml, ffyl, m, y;
                    if (arg[0] === null || !arg[0].length){
                        return false;
                        } else {
                        ff = this.filterField;
                        ffml = ff[0].value.length;
                        ffyl = ff[1].value.length;
                        m = (ffml && ff[0].value == arg[0].substr(4,2));
                        y = (ffyl && ff[1].value == arg[0].substr(0,4));
                        return ((ffml && ffyl && m & y) || (!ffyl && ffml && m) || (!ffml && ffyl && y));
                    };
                };
                break;
                case "number":
					var ffv = this.filterField[0].value;
					var re;	
					if (ffv.match(/(^[><])(\d*\.*\d+)/)){
						re = /(^[><])(\d*\.*\d+)/.exec(ffv);
						return function(arg){
							var v = arg[2];
		                    return (arg[0].length) ? ((re[1]==">")? (v > re[2]):(v < re[2])) : false;
		                };	
					} else if (ffv.match(/(^[><]=)(\d*\.*\d+)/)) {
						re = /(^[><]=)(\d*\.*\d+)/.exec(ffv);
						return function(arg){
							var v = arg[2];
		                    return (arg[0].length) ? ((re[1]==">=")? (v >= re[2]):(v <= re[2])) : false;
		                };	
					} else if (ffv.match(/(\d*\.*\d+)\s*-\s*(\d*\.*\d+)/)){
						re = /(\d*\.*\d+)\s*-\s*(\d*\.*\d+)/.exec(ffv);
						return function(arg){
							var v = arg[2];
		                    return (arg[0].length) ? (v > re[1] && v < re[2]) : false;
		                };	
					} else if (ffv.match(/(^=)(\d*\.*\d+)/)){
						re = /(^=)(\d*\.*\d+)/.exec(ffv);
						return function(arg){
							var v = arg[2];
		                    return (arg[0].length) ? (v == re[2]) : false;
		                };	
					} else {
						return function(arg){
		                    return (arg[0].length) ? (arg[0].indexOf(this.filterField[0].value) >=0): false;
		                };
					};
                break;
            };
        },
        buildFilter: function (ele){
            var tmp;
            this.filterParentEle = ele;
            this.filterField = [];
            jQuery(ele).addClass("filterRow");
            switch (this.dataType){
                case "string" :
                if (this.uniqueText.length <= this.root.uniqueLength) { this.addField(ele,"select",this.uniqueText); }
                else { this.addField(ele,"text"); };
                break;
                case "date" :
                // this.addField(ele,"text");
                this.addField(ele,"select",this.uniqueMonths);
                this.addField(ele,"select",this.uniqueYears);
                break;
                case "number" :
               // if (this.uniqueText.length <= this.root.uniqueLength) {
              //      this.addField(ele,"select",this.uniqueText);
                //    } else {
                    this.addField(ele,"text",null,true);
              //  };
                break;
            };
        },
        addField: function(target,type,options,numeric){
            options = options || [];
            type = type || "text";
			numeric = numeric || false;
            var len,tmp,opt, fld = {
                text: '<input type="text" value="" />',
                select: document.createElement('select'), //<select></select>document.createElement('select')
                option: '<option></option>'
            };
            switch (type){
                case "text":
                len = (this.maxLen > 20)? "20" : this.maxLen;
                tmp = jQuery(fld.text);
                tmp = tmp.attr("size",len).appendTo(target).addClass("filter")
                .bind("keyup",this,function(e){ e.data.filterColumn(e,this); })
                .bind("click",this,function(e){ e.data.root.filterFocus(e,this); });
				if (numeric){
					tmp.bind("keypress",this,function(e){
						var v = this.value;
						var c = e.charCode || e.keyCode;
						var t = 0;
						
						// . == 46
						var _isN = function(k){
							k = k || c;
							var tmp = ((k >= 48 && k <= 57) || k ===46)?true:false;
							return tmp;
						};
						var _isS = function(k){
							k = k || c;
							var tmp = (k === 45 || k === 60 || k === 61 || k === 62)?true:false;
							return tmp;
						};
						var _isD = function(k){
							k = k || c;
							var tmp = (k === 8 || k === 46)?true:false;
							return tmp;
						};
						switch (v.length){
							case 0:
								if (!_isN(c) && !_isS(c)) { return false; };
							break;
							case 1:
								t = v.charCodeAt(0);
								if ((t === 60 || t === 62) && c !== 61 && !_isN(c) && !_isD(c)){
									return false;
								} else if ((t === 45 || t === 61) && !_isN(c) && !_isD(c)){
									return false;
								} else if (!_isN(c) && !_isD(c) && c !== 61 && c !== 45){
									return false;
								} else if (c == 46 && v.indexOf(".")>= 0){
									return false;
								};
							break;
							default:
							var rng = v.match(/([<>=-])/g);
								if (!_isN() && !_isD(c) && c !== 45 || (c === 45 && rng)){
									return false;
								};
							break;
							
						};
					});
				};
                break;
				
                case "select":
                tmp = jQuery(fld.select).appendTo(target).addClass("filter");
                opt = fld.option;
                for (var i=0;i<options.length;i++){
                    opt += '<option value="'+options[i]+'">'+options[i]+'</option>'
                };
                jQuery(tmp).append(opt);
                tmp.bind("change",this,function(e){ e.data.filterColumn(e,this); })
                .bind("click",this,function(e){ e.data.root.filterFocus(e,this); })
                .val("");
                $(target).attr("nowrap","nowrap");
                break;
            };
            this.filterField.push(tmp[0]);
        },
        initPlugins: function(){

            var fp = this.pluginList;
            for (var i=0;i<fp.length;i++){
				$d.time("Plugin init: "+fp[i].name+" column "+fp[i].col.index);
                fp[i].preInit();
                fp[i].init();
                fp[i].postInit();
				$d.timeEnd("Plugin init: "+fp[i].name+" column "+fp[i].col.index);
            };
					
        },
        startPlugins: function(f,p,s){
            var fp = this.pluginList;
            for (var i=0;i<fp.length;i++){
                fp[i].start(f,p,s);
            };
        },
        processPlugins: function(r,v,p,a){
            var fp = this.pluginList;
            for (var i=0;i<fp.length;i++){
                fp[i].process(r,v,p,a);
            };
        },
        finishPlugins: function(){
            var fp = this.pluginList;
            for (var i=0;i<fp.length;i++){
                fp[i].finish();
            };
        }
    }
});
	
	 