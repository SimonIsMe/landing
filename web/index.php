<?php

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../vendor/autoload.php';

$config = require __DIR__.'/../config.php';

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'host'      => $config['mysql']['host'],
        'dbname'    => $config['mysql']['dbname'],
        'user'      => $config['mysql']['user'],
        'password'  => $config['mysql']['password'],
        'charset'   => 'utf8mb4',
    ),
));
$app['debug'] = true;

$app->get('/', function () use ($app, $config) {
    $token = getUnusedToken($app);
    setTokenAsUsed($app, $token);
    return $app['twig']->render('index.twig', [
        'siteUrl' => 'http://' . $config['app']['domain'],
        'token' => $token
    ]);
});

$app->get('/demo', function () use ($app) {
    return $app['twig']->render('demo.twig');
});

$app->post('/register', function (Request $request) use ($app) {
    $email = $request->get('email', '');
    $app['db']->executeQuery("INSERT INTO emails (email, created_at) VALUES ('" . addslashes($email) . "', '" . Carbon::now()->format('Y-m-d H:i:s') . "')");
    return 'ok';
});

$app->post('/message', function (Request $request) use ($app) {
    $name = $request->get('name', '');
    $email = $request->get('email', '');
    $content = $request->get('content', '');

    $app['db']->executeQuery("INSERT INTO messages (first_name, email, content, created_at) VALUES ('" . $name . "', '" . $email . "', '" . $content . "', '" . Carbon::now()->format('Y-m-d H:i:s') . "')");

    return 'ok';
});

$app->get('/db/{token}', function ($token) use ($app) {
    return $app['twig']->render('db.twig');
});

$app->get('/db', function () use ($app) {
    return 'You need to specify a token';
});

$app->get('/notify', function (Request $request) use ($app) {
    var_dump($_GET, $_POST);
    return 'You need to specify a token';
});

$app->get('/chat/{token}', function ($token) use ($app) {

    $names = [
        'Szymon', 'Adam', 'Michał', 'Grzesiek', 'Ania', 'Kasia', 'Ola', 'Marta'
    ];

    return $app['twig']->render('chat.twig', [
        'token' => $token,
        'name' => $names[array_rand($names)]
    ]);
});

$app->get('/chat/{token}/{nameId}', function (Request $request, $token, $nameId) use ($app) {

    $message = $request->get('message', '');

    $names = [
        'Szymon', 'Adam', 'Michał', 'Grzesiek', 'Ania', 'Kasia', 'Ola', 'Marta'
    ];

    return $app['twig']->render('chat.twig', [
        'token' => $token,
        'message' => $message,
        'name' => $names[$nameId % count($names)]
    ]);
});


$app->get('/init-elastic', function() use ($config) {
//    $client = new Elasticsearch\Client([
//        'hosts' => [
////            'https://0c533107fb7c1fc94fd27181d5469c6b.eu-west-1.aws.found.io:9200'
//            'https://nobackend-5873056900.eu-west-1.bonsai.io'
//        ],
//        'connectionParams' => [
////            'auth' => ['admin', '87jjj7pg3t0ng2s821', 'Basic']
//            'auth' => ['otmu0djd7t', 'rvwivgdln3', 'Basic']
//        ]
//    ]);


    $client = new Elasticsearch\Client([
        'hosts' => [
            $config['bonsai']['url']
        ],
        'connectionParams' => [
            'auth' => [$config['bonsai']['login'], $config['bonsai']['password'], 'Basic']
        ]
    ]);

    $indexParams = array();
    $indexParams['index']  = 'aaa';

    if ($client->indices()->exists($indexParams))
        $client->indices()->delete($indexParams);
    $client->indices()->create($indexParams);

    $ret = $client->index([
        'index' => 'wiki',
        'type' => 'articles',
        'id' => '1',
        'body' => [
            'title' => 'winsows',
            'content' => 'winsowswsss'
        ]
    ]);
    var_dump($ret);exit;
    return 'asdf';
});

