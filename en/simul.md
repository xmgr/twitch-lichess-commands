# Fetch simul

This command shows the current simul.

> Note: this command works only for actively running simuls atm.

## Usage

Just type `!simul`.

![Simul](../images/simul.png)


# Setup

## Setup in StreamElements

Open https://streamelements.com/dashboard/bot-commands/custom-commands and hit the "Add new command" button.

As "Command name" write `!simul`. Which command name you're using doesn't matter, you can also choose another one.

For "Response", insert the following line:

```
${urlfetch https://xmgr.io/api/lichess/simul/XXXXXXXX}
```

Replace `XXXXXXXX` with your lichess username.

Now hit the "Save" button to save the command.

## Setup in Nightbot

Open https://nightbot.tv/commands/custom and click on the "+ Add Command" button.

For "Command" enter something like `!simul`.

For "Message" insert the following line:

```
$(urlfetch json https://xmgr.io/api/lichess/simul/XXXXXXXX)
```

Replace `XXXXXXXX` with your lichess username

Now click "Submit" to save your changes.
