define([
    'ko',
    'jquery'
], function(ko) {
    'use strict';

    return {
        currentCategoryId: ko.observable(null),
        currentRule: ko.observable(null),
        preview: ko.observableArray(),
        message: ko.observable(null)
    };
});
