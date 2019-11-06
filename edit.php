<?php
    require 'connect.php';
    session_start();

    function find ($id)
        {
            global $db;
            $sql = "SELECT p.postid, p.userid, p.title, p.description, p.genre, date_created, u.username, u.userid
                    FROM posts p
                    JOIN users u ON p.userid = u.userid
                    WHERE p.postid = :id";
            $query = $db->prepare($sql);
            $query->bindParam('id', $id, PDO::PARAM_INT);
            $query->execute();
            $res = $query->fetchAll(PDO::FETCH_ASSOC);
            if (count($res) >= 1) {
                return $res[0];
            }
        return null;
        }

	$idParam = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
	$post = null;

	if ($idParam){
		$post = find($idParam);
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
    <?php if ($post != null): ?>
        <div id="wrapper">
            <?php include 'header.php'; ?>
                <div id="content">
                    <form action="process_post.php" method="post" class="editform" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="submit" />
                        <fieldset class="editreview">
                            <div class="edit">
                            <p>
                                <label for="title">Title:</label>
                                <input name="title" id="title" value="<?=$post['title']?>" />
                            </p>
                            <p>
                                <label for="description">Review:</label>
                                <textarea name="description" id="description"><?=$post['description']?></textarea>
                            </p>
                            <p>
                                <label for="genre">Genre:</label>
                                <input name="genre" id="genre" value="<?=$post['genre']?>"/>
                            </p>                               
                            <p>
                                <label for="gameimage">Image</label>
                                <input type="file" name="image">
                            </p> 
                            <p>
                                <input type="submit" name="command" value="Update" />
                            </p>
				        </fieldset>
                    </form>
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