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

        // fetch painting count
        $scope.paintingCounter = 0;
        $scope.paintingPool = [];
        $scope.paintingPoolIndex = 0;

        // first we count the present paintings
        var queryCountPaintings = 'SELECT (COUNT(?subject) AS ?count) { ?subject rdf:type yago:Painting103876519 }';
        $http.post('query.php', {query: queryCountPaintings}).
            success(function (data, status, headers, config) {
                // save the amount of paintings in the counter
                $scope.paintingCounter = parseInt(data[0].count);
                // create an array with |paintingPool|=paintingCounter and paintingPool[n]=n
                // later we will pick out randomly the offsets (uniform)
                for(var i=0;i<$scope.paintingCounter;i++){
                    $scope.paintingPool.push(i);
                }
                // start game with the first call of next()
                $scope.next();
            });


        // fetch list of possible artists and paintings
        $scope.next = function() {
            // fetch random ID out of the painting pool
            // the choosen id will set the painting by an offset (see query)
            $scope.paintingPoolIndex = Math.floor(Math.random() * $scope.paintingPool.length);
            console.debug("------------------------- NEXT PAINTING:");
            console.debug("Pool with "+$scope.paintingPool.length+ " Elements:")
            console.debug($scope.paintingPool);
            console.debug("Painting Pool Index: " + $scope.paintingPoolIndex);

            // query for selecting paintings randomly
            // with the query a stack of 50 paintings is fetched
            // if a painting throws later a picture-404 the next picture in the stack is selected
            var queryArtist = 'SELECT DISTINCT ?subject ?artist ?pic ?name ' +
                'WHERE { ?subject rdf:type yago:Painting103876519 . ' +
                '?subject dbpprop:artist ?artist . ' +
                '?subject foaf:depiction ?pic .' +
                '?artist rdfs:label ?name ' +
                '} ORDER BY ?pic OFFSET '+$scope.paintingPool[$scope.paintingPoolIndex]+' LIMIT 50';

            //console.debug(queryArtist);

            // remove used painting by the index
            // so the painting will not be fetched by a later followed next() call
            $scope.paintingPool.splice($scope.paintingPoolIndex,1);

            // round based resets
            $scope.round++;
            $scope.correct = false;
            $scope.answers = [];
            $scope.artist  = [];
            $scope.loading = true;
            $http.post('query.php', {query: queryArtist}).success(fetchRandomArtist);
        }



        function fetchRandomArtist(data, status, headers, config){
            // get data of the artist and painting
            $scope.artist = data[0];

            // check if picture is present
            while(!imageExists($scope.artist.pic)) {
                // if the image is not loading the picture will be removed from the stack
                // the next artist and painting gets loaded
                data.shift();
                $scope.artist = data[0];
                // the new painting must also be removed from the pool
                $scope.paintingPool.splice($scope.paintingPoolIndex,1);
                console.debug("no image 404 - choose next of 20 stack");
            }

            // Fetch false answers for the given artist and painting
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


            $http.post('query.php', {query: queryAnswers}).
                success(function (data, status, headers, config) {
                    $scope.answers[0] = $scope.artist;
                    $scope.answers = $scope.answers.concat(scliceArray(data));
                    shuffleArray($scope.answers);
                    $scope.loading = false;
                });
        }


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
    // Function for fetching max four wrong artists
    var m = array.length, rarray, i;
    // define count of wrong artist
    var counter = 3;
    // result array
    var rarray = [];
    var usedi = [];

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
    // Function shuffles an given array
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


function imageExists(image_url){
    // Function checks if picture is present by status code
    var http = new XMLHttpRequest();

    http.open('HEAD', image_url, false);
    http.send();

    return http.status != 404;
}
