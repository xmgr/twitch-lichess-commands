# Show last lichess game

This command shows the result and stats of the last game played on lichess.

## Usage

Just type `!lastgame <user>` to show the last game of the specified lichess user. In the output, the ⭐ indicates the
winner and the white and black circles the pieces' color.

> The upper / lower case of the lichess username does not matter;)

![Last game](../images/lastgame-default.png)

### Last game with a specific opponent

To show the last game of a player with a specific opponent, just add this player's name separated by a colon,
so `!lastgame <user1>:<user2>`-

![Last game with a specific opponent](../images/lastgame-vs.png)

# Setup

## Setup in StreamElements

Open https://streamelements.com/dashboard/bot-commands/custom-commands und hit the "Add new command" button.

For "Command name" enter something like `!lastgame`. What command name you choose doesn't matter.

Now insert the following for "Response":

```
${urlfetch https://xmgr.de/api/lichess/lastgame/${1|0}}
```

Now hit the "Save" to save your changes.

## Setup in Nightbot

Open https://nightbot.tv/commands/custom and click the "+ Add Command" on the right.

For "Command" type in your desired command name `!lastgame`.

Insert the following line in the "Message" input field:

```
$(urlfetch json https://xmgr.de/api/lichess/lastgame/$(querystring))
```

Hit the "Submit" button to save your changes.
