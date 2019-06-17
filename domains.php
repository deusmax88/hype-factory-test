<?php
$domains = [];

$pdo = new Pdo("mysql:localhost;db:somedb;charset=utf8",'test', 'test');

$IdBoundaries = "SELECT MAX(`id`), MIN(`id`) FROM `users`";
$sth = $pdo->query($IdBoundaries,PDO::FETCH_NUM);
list($maxId, $minId) = $sth->fetch();

$sizeOfChunk = 1000;

$sth = $pdo->prepare("SELECT email FROM `users` WHERE `id` >= :lowerBound AND `id` < :upperBound");
while($minId <= $maxId) {
    $sth->execute([':lowerBound' => $minId, ':upperBound' => $minId + $sizeOfChunk]);
    while($email = $sth->fetchColumn(0)) {
        if (!$email) {
            continue;
        }
        $emails = explode(",", $email);
        foreach($emails as $email) {
            list(,$domain) = explode('@', $email);
            $domains[$domain]++;
        }
    }

    $minId += $sizeOfChunk;
}

foreach($domains as $domain => $numOfUsers) {
    echo "$domain has $numOfUsers users\n";
}