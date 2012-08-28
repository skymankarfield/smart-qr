/*
 * Adds a new node with the specified
 * id: id of the node
 * parent: id of the parent
 * weight: the weight of the parent connection
 */
function addNewNode(id, parent, weight){
  if(weight == null && parent == null){
    if(lastQuestionId != null && typeof $("input[name=q]:checked").attr("id") != 'undefined'){
      lastAnswerId = $("input[name=q]:checked").attr("id").replace("n", "");
    }
  }
  if(typeof dbNodes[id] == 'undefined'){
    // This node has not been loaded from the db yet
    // Load and then cache it.
    $.get("/ajax/get_node?id=" + id, function(data) {
      dbNodes[data.node.id] = data;
      addNode(data.node.id, parent, weight, data);
    });
  }
  else{
    // This node has already been loaded from the db
    addNode(id, parent, weight, dbNodes[id]);
  }
}

/*
 * Adds a new node with the specified
 * id: id of the node
 * parent: id of the parent
 * weight: the weight of the parent connection
 * data: the database json object.
 */
function addNode(id, parent, weight, data){
  var alreadyExists = (typeof nodes["n" + id + "_l1"] != 'undefined');
  var divExists = ($("#n" + id + "_l1").length > 0);
  //Only create the node's html if it isn't already in there
  if(!alreadyExists && !divExists){
    var onClick = "";
    if(!journeyReplay){
      var onClick = "onclick='clickNode(id)'";
    }
    $("#graph").append(
    "<div class='window ui-draggable' " + onClick + " id='n" + data.node.id + "_l1'>" + 
      "<div class='window2' id='n" + data.node.id + "_l2'>" + 
        "<div class='window3' id='n" + data.node.id + "_l3'>" +
          "<strong>" + data.node.name.replace(/ /g, "&nbsp;") + "</strong>" +
        "</div>" +
      "</div>" +
    "</div>");
  }
  var val = document.getElementById("n" + data.node.id + "_l1");
  if(!alreadyExists){
    rand1 = Math.random();
    rand2 = Math.random();
    $(val).fadeIn(1000, function(){
      
    });
    //Randomly place the node
    document.getElementById($(val).attr("id")).style.left = Math.floor(rand1*(graphWidth - val.clientWidth*2) + 
        val.clientWidth) + "px";
    document.getElementById($(val).attr("id")).style.top = Math.floor(rand2*(graphHeight - val.clientHeight*2) + 
         val.clientHeight) + "px";
    
    nodes[$(val).attr("id")] = {"name" : data.node.name,
                                "desc" : data.node.description,
                                "media" : null,
                                "val" : val,
                                "left" : val.offsetLeft, 
                                "top" : val.offsetTop, 
                                "width" : val.clientWidth,
                                "height" : val.clientHeight,
                                "fontSize" : val.style.fontSize,
                                "clicked" : false};
  }
  if(weight == null){
    // The root node
    
    //Called when the ajax call for connected nodes is complete
    //Adds all of the connected nodes from the root
    function processRootConnectedNodes(data2){
      dbConnections[data.node.id] = data2;
      windows = $("div.window:not(#n" + data.node.id + "_l1)"); //Windows which should be removed
      for(nodeId in data2){
        edge = data2[nodeId].edge;
        if(data.node.id == edge.a){
          windows = windows.not("#n" + edge.b + "_l1");
          addNewNode(edge.b, data.node.id, edge.weight);
        }
        else{
          windows = windows.not("#n" + edge.a + "_l1");
          addNewNode(edge.a, data.node.id, edge.weight);
        }
      }
      /*
      //Remove nodes and connections which are not connected to the root
      $.each(windows, function(i, val){
        var winId = $(val).attr("id");
        var cons = getConnections(winId);
        for(conId in cons){
          $("#" + cons[conId].id + ":not(._jsPlumb_endpoint)").fadeOut(500,  function() {
            $(this).remove();
          });
          removeConnection(winId, conId);
        }
        jsPlumb.detachAll(winId);
        delete nodes[winId];
      });
      windows.fadeOut(500,  function() {
        
      });
      */
    }
    if(typeof dbConnections[data.node.id] == 'undefined'){
      $.get("/ajax/get_connected_nodes?node_id=" + data.node.id, processRootConnectedNodes);
    }
    else{
      processRootConnectedNodes(dbConnections[data.node.id]);
    }
  }
  else{
    // Secondary nodes
    
    //Called when the ajax call for connected nodes is complete
    //Adds connections between already existing nodes, but does not add any new nodes
    function processSecondaryConnectedNodes(data2){
      dbConnections[data.node.id] = data2;
      for(nodeId in data2){
        var edge = data2[nodeId].edge;
        var a = document.getElementById("n" + edge.a + "_l1");
        var b = document.getElementById("n" + edge.b + "_l1");
        if(a != null  && b != null && $(a).css("display") != "none" && $(b).css("display") != "none" && edge.a != parent && edge.b != parent){
          if(typeof connections["n" + edge.a + "_l1"]["n" + edge.b + "_l1"] == 'undefined' &&
             typeof connections["n" + edge.b + "_l1"]["n" + edge.a + "_l1"] == 'undefined'){
            addConnection("n" + edge.a + "_l1", "n" + edge.b + "_l1", "", edge.weight*1.5, "rgba(128,192,255,0.75)");
          }
          jsPlumb.repaint($(a));
          jsPlumb.repaint($(b));
        }
      }
    }
    if(typeof dbConnections[data.node.id] == 'undefined'){
      $.get("/ajax/get_connected_nodes?node_id=" + data.node.id, processSecondaryConnectedNodes);
    }
    else{
      processSecondaryConnectedNodes(dbConnections[data.node.id]);
    }
  }
  //Adding Connection to parent
  if(parent != null){
    if(typeof connections["n" + data.node.id + "_l1"] == 'undefined' || 
       typeof connections["n" + parent + "_l1"] == 'undefined' ||
       (typeof connections["n" + parent + "_l1"]["n" + data.node.id + "_l1"] == 'undefined' &&
        typeof connections["n" + data.node.id + "_l1"]["n" + parent + "_l1"] == 'undefined')){
      addConnection("n" + parent + "_l1", "n" + data.node.id + "_l1", "", weight*1.5, "rgba(128,192,255,0.75)");
    }
    if(clickedNode == null || clickedNode.id != "n" + parent + "_l1"){
      clickNode("n" + parent + "_l1");
    }
    if(getNumberOfConnections("n" + parent + "_l1") == dbConnections[parent].length &&
       !explanationUpdated){
      explanationUpdate = true;
      updateExplanation("n" + parent + "_l1");
    }
  }
  if(!alreadyExists && !divExists && !journeyReplay){
    initNodeEvents(data.node.id);
  }
}

