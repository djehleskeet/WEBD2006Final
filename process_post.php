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
                $values->bindValue(':username', $_SESSION['username']);
                $value->execute();
                $row = $values->fetch();

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
                $query = "SELECT username, password FROM users WHERE username = (:user)";
                $values = $db->prepare($query);
                $values->bindValue(':user', $name);
                $values->execute();
                $row = $values->fetch();
                if($name == $row['username'] && password_verify($password, $row['password']))
                {
                    $_SESSION['user'] = [ 'name' => $name, 'password' => $password ];
                }
                header('Location: index.php');
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

        if($_POST['command'] == 'Register')
        {
            if (!empty($_POST['username']) || (!empty($_POST['password'])))
            {
                $name = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $password = $_POST['password'];
                if(!isset($_POST['username'], $_POST['password']))
                {
                    header('Location: register.php');
                }else
                {
                    $query = "INSERT INTO users (username, password) values (:username, :password)";
                    $statement = $db->prepare($query);
                    $statement->bindValue(':username', $name);
                    $statement->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
                    $statement->execute();
                    $_SESSION['user'] = [ 'user' => $name, 'pass' => $password];
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

</body>
</html>