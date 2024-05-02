# Lichess Rating im Chat abfragen

Der Befehl holt sich für den angegebenen Benutzer die Lichess Ratings und gibt sie im Chat aus.

## Benutzung

Die Nutzung des Befehls ist sehr einfach, man gibt `!elo <user>` ein, wobei `<user>` der Benutzername des Lichess
Accounts ist. Statt `!elo` kannst du natürlich gern auch etwas anderes als Befehlsnamen verwenden, wie z.B. `!rating` -
das ist dir überlassen.

> Die Groß-/Kleinschreibung des Lichess Benutzernamens spielt keine Rolle ;)

### Geläufige Ratings abrufen

Standardmäßig werden die Ratings für Klassisch, Rapid, Blitz, Bullet und Ultrabullet angezeigt, zum Beispiel:

![Standard ratings](../images/elo-default.png)

### Alle Ratings anzeigen

Mit dem Befehl `!elo <user> all` lassen sich alle Ratings abrufen, das heißt auch Horde, Chess960, KingOfTheHill,
Puzzle-Rating etc..

![Alle Ratings](../images/elo-all-ratings.png)

Randinfo: statt `all` kann auch `alle` oder `*` geschrieben werden.

> Hinweis: es werden nur Ratings angezeigt, für die auch ein Wert vorliegt. Wenn
> man noch nie Antichess oder Chess960 gespielt hat, wird das entsprechend auch nicht mit gelistet.

### Selektive Ratings anzeigen

Bei Bedarf können auch nur einzelne Ratings abgefragt werden, durch Komma getrennt auch mehrere davon, zum Beispiel
mit `!elo <user> puzzle` oder `!elo <user> atomic,horde,bullet`.

![Selektive Ratings](../images/elo-selektiv.png)

# Einrichtung

## Einrichtung in StreamElements

Gehe zu https://streamelements.com/dashboard/bot-commands/custom-commands und füge über den "Add new command" einen
neuen Befehl hinzu.

Trage als "Command name" sowas wie `!elo` oder `!rating` ein. Welchen Befehlsnamen du dafür gern nehmen möchtest, ist
ganz dir überlassen.

Bei Response muss nun folgendes eingegeben werden:

```
${urlfetch https://xmgr.de/api/lichess/rating/${1|${user.name}}?type=${2|0}}
```

Jetzt auf "Save" klicken um den Befehl zu speichern.

## Einrichtung in Nightbot

Gehe zu https://nightbot.tv/commands/custom und klicke rechts auf den Button
"+ Add Command".

Gib bei "Command" den Namen des Befehls ein, z.B. `!elo`.

Als Message gib folgendes ein:

```
$(urlfetch json https://xmgr.de/api/lichess/rating/$(querystring))
```

Klicke den "Submit" button um den Befehl zu speichern.

## Einrichtung in StreamLabs Chatbot

Gehe zu https://streamlabs.com/dashboard#/cloudbot/commands/custom und klicke rechts auf den Button
"Add Command".

Gib bei "Command" den Namen des Befehls ein, z.B. `!elo`.

Als Response gib folgendes ein:

```
{readapi.https://xmgr.de/api/lichess/rating/{1}}
```

Klicke den "Submit" button um den Befehl zu speichern.
