# Eigener Server

Die Befehle laufen standardmäßig über xmgr.de, du musst dafür nichts installieren. Bei Bedarf kannst du sie aber auch
auf deinem eigenen Server betreiben: [lichess.php](../lichess.php) ist ein einzelnes PHP-Skript ohne weitere
Abhängigkeiten und liefert genau dieselben Antworten.

## Voraussetzungen

- Webserver mit PHP 8.1 oder neuer (Apache oder nginx)
- PHP-Erweiterung `curl`
- Der Server darf Anfragen an https://lichess.org stellen

## Einrichtung

1. Lade [lichess.php](../lichess.php) herunter und lege die Datei irgendwo in deinen Webroot, zum Beispiel so, dass
   sie unter `https://example.com/lichess.php` erreichbar ist.
2. Teste die Einrichtung mit `https://example.com/lichess.php/test`. Kommt "Test OK - die lichess API funktioniert
   ordnungsgemäß", ist alles bereit.
3. Nimm die Befehle aus den Anleitungen und ersetze darin `https://xmgr.de/api/lichess` durch
   `https://example.com/lichess.php`. Alles andere bleibt gleich, zum Beispiel für Nightbot:

```
$(urlfetch json https://xmgr.de/api/lichess/rating/$(querystring))
$(urlfetch json https://example.com/lichess.php/rating/$(querystring))
```

## Falls der Test nicht klappt

Die Adresse `lichess.php/test` benutzt PATH_INFO: Der Webserver erkennt `lichess.php` als Datei, führt sie aus und
gibt den Rest des Pfads an das Skript weiter. Bei Apache ist das normalerweise eingeschaltet, bei nginx muss es in der
Konfiguration aktiviert sein. Wenn der Test einen Fehler 404 oder die Meldung "bitte rufe einen gültigen Endpoint
auf" liefert, nimm stattdessen die Variante mit Querystring, die überall funktioniert:

- Test: `https://example.com/lichess.php?action=test`
- Aus `lichess.php/<befehl>/<rest>` wird `lichess.php?action=<befehl>&username=<rest>`
- Weitere Angaben wie `type` oder `lang` werden dann mit `&` statt `?` angehängt

```
$(urlfetch json https://example.com/lichess.php?action=rating&username=$(querystring))
${urlfetch https://example.com/lichess.php?action=rating&username=${1|${user.name}}&type=${2|0}}
{readapi.https://example.com/lichess.php?action=rating&username={1}}
```

Technische Details (alle Optionen, nginx-Konfiguration) stehen im Kommentar oben in [lichess.php](../lichess.php).
