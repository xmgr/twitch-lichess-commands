# Simultan abfragen

Der Befehl zeigt dein aktuelles Simultan auf Lichess samt Link.

> Laufende Simultane haben Vorrang, danach kommen welche in Vorbereitung, gerade erstellte und zuletzt bereits beendete.

## Benutzung

Einfach `!simul` eingeben.

```
Das ⏱ Simultan "taller simul" (2 Teilnehmer) läuft bereits: https://lichess.org/simul/Sp25I2ul
```

# Einrichtung

> Lieber auf dem eigenen Server? Das geht auch, siehe [Eigener Server](self-hosting.md).

## Einrichtung in StreamElements

Gehe zu https://streamelements.com/dashboard/bot-commands/custom-commands und füge über den "Add new command" einen
neuen Befehl hinzu.

Trage als "Command name" `!simul` ein. Welchen Befehlsnamen du dafür gern nehmen möchtest, ist ganz dir
überlassen.

Bei Response muss nun folgendes eingegeben werden:

```
${urlfetch https://xmgr.de/api/lichess/simul/XXXXXXXX}
```

Statt `XXXXXXXX` fügst du entsprechend deinen Lichess Benutzernamen ein.

Jetzt auf "Save" klicken um den Befehl zu speichern.

## Einrichtung in Nightbot

Gehe zu https://nightbot.tv/commands/custom und klicke rechts auf den Button
"+ Add Command".

Gib bei "Command" den Namen des Befehls ein, z.B. `!simul`.

Als Message gib folgendes ein:

```
$(urlfetch json https://xmgr.de/api/lichess/simul/XXXXXXXX)
```

Statt `XXXXXXXX` fügst du entsprechend deinen Lichess Benutzernamen ein.

Klicke den "Submit" button um den Befehl zu speichern.

## Einrichtung in StreamLabs Chatbot

Gehe zu https://streamlabs.com/dashboard#/cloudbot/commands/custom und klicke rechts auf den Button
"Add Command".

Gib bei "Command" den Namen des Befehls ein, z.B. `!simul`.

Als Response gib folgendes ein:

```
{readapi.https://xmgr.de/api/lichess/simul/XXXXXXXX}
```

Statt `XXXXXXXX` fügst du entsprechend deinen Lichess Benutzernamen ein.

Klicke den "Submit" button um den Befehl zu speichern.
