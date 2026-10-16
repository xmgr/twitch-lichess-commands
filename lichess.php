<?php
/**
 * Lichess chat commands for Twitch bots (Nightbot, StreamElements, StreamLabs) as a standalone script.
 *
 * Same answers as /api/lichess/{action}/{username} of xmgr.de, but without the framework (PHP 8.1+, curl, no cache).
 * Upload this one file anywhere below the document root, nothing else is needed.
 * Manual for chat users: https://github.com/xmgr/twitch-lichess-commands
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * Commands ("action")
 * ---------------------------------------------------------------------------------------------------------------------
 *   rating, elo   Ratings of a user. Words after the name select them: "all" (or alle, *), "blitz",
 *                 "atomic,horde,bullet".
 *                 Without name: the Twitch user who typed the command (Nightbot only, it sends a Nightbot-User header).
 *   playtime      Time spent playing and number of games.
 *   lastgame      Result of the last game, "<user>:<opponent>" for the last game against a given opponent.
 *   simul         Current simul of a host (running first, then pending, created, finished).
 *   test          Answers "Test OK ..." without calling Lichess, to check the setup.
 *
 * Query options for all commands: lang=de|en (default de). For rating also: type=<words> (instead of words after the
 * name), sep=<text> (between ratings, default space), icon=0 (no emojis), prog=0 (no ↗/↘ arrows).
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * Two URL forms
 * ---------------------------------------------------------------------------------------------------------------------
 *   Path:   https://example.com/lichess.php/rating/YOUR_USERNAME%20blitz?lang=en
 *   Query:  https://example.com/lichess.php?action=rating&username=YOUR_USERNAME%20blitz&lang=en
 *
 * Both give the same answer and can be mixed with the query options. The script takes action and name from the path
 * if there is one, else from ?action= and ?username=.
 *
 * The path form relies on PATH_INFO: the web server finds lichess.php as a file, runs it and passes the rest of the
 * path ("/rating/YOUR_USERNAME") in $_SERVER['PATH_INFO']. Whether that works depends on the server:
 *   - Apache: on by default for PHP files. Off with "AcceptPathInfo Off", then the path form answers 404.
 *   - nginx:  only with a config that passes it on, typically
 *               location ~ \.php(/|$) {
 *                   fastcgi_split_path_info ^(.+\.php)(/.*)$;
 *                   fastcgi_param PATH_INFO $fastcgi_path_info;
 *                   ...
 *               }
 *             A plain "location ~ \.php$" does not match lichess.php/rating/... at all.
 * Check it with https://example.com/lichess.php/test: "Test OK ..." means the path form works, a 404 or the
 * "no endpoint" message means it does not. Then use the query form, which works everywhere.
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * Bot setup (rating as example, the other commands work the same way, just replace "rating")
 * ---------------------------------------------------------------------------------------------------------------------
 *   Nightbot ($(querystring) = everything after the command, already URL encoded)
 *     Path:   $(urlfetch json https://example.com/lichess.php/rating/$(querystring))
 *     Query:  $(urlfetch json https://example.com/lichess.php?action=rating&username=$(querystring))
 *
 *   StreamElements (${1} = first word, ${1|${user.name}} = first word or the caller, ${2|0} = second word or "0")
 *     Path:   ${urlfetch https://example.com/lichess.php/rating/${1|${user.name}}?type=${2|0}}
 *     Query:  ${urlfetch https://example.com/lichess.php?action=rating&username=${1|${user.name}}&type=${2|0}}
 *
 *   StreamLabs Chatbot ({1} = first word)
 *     Path:   {readapi.https://example.com/lichess.php/rating/{1}}
 *     Query:  {readapi.https://example.com/lichess.php?action=rating&username={1}}
 *
 * For English answers add lang=en ("...?lang=en" in the path form, "...&lang=en" in the query form). For simul
 * put the fixed Lichess name of the host into the URL instead of a variable.
 */

