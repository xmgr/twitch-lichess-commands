# Show Lichess Rating in Chat

This command fetches the lichess ratings for a given user.

## Usage

The usage is quite simple, you type `!elo <user>` and that's it.

> the upper-/lower case for the lichess username doesn't matter

### Fetch common ratings (default)

By default the ratings for classical, rapid, blitz, bullet and ultrabullet are shown:

![Default ratings](../images/elo-default.png)

### Show all ratings

Via `!elo <user> all` all available ratings are shown, which includes also Horde, Chess960, KingOfTheHill,
Puzzle-Rating, Antichess etc..

![All ratings](../images/elo-all-ratings.png)

Side note: you can also use `*` instead of `all`, so `!elo <user> *` then.

> Note: this will only show ratings for game types you already played at all. So if you
> never played a Chess960 game there is no Chess960 rating shown.

### Show selective ratings

If needed you can show only the stats for a single type of game, for example `!elo <user> puzzle`. You can also provide
more types separated by comma, like in `!elo <user> atomic,horde,bullet`.

![Selective ratings](../images/elo-selektiv.png)

# Setup

## Setup in StreamElements

Open https://streamelements.com/dashboard/bot-commands/custom-commands and hit the "Add new command" button.

For "Command name" type something like `!elo` or `!rating`.

For "Response" insert the following line:

```
${urlfetch https://xmgr.de/api/lichess/rating/${1|${user.name}}?type=${2|0}}
```

Now click "Save" to save the command.

## Setup in Nightbot

Open https://nightbot.tv/commands/custom and hit the "+ Add Command" button.

For "Command" enter `!elo`.

In the "Message" text input field, insert the following line:

```
$(urlfetch json https://xmgr.de/api/lichess/rating/$(querystring))
```

Click the "Submit" button to save your changes.

# Additional options

## Use the command without a given username

Alternatively you can put **your** literal lichess username instead of the
[variable](help/variables.md). After that you'd only need to type `!elo` which then shows
**your** stats in the chat.

# Custom API URL parameters (optional)

If needed, there are optional parameters you can submit in the API URL.

> The username (via parameter `username`) is usually mandatory, as you can see
> in the URL: https://xmgr.de/api/lichess/rating?username=LICHESS_USERNAME but you can alternatively put the username just to the path like in https://xmgr.de/api/lichess/rating/LICHESS_USERNAME

### Remove the icons

Set `icon` to `0` to remove the game type emojies.

Example: https://xmgr.de/api/lichess/rating/LICHESS_USERNAME?icon=0

### Remove progression arrows

To remove the progression arrows ↗ and ↘ set `prog` to `0`.

Example: https://xmgr.de/api/lichess/rating/LICHESS_USERNAME?prog=0

### Separator between game types

You can pass any text as separator between the rating types via the `sep` parameter.

Example: https://xmgr.de/api/lichess/rating/LICHESS_USERNAME?sep=%20|%20&icon=0
