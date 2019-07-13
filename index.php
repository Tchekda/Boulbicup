<?php
session_start();
include_once "bootstrap.php";
date_default_timezone_set('Europe/Paris');
setlocale(LC_ALL, 'fr_FR', 'fra');

if (isset($_POST)) {
    if (isset($_POST['username']) and isset($_POST['password'])) {
        if (strtolower($_POST['username']) == 'admin'
            and password_verify($_POST['password'], '$2y$12$UZvpWqUohLjp3YTC4jAOveupIiGwfPy0bUOdeec1YL6WezJ4UF/Cy')) {
            $_SESSION['logged'] = true;
        }
    } elseif (isset($_POST['host_score']) and isset($_POST['away_score']) and isset($_POST['match_id'])) {

        /** @var Match|null $match */
        if ($match = $entityManager->getRepository('Match')->find($_POST['match_id'])) {
            $match->setFinished(isset($_POST['finished']));
            $match->setScore([intval($_POST['host_score']), intval($_POST['away_score'])]);
            $entityManager->flush();
        }
    }
}


$teams = [];
foreach ($entityManager->getRepository('Pool')->findAll() as $pool) {
    $teams[$pool->getName()] = $entityManager->getRepository('Team')->findByPool($pool);
}
$matches = $entityManager->getRepository('Match')->findAll();

?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Matchs</title>
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="css/icon.css">
    <link rel="stylesheet" href="css/materialize.css">
</head>
<body>
<nav>
    <div class="nav-wrapper">
        <a href="#" class="brand-logo">BoulbiCup</a>
        <ul class="right hide-on-med-and-down">
            <?php
            if (isset($_SESSION['logged'])) {
                ?>
                <li><a class="modal-trigger" href="logout.php">Déconnexion</a></li>
                <?php
            } else {
                ?>
                <li><a class="modal-trigger" href="#loginModal">Admin</a></li>
                <?php
            }
            ?>
        </ul>
    </div>
</nav>
<h2>Poules</h2>
<table class="responsive-table centered striped highlight">
    <thead>
    <tr>
        <?php
        $biggest = 0;
        foreach ($teams as $pool => $team) {
            if (count($teams[$pool]) > $biggest) {
                $biggest = count($teams[$pool]);
            }
            echo '<th>' . $pool . '</th>';
        } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i = 0; $i < $biggest; $i++) {
        echo '<tr>';
        /**
         * @var Pool $pool
         * @var Team[] $team
         */
        foreach ($teams as $pool => $team) {
            if (isset($team[$i])) {
                echo '<td>' . $team[$i]->getName() . '</td>';
            } else {
                echo '<td></td>';
            }
        }
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
<h2>Matchs : <?= count($matches) ?></h2>

<table class="responsive-table centered striped highlight">
    <thead>
    <tr>
        <th>Domicile</th>
        <th>Visiteur</th>
        <th>Date</th>
        <th>Status</th>
        <th>Score</th>
        <?= (isset($_SESSION['logged']) ? '<th>Valider</th>' : '') ?>
    </tr>
    </thead>
    <tbody>
    <?php
    /** @var Match $match */
    for ($i = 1;
         $i <= count($matches);
         $i++) {
        $match = $matches[$i - 1];
        ?>
        <tr>
        <td><?= $match->getHost()->getName() ?></td>
        <td><?= $match->getAway()->getName() ?></td>
        <td><?= $match->getDatetime()->format("d M Y, H:i") ?></td>

        <?php
        if (isset($_SESSION['logged'])) {
            ?>
            <form action="index.php" method="post">
                <td>
                    <p>
                        <label>
                            <input type="checkbox"
                                   name="finished" <?= ($match->isFinished() ? 'checked="checked"' : "") ?>">
                            <span>Terminé?</span>
                        </label>
                    </p>
                </td>
                <td class="inline" width="100px">
                    <input class="inline" type="number" name="host_score" value="<?= $match->getScore()[0] ?>">:<input
                            class="inline" type="number"
                            name="away_score"
                            value="<?= $match->getScore()[1] ?>">
                </td>
                <td>
                    <input type="number" name="match_id" value="<?= $match->getId() ?>" hidden>
                    <button type="submit" class="btn reen"><i class="material-icons">check</i></button>
                </td>
            </form>

            <?php
        } else {
            ?>
            <td><?= ($match->isFinished() ? 'Terminé' : 'Prévu') ?></td>
            <td><?= $match->formatScore() ?></td>
            <?php
        }

        echo '</tr>';
        if ($i % 4 == 0 and $i != 0) {
            ?>
            <tr class="blue">
                <td>Surfacage</td>
                <td>Surfacage</td>
                <td>10 min</td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>

<?php include_once 'inc/modal.html' ?>

<!-- Compiled and minified JavaScript -->
<script src="js/materialize.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var elem = document.querySelectorAll('.modal');
        M.Modal.init(elem);
    });

</script>
</body>
</html>
