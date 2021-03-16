
function genJSON(csvData, groups) {

    var genGroups = function(data) {
        return _.map(data, function(element, index) {
            return { name : index, children : element };
        });
    };

    var nest = function(node, curIndex) {
        if (curIndex === 0) {
            node.children = genGroups(_.groupBy(csvData, groups[0]));
            _.each(node.children, function (child) {
                nest(child, curIndex + 1);
            });
        }
        else {
            if (curIndex < groups.length) {
                node.children = genGroups(
                    _.groupBy(node.children, groups[curIndex])
                );
                _.each(node.children, function (child) {
                    nest(child, curIndex + 1);
                });
            }
        }
        return node;
    };
    return nest({}, 0);
}

function isInt(n) {
    return n % 1 === 0;
}