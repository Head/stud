'use strict';

angular.module('myApp.view1', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/view1', {
    templateUrl: 'view1/view1.html',
    controller: 'View1Ctrl'
  });
}])

.controller('View1Ctrl', ['$scope', '$http', function($scope) {
        $scope.table = 'Process  Period T  Computation Time C\n'+
                        'a  25  3\n'+
                        'b  25  8\n'+
                        'c  50  5\n'+
                        'd  50  4\n'+
                        'e  100  2';
        $scope.threads = [];

        $scope.$watch('table', function(newValue, oldValue) {
            $scope.threads = $scope.calculate(newValue);
        });

        $scope.calculate = function(rawData) {
            var re = /([a-z]?)\s+([0-9]{1,3})\s+([0-9]{1,3})/gim;
            var str = rawData;
            var m;
            var data = [];

            while ((m = re.exec(str)) != null) {
                if (m.index === re.lastIndex) {
                    re.lastIndex++;
                }

                data.push({name: m[1], T: m[2], C:m[3], P:1});
                data.sort(function(a,b){return (a.T - b.T)});

                $scope.utilization = 0;
                var priority=1;
                $scope.LCM = Math.abs(data[0].T);
                data.forEach(function(entry) {
                    entry.P = priority;
                    priority++;

                    entry.utilization = entry.C/entry.T;
                    $scope.utilization += entry.utilization;

                    var b = Math.abs(entry.T), c = $scope.LCM;
                    while ($scope.LCM && b){ $scope.LCM > b ? $scope.LCM %= b : b %= $scope.LCM; }
                    $scope.LCM = Math.abs(c*entry.T)/($scope.LCM+b);
                });
                $scope.maxUtilization = data.length * (Math.pow(2, 1/data.length)-1)
            }

            return data;
        }

        $scope.isThreadRunning = function(thread, threadIndex, cyclus) {
            if(threadIndex < 0 ) return false; //Ready

            if(    (cyclus%thread.T)<=thread.T //new Period T
                && (cyclus%thread.T)+1<=thread.C //Computation C not elapsed
                && !$scope.isThreadRunning($scope.threads[threadIndex-1], threadIndex-1, cyclus) //higher prio Task not running
            ) return true;
            else return false;
        }

        $scope.getNumber = function(num) {
            return new Array(num);
        }
}]);


function LCM(A)  // A is an integer array (e.g. [-50,25,-45,-18,90,447])
{
    var n = A.length, a = Math.abs(A[0]);
    for (var i = 1; i < n; i++)
    { var b = Math.abs(A[i]), c = a;
        while (a && b){ a > b ? a %= b : b %= a; }
        a = Math.abs(c*A[i])/(a+b);
    }
    return a;
}