/*
 * Initializes all of the events related to 
 * node(windows)
 */
function initNodeEvents(id){
  val = document.getElementById("n" + id + "_l1");
  //Called when mouse hovers over a node
  $(val).mouseenter(function() {
    toolTipType="node";
    //Shows the description of a node beside it
    function showNodeMessageBox(desc){
      nodes[nid].desc = desc;
      closeNow('div_help_box');
      
      toolTipType="node";
      openNodeToolTip(this, nodes[nid].desc, 'div_help_box', 
        document.getElementById(nid).style.top, 
        document.getElementById(nid).style.left,
        document.getElementById(nid).clientWidth);
        
      toolTipType="node";
    }
  
    nid= "n" + id + "_l1";
    //Loads message box when a node is hovered
    if(popupsEnabled && nodes[nid].desc == null){
      $.get("/ajax/get_node_description?name=" + nodes[nid].name, showNodeMessageBox);
    }
    //Change the css of the node to hovered
    hoverNode(nid);
    //Displays the popup
    if(nodes[nid].desc != null && popupsEnabled && ((clickedNode==null) ||(clickedNode.id != nid))){
      showNodeMessageBox(nodes[nid].desc);
    }
  });
  
  //Called when the mouse leaves a node
  //Unhovers the node, and closes any message box
  $(val).mouseleave(function() {
    nid= "n" + id + "_l1";
    unHoverNode(nid);
    closeNow('div_help_box');
  });
  
  $(val).draggable({
    //Called when an object starts to drag
    start: function(event, ui) {
      if(typeof nodes[event.target.id] == 'undefined'){
        // Return out of the function since the node most likeley non-existent
        // or is being faded out.
        return;
      }
      clickNode(event.target.id);
      draggedNode = event.target;
    }
  });
  
  $(val).bind("dragstop", function(event, ui) {
    clickNode(event.target.id);
  });
}

