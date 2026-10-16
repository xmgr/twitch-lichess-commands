# Letzte Lichess Partie anzeigen

Der Befehl zeigt das Ergebnis der letzten gespielten Partie dieses Benutzers.

## Benutzung

Einfach `!lastgame <user>` eingeben, um die Ergebnisse der zuletzt gespielten Partie anzuzeigen. ♙ steht vor dem
Spieler mit Weiß, ♟ vor dem Spieler mit Schwarz, und die 🏅 Medaille zeigt an, wer gewonnen hat. Dazu kommen
Bedenkzeit, Variante, Dauer der Partie, wie sie ausging und der Link zur Partie.

> Die Groß-/Kleinschreibung des Lichess Benutzernamens spielt keine Rolle ;)

```
♙ Colt_tv (1596) vs. ♟ marius_gerum🏅 (1740) in einer ungewerteten 2+0 Standard 🚀 Bullet-Partie die 4 Minuten und 19 Sekunden dauerte, weiß hat die Partie nach 47 Zügen auf Zeit verloren. ~ https://lichess.org/hY6x1y3R
```

### Letzte Partie mit einem bestimmten Gegner

Um die letzte Partie eines Spielers gegen einen bestimmten Gegner abzufragen, gib zusätzlich nach einem Doppelpunkt den
Accountnamen des gegnerischen Spielers an, also `!lastgame <user1>:<user2>`.

# Einrichtung

> Lieber auf dem eigenen Server? Das geht auch, siehe [Eigener Server](self-hosting.md).

## Einrichtung in StreamElements

Gehe zu https://streamelements.com/dashboard/bot-commands/custom-commands und füge über den "Add new command" einen
neuen Befehl hinzu.

Trage als "Command name" sowas wie `!lastgame` ein. Welchen Befehlsnamen du dafür gern nehmen möchtest, ist ganz dir
überlassen.

Bei Response muss nun folgendes eingegeben werden:

```
${urlfetch https://xmgr.de/api/lichess/lastgame/${1|${user.name}}}
```

Ohne Namen wird so der Twitch-Name dessen genommen, der den Befehl eingibt.

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

## Einrichtung in StreamLabs Chatbot

Gehe zu https://streamlabs.com/dashboard#/cloudbot/commands/custom und klicke rechts auf den Button
"Add Command".

Gib bei "Command" den Namen des Befehls ein, z.B. `!lastgame`.

Als Response gib folgendes ein:

```
{readapi.https://xmgr.de/api/lichess/lastgame/{1}}
```

Klicke den "Submit" button um den Befehl zu speichern.