declare(strict_types=1);

final class LichessCommands {

    private const API = 'https://lichess.org/api/';

    /** Words after the username that show the ratings of DrNykterstein under the given username. */
    private const KAPPA = ['lol', 'lool', 'lololo', 'trololo', 'lul', 'lel', 'lule', 'lulz', 'lulw', 'kappa', 'kek', 'kekw'];

    /** Words that select all ratings. */
    private const ALL = ['all', 'alle', 'alles', '*'];

    /** Ratings shown without selection. */
    private const DEFAULT_PERFS = ['classical', 'rapid', 'blitz', 'bullet', 'ultraBullet'];

    /** Lichess perf keys with icon, label and the words that select them, in output order. */
    private const PERFS = [
        'ultraBullet'    => ['⚡', 'Ultrabullet', ['ultrabullet', 'hyperbullet']],
        'bullet'         => ['🚀', 'Bullet', ['bullet']],
        'blitz'          => ['🔥', 'Blitz', ['blitz', 'blitzen']],
        'rapid'          => ['🐇', 'Rapid', ['rapid', 'schnellschach', 'schnell-schach', 'rapids']],
        'classical'      => ['🐢', 'Classical', ['classical', 'klassisch', 'classic', 'klassik']],
        'correspondence' => ['⌛', 'Correspondence', ['correspondence', 'fernschach', 'fern-schach']],
        'chess960'       => ['🔀', 'Chess960', ['chess960', '960']],
        'atomic'         => ['⚛️', 'Atomic', ['atomic']],
        'racingKings'    => ['🏎️', 'Racing Kings', ['racingkings', 'racing-kings', 'racing', 'race']],
        'kingOfTheHill'  => ['♔', 'King Of The Hill', ['kingofthehill', 'king-of-the-hill']],
        'horde'          => ['♙', 'Horde', ['horde']],
        'puzzle'         => ['🧩', 'Puzzle', ['puzzle', 'puzzel', 'puzzles', 'puzzels', 'tactics', 'taktik', 'taktikaufgaben']],
        'antichess'      => ['💢', 'Antichess', ['antichess', 'antischach']],
        'crazyhouse'     => ['☢️', 'Crazyhouse', ['crazyhouse', 'einsetzschach']],
        'threeCheck'     => ['3️⃣', 'ThreeCheck', ['threecheck', 'three-check', '3check', '3chess', 'e-check']],
    ];

