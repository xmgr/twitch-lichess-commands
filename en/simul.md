# Fetch simul

This command shows your current simul on Lichess with a link.

> Running simuls come first, then the ones being prepared, newly created ones and finally finished ones.

## Usage

Just type `!simul`.

```
The ⏱ simul "taller simul" (2 participants) is already running: https://lichess.org/simul/Sp25I2ul
```

# Setup

## Setup in StreamElements

Open https://streamelements.com/dashboard/bot-commands/custom-commands and hit the "Add new command" button.

As "Command name" write `!simul`. Which command name you're using doesn't matter, you can also choose another one.

For "Response", insert the following line:

```
${urlfetch https://xmgr.de/api/lichess/simul/XXXXXXXX?lang=en}
```

Replace `XXXXXXXX` with your Lichess username.

Now hit the "Save" button to save the command.

## Setup in Nightbot

Open https://nightbot.tv/commands/custom and click on the "+ Add Command" button.

For "Command" enter something like `!simul`.

For "Message" insert the following line:

```
$(urlfetch json https://xmgr.de/api/lichess/simul/XXXXXXXX?lang=en)
```

Replace `XXXXXXXX` with your Lichess username.

Now click "Submit" to save your changes.

## Setup in StreamLabs Chatbot

Open https://streamlabs.com/dashboard#/cloudbot/commands/custom and click the "Add Command" button on the right.

For "Command" enter `!simul`.

For "Response" insert the following line:

```
{readapi.https://xmgr.de/api/lichess/simul/XXXXXXXX?lang=en}
```

Replace `XXXXXXXX` with your Lichess username.

Click the "Submit" button to save your changes.
