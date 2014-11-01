'use strict';

angular.module('myApp.view2', ['ngRoute'])

    .config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/view2', {
            templateUrl: 'view1/view2.html',
            controller: 'View2Ctrl'
        });
    }])


    .controller('View2Ctrl', ['$scope', '$http', function($scope, $http) {

        $scope.result = '';

        $scope.myQuery = 'SELECT ?s ?p ?o\n'+
        'WHERE { ?s rdfs:label "Raphael"@en ;\n'+
        '?p ?o }';

        $scope.doQuery = function() {
            $http.post('query.php', {query:$scope.myQuery}).
                success(function(data, status, headers, config) {
                    $scope.result = data;
                })
        }
    }]);