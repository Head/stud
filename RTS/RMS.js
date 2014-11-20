'use strict';

angular.module('myApp.RTS', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/RTS', {
    templateUrl: 'RTS/RMS.html',
    controller: 'RMSCtrl'
  });
}])

.controller('RMSCtrl', ['$scope', '$http', function($scope) {
        $scope.table = 'Process Period T Computation Time C Deadline D \n'+
                        'a 3 1 3\n'+
                        'b 6 1 6\n'+
                        'c 5 1 5\n'+
                        'd 10 2 10 ';

        $scope.$watch('table', function(newValue, oldValue) {
            var threads = $scope.getThreadsFromRaw(newValue);

            $scope.threadsRMS   = $scope.calculate(threads, 'RMS');
            $scope.tableRMS     = $scope.calculateTable($scope.threadsRMS);

            $scope.threadsDMS   = $scope.calculate(threads, 'DMS');
            $scope.tableDMS     = $scope.calculateTable($scope.threadsDMS);

            $scope.threadsRTA   = $scope.calculate(threads, 'RTA');
            $scope.tableRTA     = $scope.calculateTable($scope.threadsRTA);
        });

        $scope.calculateSimpleRTA = function(threads) {
            $scope.debugSimpleRTA = '';
            var responseTime = [];

            var threadIndex = 0;
            threads.forEach(function(thread) {
                $scope.debugSimpleRTA += "\nR("+thread.name+") = "+ thread.C+ (threadIndex>0?" + ":"");
                var sum =0;
                for(var j=threadIndex; j>0 ;j--) {
                    $scope.debugSimpleRTA += "ceil( D("+ thread.name+ ")/T("+ threads[j-1].name+ ") ) * C("+ threads[j-1].name+")";
                    if(j>1) $scope.debugSimpleRTA += " + ";
                }

                $scope.debugSimpleRTA += "\nR("+thread.name+") = "+ thread.C+ (threadIndex>0?" + ":"");
                for(var j=threadIndex; j>0 ;j--) {
                    sum += Math.ceil(thread.D / threads[j-1].T) * threads[j-1].C;
                    $scope.debugSimpleRTA += "\n\t\tceil("+ thread.D+ "/"+ threads[j-1].T+ ") * "+ threads[j-1].C+ "\t= "+ Math.ceil(thread.D / threads[j-1].T) * threads[j-1].C;
                    if(j>1) $scope.debugSimpleRTA += " + ";
                }

                thread.simpleRTA = responseTime[threadIndex] = thread.C + sum;
                $scope.debugSimpleRTA += "\n\t\t\t\t ---";
                $scope.debugSimpleRTA += "\n\t\t\t\t= "+ responseTime[threadIndex];
                $scope.debugSimpleRTA += "\n";

                threadIndex++;
            });

            return responseTime;
        }

        $scope.calculateComplexRTA = function(threads) {
            $scope.debugComplexRTA = '';
            var responseTime = [];

            var threadIndex = 0;
            threads.forEach(function(thread) {
                var old = 0;
                var i = 0;

                responseTime[threadIndex] = thread.C;
                do {
                    old = responseTime[threadIndex];
                    $scope.debugComplexRTA += "\nR(" + thread.name + ") = " + thread.C + (threadIndex > 0 ? " + " : "");
                    var sum = 0;
                    for (var j = threadIndex; j > 0; j--) {
                        $scope.debugComplexRTA += "ceil( R(" + thread.name + ")/T(" + threads[j - 1].name + ") ) * C(" + threads[j - 1].name + ")";
                        if (j > 1) $scope.debugComplexRTA += " + ";
                    }

                    $scope.debugComplexRTA += "\nR(" + thread.name + ") = " + thread.C + (threadIndex > 0 ? " + " : "");
                    for (var j = threadIndex; j > 0; j--) {
                        sum += Math.ceil(responseTime[threadIndex] / threads[j - 1].T) * threads[j - 1].C;
                        $scope.debugComplexRTA += "\n\t\tceil(" + responseTime[threadIndex] + "/" + threads[j - 1].T + ") * " + threads[j - 1].C + "\t= " + Math.ceil(thread.D / threads[j - 1].T) * threads[j - 1].C;
                        if (j > 1) $scope.debugComplexRTA += " + ";
                    }

                    thread.complexRTA = responseTime[threadIndex] = thread.C + sum;
                    $scope.debugComplexRTA += "\n\t\t\t\t ---";
                    $scope.debugComplexRTA += "\n\t\t\t\t= " + responseTime[threadIndex];
                    $scope.debugComplexRTA += "\n";
                    i++;
                }while(old!=responseTime[threadIndex] && i < $scope.LCM);

                threadIndex++;
            });

            return responseTime;
        }

        $scope.isFeasible = function(thread) {
            console.log('isFeasible', thread.complexRTA, thread.D);
            if(thread.complexRTA>thread.D) return false;
            else return true;
        }

        $scope.optimalPriorityAssignmen = function(threads) {
            var N = threads.length;
            for(var K=1; K<N; N++) {
                for(var Next=K; Next<N; Next++) {
                    console.log("swap", Next, K);
                    var tmp = threads[K];
                    threads[K] = threads[Next];
                    threads[Next] = tmp;

                    var ok = $scope.isFeasible(threads[K]);
                    console.log('isFeasible', K, threads[K]);
                    if(ok) break;

                }
                if(!ok) console.error('no RTA found');
                return false;
            }

        }

        $scope.getThreadsFromRaw = function(rawData) {
            var re = /([a-z]?)\s+([0-9]{1,3})\s+([0-9]{1,3})\s?([0-9]{1,3})?/gim;
            var str = rawData;
            var m;
            var data = [];

            while ((m = re.exec(str)) != null) {
                if (m.index === re.lastIndex) {
                    re.lastIndex++;
                }

                var D = m[4] || m[2];
                var priority = 1;
                data.push({
                    name: m[1],
                    T: parseInt(m[2]),
                    C: parseInt(m[3]),
                    D: parseInt(D),
                    P: priority++,
                    suspended: 0,
                    runcount: 0,
                    cycluscount: 0
                });
            }
            return data;
        }
        $scope.calculate = function(data, scheduler) {
            if(scheduler=='DMS') {
                data.sort(function (a, b) {
                    return (a.D - b.D)
                });
            }else if(scheduler=='RTA') {
                $scope.calculateSimpleRTA(data);
                $scope.calculateComplexRTA(data);

                $scope.optimalPriorityAssignmen(data);
                data.sort(function (a, b) {
                    return (a.P - b.P)
                });
            }else{
                data.sort(function (a, b) {
                    return (a.T - b.T)
                });
            }

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

            return data.slice(); //return a copy!
        }

        $scope.calculateTable = function(threads) {
            var table = [];
            var threadIndex = 0;
            threads.forEach(function(thread) {
                for(var cyclus=0;cyclus<$scope.LCM;cyclus++) {
                    table[threadIndex] = table[threadIndex] || [];

                    var blocked = $scope.isThreadBlocked(threads, table, threadIndex-1, cyclus);
                    var self = (cyclus%thread.T)<=thread.T //new Period T
                        && (cyclus%thread.T)+1<=thread.C; //Computation C not elapsed

                    if(self && blocked) {
                        thread.suspended++;
                    }else if(!self && !blocked && thread.suspended>0){
                        thread.suspended--;
                        self = true;
                    }
                    if(self && !blocked) {
                        if(thread.C == 1) {
                            table[threadIndex][cyclus] = {running: true, symbol: '<>', class:'startend'};
                            thread.cycluscount++;
                        }else if (thread.runcount == 0) {
                            table[threadIndex][cyclus] = {running: true, symbol: '<', class:'start'};
                            thread.runcount++;
                            thread.cycluscount++;
                        } else if (thread.runcount == thread.C-1) {
                            table[threadIndex][cyclus] = {running: true, symbol: '>', class:'end'};
                            thread.runcount = 0;
                        } else {
                            table[threadIndex][cyclus] = {running: true, symbol: thread.runcount+1, class:'running'};
                            thread.runcount++;
                        }

                        //var cyclusCount = Math.ceil(cyclus/thread.D);

                        if(cyclus>=thread.cycluscount*(thread.D)) {
                            table[threadIndex][cyclus].class = table[threadIndex][cyclus].class+' deadline';
                        }
                    }
                    else{
                        table[threadIndex][cyclus] = {running: false, symbol: '', class:''};
                    }
                };
                threadIndex++;
            });

            return table;
        }

        $scope.isThreadRunning = function(running, thread, threadIndex, cyclus) {
            return running[threadIndex][cyclus];
        }

        $scope.isThreadBlocked = function(threads, running, threadIndex, cyclus) {
            if(threadIndex < 0 ) return false; //Ready
            var parent = $scope.isThreadBlocked(threads, running, threadIndex-1, cyclus); //higher prio Task not running

            if(running[threadIndex][cyclus].running) return true || parent;
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