<?php
    require 'connect.php';
    session_start();

    $query = "SELECT p.title, p.description, p.date_created, p.genre, p.postid, u.username, p.userid FROM posts p JOIN users u ON p.userid = u.userid ORDER BY p.date_created DESC LIMIT 10";
    $values = $db->prepare($query);
    $values->execute();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Gamerate</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include 'header.php'; ?>
            <div id="content">
                <?php while ($row = $values->fetch()): ?>
                <h2 class="font-italic" class="text-dark">
                    <a href="show.php?id=<?=$row['postid']?>"><?=$row['title'] ?></a>
                </h2>
                <p>
                    Posted on <?= $row['date_created'] ?>
                </p>
                <p>
                    Written by <a href="search.php?username=<?=$row['username'] ?>"><?=$row['username'] ?></a>
                </p>
                <?php endwhile ?>
            </div>           
        </div>
        <?php include 'footer.php'; ?>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>