$app->get('/fill-elastic', function() use ($app, $config) {
    $articles = [
        1 => [
            'title' => 'Microsoft Windows',
            'content' => 'Rodzina systemów operacyjnych wyprodukowanych przez firmę Microsoft. Systemy rodziny Windows działają na serwerach, systemach wbudowanych oraz na komputerach osobistych, z którymi są najczęściej kojarzone.',
        ],
        2 => [
            'title' => 'Linux',
            'content' => 'Rodzina uniksopodobnych systemów operacyjnych opartych na jądrze Linux. Linux jest jednym z przykładów wolnego i otwartego oprogramowania (FLOSS): jego kod źródłowy może być dowolnie wykorzystywany, modyfikowany i rozpowszechniany.',
        ],
        3 => [
            'title' => 'PHP',
            'content' => 'Interpretowany skryptowy język programowania zaprojektowany do generowania stron internetowych i budowania aplikacji webowych w czasie rzeczywistym',
        ],
        4 => [
            'title' => 'Liczby Catalana',
            'content' => 'Szczególny ciąg liczbowy, mający zastosowanie w różnych aspektach kombinatoryki. Nazwane zostały na cześć belgijskiego matematyka Eugène Charlesa Catalana (1814–1894)[1]. Bywają również nazywane liczbami Segnera, na cześć Jána Andreja Segnera (1704-1777), matematyka pochodzącego z Karpat Niemieckich.'
        ],
        5 => [
            'title' => 'Liczby Fibonacciego',
            'content' => 'ciąg liczb naturalnych określony rekurencyjnie w sposób następujący: pierwszy wyraz jest równy 0, drugi jest równy 1, każdy następny jest sumą dwóch poprzednich.'
        ],
        6 => [
            'title' => 'Wielka Brytania',
            'content' => 'Unitarne państwo wyspiarskie położone w Europie Zachodniej. W skład Wielkiej Brytanii wchodzą: Anglia, Walia i Szkocja położone na wyspie Wielka Brytania oraz Irlandia Północna leżąca w północnej części wyspy Irlandia. Na wyspie tej znajduje się jedyna granica lądowa Zjednoczonego Królestwa z innym państwem – Irlandią. Poza nią Wielka Brytania otoczona jest przez Ocean Atlantycki na zachodzie i północy, Morze Północne na wschodzie, Kanał Angielski (ang. English Channel; fr. La Manche) na południu i Morze Irlandzkie na zachodzie.'
        ],
        7 => [
            'title' => 'Polska',
            'content' => 'Państwo unitarne w Europie Środkowej położone między Morzem Bałtyckim na północy a Sudetami i Karpatami na południu, w przeważającej części w dorzeczu Wisły i Odry. Powierzchnia administracyjna Polski wynosi 312 679 km², co daje jej 70. miejsce na świecie i 9. w Europie. Zamieszkana przez prawie 38,5 miliona ludzi (2014), zajmuje pod względem liczby ludności 34. miejsce na świecie, a 6. w Unii Europejskiej.'
        ],
        8 => [
            'title' => 'Hiszpania',
            'content' => 'Państwo w Europie Południowej, największe z trzech państw położonych na Półwyspie Iberyjskim. Na zachodzie Hiszpania graniczy z Portugalią, na południu z należącym do Wielkiej Brytanii Gibraltarem, oraz przez Ceutę i Melillę z Marokiem. Na północnym wschodzie, przez Pireneje, kraj graniczy z Francją i Andorą.'
        ],
        9 => [
            'title' => 'Włochy',
            'content' => 'Państwo położone w Europie Południowej, na Półwyspie Apenińskim, będące członkiem Unii Europejskiej oraz wielu organizacji, m.in.: NATO, należące do siedmiu najbardziej uprzemysłowionych i bogatych państw świata – G7'
        ],
        10 => [
            'title' => 'Brazylia',
            'content' => 'Największe pod względem powierzchni i liczby mieszkańców państwo Ameryki Południowej oraz piąte pod względem wielkości państwo świata. Zajmuje ponad 47,5% powierzchni Ameryki Południowej i liczy ponad 200 milionów mieszkańców.'
        ]
    ];

    $client = new Elasticsearch\Client([
        'hosts' => [
            $config['bonsai']['url']
        ],
        'connectionParams' => [
            'auth' => [$config['bonsai']['login'], $config['bonsai']['password'], 'Basic']
        ]
    ]);

    $token = generateToken($app);
    addTokenToDb($app, $token);

    foreach ($articles as $id => $article) {
        $client->index([
            'index' => 'aaa',
            'type' => 'articles',
            'id' => $id,
            'body' => $article
        ]);
    }


    $comments = [
        1 => [
            'articleId' => 1,
            '' => 'd'
        ],
        2 => [
            'title' => 'Linux',
            'content' => 'Rodzina uniksopodobnych systemów operacyjnych opartych na jądrze Linux. Linux jest jednym z przykładów wolnego i otwartego oprogramowania (FLOSS): jego kod źródłowy może być dowolnie wykorzystywany, modyfikowany i rozpowszechniany.',
        ],
        3 => [
            'title' => 'PHP',
            'content' => 'Interpretowany skryptowy język programowania zaprojektowany do generowania stron internetowych i budowania aplikacji webowych w czasie rzeczywistym',
        ],
        4 => [
            'title' => 'Liczby Catalana',
            'content' => 'Szczególny ciąg liczbowy, mający zastosowanie w różnych aspektach kombinatoryki. Nazwane zostały na cześć belgijskiego matematyka Eugène Charlesa Catalana (1814–1894)[1]. Bywają również nazywane liczbami Segnera, na cześć Jána Andreja Segnera (1704-1777), matematyka pochodzącego z Karpat Niemieckich.'
        ],
        5 => [
            'title' => 'Liczby Fibonacciego',
            'content' => 'ciąg liczb naturalnych określony rekurencyjnie w sposób następujący: pierwszy wyraz jest równy 0, drugi jest równy 1, każdy następny jest sumą dwóch poprzednich.'
        ],
        6 => [
            'title' => 'Wielka Brytania',
            'content' => 'Unitarne państwo wyspiarskie położone w Europie Zachodniej. W skład Wielkiej Brytanii wchodzą: Anglia, Walia i Szkocja położone na wyspie Wielka Brytania oraz Irlandia Północna leżąca w północnej części wyspy Irlandia. Na wyspie tej znajduje się jedyna granica lądowa Zjednoczonego Królestwa z innym państwem – Irlandią. Poza nią Wielka Brytania otoczona jest przez Ocean Atlantycki na zachodzie i północy, Morze Północne na wschodzie, Kanał Angielski (ang. English Channel; fr. La Manche) na południu i Morze Irlandzkie na zachodzie.'
        ],
        7 => [
            'title' => 'Polska',
            'content' => 'Państwo unitarne w Europie Środkowej położone między Morzem Bałtyckim na północy a Sudetami i Karpatami na południu, w przeważającej części w dorzeczu Wisły i Odry. Powierzchnia administracyjna Polski wynosi 312 679 km², co daje jej 70. miejsce na świecie i 9. w Europie. Zamieszkana przez prawie 38,5 miliona ludzi (2014), zajmuje pod względem liczby ludności 34. miejsce na świecie, a 6. w Unii Europejskiej.'
        ],
        8 => [
            'title' => 'Hiszpania',
            'content' => 'Państwo w Europie Południowej, największe z trzech państw położonych na Półwyspie Iberyjskim. Na zachodzie Hiszpania graniczy z Portugalią, na południu z należącym do Wielkiej Brytanii Gibraltarem, oraz przez Ceutę i Melillę z Marokiem. Na północnym wschodzie, przez Pireneje, kraj graniczy z Francją i Andorą.'
        ],
        9 => [
            'title' => 'Włochy',
            'content' => 'Państwo położone w Europie Południowej, na Półwyspie Apenińskim, będące członkiem Unii Europejskiej oraz wielu organizacji, m.in.: NATO, należące do siedmiu najbardziej uprzemysłowionych i bogatych państw świata – G7'
        ],
        10 => [
            'title' => 'Brazylia',
            'content' => 'Największe pod względem powierzchni i liczby mieszkańców państwo Ameryki Południowej oraz piąte pod względem wielkości państwo świata. Zajmuje ponad 47,5% powierzchni Ameryki Południowej i liczy ponad 200 milionów mieszkańców.'
        ]
    ];


    return 'ok';
});

$app->run();


function getUnusedToken($app)
{
    $row = $app['db']->fetchAll('SELECT * FROM collections WHERE is_used = 0 LIMIT 1');
    if (!empty($row))
        return $row[0]['id'];

    $row = $app['db']->fetchAll('SELECT * FROM collections WHERE is_used = 1 LIMIT 1');
    return $row[0]['id'];
}

function setTokenAsUsed($app, $token)
{
    $app['db']->executeUpdate("UPDATE collections SET is_used = 1 WHERE id = '" . $token . "'");
}

function generateToken($app)
{
    do {
        $token = md5(microtime() . random_int(0, 99999999999));
        $row = $app['db']->fetchAll('SELECT * FROM collections WHERE id=\'' . $token . '\' LIMIT 1');
    } while (!empty($row));
    return $token;
}

function addTokenToDb($app, $token)
{
    $app['db']->executeQuery("INSERT INTO collections (id) VALUES ('" . $token . "')");
}
