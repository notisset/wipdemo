<?php
require_once('Post.php');

$msg = '';
if(isset($_GET['id'])){
    $post = new Post();
    if($post->find($_GET['id'])){
        if(isset($_POST['content'])){
            $post->content = $_POST['content'];
            $post->save();
            header('location: index.php');
         }
    }else{
        $msg = "post not found";
    }
}else{
    $msg = "you need to specify a valid id";
}

?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title>WipDemo - view post</title>
    <meta name="description" content="WipDemo simple crud with pdo">
    <meta name="author" content="Notisset">

</head>

<body>
<h1><a href="index.php">WipDemo</a></h1>
<p>
    <?php
    echo $msg;
    ?>
</p>

<h2>Edit post</h2>
<?php
    if($post->isLinked()){
        echo(join("",[
            '<form action="view.php?id=',
            $post->id,
            '&action=edit" method="post">',
                '<input type="text" name="content" id="content" value="',
            htmlspecialchars($post->content),
            '"/>',
                '<input type="submit" name="update" id="update" value="update"/>',
            '</form>'
        ]));
    }
?>
<hr/>
<h4><a href="https://github.com/notisset/wipdemo">GitHub Repo</a></h4>
</body>
</html>