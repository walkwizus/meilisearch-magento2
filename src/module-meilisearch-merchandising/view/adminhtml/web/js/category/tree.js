define([
    'jquery',
    'jsTree'
], function($) {
    'use strict';

    return function(config, element) {
        $(element).jstree({
            'plugins': ['wholerow'],
            'core': {
                'multiple': false,
                'data': config.categoryTree
            }
        }).on('select_node.jstree', function(node, selected) {
            let categoryId = selected.node.id;

            $.ajax(config.loadRuleUrl, {
                type: 'POST',
                data: {
                    form_key: window.FORM_KEY,
                    category_id: categoryId
                },
                dataType: 'json',
                showLoader: true,
                success: function(r) {
                    $('#category_id').val(categoryId);
                    if (r.length > 0) {
                        $('#query-builder').queryBuilder('setRules', $.parseJSON(r));
                    } else {
                        $('#query-builder').queryBuilder('setRules', [{empty: true}]);
                        $('#category-merchandising-preview').html('');
                    }
                }
            });
        });
    }
});
