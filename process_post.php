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
            if(!empty($_POST['title']))
            {
                $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $genre =  filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                $query = "SELECT userid FROM users WHERE username = :username";
                $values = $db->prepare($query);
                $values->bindValue(':username', $_SESSION['username']);        
                $values->execute();
                $row = $values->fetch();

                function file_upload_path($original_filename, $upload_subfolder_name = 'images') 
                {
                    $current_folder = dirname(__FILE__);
                    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];       
                    return join(DIRECTORY_SEPARATOR, $path_segments);
                }
        
                function file_is_an_image($temporary_path, $new_path) 
                {
                    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
                    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
                    
                    $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
                    $actual_mime_type        = mime_content_type($temporary_path);
                    
                    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
                    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
                    
                    return $file_extension_is_valid && $mime_type_is_valid;
                }
        
                $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
                $upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);
    
                if ($image_upload_detected) 
                { 
                    $filename        = $_FILES['image']['name'];
                    $temporary_image_path  = $_FILES['image']['tmp_name'];
                    $new_image_path        = file_upload_path($filename);
                    $actual_file_extension   = pathinfo($new_image_path, PATHINFO_EXTENSION);
    
    
                    if (file_is_an_image($temporary_image_path, $new_image_path)) 
                    {
                        $imagename = $_POST['title'].'.'.$actual_file_extension;
                        move_uploaded_file($temporary_image_path, $new_image_path);

                        $image = new ImageResize($new_image_path);
                        $image -> resizeToWidth(400);
                        $image->save ('./images/'.$imagename);

                        $query = "INSERT INTO posts (userid, title, description, genre, imageName) values (:userid, :title, :description, :genre, :image)";
                        $statement = $db->prepare($query);
                        $statement->bindValue(':userid', $row['userid']);
                        $statement->bindValue(':title', $title);
                        $statement->bindValue(':description', $description);
                        $statement->bindValue(':genre', $genre);
                        $statement->bindValue(':image', $imagename);
                        $statement->execute();
                    }
                }

                else
                {
                    $query = "INSERT INTO posts (userid, title, description, genre) values (:userid, :title, :description, :genre)";
                    $statement = $db->prepare($query);
                    $statement->bindValue(':userid', $row['userid']);
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

                $query = "SELECT username FROM users WHERE username = :username LIMIT 1";
                $statement = $db->prepare($query);
                $statement->bindValue(':username', $name);
                $statement->execute();

                if($statement->rowCount() == 0)
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
                else
                {
                    $error = true;
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
    <?php elseif ($upload_error_detected): ?>
        <p>The file you uploaded was not a real file. Try uploading a proper file next time!</p>
    <?php endif ?>    
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>