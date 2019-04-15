'use strict';
var resourceAvailabilityApp = angular.module('resourceAvailabilityApp', []);
resourceAvailabilityApp.controller('rsrcAvailCtrl', function ($scope, $http) {
    
    // Load data from server
    $http.get(HTTP_ROOT+'Timelog/LogTimes/ajax_resource_availability').success(function (response) {
        $scope.data = response.data;
        $scope.dates = response.dates;
    });
    
});
