'use strict';

angular.module('myApp.RTS', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/RTS', {
    templateUrl: 'RTS/RMS.html',
    controller: 'RMSCtrl'
  });
}])

.controller('RMSCtrl', ['$scope', '$http', function($scope) {
        $scope.table = 'Process  Period T  Computation Time C\n'+
                        'a  25  3\n'+
                        'b  25  8\n'+
                        'c  50  5\n'+
                        'd  50  4\n'+
                        'e  100  2';
        $scope.threads = [];
        $scope.running = {};

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

                data.push({name: m[1], T: m[2], C:m[3], P:1, suspended:0});
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
            var blocked = $scope.isThreadBlocked($scope.threads[threadIndex-1], threadIndex-1, cyclus);
            var self = (cyclus%thread.T)<=thread.T //new Period T
                    && (cyclus%thread.T)+1<=thread.C; //Computation C not elapsed
            if(self && blocked) {
                thread.suspended++;
            }else if(!self && !blocked && thread.suspended>0){
                thread.suspended--;
                self = true;
            }
            if(self && !blocked) {
                $scope.running[threadIndex] = $scope.running[threadIndex] || [];
                $scope.running[threadIndex][cyclus] = true;
                return true;
            }
            else{
                $scope.running[threadIndex] = $scope.running[threadIndex] || [];
                $scope.running[threadIndex][cyclus] = false;
                return false;
            }
        }

        $scope.isThreadBlocked = function(thread, threadIndex, cyclus) {
            if(threadIndex < 0 ) return false; //Ready
            var parent = $scope.isThreadBlocked($scope.threads[threadIndex-1], threadIndex-1, cyclus); //higher prio Task not running

            if($scope.running[threadIndex][cyclus]) return true || parent;
            else return false || parent;
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