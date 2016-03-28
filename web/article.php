<?php
require('../lib/Pusher.php');

$options = array(
    'cluster' => 'eu',
    'encrypted' => true
);
$pusher = new Pusher(
    '232a0606aba9004dcbe2',
    'f30b892c967d7b871919',
    '189770',
    $options
);

$data['id'] = '1123123';
$data['title'] = 'Tytuł';
$data['content'] = 'Content';
$pusher->trigger('articles_0123', 'my_event', $data);