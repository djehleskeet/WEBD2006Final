<?php
    require 'connect.php';
    session_start();

    if(isset($_GET['id']))
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $query = "SELECT postid, title, description, genre, imageName FROM posts WHERE postid = :id";
        $values = $db->prepare($query);
        $values->bindValue(':id', $id);
        $values->execute();
    }

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
    <?php if(isset($_SESSION['loggedin'])): ?>
        <div id="wrapper">
            <?php include 'header.php'; ?>
                <div id="content">
                <?php while ($row = $values->fetch()): ?>
                    <form action="process_post.php" method="post" class="editform" enctype="multipart/form-data">
                        <fieldset class="editreview">
                            <div class="edit">
                                <input type="hidden" name="id" value="<?=$row['postid']?>" />
                            <p>
                                <label for="title">Title:</label>
                                <input name="title" id="title" value="<?=$row['title']?>" />
                            </p>
                            <p>
                                <label for="description">Review:</label>
                                <textarea name="description" id="description"><?=$row['description']?></textarea>
                            </p>
                            <p>
                                <label for="genre">Genre:</label>
                                <input name="genre" id="genre" value="<?=$row['genre']?>"/>
                            </p>                               
                            <p>
                                <input type="submit" name="command" value="Update" />
                            </p>
                            <p>
                                <input type="submit" name="command" value="Delete" />
                            </p>
				        </fieldset>
                    </form>
                    <?php endwhile ?>
                </div>
        </div>
        <?php include 'footer.php'; ?>


        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <?php else: ?>
		  <?php header('Location: index.php'); ?>		
	<?php endif ?>
    </body>
</html>