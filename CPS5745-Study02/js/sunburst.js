function sunburst(labels,duration=1000) {
    var instance = this,
        svg = null,
        timestamp = new Date().getTime(),
        widgetHeight = 600,
        widgetWidth = 400,
        widgetSize = 'large',
        margin = {top: 0, right: 0, bottom: 0, left: 10},
        width = widgetWidth - margin.left - margin.right,
        height = widgetHeight - margin.top - margin.bottom,
        radius = Math.min(width, height) / 2,
        x = d3.scale.linear().range([0, 2 * Math.PI]),
        y = d3.scale.pow().exponent(1),
        pgColor = d3.scale.ordinal().range([
            {"family": "Blue", 1: "#0000CC", 2: "#0099FF", 3: "#CCFFFF"},
            {"family": "Orange", 1: "#FF6600", 2: "#FFCC00", 3: "#FFFFCC"},
            {"family": "Green", 1: "#009900", 2: "#99CC33", 3: "#CCFF99"},
            {"family": "Red", 1: "#FF3333", 2: "#FF9999", 3: "#FFCCCC"},
            {"family": "Purple", 1: "#CC0099", 2: "#FF66CC", 3: "#FFCCFF"}]),
        luminance = d3.scale.sqrt()
            .domain([0, 1e6])
            .clamp(true)
            .range([90, 20]),
        i = 0,
        partition = d3.layout.partition().sort(function(a, b) { return d3.ascending(a.name || a[labels[labels.length - 1]], b.name || b[labels[labels.length - 1]])}),
        arc = d3.svg.arc()
            .startAngle(function(d) { return Math.max(0, Math.min(2 * Math.PI, x(d.x))); })
            .endAngle(function(d) { return Math.max(0, Math.min(2 * Math.PI, x(d.x + d.dx))); })
            .innerRadius(function(d) { return Math.max(0, d.y ? y(d.y) : d.y); })
            .outerRadius(function(d) { return Math.max(0, y(d.y + d.dy)); });

    function chart(selection) {
        selection.each(function(data) {
            instance.data = data;
            width = widgetWidth - margin.left - margin.right;
            height = widgetHeight - margin.top - margin.bottom;
            radius = Math.min(width, height) / 2;
            
            y.range([0, radius]);
            
            // Select the svg element, if it exists.
            svg = d3.select(this).selectAll("svg").data([data]);

            var gEnter = svg.enter()
                .append("svg")
                .attr("width", "100%")
                .attr("height", "100%")
                .append("g")
                .attr("class", "main-group");

            gEnter.append("defs")
                .append("clipPath")
                .attr("id", "clip-" + timestamp)
                .append("rect")
                .attr("x", 0)
                .attr("y", 0);

            var sunburstGroup = gEnter.append("g")
                .attr("class", "sunburst-area")
                .append("g")
                .attr("class", "sunburst-group");
                

            sunburstGroup.append("rect")
                .attr("class", "sunburst-background")
                .attr("x", 0)
                .attr("y", 0)
                .style("fill", "white");
            
            // Update the inner group dimensions.
            var g = svg.select("g.main-group")
                .attr("transform", "translate(" + (width / 2 + margin.left) + "," + (height / 2 + margin.top) + ")");

            g.select(".sunburst-background")
                .attr("width", width)
                .attr("height", height);
                
            partition.value(function(d) { return d.size; })
            .nodes(data)
            .forEach(function(d) {
                d.key = key(d);
            });
            
            var path = g.select(".sunburst-group").selectAll(".sunArcs")
                .data(partition.nodes(data), function(d) { return d.key; });
                
            path.enter().append("path")
                .attr("class", "sunArcs")
                .attr("d", arc)
                .style("fill", function(d) {
                    if(d.depth === 0) return "#fff";
                    var color = pgColor(d.key.split(".")[0]);
                    return color[d.depth];
                })
                .style("fill-opacity", 0)
                .on("click", click)
                .on("mouseover", mouseover)
                .on("mouseleave", mouseleave)
                .each(function(d) {
                    this.x0 = d.x;
                    this.dx0 = d.dx;
                });
                
            path.transition()
                .duration(duration)
                .style("fill-opacity", 1)
                .attrTween("d", arcTweenUpdate);

            path.exit()
                .transition()
                .duration(duration)
                .attrTween("d", arcTweenUpdate)
                .style("fill-opacity", 0)
                .remove();
                
            function key(d) {
                var k = [], p = d;
                while (p.depth) k.push(p.name || p[labels[labels.length - 1]]), p = p.parent;
                return k.reverse().join(".");
            }    

            function click(d) {
                path.transition()
                    .duration(duration)
                    .attrTween("d", arcTween(d));
            }
            
            function getParents(d) {
                var parents = [], p = d;
                while (p.depth >= 1) {
                    parents.push(p);
                    p = p.parent;
                }
                return parents;
            }
            
            function mouseover(d) {
                if(d.depth === 0) return;
                var parentNodes = getParents(d);
                 // Fade all the arcs.
                d3.selectAll(".sunArcs")
                .style("opacity", 0.3);
                
                // Highlight all arcs in path    
                d3.selectAll(".sunArcs").filter(function(d){
                    return (parentNodes.indexOf(d) >= 0);
                })
                .style("opacity", 1);
      
    
                // Initialize variables for tooltip
                var group = d.name || d[labels[labels.length - 1]],
                    valueFormat = d3.format(",.0f"),
                    textMargin = 5,
                    popupMargin = 10,
                    opacity = 1,
                    fill = d3.select(this).style("fill"),
                    hoveredPoint = d3.select(this),
                    pathEl = hoveredPoint.node(),
                    
                // Fade the popup stroke mixing the shape fill with 60% white
                    popupStrokeColor = d3.rgb(
                        d3.rgb(fill).r + 0.6 * (255 - d3.rgb(fill).r),
                        d3.rgb(fill).g + 0.6 * (255 - d3.rgb(fill).g),
                        d3.rgb(fill).b + 0.6 * (255 - d3.rgb(fill).b)
                    ),
                    
                // Fade the popup fill mixing the shape fill with 80% white
                    popupFillColor = d3.rgb(
                        d3.rgb(fill).r + 0.8 * (255 - d3.rgb(fill).r),
                        d3.rgb(fill).g + 0.8 * (255 - d3.rgb(fill).g),
                        d3.rgb(fill).b + 0.8 * (255 - d3.rgb(fill).b)
                    ),
                    
                // The running y value for the text elements
                    y = 0,
                // The maximum bounds of the text elements
                    w = 0,
                    h = 0,
                    t,
                    box,
                    rows = [], p = d,
                    overlap;
 
                var hoverGroup = d3.select(this.parentNode.parentNode.parentNode.parentNode).append("g").attr("class", "hoverGroup");
                    
                // Add a group for text   
                t = hoverGroup.append("g");
                // Create a box for the popup in the text group
                box = t.append("rect")
                    .attr("class", "tooltip");

               
                    if(!isInt(d.value)) {
                        valueFormat = d3.format(",.2f");
                    }
                
                
                while (p.depth >= 1) {
                    rows.push(labels[p.depth - 1] + ": " + (p.name || p[labels[labels.length - 1]]));
                    p = p.parent;
                }
                rows.reverse();
                rows.push(`${QUANT}: ` + valueFormat(d.value));
                    
                t.selectAll(".textHoverShapes").data(rows).enter()
                    .append("text")
                    .attr("class", "textHoverShapes")
                    .text(function (d) { return d; })
                    .style("font-size", 14);
                    
                // Get the max height and width of the text items
                t.each(function () {
                    w = (this.getBBox().width > w ? this.getBBox().width : w);
                    h = (this.getBBox().width > h ? this.getBBox().height : h);
                });
                
                // Position the text relatve to the bubble, the absolute positioning
                // will be done by translating the group
                t.selectAll("text")
                    .attr("x", 0)
                    .attr("y", function () {
                        // Increment the y position
                        y += this.getBBox().height;
                        // Position the text at the centre point
                        return y - (this.getBBox().height / 2);
                    });
                    
                // Draw the box with a margin around the text
                box.attr("x", -textMargin)
                    .attr("y", -textMargin)
                    .attr("height", Math.floor(y + textMargin) - 0.5)
                    .attr("width", w + 2 * textMargin)
                    .attr("rx", 5)
                    .attr("ry", 5)
                    .style("fill", popupFillColor)
                    .style("stroke", popupStrokeColor)
                    .style("stroke-width", 2)
                    .style("opacity", 0.95);
    
                // Move the tooltip box next to the line point
                t.attr("transform", "translate(" + margin.left + " , " + 10 + ")");
            }
            
            // Mouseleave Handler
            function mouseleave(d) {
                d3.selectAll(".sunArcs")
                .style("opacity", 1);
                d3.selectAll(".hoverGroup")
                .remove();
            }            
            
            // Interpolate the scales!
            function arcTween(d) {
                xd = d3.interpolate(x.domain(), [d.x, d.x + d.dx]),
                yd = d3.interpolate(y.domain(), [d.y, 1]),
                yr = d3.interpolate(y.range(), [d.y ? 20 : 0, radius]);
                return function(d, i) {
                    return i 
                    ? function(t) { return arc(d); }
                    : function(t) { x.domain(xd(t)); y.domain(yd(t)).range(yr(t)); return arc(d); };
                };
            }
            
            function arcTweenUpdate(a) {
                var updateArc = this;
                var i = d3.interpolate({x: updateArc.x0, dx: updateArc.dx0}, a);
                return function(t) {
                    var b = i(t);
                    updateArc.x0 = b.x;
                    updateArc.dx0 = b.dx;
                    return arc(i(t));
                };
            }            
        });
    }
  

    // Setter and getter methods
    chart.margin = function(_) {
        if (!arguments.length) return margin;
        margin = _;
        width = widgetWidth - margin.left - margin.right;
        height = widgetHeight - margin.top - margin.bottom;
        return chart;
    };

    chart.width = function(_) {
        if (!arguments.length) return widgetWidth;
        widgetWidth = _;
        width = widgetWidth - margin.left - margin.right;
        return chart;
    };

    chart.height = function(_) {
        if (!arguments.length) return widgetHeight;
        widgetHeight = _;
        height = widgetHeight - margin.top - margin.bottom;
        return chart;
    };

    chart.duration = function(_) {
        if (!arguments.length) return duration;
        duration = _;
        return chart;
    };

    chart.ease = function(_) {
        if (!arguments.length) return ease;
        ease = _;
        return chart;
    };

    chart.data = function(_) {
        if (!arguments.length) return data;
        data = _;
        return chart;
    };

    return chart;
}