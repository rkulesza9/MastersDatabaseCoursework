var controller = function(labels, data, progress, chart, selectedPath) {
    if(progress === 100) {
        selectedPath = $.extend(true, [], selectedPath);
        var flatData = [];
        data.map(function(d) {
            var item = {};
            for(var i = 0; i < labels.length; i++) {
                item[labels[i]] = d.group[i];
            }
            item.size = d3.selectAll("#controller").filter(function (d) { return this.checked; }).attr("value") === "count" ? d.current.count : d.current.metrics.price.sum;
            item.model = d;
            return flatData.push(item);
        });
        flatData.forEach(function(d) {
            d.model.group = d.model.group[d.model.group.length - 1];
        });

        var treeData = genJSON(flatData, labels.slice(0, labels.length - 1));
        d3.select("#vis")
            .datum(treeData)
            .call(chart);
    }

    return selectedPath;
};