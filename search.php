<?php
    require 'connect.php';
    session_start();
    $error = false;

    if(isset($_GET['search']))
    {
        $string = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $string = htmlspecialchars($string);
        $string = '%'.$string.'%';


        $query = "SELECT * FROM users u JOIN posts p ON p.userid = u.userid WHERE u.username LIKE :string OR p.title LIKE :string OR p.genre LIKE :string";
        $statement = $db->prepare($query);
        $statement->bindValue(':string', $string);
        $statement->execute();
    }
    else
    {
        $error = true;
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <?php include 'header.php'; ?>
    </head>
    <body>
        <?php if(!$error): ?>
        <div id="wrapper">
            <div id="content">
                <ul>
                    <?php while ($row = $statement->fetch()): ?>
                       <li><a href="show.php?id=<?=$row['postid']?>"><?=$row['title'] ?></a></li> 
                    <?php endwhile ?>
                </ul>
                <?php endif ?>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </body>
</html>