    /** Texts as [english, german]. */
    private const TEXTS = [
        // General
        'username_required'      => ['Sorry, but a username needs to be specified ;)', 'Sorry, aber du musst schon einen Benutzernamen angeben ;)'],
        'user_not_found'         => ['Sorry, there is no lichess user "{user}"', 'Sorry, einen lichess Benutzer "{user}" gibt es nicht'],
        'unreachable'            => ['Lichess is not reachable right now, please try again later', 'Lichess ist gerade nicht erreichbar, bitte versuche es später nochmal'],
        'something_went_wrong'   => ['Ooops, something went wrong ...', 'Upps, da ist irgendwas schiefgelaufen ...'],
        'no_endpoint_called'     => ['Oops, no results here, please call a valid endpoint. See also https://github.com/xmgr/twitch-lichess-commands', 'Upps, leider nix gefunden, bitte rufe einen gültigen Endpoint auf. Siehe https://github.com/xmgr/twitch-lichess-commands'],
        'unknown_endpoint'       => ['Unknown endpoint "{action}" for lichess API', 'Unbekannter Endpoint "{action}" für die lichess API'],
        'test_endpoint_response' => ['Test OK - the lichess API is operating as intended', 'Test OK - die lichess API funktioniert ordnungsgemäß'],
        // Ratings
        'game_classical'         => ['Classical', 'Klassisch'],
        'game_correspondence'    => ['Correspondence', 'Fernschach'],
        'no_ratings'             => ['{user} has no rating for this selection yet', '{user} hat für diese Auswahl noch kein Rating'],
        // Playtime
        'playtime'               => ['{user} has already spent {duration} playing chess games on lichess ({games} games)', '{user} hat bereits {duration} mit Schachspielen auf lichess verbracht ({games} Partien)'],
        'never_played'           => ['{user} has never played a chess game on lichess', '{user} hat überhaupt noch nie eine Partie auf lichess gespielt'],
        // Last game
        'no_game_found'          => ['No game found', 'Es wurde keine Partie gefunden'],
        'white'                  => ['white', 'weiß'],
        'black'                  => ['black', 'schwarz'],
        'stockfish'              => ['Stockfish level {level}', 'Stockfish Stufe {level}'],
        'anonymous'              => ['Anonymous', 'Anonym'],
        'game_rated'             => ['in a rated', 'in einer gewerteten'],
        'game_unrated'           => ['in an unrated', 'in einer ungewerteten'],
        'game_label'             => ['{label} game', '{label}-Partie'],
        'took'                   => ['that took {duration}', 'die {duration} dauerte'],
        'move'                   => ['{count} move', '{count} Zug'],
        'moves'                  => ['{count} moves', '{count} Zügen'],
        'status_resign'          => [', {loser} resigned after {moves}.', ', {loser} gab nach {moves} auf.'],
        'status_outoftime'       => [', {loser} lost the game on time after {moves}.', ', {loser} hat die Partie nach {moves} auf Zeit verloren.'],
        'status_mate'            => [', {winner} mated after {moves}.', ', {winner} setzte nach {moves} matt.'],
        'status_won'             => [', {winner} won after {moves}.', ', {winner} gewann nach {moves}.'],
        'status_draw'            => ['. The game ended in a draw after {moves}.', '. Die Partie endete nach {moves} Remis.'],
        'status_stalemate'       => ['. The game ended in stalemate after {moves}.', '. Die Partie endete nach {moves} mit Patt.'],
        'status_ended'           => ['. The game ended after {moves}.', '. Die Partie endete nach {moves}.'],
        'status_timeout'         => ['. The game ended due to a timeout.', '. Die Partie endete wegen Timeout.'],
        'status_cancelled'       => ['. The game was cancelled.', '. Die Partie wurde abgebrochen.'],
        'status_cheat'           => ['. The game was ended because cheating was detected.', '. Die Partie wurde wegen Betrugs beendet.'],
        'status_started'         => ['. The game is still running.', '. Die Partie läuft noch.'],
        // Simul
        'simul_started'          => ['The ⏱ simul "{name}"{count} is already running: {url}', 'Das ⏱ Simultan "{name}"{count} läuft bereits: {url}'],
        'simul_pending'          => ['The ⏱ simul "{name}"{count} is being prepared - join in: {url}', 'Das ⏱ Simultan "{name}"{count} ist in Vorbereitung - komm mit rein: {url}'],
        'simul_created'          => ['The ⏱ simul "{name}"{count} was created, starting soon: {url}', 'Das ⏱ Simultan "{name}"{count} wurde erstellt, gleich geht\'s los: {url}'],
        'simul_finished'         => ['The ⏱ simul "{name}"{count} is already over: {url}', 'Das ⏱ Simultan "{name}"{count} ist bereits vorbei: {url}'],
        'simul_participants'     => [' ({count} participants)', ' ({count} Teilnehmer)'],
        'no_simuls'              => ['Sorry, there are no simuls to show 🤷', 'Sorry, es gibt keine anzeigbaren Simuls 🤷'],
        // Durations: singular, plural
        'day'                    => [['day', 'days'], ['Tag', 'Tage']],
        'hour'                   => [['hour', 'hours'], ['Stunde', 'Stunden']],
        'minute'                 => [['minute', 'minutes'], ['Minute', 'Minuten']],
        'second'                 => [['second', 'seconds'], ['Sekunde', 'Sekunden']],
        'and'                    => ['and', 'und'],
    ];

