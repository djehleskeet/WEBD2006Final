<?php
    require 'connect.php';

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
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro&display=swap" rel="stylesheet">
</head>
<body>
	<?php if ($post != null): ?>
		<div id="wrapper">
			<div id="header">
                <?php include 'header.php'; ?>
			</div>
			<div id="all_blogs">
				<div class="blog_post">
					<h2><?= $post['title'] ?></h2>
					<div class="blog_content">
						    <?php foreach (preg_split('/(\n|\r\n)/', $post['description']) as $para): ?>
						      <p>
						        <?=$para?>
						      </p>
						    <?php endforeach; ?>
                        <a href="search.php?=<?= $post['genre'] ?>"><?= $post['genre'] ?></a>
					</div>
				</div>
			</div>
            <?php include 'footer.php'; ?>
		</div>
	<?php else: ?>
		<?php header('Location: index.php'); ?>		
	<?php endif ?>
</body>
</html>