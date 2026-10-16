# Show last Lichess game

This command shows the result and stats of the last game played on Lichess.

## Usage

Just type `!lastgame <user>` to show the last game of the specified Lichess user. In the output, ♙ marks the player
with white, ♟ the player with black and the 🏅 medal shows the winner. It also shows the time control, the variant, how
long the game took, how it ended and the link to the game.

> The upper / lower case of the Lichess username does not matter ;)

```
♙ Colt_tv (1596) vs. ♟ marius_gerum🏅 (1740) in an unrated 2+0 Standard 🚀 Bullet game that took 4 minutes and 19 seconds, white lost the game on time after 47 moves. ~ https://lichess.org/hY6x1y3R
```

### Last game with a specific opponent

To show the last game of a player against a specific opponent, just add the opponent's name separated by a colon,
so `!lastgame <user1>:<user2>`.

# Setup

## Setup in StreamElements

Open https://streamelements.com/dashboard/bot-commands/custom-commands and hit the "Add new command" button.

For "Command name" enter something like `!lastgame`. What command name you choose doesn't matter.

Now insert the following for "Response":

```
${urlfetch https://xmgr.de/api/lichess/lastgame/${1|${user.name}}?lang=en}
```

Without a name, the Twitch name of the user who typed the command is used.

Now hit "Save" to save your changes.

## Setup in Nightbot

Open https://nightbot.tv/commands/custom and click the "+ Add Command" on the right.

For "Command" type in your desired command name `!lastgame`.

Insert the following line in the "Message" input field:

```
$(urlfetch json https://xmgr.de/api/lichess/lastgame/$(querystring)?lang=en)
```

Hit the "Submit" button to save your changes.

## Setup in StreamLabs Chatbot

Open https://streamlabs.com/dashboard#/cloudbot/commands/custom and click the "Add Command" button on the right.

For "Command" enter `!lastgame`.

For "Response" insert the following line:

```
{readapi.https://xmgr.de/api/lichess/lastgame/{1}?lang=en}
```

Click the "Submit" button to save your changes.
