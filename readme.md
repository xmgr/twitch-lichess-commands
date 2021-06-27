# Lichess Befehle für Twitch Chats

Eine kleine Sammlung an Befehlen um beispielsweise lichess ratings oder Spieldauer-Statistiken abzurufen, die sich
direkt im Twitch Chat nutzen lassen.

### Einrichtung eines Befehls

Für jede verfügbare Funktion ist hier eine Anleitung verlinkt, um die Befehle in StreamElements oder Nightbot einfach
einzubinden.

> Tipp: falls du Nightbot benutzt und Nightbot auf deinen Discord Server gejoined
> ist, kannst du die ganzen Befehle auch dort nutzen. Dazu gibt's weiter unten noch einen Abschnitt.

## Liste der Funktionen

Eine Auflistung der verfügbaren Befehle

### lichess Rating abrufen

Durch Eingabe von `!elo <user>` lässt sich das lichess Rating im Chat ausgeben.

Lies [hier](de/rating.md) wie du es einbinden kannst.

![lichess rating](images/lichess-rating-example.png)

### Spieldauer und Anzahl gespielter Partien

Einfach `!playtime <user>` eingeben, um zu sehen wieviele Partien der lichess Benutzer bereits gespielt und wieviel Zeit
er insgesamt mit Schachspielen verbracht hat.

Du kannst [hier](de/playtime.md) lesen wie man den Befehl aufsetzt.

![Playtime](images/playtime-example.png)

## Allgemeines zur Einrichtung

Egal ob du Nightbot oder StreamElements benutzt, in jedem Fall muss der Bot Mod-Berechtigungen haben und dem Chat
beigetreten sein - ist zwar eine ganz offensichtliche Voraussetzung, aber ich erwähne es der Vollständigkeit halber trotzdem ;)

## Einrichtung auf Discord

Wenn du die oberen Befehle für den Nightbot eingerichtet hast und dein Nightbot mit deinem Discord Server verknüpft ist,
lassen sich die Befehle auch auf Discord benutzen. Das heißt du rufst https://nightbot.tv/integrations auf und lässt
einfach den Nightbot per Klick auf deinen Discord Server joinen und das war's, mehr muss nicht gemacht werden.

![Nightbot auf Discord](images/nightbot-discord.png)

