<?php
require('../lib/Pusher.php');
$config = require('../config.php');

$pusher = new Pusher($config['pusher']['auth_key'], $config['pusher']['secret'], $config['pusher']['app_id']);
echo $pusher->socket_auth($_POST['channel_name'], $_POST['socket_id']);