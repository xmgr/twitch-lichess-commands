# Own server

The commands run on xmgr.de by default, you don't need to install anything. If you like, you can also run them on your
own server: [lichess.php](../lichess.php) is a single PHP script without further dependencies and gives exactly the
same answers.

## Requirements

- Web server with PHP 8.1 or newer (Apache or nginx)
- PHP extension `curl`
- The server must be allowed to send requests to https://lichess.org

## Setup

1. Download [lichess.php](../lichess.php) and put it anywhere into your web root, for example so that it is reachable
   at `https://example.com/lichess.php`.
2. Test the setup with `https://example.com/lichess.php/test?lang=en`. If it answers "Test OK - the lichess API is
   operating as intended", everything is ready.
3. Take the commands from the manuals and replace `https://xmgr.de/api/lichess` with `https://example.com/lichess.php`.
   Everything else stays the same, for example for Nightbot:

```
$(urlfetch json https://xmgr.de/api/lichess/rating/$(querystring)?lang=en)
$(urlfetch json https://example.com/lichess.php/rating/$(querystring)?lang=en)
```

## If the test fails

The address `lichess.php/test` uses PATH_INFO: the web server recognizes `lichess.php` as a file, runs it and passes
the rest of the path to the script. Apache usually has this enabled, nginx needs it in its configuration. If the test
gives a 404 error or the message "please call a valid endpoint", use the query string form instead, which works
everywhere:

- Test: `https://example.com/lichess.php?action=test&lang=en`
- `lichess.php/<command>/<rest>` becomes `lichess.php?action=<command>&username=<rest>`
- Further options like `type` or `lang` are then added with `&` instead of `?`

```
$(urlfetch json https://example.com/lichess.php?action=rating&username=$(querystring)&lang=en)
${urlfetch https://example.com/lichess.php?action=rating&username=${1|${user.name}}&type=${2|0}&lang=en}
{readapi.https://example.com/lichess.php?action=rating&username={1}&lang=en}
```

Technical details (all options, nginx configuration) are in the comment at the top of [lichess.php](../lichess.php).
