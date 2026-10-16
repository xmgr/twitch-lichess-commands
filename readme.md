# Lichess Befehle für Twitch Chats

Eine kleine Sammlung an Befehlen um beispielsweise Lichess Ratings oder Spieldauer-Statistiken abzurufen, die sich
direkt im Twitch Chat nutzen lassen.

Für jede verfügbare Funktion ist hier eine Anleitung verlinkt, um die Befehle in
**StreamElements**, **StreamLabs**, **Nightbot** oder **Discord** einfach einzubinden.

> Info: there's also an [english manual](readme-en.md) available.

## Quicklinks

Um direkt zur Einrichtung zu springen:

- [Lichess Rating abrufen](de/rating.md) (`!elo <user>`)
- [Spieldauer Statistik](de/playtime.md) (`!playtime <user>`)
- [Letzte Partie abrufen](de/lastgame.md) (`!lastgame <user>`)
- [Simultan abfragen](de/simul.md) (`!simul`)

## Lichess Rating abrufen ([Anleitung](de/rating.md))

Durch Eingabe von `!elo <user>` lässt sich das Lichess Rating im Chat ausgeben.

![Lichess rating](images/lichess-rating-example.png)

## Spieldauer Statistik ([Anleitung](de/playtime.md))

Einfach `!playtime <user>` eingeben, um zu sehen wie viele Partien der Lichess Benutzer bereits gespielt und wie viel
Zeit er insgesamt mit Schachspielen verbracht hat.

![Playtime](images/playtime-example.png)

## Letzte Partie abrufen ([Anleitung](de/lastgame.md))

Zeigt mit `!lastgame <user>` das Ergebnis der von diesem Benutzer zuletzt gespielten Partie.

![Letzte Partie](images/lastgame.png)

## Simultan abfragen ([Anleitung](de/simul.md))

Zeigt mit `!simul` dein aktuelles Simultan auf Lichess samt Link, damit dein Chat direkt mitspielen kann.

![Simul](images/simul.png)

## Allgemeines zur Einrichtung

Egal welchen Chatbot du benutzt, in jedem Fall muss der Bot Mod-Berechtigungen haben und dem Chat
beigetreten sein - ist zwar eine offensichtliche Voraussetzung, aber es sei der Vollständigkeit halber nochmal erwähnt.

Die Befehle arbeiten mit Variablen wie `$(querystring)` oder `${1}`, die der Bot durch das ersetzt, was im Chat hinter
dem Befehl eingegeben wurde. Mehr dazu unter [Variablen](de/help/variables.md).

### Einrichtung auf Discord

Wenn du Nightbot verwendest und dein Nightbot mit deinem Discord Server verknüpft ist,
lassen sich die dort eingerichteten Befehle auch auf Discord benutzen. Rufe https://nightbot.tv/integrations auf und lasse einfach den
Nightbot per Klick auf deinen Discord Server joinen - das war's schon.

![Nightbot auf Discord](images/nightbot-discord.png)

### Eigener Server

Alle Befehle laufen über xmgr.de, du musst nichts installieren. Wer sie lieber selbst betreiben will, findet mit
[lichess.php](lichess.php) ein einzelnes PHP-Skript, das genau dieselben Antworten liefert. Wie das geht, steht unter
[Eigener Server](de/self-hosting.md).

## In Aktion

Diese nicen Streamer nutzen die Lichess Befehle bereits für ihr Twitch oder auf ihren Discord Servern:

- [Colt_TV1](https://www.twitch.tv/colt_tv1)
- [chris1996](https://www.twitch.tv/chris1996)
- [FritziSchach](https://www.twitch.tv/fritzischach)
- [Willeinhelm](https://www.twitch.tv/willeinhelm)
- [Ganzling](https://www.twitch.tv/ganzling)
- [GambitGambler](https://www.twitch.tv/gambitgambler)
- [GunnysWorld](https://www.twitch.tv/gunnysworld)
- [Lotnisko7](https://www.twitch.tv/lotnisko7)
- [Schachopa](https://www.twitch.tv/schachopa)

## Support

Bei Fragen oder Ideen schreib mir gerne auf Discord (`.link4`).
