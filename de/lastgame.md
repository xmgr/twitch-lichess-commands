# Letzte Lichess Partie anzeigen

Der Befehl zeigt das Ergebnis der letzten gespielten Partie dieses Benutzers.

## Benutzung

Einfach `!lastgame <user>` eingeben, um die Ergebnisse der zuletzt gespielten Partie anzuzeigen. Der ⭐ Stern zeigt an,
wer der Gewinner war, die schwarzen und weßen Kreise entsprechend die Figurenfarbe der Spieler.

> Die Groß-/Kleinschreibung des Lichess Benutzernamens spielt keine Rolle ;)

![Letzte Partie](../images/lastgame-default.png)

### Letzte Partie mit einem bestimmten Gegner

Um die letze Partie von einem Spieler und einem bestimmten Gegner abzufragen, gib einfach zusätzlich zu einem
Doppelpunkt den Accountnamen des gegnerischen Spieler an, quasi `!lastgame <user1>:<user2>`.

![Letzte Partie gegen bestimmten Gegner](../images/lastgame-vs.png)

# Einrichtung

## Einrichtung in StreamElements

Gehe zu https://streamelements.com/dashboard/bot-commands/custom-commands und füge über den "Add new command" einen
neuen Befehl hinzu.

Trage als "Command name" sowas wie `!lastgame` ein. Welchen Befehlsnamen du dafür gern nehmen möchtest, ist ganz dir
überlassen.

Bei Response muss nun folgendes eingegeben werden:

```
${urlfetch https://xmgr.de/api/lichess/lastgame/${1|0}}
```

Jetzt auf "Save" klicken um den Befehl zu speichern.

## Einrichtung in Nightbot

Gehe zu https://nightbot.tv/commands/custom und klicke rechts auf den Button
"+ Add Command".

Gib bei "Command" den Namen des Befehls ein, z.B. `!lastgame`.

Als Message gib folgendes ein:

```
$(urlfetch json https://xmgr.de/api/lichess/lastgame/$(querystring))
```

Klicke den "Submit" button um den Befehl zu speichern.