/*
 * Recomputes the partitions for node #id.
 * If id == "all", then all nodes' partitions are recomputed.
 * This should theoretically improve performance if there are lots of nodes.
 * 
 * NOTE: This is currently not being used; however, may be usefull in the future
 */
function partitionNodes(id, left, top, width, height){
  if(id == "all"){
    $.each($("div.window"), function(i, val){
      valId = $(val).attr("id");
      partitions[valId] = [];
      x0 = Math.floor(Math.max(0, (val.offsetLeft - val.clientWidth)/PARTITION_WIDTH));
      y0 = Math.floor(Math.max(0, (val.offsetTop - val.clientHeight)/PARTITION_HEIGHT));
      x1 = Math.floor(Math.min(graphWidth, (val.offsetLeft + val.clientWidth*2)/PARTITION_WIDTH));
      y1 = Math.floor(Math.min(graphHeight, (val.offsetTop + val.clientHeight*2)/PARTITION_HEIGHT));
      for(x = x0; x <= x1; x++){
        for(y = y0; y <= y1; y++){
          partitions[valId][x + "," + y] = true;
        }
      }
    });
  }
  else{
    var val = document.getElementById(id);
    partitions[id] = [];
    x0 = Math.floor(Math.max(0, (left - width)/PARTITION_WIDTH));
    y0 = Math.floor(Math.max(0, (top - height)/PARTITION_HEIGHT));
    x1 = Math.floor(Math.min(graphWidth, (left + width*2)/PARTITION_WIDTH));
    y1 = Math.floor(Math.min(graphHeight, (top + height*2)/PARTITION_HEIGHT));
    for(x = x0; x <= x1; x++){
      for(y = y0; y <= y1; y++){
        partitions[id][x + "," + y] = true;
      }
    }
  }
}

/*
 * Returns true if the coordinates (x,y) are inside of the partitions being occupied by node #id
 * If x or y are equal to -1, then it returns true(force an update, ie. unclicking a node)
 * 
 * NOTE: This is currently not being used; however, may be usefull in the future
 */
function inPartition(id, x, y){
  if(x == -1 || y == -1){
    return true;
  }
  x = Math.floor(x/PARTITION_WIDTH);
  y = Math.floor(y/PARTITION_HEIGHT);
  return (typeof partitions[id][x + "," + y] != 'undefined');
}

/*
 * Recomputes the node positions when the window is resized so no nodes get
 * stuck behind the explanation box
 */
function recomputeNodePositions(){
  graph = document.getElementById("graph");
  if(graph == null){
    return;
  }
  graphWidth = document.getElementById("graph").clientWidth;
  graphHeight = document.getElementById("graph").clientHeight;
  for(id in nodes){
    node = nodes[id];
    var left = node.val.offsetLeft;
    var top = node.val.offsetTop;
    var minWidth = Math.max(0, Math.min(left, graphWidth - 2 - node.val.clientWidth));
    var minHeight = Math.max(0, Math.min(top, graphHeight - 2 - node.val.clientHeight));
    if(minWidth != left || minHeight != top){
      node.left = minWidth;
      node.top = minHeight;
      
      node.val.style.left = minWidth + "px";
      node.val.style.top = minHeight + "px";
      
      jsPlumb.repaint($(node.val));
    }
  }
}

/*
 * Repels the nodes from each other.
 * If one node is overapping another, they will both move out of the way.
 */
