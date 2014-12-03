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
        $scope.points = 50;

        // fetch painting count
        $scope.paintingCounter = 0;
        $scope.paintingPool = [];
        $scope.paintingPoolIndex = 0;

        $scope.disabled_answers = [];
        $scope.hiddenTips = [];
        $scope.shownTips = [];

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
            $scope.message = "";

			//reset Search joker
			$scope.showSearch = $scope.SearchLeft ;
			$scope.searchResult = "";

            //restet tip joker
            //$scope.showtipps = false;
            $scope.tips = "";

            // fetch random ID out of the painting pool
            // the choosen id will set the painting by an offset (see query)
            $scope.paintingPoolIndex = Math.floor(Math.random() * $scope.paintingPool.length);
            //console.debug("------------------------- NEXT PAINTING:");
            //console.debug("Pool with "+$scope.paintingPool.length+ " Elements:")
            //console.debug($scope.paintingPool);
            //console.debug("Painting Pool Index: " + $scope.paintingPoolIndex);

            // query for selecting paintings randomly
            // with the query a stack of 50 paintings is fetched
            // if a painting throws later a picture-404 the next picture in the stack is selected
            var queryArtist = 'SELECT DISTINCT ?subject ?artist ?pic ?name ?tip ' +
                'WHERE { ?subject rdf:type yago:Painting103876519 . ' +
                '?subject dbpprop:artist ?artist . ' +
                '?subject foaf:depiction ?pic .' +
                '?artist rdfs:label ?name .' +
                '?pic dbpedia:isTipOf ?tip' +
                '} ORDER BY ?pic OFFSET '+$scope.paintingPool[$scope.paintingPoolIndex]+' LIMIT 50';

            console.debug(queryArtist);

            // remove used painting by the index
            // so the painting will not be fetched by a later followed next() call
            $scope.paintingPool.splice($scope.paintingPoolIndex,1);

            // round based resets
            $scope.round++;
            $scope.correct = false;
            $scope.answers = [];
            $scope.artist  = [];
            $scope.disabled_answers = [];
            $scope.hiddenTips = [];
            $scope.shownTips = [];
            //$scope.loading = true;
            $scope.artist.pic = "https://d13yacurqjgara.cloudfront.net/users/121337/screenshots/916951/small-load.gif";
            $http.post('query.php', {query: queryArtist}).success(fetchRandomArtist);
        }



        function fetchRandomArtist(data, status, headers, config){
            // get data of the artist and painting
            $scope.artist = data[0];

            console.debug("ARTIST PIC");
            console.debug(data);

            // TODO: sep func
            $scope.artist.oripic = $scope.artist.pic;
            var ext = $scope.artist.pic.substr($scope.artist.pic.lastIndexOf('.') + 1);
            $scope.artist.pic = "AAI/imgs/"+$scope.artist.pic.replace(ext, '').replace(/[^\w\s]/gi, '').replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '')+"_small."+ext;


            // check if picture is present
            while(!imageExists($scope.artist.pic)) {
                // if the image is not loading the picture will be removed from the stack
                // the next artist and painting gets loaded
                data.shift();
                $scope.artist = data[0];
                // the new painting must also be removed from the pool
                $scope.paintingPool.splice($scope.paintingPoolIndex,1);
                console.debug("no image 404 - choose next of 20 stack");

                // TODO: sep func
                $scope.artist.oripic = $scope.artist.pic;
                var ext = $scope.artist.pic.substr($scope.artist.pic.lastIndexOf('.') + 1);
                $scope.artist.pic = "AAI/imgs/"+$scope.artist.pic.replace(ext, '').replace(/[^\w\s]/gi, '').replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '')+"_small."+ext;
            }

            // Fetch false answers for the given artist and painting
            // FIXME: queryAnswers = 'SELECT DISTINCT ?artist SAMPLE(?name) ' + fails, empty result
            var queryAnswers = 'SELECT DISTINCT ?artist ?name ' +
                'WHERE { {' +
                '<'+$scope.artist.artist + '> dbpedia-owl:birthPlace ?birthplace . ' +
                '?artist dbpedia-owl:birthPlace ?birthplace . ' +
                //'?artist dbpprop:artist ?subject . ' +
                //'?subject rdf:type yago:Painting103876519 . ' +
                '?artist rdfs:label ?name ' +
                'FILTER ( ?artist != <'+ $scope.artist.artist + '> ). ' +
                '} UNION { ' +
                '<'+$scope.artist.artist + '> dbpedia-owl:movement ?movement . ' +
                '?artist dbpedia-owl:movement ?movement . ' +
                //'?artist dbpprop:artist ?subject . ' +
                //'?subject rdf:type yago:Painting103876519 . ' +
                '?artist rdfs:label ?name ' +
                'FILTER ( ?artist != <'+ $scope.artist.artist + '> ). ' +
                '} UNION { ' +
                '<'+$scope.artist.artist + '> dbpedia-owl:influencedBy ?artist .' +
                //'?artist dbpprop:artist ?subject . ' +
                //'?subject rdf:type yago:Painting103876519 . ' +
                '?artist rdfs:label ?name ' +
                'FILTER ( ?artist != <'+ $scope.artist.artist + '> ). ' +
				'} UNION { ' +
                '?painting rdf:type yago:Painting103876519 .' +
                '?painting dbpprop:artist ?artist .' +
				'?artist rdfs:label ?name ' +
                'FILTER ( ?artist != <'+ $scope.artist.artist + '> ). ' +
                '} } ';

            $http.post('query.php', {query: queryAnswers}).
                success(function (data, status, headers, config) {
                    $scope.answers[0] = $scope.artist;
                    $scope.answers = $scope.answers.concat(scliceArray(data));

                    shuffleArray($scope.answers);

                    // TODO: pruefen ob bestenfalls direk im query auslagern
                    if ($scope.answers.length != 4) {
                        console.debug("Not able to found 4 answers; choose next;");
                        $scope.next();
                        return;
                    }
                    //$scope.loading = false;
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
                disableAnswer(answer);
            }
        }

        $scope.joker5050 = function() {
            var JOKER_5050_POINTS = 2
            if($scope.points >= JOKER_5050_POINTS){
                if ($scope.disabled_answers.length < 2){

                    $scope.points -= JOKER_5050_POINTS;

                     var i;
                     var max = 4;
                     var prev = -1;

                     while(max > 2){
                        i = Math.floor( Math.random() * max);

                        // Exclude already guessed answers
                        var isAnswerAlreadyDisabled = ($scope.disabled_answers.indexOf($scope.answers[i])  != -1);
                        if (isAnswerAlreadyDisabled) {
                            continue;
                        }

                        // Exclude first 50-50 answer
                        if(prev == i) {
                            continue;
                        }


                        if($scope.answers[i].artist != $scope.artist.artist ){
                            disableAnswer($scope.answers[i]);
                            prev = i;
                            max--;
                        }
                    }
                }else{
                    $scope.message = "You already used this Joker, no cheating!";
                }
            }
            else {
                $scope.message = "You have only " + $scope.points + " point(s) left. You need at least " + JOKER_5050_POINTS + " points for this Joker!";
            }
        }

        $scope.isAnswerEnabled = function (answer) {
            if ($scope.disabled_answers.indexOf(answer) == -1) {
                return true;
            }
            return false;
        }

        function disableAnswer(answer) {
            $scope.disabled_answers.push(answer);
        }

        $scope.jokerArtistsPics = function() {
            var JOKER_ARTIST_POINTS = 2;
            if($scope.points >= JOKER_ARTIST_POINTS){
                $scope.points -= JOKER_ARTIST_POINTS;

                // TODO: eine Antwort könnte schon vorzeitig gewählt worden sein -> keine Answer im Scope
                $scope.answers[0].url = "http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif";
                $scope.answers[1].url = "http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif";
                $scope.answers[2].url = "http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif";
                $scope.answers[3].url = "http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif";

                // zeigt fuer die vier moeglichen Kuenstler jeweils ein Bild als zusaetzlichen Hinweis an
                console.debug($scope.answers[0]);
                console.debug($scope.answers[1]);
                console.debug($scope.answers[2]);
                console.debug($scope.answers[3]);

                // TODO:
                // Bild prüfen
                //      -> Bilder 404 image exit um timeout erweitern
                // Punktesystem anpassen

                // TODO: eine Antwort könnte schon vorzeitig gewählt worden sein -> keine Answer im Scope
                selectJokerPics(0);
                selectJokerPics(1);
                selectJokerPics(2);
                selectJokerPics(3);
            }
            else {
                $scope.message = "You have only " + $scope.points + " point(s) left. You need at least " + JOKER_ARTIST_POINTS + " points for this Joker!";
            }
        }

		var selectJokerPics = function(i) {

			var queryArtistPic;

			console.debug($scope.answers[i].artist);

			queryArtistPic = 'SELECT DISTINCT ?pic ' +
				'WHERE { ?subject rdf:type yago:Painting103876519 . ' +
				'?subject dbpprop:artist <'+$scope.answers[i].artist+'> . ' +
				'?subject foaf:depiction ?pic .' +
				'} ORDER BY ?pic ';
			$http.post('query.php', {query: queryArtistPic}).
				success(function (data, status, headers, config) {
					// shuffle array für random Bild
					data = shuffleArray(data);
					var img = 0;
					var ok = true;

					console.debug(data.length);

                    // TODO: sep func
                    var ext = data[img].pic.substr(data[img].pic.lastIndexOf('.') + 1);
                    data[img].pic = "AAI/imgs/"+data[img].pic.replace(ext, '').replace(/[^\w\s]/gi, '').replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '')+"_small."+ext;
                    console.debug(data[img].pic);

					if(data.length > 0){

						// Schauen das das Bild erreichbar ist, wenn nicht nächstes
						while(!imageExists(data[img].pic)){
                            console.debug(data[img].pic);
							img++;
							if(img >= data.length){
								ok = false;
								break;
							}

                            // TODO: sep func
                            var ext = data[img].pic.substr(data[img].pic.lastIndexOf('.') + 1);
                            data[img].pic = "AAI/imgs/"+data[img].pic.replace(ext, '').replace(/[^\w\s]/gi, '').replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '')+"_small."+ext;
						}

						// Für den Fall das das ausgewählte Bild das gerade dargestellte vom Künstler ist, nächstes Bild suchen
						if($scope.answers[i].artist == $scope.artist.artist && data[img].pic == $scope.artist.pic){
							img++;
							if(img < data.length){
								while(!imageExists(data[img].pic)){
									img++;
									if(img >= data.length){
										ok = false;
										break;
									}

                                    // TODO: sep func
                                    var ext = data[img].pic.substr(data[img].pic.lastIndexOf('.') + 1);
                                    data[img].pic = "AAI/imgs/"+data[img].pic.replace(ext, '').replace(/[^\w\s]/gi, '').replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '')+"_small."+ext;

								}
							}else{
								ok = false;
							}
						}
					}else{
						ok = false;
					}

					// Bild anzeigen
					if(ok){
                        $scope.answers[i].url = data[img].pic;
					}else{
						$scope.answers[i].url = "http://i58.tinypic.com/4tlwlc.png";
					}
					console.debug($scope.answers[i].url);
			});
		}

        $scope.jokerSearch = function() {
            var JOKER_SEARCH_POINTS = 2;
			if($scope.showSearch != true){
				if($scope.points >= 2){
						$scope.points -= 2;

					$scope.SearchLeft = true;
					$scope.showSearch = true;
					$scope.searchPhrase = "";	//clear searchfield

				}
				else {
                    $scope.message = "You have only " + $scope.points + " point(s) left. You need at least " + JOKER_SEARCH_POINTS + " points for this Joker!";
				}
			}
        }


        $scope.jokerTextTip = function() {
            var JOKER_ARTIST_POINTS = 2;

            if($scope.points >= JOKER_ARTIST_POINTS){
                //Reset points

                $scope.showtipps = true;

                //if first call, get the tips
                if($scope.shownTips.length < 1){
                    var queryArtistTips;

                    console.debug("anz Tips " + $scope.hiddenTips.length);

                    var queryArtistTips = 'SELECT ?tip WHERE { <'+$scope.artist.oripic+'> dbpedia:isTipOf ?tip } ';
                    console.debug(queryArtistTips);
                    $http.post('query.php', {query: queryArtistTips}).
                    success(function (data, status, headers, config) {
                        $scope.hiddenTips = data;

                        //display the first tip but replace the name of the artist
                        var obj = $scope.hiddenTips.shift();
                        obj.tip = obj.tip.replace($scope.artist.name, "THE ARTIST");
                        $scope.shownTips.push(obj);
                        $scope.tips = $scope.shownTips;
                        $scope.points -= JOKER_ARTIST_POINTS;
                    });
                }else{
                    if($scope.hiddenTips.length > 0){
                        //display the the remaining tips one by one
                        var obj = $scope.hiddenTips.shift();
                        obj.tip = obj.tip.replace($scope.artist.name, "THE ARTIST");
                        $scope.shownTips.push(obj);
                        $scope.tips = $scope.shownTips;
                        $scope.points -= JOKER_ARTIST_POINTS;
                    }else{
                        $scope.message = "No tips for this painting left to display!"
                    }
                }

            }else{
                    $scope.message = "You have only " + $scope.points + " point(s) left. You need at least " + JOKER_ARTIST_POINTS + " points for this Joker!";
            }
        }

		$scope.search = function() {
			$http.post('AAI/query_index.php', {query: $scope.searchPhrase}).
                success(function (data, status, headers, config) {
                    $scope.searchResult = data;
					$scope.SearchLeft = false;
                });
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
