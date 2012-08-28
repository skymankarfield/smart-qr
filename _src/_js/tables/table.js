var NORMAL = 0;
var LONG = 1;
var SMALL = 2;

var tables = Array();

/*
 * Collapsible Table
 * This Table will sortable and be able to have different column types, specified by a schema
 */
function Table(id, schema){
    tables[tables.length] = this;
	this.html = "";
	this.id = id;
	this.schema = schema;
	this.rows = Array();
	
	/*
	 * Initializes a collapsable sortable table given by the id, and the table schema
	 * Schema is an array of column types, with possible string values as:
	 * 		NORMAL
	 *		LONG
	 */
	this.initTable = function(id, schema){
		this.html = "<table cellspacing='1' cellpadding='0' id='" + id + "' class='table sortable'>\n" +
					"	<thead><tr class='table'>\n";
		for(var i = 0; i < schema.length; i++){
		    if(schema[i].type == NORMAL){
			    this.html += "		<th onselect='return false' onmousedown='return false' class='table'>" + schema[i].title + "<span class='sorttable_start'><img src='http://" + document.domain + "/_src/_img/updown.gif' /></span></th>\n";
			}
			else if(schema[i].type == SMALL){
			    this.html += "		<th width='1' onselect='return false' onmousedown='return false' class='table'>" + schema[i].title + "<span class='sorttable_start'><img src='http://" + document.domain + "/_src/_img/updown.gif' /></span></th>\n";
			}
		}
		this.html += "	</tr></thead>\n" +
		             "  <tbody>\n";
	}

	/*
	 * Adds a row to the Table from the
	 */
	this.addRow = function(row){
		this.rows[this.rows.length] = row;
	};
	
	this.printTableIn = function(divId){
		for(var i = 0; i < this.rows.length; i++){
			this.html += "	<tr class='table'>\n";
			for(var j = 0; j < schema.length; j++){
				this.html += "		<td onselect='return false' onmousedown='return false' class='table'>" + this.rows[i][j] + "</td>\n";
			}
			this.html += "	</tr>\n";
		}
		
		this.html += "</tbody></table>";
		$("#" + divId).html(this.html);
	};
	
	//Run once the object is created
	this.initTable(this.id, this.schema);
};

/*
 * Instantiates all of the tables in the dom
 */
$(document).ready(function() 
{
    for(var i = 0; i < tables.length; i++){
        tables[i].printTableIn("table");
    }
    if(i == 0){
        $("#table").html("There have been no scans");
    }
});
