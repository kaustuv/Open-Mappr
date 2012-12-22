$(document).ready(function() {

    var nodes = {};
    var linkData;
    var nodeData;
    var isLinkWebOpen = false;

    function setUpData()
    {

      for (var i = 0; i < nodeData.length; i++) {
          var thisNode = nodeData[i];
          thisNode.incomingLinks = new Array();
          thisNode.outgoingLinks = new Array();
          nodes[thisNode.name] = thisNode;
      }

      // now set up the references
      for (var i = 0; i < linkData.length; i++) {
          var thisLink = linkData[i];

          var sourceNode = nodes[thisLink.source];
          thisLink.source = sourceNode;
          sourceNode.outgoingLinks.push(thisLink);

          var targetNode = nodes[thisLink.target];
          thisLink.target = targetNode;
          targetNode.incomingLinks.push(thisLink);
      }
      ;

      beginLinkViz();
    }

    function loadJSON()
    {
      var numDataLoaded = 0;
      $.getJSON(baseURL+'links/get_users_json_links',function(data) {
        numDataLoaded++;
        linkData = data;
        if(numDataLoaded == 2)
        {
          setUpData();
        }
      })
      $.getJSON(baseURL+'links/get_users_json_nodes',function(data) {
        numDataLoaded++;
        nodeData = data;
        if(numDataLoaded == 2)
        {
          setUpData();
        }
      })
    }

    function beginLinkViz()
    {
        //check to see that all nodes are linked at least once...
        var numNodes = 0;
        for (var key in nodes) {
            var thisNode = nodes[key];
            if (thisNode.incomingLinks.length == 0 && thisNode.outgoingLinks.length == 0) {
                delete nodes[key];

            }
            numNodes++;
        }

        var nodeRadius = 6;
        var width = 560;
        var height = 440;
        var charge = -300;
        var gravity = .1;
        var linkDistance = 80;

        var force = d3.layout.force()
                .nodes(d3.values(nodes))
                .links(linkData)
                .size([width, height])
                .linkDistance(linkDistance)
                .charge(charge)
                .gravity(gravity)
                .on("tick", tick)
                .start();

        var svg = d3.select("#link-viz").append("svg:svg")
                .attr("width", width)
                .attr("height", height)
                .attr("class", "svg default");


        var path = svg.append("svg:g").selectAll("path")
                .data(force.links())
                .enter().append("svg:path")
                .attr("class", function (d) {
                    return "link " + d.type;
                });


        var circle = svg.append("svg:g").selectAll("circle")
                .data(force.nodes())
                .enter().append("svg:circle")
                .attr("r", nodeRadius)
                .attr("class", function (d) {
                    return "circle " + d.type;
                })
                .call(force.drag);

        var text = svg.append("svg:g").selectAll("g")
                .data(force.nodes())
                .enter().append("svg:g");


        // Use elliptical arc path segments to doubly-encode directionality.
        function tick() {
            path.attr("d", function (d) {

                var dx = d.target.x - d.source.x;
                var dy = d.target.y - d.source.y;
                var directionFromSource = Math.atan2(dy, dx);
                var directionFromTarget = directionFromSource - Math.PI;

                //find the path info

                var halfThickness = .4;
                var arcRadius = Math.sqrt(dx * dx + dy * dy);
                var startPathX = d.source.x + nodeRadius * Math.cos(directionFromSource - halfThickness);
                var startPathY = d.source.y + nodeRadius * Math.sin(directionFromSource - halfThickness);
                var endPathX = d.source.x + nodeRadius * Math.cos(directionFromSource);
                var endPathY = d.source.y + nodeRadius * Math.sin(directionFromSource);

                var midpathX = d.target.x + nodeRadius * Math.cos(directionFromTarget + halfThickness);
                var midpathY = d.target.y + nodeRadius * Math.sin(directionFromTarget + halfThickness);

                //build the path cmd use the arc thing

                var pathString = "";
                pathString += "M " + startPathX + " " + startPathY;
                pathString += " A " + arcRadius + " " + arcRadius + " 0 0 1 " + midpathX + " " + midpathY;
                pathString += " A " + arcRadius + " " + arcRadius + " 0 0 0 " + endPathX + " " + endPathY;
                pathString += " Z ";


                return pathString;


            });

            circle.attr("cx", function(d) { return d.x = Math.max(nodeRadius, Math.min(width - nodeRadius, d.x)); })
                    .attr("cy", function(d) { return d.y = Math.max(nodeRadius, Math.min(height - nodeRadius, d.y)); });

        } 
    }


    $('.link-web-holder .link-web-but').click(function(e) {
      e.stopPropagation();
      if(isLinkWebOpen)
      {
        isLinkWebOpen = false;
        $(this).parent().animate({
          'left':$(this).parent().width()*-1-1
        })
      } else
      {
        //remove currnet link web
        $('#link-viz svg').remove();
        isLinkWebOpen = true;
        $(this).parent().animate({
          'left':0
        },function() {
          loadJSON();
        }) 
      }
    })

    $('body').click(function() {
      if(isLinkWebOpen)
      {
        $('.link-web-holder .link-web-but').trigger('click');
      }
    })

})