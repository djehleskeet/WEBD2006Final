<?php
    session_start(); 
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
                    <form action="process_post.php" method="post" class="createform" enctype="multipart/form-data"> 
                        <fieldset class="createreview">
                            <div class="create">
                            <input type="hidden" name="username" value="<?=$_SESSION['username']?>" />
                            <p>
                                <label for="title">Title</label>
                                <input name="title" id="title" />
                            </p>
                            <p>
                                <label for="description">Review</label>
                                <textarea name="description" id="description"></textarea>
                            </p>
                            <p>
                                <label for="genre">Genre</label>
                                <input name="genre" id="genre" />
                            </p>
                            <p>
                                <label for="image">Image</label>
                                <input type="file" name="image" id="image">                               
                            <p>
                                <input type="submit" name="command" value="Create" />
                            </p>
				        </fieldset>
                    </form>
                </div>
        </div>
        <?php include 'footer.php'; ?>


        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>