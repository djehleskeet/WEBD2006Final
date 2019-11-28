<?php
    require 'connect.php';
    session_start();

    $query = "SELECT * FROM users";
    $values = $db->prepare($query);
    $values->execute();

?>

<!DOCTYPE html>
<html>
    <head>
		<?php include 'header.php'; ?>
    </head>
    <body>
    	<?php if(isset($_SESSION['admin'])): ?>
        <div id="wrapper">
            <div id="content">
                <form action="process_post.php" method="post" class="createform" enctype="multipart/form-data"> 
                	<select name='userid'>
                		<option> -- select an option -- </option>
                				<?php while ($row = $values->fetch()): ?>
                                    <?php if ($row['admin'] == 0): ?>
                					   <option value="<?= $row['userid'] ?>"><?= $row['username'] ?></option>
                                    <?php endif ?>
                				<?php endwhile ?>
                				<input type="submit" name="command" value="Delete User"/>
                	</select>
	             </form>
	             <form action="process_post.php" method="post" class="createform" enctype="multipart/form-data"> 
                        <fieldset class="createuser">
                            <div class="create">
                            <p>
                                <label>Username</label>
                                <input name="createduser" id="createduser" />
                            </p>
                            <p>
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password"></textarea>
                            </p>
                            <p>
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" />
                            </p>
                        	</div>
                                <input type="submit" name="command" value="Add User" />
                            </p>
				        </fieldset>
                </form>
            </div>
        </div>
        	<?php else: ?>
				<?php header('Location: index.php'); ?>		
			<?php endif ?>
        <?php include 'footer.php'; ?>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>