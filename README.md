# GTA (aka Guess The Artist)
Übersicht über mögliche Probleme und Fragen.

## Aktuelle Probleme
1. Hintergrund: zum Darstellen "schöner Namen" wird jetzt das <code>?name</code>-Feld mit in den Query aufgenommen. Allerdings besitzen Artist teilweise mehrfach diese Property. Mittels <code>SAMPLE(?name)</code> wird das Problem normalerweise gelöst. Allerdings funktioniert es nicht per Implementierung.
Fragen:
    - ARC2 unterstützt kein SPARQL 1.1 -> Repo mit Fix gefunden, dieses eventuell ausprobieren
    - https://groups.google.com/forum/#!msg/arc-dev/wiA0d6gS9UQ/loKl32jWlxAJ
    - https://github.com/stuartraetaylor/arc2-sparql11
    ```sql
    -- Fuehre Query unter http://dbpedia.org/sparql aus -> gibt Ergebnisse zurück
    SELECT ?artist SAMPLE(?name) WHERE {?artist dbpprop:name ?name {SELECT DISTINCT ?artist WHERE { {<http://dbpedia.org/resource/Sandro_Botticelli> dbpedia-owl:birthPlace ?birthplace . ?artist dbpedia-owl:birthPlace ?birthplace . ?artist dbpedia-owl:movement ?movement FILTER ( ?artist != <http://dbpedia.org/resource/Sandro_Botticelli> ). } UNION { <http://dbpedia.org/resource/Sandro_Botticelli> dbpedia-owl:movement ?movement . ?artist dbpedia-owl:movement ?movement FILTER ( ?artist != <http://dbpedia.org/resource/Sandro_Botticelli> ). } UNION { <http://dbpedia.org/resource/Sandro_Botticelli> dbpprop:birthPlace ?birthplace . ?artist dbpprop:birthPlace ?birthplace . ?artist dbpedia-owl:movement ?movement } } } }
```
Wenn der Query per Implementierung ausgeführt wird fehlen die "Falschantworten" - siehe <code>FIXME</code> in <code>view1.js</code>
- Bilder haben immer die gleiche Reihenfolge
- in der Humm-Ontologie sind viele Bildquellen falsch -> Bildcheck durchführen
- Falschantworten Query unschön
    - Random pick aktuell per JS aus gesamten result query
    - besteht aktuell aus (Movement + Birthplace)
    - Pool noch nicht groß genug (teilweise nur drei Antworten)
    - in der Humm-Ontologie liefert Movement und Birthplace teilweise noch weniger Resultate als vorher
- Queries auslagern
- Konfigurationsparameter auslagern (Anzahl Antworten, Minuspunkte etc.)
- Bilder laden eventuell nach (zeitweise falsches Bild)
- Feature: Mal schauen ob man auf dem FUSEKI auch sowas wie stored-procedures definieren kann (Soundex-Query)
- Beantworte Fragen nicht löschen sondern farblich markieren

## Zukünftige Feature
Wähle Falschartist nicht nach "movement,birthplace,etc. pp.", sondern nach gleich klingenden Namen (Soundex/Levenshtein)

## Gefixt
- Löschen der Falschantwort
- Schöne Namen (dadurch Problem Nr. 1)
- Endpoint Latenz nervt
- Bug bei manchen Künstlern (scheinbar wenn der Endpoint down ist). Wenn der Endpoint down ist - wo werden die alten Antworten/Fragen zwischengespeichert? (eigener FUSEKI)
