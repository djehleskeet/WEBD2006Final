<?php

require 'connect.php';

function insert ($title, $content)
{
  $db = connect();
  $sql = "INSERT INTO posts (title, description) VALUES (:title, :description)";
  $query = $db->prepare($sql);
  $res = $query->execute(['title'=>$title, 'description'=>$content]);
  return $db->lastInsertId();
}

function update ($id, $title, $content)
{
  $db = connect();
  $sql = "UPDATE posts
          SET title = :title, description = :content
          WHERE postid = :id";
  $query = $db->prepare($sql);
  $res = $query->execute(['userid' => intval($id), 'title'=>$title, 'content'=>$content]);
  return $id;
}

function recentPosts ($limit)
{
  $db = connect();
  $sql = "SELECT p.postid, p.userid, p.title, p.description, p.date_created, u.username, u.userid
          FROM posts p
          JOIN users u ON p.userid = u.userid
          ORDER BY date_created DESC
          LIMIT :limitNum";
  $query = $db->prepare($sql);
  $query->bindParam('limitNum', $limit, PDO::PARAM_INT);
  $query->execute();
  return $query->fetchAll(PDO::FETCH_ASSOC);
}

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

function delete ($id) {
  $db = connect();
  $sql = "DELETE FROM posts
          WHERE postid = :id";
  $query = $db->prepare($sql);
  $query->bindParam('id', $id, PDO::PARAM_INT);
  $query->execute();
}

function displayDate($date)
{
  return date("F d, Y, g:i a",strtotime($date));
}

function showPost ($postid)
{
  global $db;
  global $statement;
  $query
}
?>