function repelNodes(){
  stopWatches['REPEL'].start();
  var widths = [];
  var heights = [];
  var offsets = [];
  var redrawNodes = [];
  var ids = [];
  var draggedNodeId = $(draggedNode).attr("id");
  if(draggedNode != null){
    nodes[draggedNodeId].left = draggedNode.offsetLeft;
    nodes[draggedNodeId].top = draggedNode.offsetTop;
  }
  for(id in nodes){
    node = nodes[id];
    var width = node.width;document.getElementById("graph") != null
    var height = node.height;
    
    var centerX = node.left + width/2;
    var centerY = node.top + height/2;
    
    for(id1 in nodes){
      if(typeof nodes[id1] != 'undefined'){
        node1 = nodes[id1];
        if((id != id1 && typeof redrawNodes[id1] == 'undefined') && 
           (draggedNode == null || (draggedNode != null && draggedNodeId != id1))
          ){
          var width1 = node1.width;
          var height1 = node1.height;
          
          var centerX1 = node1.left + width1/2;
          var centerY1 = node1.top + height1/2;
          
          var dX = centerX - centerX1;
          var dY = centerY - centerY1;

          //Only move the node if it is actually in the way of another
          if(Math.abs(dX) <= (width + width1)/2 && Math.abs(dY) <= (height + height1)/2){
            var signX = (dX == 0) ? 0 : (dX > 0) ? 1 : -1;
            var signY = (dY == 0) ? 0 : (dY > 0) ? 1 : -1;
            
            var left = Math.round(centerX1 - width1/2 - Math.min(10, Math.abs((width + width1)/(dX + signX)))*signX);
            var top = Math.round(centerY1 - height1/2 - Math.min(10, Math.abs((height + height1)/(dY + signY)))*signY);
            node1.left = Math.max(0, Math.min(left, graphWidth - width1 - 2));
            node1.top = Math.max(0, Math.min(top, graphHeight - 3));
            node1.val.style.left = node1.left + "px";
            node1.val.style.top = node1.top + "px";
            redrawNodes[id1] = true;
            jsPlumb.repaint($(node1.val));
          }
        }
      }
    }
  }
  stopWatches['REPEL'].finish();
}

/*
 * Resizes nodes based on the mouse distance to the node
 */
function resizeNodes(mouseX, mouseY){
  if($("#colorbox").css("display") != "none"){
    return;
  }
  stopWatches['RESIZE'].start();
  mouseY = mouseY;
  var minFont = 0.91;
  var maxFont = 1.5;
  var xDistFactor = 0.25;
  var yDistFactor = 0;
  for(id in nodes){
    if(typeof nodes[id] != 'undefined'){
      node = nodes[id];
      var size = null;
      if(node.clicked){
        size = maxFont;
      }
      var width = node.width;
      var height = node.height;
      
      var centerX = Math.round(node.left) + width/2;
      var centerY = Math.round(node.top) + height/2;
      if(size == null){
        var dX = centerX - mouseX;
        var dY = centerY - mouseY;

        var thresholdDistance = 10/(Math.sqrt(Math.sqrt(Math.pow(dX, 2)/Math.pow(width, xDistFactor) + 
            Math.pow(dY, 2)/Math.pow(height, yDistFactor)))+1);
        size = Math.min(maxFont, Math.max(minFont, thresholdDistance));
      }

      if(Math.round(node.fontSize*50) != Math.round(size*50)){
        // Preliminary Check, if font hasn't changed hardly any, then don't continue
        node.fontSize = size;
        node.val.style.fontSize = size + "em";
        node.width = node.val.clientWidth;
        node.height = node.val.clientHeight;
        
        var newWidth = node.width;
        var newHeight = node.height;
        //Only redraw the node if the size has actually changed
        if(width != newWidth || height != newHeight){
          deltaWidth = newWidth - width;
          deltaHeight = newHeight - height;
          newX = Math.min(graphWidth - 2 - newWidth, Math.max(0, centerX - deltaWidth/2 - Math.floor(width/2)));
          newY = Math.max(0, Math.min(graphHeight - 2 - newHeight, centerY -deltaHeight/2 - Math.floor(height/2)));
          node.val.style.left = Math.floor(newX) + "px";
          node.val.style.top = Math.floor(newY) + "px";
          
          node.top = node.val.offsetTop;
          node.left = node.val.offsetLeft;
          if(hoveredNode != null && hoveredNode == id){
            popup = $("#div_help_box");
            $(popup).css("left", Math.floor(newX + newWidth + 10));
            $(popup).css("top", getNodeToolTipTop(Math.floor(newY)));
          }
          jsPlumb.repaint($(node.val));
        }
      }
    }
  }
  //Just in case, make sure the hovertext is in the right place
  if(toolTipType == "hovertext"){
    getToolTipLocation(mouse.x,mouse.y,0);
    
    popup = $("#div_help_box");
    $(popup).css("left", toolTipLoc.left);
    $(popup).css("top", toolTipLoc.top);
  }
  stopWatches['RESIZE'].finish();
}

/*
 * Changes the CSS so that when the mouse clicks a node
 * the previously clicked node is unclicked, and the new node
 * has a clicked style.
 */
