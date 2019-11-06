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
            if(!empty(trim($_POST['title'])))
            {
                function file_upload_path($original_filename, $upload_subfolder_name = 'images')
                {
                    $current_folder = dirname(__FILE__);
                    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
                    return join(DIRECTORY_SEPARATOR, $path_segments);
                }

                $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);

                if ($image_upload_detected)
                {
                    $image_filename = $_FILES['image']['name'];
                    $temporary_image_path = $_FILES['image']['tmp_name'];
                    $new_image_path = file_upload_path($image_filename);

                    move_uploaded_file($temporary_image_path, $new_image_path);

                    $image = new ImageResize($new_image_path);
                    $image->resizeToWidth(75);
                    $image->save('./images/'.pathinfo($image_filename, PATHINFO_FILENAME).'_thumbnail.'.pathinfo($image_filename, PATHINFO_EXTENSION));

                    $image = new ImageResize($new_image_path);
                    $image->save('./images/'.$image_filename);
                }

                $query = "SELECT userid FROM users where username = :username";
                $values = $db->prepare($query);
                $values->bindValue(':username', $_SESSION['user']['name']);
                $values->execute();
                $row = $values->fetch();

                if(!isset($_FILES['image']))
                {
                    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $genre =  filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $query = "INSERT INTO posts (userid, title, description, genre) values (:userid, :title, :description, :genre)";
                    $statement = $db->prepare($query);
                    $statement->bindValue(':userid', $_SESSION['userid']);
                    $statement->bindValue(':title', $title);
                    $statement->bindValue(':description', $description);
                    $statement->bindValue(':genre', $genre);
                    $statement->execute();
                }

                header('Location: index.php');

            }
            else
            {
                $error = true;
            }
        }

        if($_POST['command'] == 'Login')
        {
            if(!empty(trim($_POST['name'])))
            {
                $name         = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $password     = $_POST['password'];
                $query = "SELECT username, password FROM users WHERE username = (:name)";
                $values = $db->prepare($query);
                $values->bindValue(':name', $name);
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
        }

        if($_POST['command'] == 'Logout')
        {
            session_destroy();
            header('Location: index.php');
        }

        if($_POST['command'] == 'Delete')
        {
            if(filter_var($_POST['action'], FILTER_VALIDATE_INT))
            {
                $id = $_POST['action'];
                $query = "DELETE FROM posts WHERE postid = :postid";
                $statement = $db->prepare($query);
                $statement->bindValue(':postid', $id, PDO::PARAM_INT);
                $statement->execute();

                header('Location: index.php');
            } else
            {
                header('Location: index.php');
            }
        }

        if($_POST['command'] == 'Update')
        {
            if (filter_var($_POST['action'], FILTER_VALIDATE_INT))
            {
                if(!empty(trim($_POST['title'])) && !empty(trim($_POST['description'])))
                {
                    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $content = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $id = $_POST['action'];

                    $query = "UPDATE posts SET title = :title, description = :description WHERE id = :id";
                    $statement = $db->prepare($query);
                    $statement->bindValue(':title', $title);        
                    $statement->bindValue(':content', $content);
                    $statement->bindValue(':id', $id, PDO::PARAM_INT);
                    
                    $statement->execute();
                    header('Location: index.php');
                }
                else
                {
                    $error = true;
                }
            } else{
                header('Location: index.php');
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
                    $error = false;
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
        <p>An error has occurred, try to do what you were doing again. <a href="index.php">Return</a></p>
    </div>
    <?php endif ?>
</body>
</html>