<?php
	require 'connect.php';
	session_start();

    if(isset($_GET['id']))
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $query = "SELECT p.postid, p.title, p.description, p.genre, p.imageName, u.username FROM posts p JOIN users u ON p.userid = u.userid WHERE p.postid = :id";
        $values = $db->prepare($query);
        $values->bindValue(':id', $id);
        $values->execute();
    }

    if (empty($_SESSION['username']))
    {
    	$_SESSION['username'] = '';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<?php include 'header.php'; ?>
</head>
<body>
	<?php if (isset($_GET['id'])): ?>
		<div id="wrapper">
			<div id="header">
				<div id="content">
				<?php while ($row = $values->fetch()): ?>
					<?php if(isset($_SESSION['admin']) || $row['username'] == $_SESSION['username']): ?>
						<a href="edit.php?id=<?= $row['postid'] ?>">Edit this post!</a>
					<?php endif ?>
					<h2><?= $row['title'] ?></h2>
					<p>
						<?= $row['description']?>
					</p>
					<?php if (!empty($row['imageName'])): ?>
						<img src="./images/<?=$row['imageName']?>"alt="<?=$row['title']?>">
					<?php endif ?>
					<a href="search.php?search=<?= $row['genre'] ?>">Search similar genres like '<?= $row['genre']?>'</a>
				<?php endwhile ?>
				</div>
			</div>
            <?php include 'footer.php'; ?>
		</div>
	<?php else: ?>
		<?php header('Location: index.php'); ?>		
	<?php endif ?>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>