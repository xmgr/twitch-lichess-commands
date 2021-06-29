# Spieldauer Statistik

Der Befehl holt sich für den angegebenen lichess Benutzer die Anzahl gespielter Partien sowie die Dauer die man auf
lichess insgesamt mit Schachspielen verbracht hat.

## Benutzung

Die Nutzung des Befehls ist sehr einfach, man gibt `!playtime <user>` ein, wobei `<user>` der Benutzername des lichess
Accounts ist. Statt `!playtime` kannst du natürlich gern auch etwas anderes als Befehlsnamen verwenden, das ist dir
überlassen.

> Die Groß-/Kleinschreibung des lichess Benutzernamens spielt keine Rolle ;)

![img.png](../images/playtime-fritzi.png)

# Einrichtung

## Einrichtung in StreamElements

Gehe zu https://streamelements.com/dashboard/bot-commands/custom-commands und füge über den "Add new command" einen
neuen Befehl hinzu.

Trage als "Command name" sowas wie `!playtime` ein. Welchen Befehlsnamen du dafür gern nehmen möchtest, ist ganz dir
überlassen.

Bei Response muss nun folgendes eingegeben werden:

```
${urlfetch https://xmgr.io/api/lichess/playtime?username=${1}}
```

Jetzt auf "Save" klicken um den Befehl zu speichern.

## Einrichtung im Nightbot

Gehe zu https://nightbot.tv/commands/custom und klicke rechts auf den Button
"+ Add Command".

Gib bei "Command" den Namen des Befehls ein, z.B. `!playtime`.

Als Message gib folgendes ein:

```
$(urlfetch json https://xmgr.io/api/lichess/playtime?username=$(querystring))
```

Klicke den "Submit" button um den Befehl zu speichern.

# Anpassungen

## Statistik ohne Angabe des Benutzernamens verwenden

Du kannst in der API URL statt der Variable auch direkt deinen lichess Benutzernamen eintragen. So kann man im Chat
durch Eingabe von `!playtime` immer dein Rating sehen.