    private string $lang = 'de';

    /**
     * @param array $query  Query parameters ($_GET).
     * @param array $server Server variables ($_SERVER), for PATH_INFO and the Nightbot-User header.
     */
    public function __construct(private array $query, private array $server) {
        $lang       = strtolower((string)($query['lang'] ?? 'de'));
        $this->lang = in_array($lang, ['de', 'en'], true) ? $lang : 'de';
    }

    /**
     * Runs the command and returns its answer as text. Errors become a chat message too.
     */
    public function run(): string {
        try {
            [$action, $raw] = $this->command();
            [$username, $opponent, $words] = $this->input($raw);

            return match ($action) {
                'elo', 'rating' => $this->rating($username, $words),
                'playtime'      => $this->playtime($username),
                'lastgame'      => $this->lastgame($username, $opponent),
                'simul'         => $this->simul($username),
                'test'          => $this->t('test_endpoint_response'),
                ''              => throw new DomainException($this->t('no_endpoint_called')),
                default         => throw new DomainException($this->t('unknown_endpoint', ['action' => $action])),
            };
        } catch (DomainException $e) {
            return $e->getMessage();
        } catch (Throwable $e) {
            error_log('Lichess: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            return $this->t('something_went_wrong');
        }
    }

    /**
     * Action and what was typed after the command: from the path after the script ("lichess.php/rating/YOUR_USERNAME",
     * PATH_INFO), else from the "action" and "username" query parameters. A path without name uses ?username=.
     *
     * @return array{string, string}
     */
    private function command(): array {
        $path = trim((string)($this->server['PATH_INFO'] ?? ''), '/');
        [$action, $raw] = $path !== '' ? array_pad(explode('/', $path, 2), 2, '') : [(string)($this->query['action'] ?? ''), ''];
        if (trim($raw) === '') {
            $raw = (string)($this->query['username'] ?? '');
        }

        return [strtolower(trim($action)), $raw];
    }

    /**
     * Splits what was typed after the command into username, opponent ("user:opponent") and the remaining words.
     * StreamElements sends "0" for a missing argument.
     *
     * @return array{string, string, list<string>}
     */
    private function input(string $raw): array {
        $words = $this->words($raw);
        [$username, $opponent] = array_pad(explode(':', (string)array_shift($words), 2), 2, '');

        return [$this->cleanName($username), $this->cleanName($opponent), $words];
    }

    /**
     * Ratings of a user, by default classical to ultrabullet: "🔥 Blitz: 3194↗ 🚀 Bullet: 3263↗ ~ <profile url>".
     *
     * Without username the Twitch user who called the command is used (Nightbot sends it as header).
     */
    private function rating(string $username, array $words): string {
        $username = $username ?: $this->nightbotUser();
        $this->require($username);

        $user = $this->user($username);
        $url  = self::str($user, 'url');
        if (array_intersect(self::KAPPA, $words)) {
            $user = $this->user('DrNykterstein');
            $url  = 'https://lichess.org/@/' . $username;
        }

        $type = strtolower(trim((string)($this->query['type'] ?? '')));
        if ($type !== '' && $type !== '0') {
            $words = $this->words($type);
        }
        $icon  = $this->flag('icon');
        $prog  = $this->flag('prog');
        $parts = [];
        foreach ($this->selectPerfs($words) as $perf) {
            $rating = self::int($user, 'perfs.' . $perf . '.rating');
            if (!$rating) {
                continue;
            }
            $progress = self::int($user, 'perfs.' . $perf . '.prog');
            $parts[]  = ($icon ? self::PERFS[$perf][0] . ' ' : '') . $this->label($perf) . ': ' . $rating
                        . ($prog && $progress ? ($progress < 0 ? '↘' : '↗') : '');
        }
        $ratings = $parts ? implode((string)($this->query['sep'] ?? ' '), $parts) : $this->t('no_ratings', ['user' => self::str($user, 'username')]);

        return $ratings . ' ~ ' . $url;
    }

    /**
     * Time a user spent playing and the number of games.
     */
    private function playtime(string $username): string {
        $this->require($username);
        $user    = $this->user($username);
        $seconds = self::int($user, 'playTime.total');
        $name    = self::str($user, 'username');
        if (!$seconds) {
            return $this->t('never_played', ['user' => $name]) . ' ~ ' . self::str($user, 'url');
        }
        $games = self::int($user, 'count.all');

        return $this->t('playtime', [
                'user'     => $name,
                'duration' => $this->duration($seconds),
                'games'    => $this->lang === 'de' ? number_format($games, 0, ',', '.') : number_format($games),
            ]) . ' ~ ' . self::str($user, 'url');
    }

    /**
     * Result of the last game of a user, optionally against a given opponent.
     */
    private function lastgame(string $username, string $opponent): string {
        $this->require($username);
        $query = ['max' => 1] + ($opponent !== '' ? ['vs' => $opponent] : []);
        [$status, $body] = $this->api('games/user/' . rawurlencode($username) . '?' . http_build_query($query), ['Accept: application/x-ndjson']);
        if ($status === 404) {
            throw new DomainException($this->t('user_not_found', ['user' => $username]));
        }
        $first   = strtok($body, "\n");
        $game    = is_string($first) ? json_decode(trim($first), true) : null;
        $game    = is_array($game) ? $game : [];
        $players = [self::str($game, 'players.white.user.id'), self::str($game, 'players.black.user.id')];
        // Lichess ignores an unknown opponent and returns the last game against anyone
        if (self::str($game, 'id') === '' || ($opponent !== '' && !in_array($opponent, $players, true))) {
            throw new DomainException($this->t('no_game_found'));
        }

        return $this->describeGame($game);
    }

    /**
     * One game as chat text: players, rated, clock, variant, duration, result and link.
     */
    private function describeGame(array $game): string {
        $winner = self::str($game, 'winner');
        $loser  = match ($winner) {
            'white' => 'black',
            'black' => 'white',
            default => '',
        };
        $status = self::str($game, 'status');
        $perf   = $this->perfKey(self::str($game, 'perf'));

        $output = '♙ ' . $this->player($game, 'white', $winner) . ' vs. ♟ ' . $this->player($game, 'black', $winner);
        $output .= ' ' . $this->t(self::get($game, 'rated') === true ? 'game_rated' : 'game_unrated') . ' ';
        if (self::get($game, 'clock')) {
            $output .= $this->clock(self::int($game, 'clock.initial')) . '+' . self::int($game, 'clock.increment') . ' ';
        }
        $output .= implode(' ', array_filter([
            ucfirst(self::str($game, 'variant') ?: 'standard'),
            $perf !== null ? self::PERFS[$perf][0] : '',
            $this->t('game_label', ['label' => $perf !== null ? $this->label($perf) : 'Standard']),
        ]));
        if ($status !== 'started') {
            $duration = (self::int($game, 'lastMoveAt') - self::int($game, 'createdAt')) / 1000;
            $output   .= ' ' . $this->t('took', ['duration' => $this->duration($duration)]);
        }

        $moves     = trim(self::str($game, 'moves'));
        $halfmoves = $moves === '' ? 0 : count(explode(' ', $moves));
        $key       = match ($status) {
            'resign', 'outoftime'                      => $loser !== '' ? 'status_' . $status : 'status_ended',
            'mate'                                     => $winner !== '' ? 'status_mate' : 'status_ended',
            'draw', 'insufficientMaterialClaim'        => 'status_draw',
            'stalemate', 'timeout', 'started', 'cheat' => 'status_' . $status,
            'created', 'aborted', 'noStart'            => 'status_cancelled',
            default                                    => $winner !== '' ? 'status_won' : 'status_ended',
        };
        $output .= $this->t($key, [
            'winner' => $winner !== '' ? $this->t($winner) : '',
            'loser'  => $loser !== '' ? $this->t($loser) : '',
            'moves'  => $this->moves((int)ceil($halfmoves / 2)),
        ]);

        return $output . ' ~ https://lichess.org/' . self::str($game, 'id');
    }

    /**
     * The current simul of a host (running first, then pending, created and finished ones).
     */
    private function simul(string $username): string {
        $this->require($username);
        [, $body] = $this->api('simul');
        $simuls = json_decode($body, true);
        $simuls = is_array($simuls) ? $simuls : [];
        foreach (['started', 'pending', 'created', 'finished'] as $state) {
            foreach ((array)($simuls[$state] ?? []) as $simul) {
                if (!is_array($simul) || self::str($simul, 'id') === '' || strcasecmp(self::str($simul, 'host.name'), $username) !== 0) {
                    continue;
                }
                $pairings = self::int($simul, 'nbPairings');

                return $this->t('simul_' . $state, [
                    'name'  => self::str($simul, 'fullName'),
                    'count' => $pairings ? $this->t('simul_participants', ['count' => $pairings]) : '',
                    'url'   => 'https://lichess.org/simul/' . self::str($simul, 'id'),
                ]);
            }
        }
        throw new DomainException($this->t('no_simuls'));
    }

    /**
     * Lowercase words of a text, separated by spaces, commas, "|" or ";" (keeps ":" for "user:opponent").
     *
     * @return list<string>
     */
    private function words(string $text): array {
        return preg_split('/[\s,|;]+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Keeps only the characters Lichess allows in usernames, "0" counts as empty.
     */
    private function cleanName(string $name): string {
        $name = (string)preg_replace('/[^a-z0-9_-]/', '', strtolower($name));

        return $name === '0' ? '' : $name;
    }

    /**
     * Twitch user who called the command, sent by Nightbot as "Nightbot-User: name=...&displayName=...".
     */
    private function nightbotUser(): string {
        parse_str((string)($this->server['HTTP_NIGHTBOT_USER'] ?? ''), $user);

        return $this->cleanName((string)($user['name'] ?? '') ?: (string)($user['displayName'] ?? ''));
    }

    /**
     * Perf keys selected by the words: all for "all", the matching ones in output order, else the default ones.
     *
     * @return list<string>
     */
    private function selectPerfs(array $words): array {
        if (array_intersect(self::ALL, $words)) {
            return array_keys(self::PERFS);
        }
        $selected = array_keys(array_filter(self::PERFS, fn(array $perf) => (bool)array_intersect($perf[2], $words)));

        return $selected ?: self::DEFAULT_PERFS;
    }

    /**
     * Perf key in the spelling of PERFS ("ultrabullet" → "ultraBullet"), null if unknown.
     */
    private function perfKey(string $perf): ?string {
        foreach (array_keys(self::PERFS) as $key) {
            if (strcasecmp($key, $perf) === 0) {
                return $key;
            }
        }

        return null;
    }

    private function label(string $perf): string {
        return match ($perf) {
            'classical'      => $this->t('game_classical'),
            'correspondence' => $this->t('game_correspondence'),
            default          => self::PERFS[$perf][1],
        };
    }

    /**
     * Player of a game: name (Stockfish with level, "Anonym" without account), medal for the winner and rating.
     */
    private function player(array $game, string $color, string $winner): string {
        $name = self::str($game, "players.$color.user.name");
        if ($name === '') {
            $level = self::int($game, "players.$color.aiLevel");
            $name  = $level ? $this->t('stockfish', ['level' => $level]) : $this->t('anonymous');
        }
        $rating = self::str($game, "players.$color.rating");

        return $name . ($winner === $color ? '🏅' : '') . ($rating !== '' ? ' (' . $rating . ')' : '');
    }

    /**
     * Initial clock time in minutes: "3", "1.5", quarters of a minute as "¼", "½" and "¾".
     */
    private function clock(int $seconds): string {
        return match ($seconds) {
            0       => '0',
            15      => '¼',
            30      => '½',
            45      => '¾',
            default => (string)($seconds / 60),
        };
    }

    private function moves(int $count): string {
        return $this->t($count === 1 ? 'move' : 'moves', ['count' => $count]);
    }

    /**
     * Duration as text: "13 Tage, 2 Stunden und 1 Sekunde", units without value are left out.
     */
    private function duration(float|int $seconds): string {
        $rest  = (int)round(abs($seconds));
        $index = $this->lang === 'de' ? 1 : 0;
        $parts = [];
        foreach (['day' => 86400, 'hour' => 3600, 'minute' => 60, 'second' => 1] as $unit => $size) {
            $count = intdiv($rest, $size);
            $rest  %= $size;
            if ($count > 0) {
                $parts[] = $count . ' ' . self::TEXTS[$unit][$index][$count === 1 ? 0 : 1];
            }
        }
        if (!$parts) {
            return '0 ' . self::TEXTS['second'][$index][1];
        }
        $last = array_pop($parts);

        return $parts ? implode(', ', $parts) . ' ' . $this->t('and') . ' ' . $last : $last;
    }

    /**
     * Query flag that is on unless set to something else than 1/yes/on/ok/enable/true.
     */
    private function flag(string $name): bool {
        $value = $this->query[$name] ?? null;

        return $value === null || in_array(strtolower((string)$value), ['1', 'yes', 'on', 'ok', 'enable', 'true'], true);
    }

    private function require(string $username): void {
        if ($username === '') {
            throw new DomainException($this->t('username_required'));
        }
    }

    /**
     * Profile of a user (GET /api/user/{username}).
     */
    private function user(string $username): array {
        [$status, $body] = $this->api('user/' . rawurlencode($username));
        $user = $status >= 200 && $status < 300 ? json_decode($body, true) : null;
        if (!is_array($user) || self::str($user, 'username') === '') {
            throw new DomainException($this->t('user_not_found', ['user' => $username]));
        }

        return $user;
    }

    /**
     * Sends a GET request to the Lichess API. Connection errors, rate limits and server errors become a chat message,
     * other status codes are left to the caller.
     *
     * @return array{int, string} Status code and body.
     */
    private function api(string $path, array $headers = []): array {
        $curl = curl_init(self::API . $path);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_USERAGENT      => 'xmgr.de lichess commands',
        ]);
        $body   = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $failed = curl_errno($curl) !== 0;
        curl_close($curl);
        if ($failed || !is_string($body) || $status >= 500 || $status === 429) {
            throw new DomainException($this->t('unreachable'));
        }

        return [$status, $body];
    }

    /**
     * Text in the requested language, {name} placeholders replaced.
     */
    private function t(string $key, array $replace = []): string {
        $text = self::TEXTS[$key][$this->lang === 'de' ? 1 : 0] ?? $key;
        foreach ($replace as $name => $value) {
            $text = str_replace('{' . $name . '}', (string)$value, $text);
        }

        return $text;
    }

    /**
     * Value from a nested array by dot path ("players.white.user.name"), $default if missing.
     */
    private static function get(array $data, string $path, mixed $default = null): mixed {
        foreach (explode('.', $path) as $key) {
            if (!is_array($data) || !array_key_exists($key, $data)) {
                return $default;
            }
            $data = $data[$key];
        }

        return $data;
    }

    private static function str(array $data, string $path): string {
        $value = self::get($data, $path);

        return is_scalar($value) ? (string)$value : '';
    }

    private static function int(array $data, string $path): int {
        $value = self::get($data, $path);

        return is_numeric($value) ? (int)$value : 0;
    }

}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    header('Content-Type: text/plain; charset=utf-8');
    echo (new LichessCommands($_GET, $_SERVER))->run();
}
