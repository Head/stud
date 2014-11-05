'use strict';

angular.module('myApp.AAI', ['ngRoute'])

    .config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/AAI', {
            templateUrl: 'AAI/GTA.html',
            controller: 'GTACtrl'
        });
    }])

    .filter('replaceURI', function ($sce) {
        return function (text) {
            return text ? $sce.trustAsHtml(decodeURIComponent(text.replace(/http:\/\/dbpedia\.org\/resource\//g, ''))) : '';
        };
    })

    .controller('GTACtrl', ['$scope', '$http', function($scope, $http) {
        // rounds played
        $scope.round = 0;
        // total points of player
        $scope.points = 0;

        // fetch question to answer
        $scope.next = function() {
        var queryArtist = 'SELECT DISTINCT ?subject ?artist ?pic ?name ' +
            'WHERE { ?subject rdf:type yago:Painting103876519 . ' +
            '?subject dbpprop:artist ?artist . ' +
            '?subject foaf:depiction ?pic .' +
            '?artist rdfs:label ?name ' +
            '} ORDER BY ?pic OFFSET '+$scope.round+' LIMIT 1';

            $scope.round++;
            $scope.correct = false;
            $scope.answers = [];
            $scope.artist  = [];

            $scope.loading = true;
            $http.post('query.php', {query: queryArtist}).
                success(function (data, status, headers, config) {
                    $scope.artist = data[0];

                    // log current artist
                    console.debug(queryArtist)
                    // log current artist
                    //console.debug($scope.round)
                    // log current artist
                    //console.debug($scope.artist)
                    console.debug(config)

                    // fetch false answers
                    // FIXME: queryAnswers = 'SELECT DISTINCT ?artist SAMPLE(?name) ' + fails, empty result
                    var queryAnswers = 'SELECT DISTINCT ?artist ?name ' +
                        'WHERE { {' +
                        '<'+$scope.artist.artist + '> dbpedia-owl:birthPlace ?birthplace . ' +
                        '?artist dbpedia-owl:birthPlace ?birthplace . ' +
                        '?artist dbpedia-owl:movement ?movement . ' +
                        '?artist rdfs:label ?name ' +
                        'FILTER ( ?artist != <'+ $scope.artist.artist + '> ). ' +
                        '} UNION { ' +
                        '<'+$scope.artist.artist + '> dbpedia-owl:movement ?movement . ' +
                        '?artist dbpedia-owl:movement ?movement . ' +
                        '?artist rdfs:label ?name ' +
                        'FILTER ( ?artist != <'+ $scope.artist.artist + '> ). ' +
                        '} UNION { ' +
                        '<'+$scope.artist.artist + '> dbpprop:birthPlace ?birthplace . ' +
                        '?artist dbpprop:birthPlace ?birthplace . ' +
                        '?artist dbpedia-owl:movement ?movement . ' +
                        '?artist rdfs:label ?name ' +
                        '} } ';


                    console.debug(queryAnswers);

                    $http.post('query.php', {query: queryAnswers}).
                        success(function (data, status, headers, config) {
                            $scope.answers[0] = $scope.artist;
                            $scope.answers = $scope.answers.concat(scliceArray(data));
                            //console.debug(data)
                            //console.debug(data[0])
                            //console.debug(data[1])
                            //console.debug(data[2])
                            //console.debug(data[3])
                            shuffleArray($scope.answers);
                            $scope.loading = false;
                        });
                });
        }

        $scope.next();

        $scope.guess = function(answer) {
            if(answer.artist == $scope.artist.artist) {
                // inc points and show next option
                $scope.correct = true;
                $scope.points += 2;
            }else{
                // dec points and remove wrong answer
                $scope.points--;
                $scope.remove(answer);
            }
        }

        $scope.remove = function(answer) {
            // remove answer from scope
            var index = $scope.answers.indexOf(answer)
            $scope.answers.splice(index, 1);
        }
    }]);






var scliceArray = function(array) {
    var m = array.length, rarray, i;
    // define count of wrong artist
    var counter = 3;
    // result array
    var rarray = [];
    var usedi = [];

    // log number of input artist
    console.debug(m)

    // artist count too low return original
    if (m < 4) { return array; }

    while (counter) {
        // Pick a remaining element…
        i = Math.floor(Math.random() * m);

        if (usedi.indexOf(i) == -1) {
            // Push random artist on result array
            rarray.push(array[i]);
            usedi.push(i);
            counter--;
        }
    }
    return rarray;
}



var shuffleArray = function(array) {
    var m = array.length, t, i;

    // While there remain elements to shuffle
    while (m) {
        // Pick a remaining element…
        i = Math.floor(Math.random() * m--);

        // And swap it with the current element.
        t = array[m];
        array[m] = array[i];
        array[i] = t;
    }

    return array;
}