function clickNode(id){
  playSound('click');
  explanationUpdated = false;
  var lastClickedNode = clickedNode;
  if(typeof nodes[id] == 'undefined'){
    // Return out of the function since the node most likeley non-existent
    // or is being faded out.
    return;
  }
  var l1 = document.getElementById(id);
  var l2 = document.getElementById(id.replace("l1", "l2"));
  var l3 = document.getElementById(id.replace("l1", "l3"));

  $(l1).addClass("window_click");
  $(l2).addClass("window2_click");
  $(l3).addClass("window3_click");
  if(draggedNode == null){
    updateExplanation(id);
  }
  if(clickedNode != null && id != clickedNode.id){
    nodes[id].clicked = true;
    /*
    if(journeyId != null && !journeyReplay){
      if(lastClickedNode != null && lastJourneyLegId != null){
        $.get("/ajax/update_leg?journey_leg_id=" + lastJourneyLegId + "&question_id=" + lastQuestionId + "&answer_id=" + lastAnswerId);
      }
      lastJourneyLegId = null;
      $.get("/ajax/add_leg?journey_id=" + journeyId + "&node_id=" + id.replace("n", "").replace("_l1", ""), function(data){
        lastJourneyLegId = data.journey_leg.id;
      });
      lastQuestionId = null;
      lastAnswerId = null;
    }
    */
    unClickNode();
  }
  else if(clickedNode == null){
    /*
    if(journeyId != null && !journeyReplay){
      if(lastClickedNode != null && lastJourneyLegId != null){
        $.get("/ajax/update_leg?journey_leg_id=" + lastJourneyLegId + "&question_id=" + lastQuestionId + "&answer_id=" + lastAnswerId);
      }
      lastJourneyLegId = null;
      $.get("/ajax/add_leg?journey_id=" + journeyId + "&node_id=" + id.replace("n", "").replace("_l1", ""), function(data){
        lastJourneyLegId = data.journey_leg.id;
      });
      lastQuestionId = null;
      lastAnswerId = null;
    }
    */
    nodes[id].clicked = true;
    resizeNodes(-1, -1);
  }
  else{
    nodes[id].clicked = true;
    resizeNodes(-1, -1);
  }
  
  if(lastClickedNode != null && id != lastClickedNode.id && draggedNode == null){
    clickedNode = document.getElementById(id);
    addNewNode(id.replace("n", "").replace("_l1", ""), null, null);
  }
  clickedNode = document.getElementById(id);
  draggedNode = null;
}

/*
 * Resets the style of the node to it's original form after a click.
 */
function unClickNode(){
  if(clickedNode != null){
    id = clickedNode.id;
    var l1 = clickedNode;
    var l2 = document.getElementById(id.replace("l1", "l2"));
    var l3 = document.getElementById(id.replace("l1", "l3"));
    
    $(l1).removeClass("window_click window_hover");
    $(l2).removeClass("window2_click window_hover");
    $(l3).removeClass("window3_click window_hover");
    nodes[id].clicked = false;
  }
  clickedNode = null;
  resizeNodes(-1, -1);
  jsPlumb.repaint($(l1));
}

/*
 * Changes the CSS so that when the mouse hovers over a node
 * there is a hover effect.
 */
function hoverNode(id){
  toolTipType="node";
  hoveredNode = id;
  if(draggedNode == null){
    playSound('hover');
  }
  var l1 = document.getElementById(id);
  var l3 = document.getElementById(id.replace("l1", "l3"));
  
  if(clickedNode != null && id == clickedNode.id){
    $(l1).addClass("window_click_hover");
    $(l3).addClass("window3_click_hover");
  }
  else{
    $(l1).addClass("window_hover");
    $(l3).addClass("window3_hover");
  }
}

/*
 * Resets the CSS so that when the mouse unhovers a node
 * the node goes back to its original style.
 */
function unHoverNode(id){
  hoveredNode = null;
  var l1 = document.getElementById(id);
  var l3 = document.getElementById(id.replace("l1", "l3"));
  
  if(clickedNode != null && id == clickedNode.id){
    $(l1).removeClass("window_click_hover");
    $(l3).removeClass("window3_click_hover");
  }
  else{
    $(l1).removeClass("window_hover");
    $(l3).removeClass("window3_hover");
  }
}
