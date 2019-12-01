<?php
    require 'connect.php';
    session_start();

    $sorted = false;

    if(isset($_GET['sort']))
    {
        $sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $sorted = true;
    }

    $query = "SELECT p.title, p.description, p.date_created, p.genre, p.postid, u.username, p.userid, p.imageName, u.admin FROM posts p JOIN users u ON p.userid = u.userid ORDER BY p.date_created DESC LIMIT 10";
    $values = $db->prepare($query);
    $values->execute();

    $queryasc = "SELECT p.title, p.description, p.date_created, p.genre, p.postid, u.username, p.userid, p.imageName, u.admin FROM posts p JOIN users u ON p.userid = u.userid ORDER BY p.date_created ASC LIMIT 10";
    $valuesasc = $db->prepare($queryasc);
    $valuesasc->execute();
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include 'header.php'; ?>
    </head>
    <body>
        <div id="wrapper">
            <div id="content">
            <?php if ($sorted): ?>
            <h1 class="font-weight-bold">Sorted by oldest posts!</h1>
                <?php while ($row = $valuesasc->fetch()): ?>
                <h2 class="font-italic" class="text-dark">
                    <a href="show.php?id=<?=$row['postid']?>"><?=$row['title'] ?></a>
                </h2>
                <p>
                    Posted on <?= $row['date_created'] ?>
                </p>
                <p>
                    Written by <a href="search.php?search=<?=$row['username'] ?>"><?=$row['username'] ?></a>
                </p>
                <?php endwhile ?>
                <a class="font-weight-bold" href="index.php?">Sort by newest posts..</a>
                <?php else: ?>
                <h1 class="font-weight-bold">Sorted by newest posts!</h1>
                <?php while ($row = $values->fetch()): ?>
                <h2 class="font-italic" class="text-dark">
                    <a  href="show.php?id=<?=$row['postid']?>"><?=$row['title'] ?></a>
                </h2>
                <p>
                    Posted on <?= $row['date_created'] ?>
                </p>
                <p>
                    Written by <a href="search.php?search=<?=$row['username'] ?>"><?=$row['username'] ?></a>
                </p>
                <?php endwhile ?>
                <a class="font-weight-bold" href="index.php?sort">Sort by oldest posts..</a>
            <?php endif ?>
            </div>           
        </div>
        <?php include 'footer.php'; ?>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>