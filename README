# GTA (aka Guess The Artist)
Übersicht über mögliche Probleme und Fragen.

## Aktuelle Probleme
1. Hintergrund: zum Darstellen "schöner Namen" wird jetzt das <code>?name</code>-Feld mit in den Query aufgenommen. Allerdings besitzen Artist teilweise mehrfach diese Property. Mittels <code>SAMPLE(?name)</code> wird das Problem normalerweise gelöst. Allerdings funktioniert es nicht per Implementierung.
Fragen:
    - Endpoint kann per ARC keine SPARQL 1.1 Paradigmen wie <code>SAMPLE(?name)</code> oder subqueries??
    - In der Webschnittstelle geht es jedoch - warum !?
    ```sql
    -- Fuehre Query unter http://dbpedia.org/sparql aus -> gibt Ergebnisse zurück
    SELECT ?artist SAMPLE(?name) WHERE {?artist dbpprop:name ?name {SELECT DISTINCT ?artist WHERE { {<http://dbpedia.org/resource/Sandro_Botticelli> dbpedia-owl:birthPlace ?birthplace . ?artist dbpedia-owl:birthPlace ?birthplace . ?artist dbpedia-owl:movement ?movement FILTER ( ?artist != <http://dbpedia.org/resource/Sandro_Botticelli> ). } UNION { <http://dbpedia.org/resource/Sandro_Botticelli> dbpedia-owl:movement ?movement . ?artist dbpedia-owl:movement ?movement FILTER ( ?artist != <http://dbpedia.org/resource/Sandro_Botticelli> ). } UNION { <http://dbpedia.org/resource/Sandro_Botticelli> dbpprop:birthPlace ?birthplace . ?artist dbpprop:birthPlace ?birthplace . ?artist dbpedia-owl:movement ?movement } } } }
```
Wenn der Query per Implementierung ausgeführt wird fehlen die "Falschantworten" - siehe <code>FIXME</code> in <code>view1.js</code>
![FehlendeAntwortSAMPLE](http://cl.ly/image/0j2q3o0w261B/Bildschirmfoto%202014-11-02%20um%2014.00.38.png)

- Bug bei manchen Künstlern (scheinbar wenn der Endpoint down ist). Wenn der Endpoint down ist - wo werden die alten Antworten/Fragen zwischengespeichert?
```javascript
Error: $scope.artist is undefined
$scope.next/<@http://localhost:8888/AAI/view1/view1.js:45:1
$http/promise.success/<@http://localhost:8888/AAI/bower_components/angular/angular.js:8113:11
qFactory/defer/deferred.promise.then/wrappedCallback@http://localhost:8888/AAI/bower_components/angular/angular.js:11573:31
qFactory/defer/deferred.promise.then/wrappedCallback@http://localhost:8888/AAI/bower_components/angular/angular.js:11573:31
qFactory/ref/<.then/<@http://localhost:8888/AAI/bower_components/angular/angular.js:11659:26
$RootScopeProvider/this.$get</Scope.prototype.$eval@http://localhost:8888/AAI/bower_components/angular/angular.js:12702:16
$RootScopeProvider/this.$get</Scope.prototype.$digest@http://localhost:8888/AAI/bower_components/angular/angular.js:12514:15
$RootScopeProvider/this.$get</Scope.prototype.$apply@http://localhost:8888/AAI/bower_components/angular/angular.js:12806:13
done@http://localhost:8888/AAI/bower_components/angular/angular.js:8379:34
completeRequest@http://localhost:8888/AAI/bower_components/angular/angular.js:8593:7
createHttpBackend/</xhr.onreadystatechange@http://localhost:8888/AAI/bower_components/angular/angular.js:8532:1
```
- Bilder haben immer die gleiche Reihenfolge
- Endpoint Latenz nervt
- Falschantworten Query unschön
    - Random pick aktuell per JS aus gesamten result query
    - besteht aktuell aus (Movement + Birthplace)
    - Pool noch nicht groß genug (teilweise nur drei Antworten)
![FehlendeAntwort](http://cl.ly/image/3i40193S1d30/Bildschirmfoto%202014-10-31%20um%2012.53.58.png)
- Queries auslagern
- Konfigurationsparameter auslagern (Anzahl Antworten, Minuspunkte etc.)
- Bilder laden eventuell nach (zeitweise falsches Bild)

## Zukünftige Feature
Wähle Falschartist nicht nach "movement,birthplace,etc. pp.", sondern nach gleich klingenden Namen (Soundex/Levenshtein)

## Gefixt
- Löschen der Falschantwort
- Schöne Namen (dadurch Problem Nr. 1)
