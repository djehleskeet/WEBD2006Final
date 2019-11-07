<?php
    require 'connect.php';
    include 'ImageResize.php';
    include 'ImageResizeException.php';
    use Gumlet\ImageResize;

    $error = false;
    session_start();

    if(isset($_POST['command']))
    {
        if($_POST['command'] == 'Create')
        {
            $query = "SELECT userid FROM users WHERE username = :username";
            $values = $db->prepare($query);
            $values->bindValue(':username', $_SESSION['username']);        
            $values->execute();
            $row = $values->fetch();

            if(!empty($_POST['title'] && !empty($_POST['description']) && !empty($_POST['genre'])))
            {

                    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $genre =  filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $query = "INSERT INTO posts (userid, title, description, genre) values (:userid, :title, :description, :genre)";
                    $statement = $db->prepare($query);
                    $statement->bindValue(':userid', $row['userid']);
                    $statement->bindValue(':title', $title);
                    $statement->bindValue(':description', $description);
                    $statement->bindValue(':genre', $genre);
                    $statement->execute();
                    header('Location: index.php');
            }
            else
            {
                $error = true;
            }
        }

        if($_POST['command'] == 'Login')
        {
            if(!empty($_POST['username']))
            {
                $name         = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $password     = $_POST['password'];
                $query = "SELECT username, password FROM users WHERE username = :username";
                $values = $db->prepare($query);
                $values->bindValue(':username', $name);
                $values->execute();
                $row = $values->fetch();

                if($name == $row['username'] && password_verify($password, $row['password']))
                {
                    $_SESSION['username'] = $name;
                    $_SESSION['loggedin'] = true;
                    header('Location: index.php');
                }
                else
                {
                    $error = true;
                }
            }
            $error = true;
        }

        if($_POST['command'] == 'Logout')
        {
            session_destroy();
            header('Location: index.php');
        }

        if($_POST['command'] == 'Delete')
        {
            if(filter_var($_POST['id'], FILTER_VALIDATE_INT))
            {
                $id = $_POST['id'];
                $query = "DELETE FROM posts WHERE postid = :id";
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->execute();

                header('Location: index.php');
            } 
            else
            {
                $error = true;
            }
        }

        if($_POST['command'] == 'Update')
        {
            if (filter_var($_POST['id'], FILTER_VALIDATE_INT))
            {
                if(!empty(trim($_POST['title'])) && !empty(trim($_POST['description'])))
                {
                    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $content = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $id = $_POST['id'];                    
    
                    $query = "UPDATE posts SET title = :title, description = :description, genre = :genre WHERE postid = :id";
                    $statement = $db->prepare($query);
                    $statement->bindValue(':title', $title);        
                    $statement->bindValue(':description', $content);
                    $statement->bindValue(':genre', $genre);
                    $statement->bindValue(':id', $id, PDO::PARAM_INT);

                    $statement->execute();    
                    header('Location: index.php');
                    }
                    else
                    {
                        $error = true;
                    }                
            }else
            {
                $error = true;
            }
        }

        if($_POST['command'] == 'Register')
        {
            if (!empty($_POST['username']) || (!empty($_POST['password'])))
            {
                $name = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                if(!isset($_POST['username'], $_POST['password'], $_POST['email']))
                {
                    $error = true;
                    header('Location: process_post.php');
                }else
                {
                    $query = "INSERT INTO users (username, password, email) values (:username, :password, :email)";
                    $statement = $db->prepare($query);
                    $statement->bindValue(':username', $name);
                    $statement->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
                    $statement->bindValue(':email', $email);
                    $statement->execute();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $name;
                    header('Location: index.php');
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Processing..</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
    <?php if($error): ?>
    <div id="wrapper">
        <p><a href="index.php">An error has occurred, click here to return to the home page. </a></p>
    </div>
    <?php endif ?>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>