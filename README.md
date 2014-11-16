# GTA (aka Guess The Artist)
Übersicht über mögliche Probleme und Fragen.

## Aktuelle Probleme
1. SPARQL 1.1 Unterstützung für zB <code>SAMPLE(?name)</code> (siehe <code>FIXME</code> in <code>view1.js</code>)
Fragen:
    - Repo mit Fix als Submodule eingebunden - noch zu testen
        - https://groups.google.com/forum/#!msg/arc-dev/wiA0d6gS9UQ/loKl32jWlxAJ
        - https://github.com/stuartraetaylor/arc2-sparql11

- Falschantworten Query unschön
    - Random pick aktuell per JS aus gesamten result query
    - in der Humm-Ontologie liefert Movement und Birthplace teilweise noch weniger Resultate als vorher
- Bildprüfung mit Timeout-Funktion (Darstellung des falschen Bildes)
- Queries auslagern
- Konfigurationsparameter auslagern (Anzahl Antworten, Minuspunkte etc.)
- Feature: Mal schauen ob man auf dem FUSEKI auch sowas wie stored-procedures definieren kann (Soundex-Query)
- Beantworte Fragen nicht löschen sondern farblich markieren
- Wenn der 50-50 Joker zwei mal hintereinander ausgeführt wird -> nur noch richtiges Ergebnis

## Zukünftige Feature
- Wähle Falschartist nicht nach "movement,birthplace,etc. pp.", sondern nach gleich klingenden Namen (Soundex/Levenshtein)
- Hinweis-Feature: zu jedem Artisten in der Auswahl wird ein Bild geladen (Spieler erkennt evtl Malstil)
- Hinweis-Feature: zu dem gesuchten Künstler werden noch andere Bilder angezeigt (eventuell ein bekanntes Bild vorhanden)
- Hinweis-Feature: allgemeine Hinweise zum Künstler

## Gefixt
- Bilder laden eventuell nach (zeitweise falsches Bild) - <code>$scope.loading</code> -> GIF als Ladebild
- Falschantworten aus Movement + Birthplace + influenced by
- Falschantworten sind auch wirklich Painter
- in der Humm-Ontologie sind viele Bildquellen falsch -> Bildcheck durchführen
- Bilder haben immer die gleiche Reihenfolge (random select mit offset/SPARQL und pool/JS)
- Löschen der Falschantwort
- Schöne Namen (dadurch Problem Nr. 1)
- Endpoint Latenz nervt
- Bug bei manchen Künstlern (scheinbar wenn der Endpoint down ist). Wenn der Endpoint down ist - wo werden die alten Antworten/Fragen zwischengespeichert? (eigener FUSEKI)
