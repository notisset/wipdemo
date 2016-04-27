<?php
require_once('Post.php');

$msg = '';
if(isset($_GET['action'])){
    switch($_GET['action']){
        case 'create':
            if(isset($_POST['content'])){
                $newPost = new Post(['content'=>$_POST['content']]);
                $newPost->create();
                $msg = "post inserted succesfully";
            }else{
                $msg = "missing post content";
            }
            break;
        case 'delete':
            if(isset($_GET['id'])){
                $deadPost = new Post();
                if($deadPost->find($_GET['id'])){
                    $deadPost->delete();
                    $msg = "post deleted succesfully";
                }else{
                    $msg = "post not found";
                }
            }else{
                $msg = "missing id field";
            }
            break;
        default:
            $msg = "unsupported action";
            break;
    }
}

$post = new Post();
$allposts = $post->all();
?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title>WipDemo y'all!</title>
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

    <h2>Insert new post</h2>

    <form action="index.php?action=create" method="post">
        <input type="text" name="content" id="content" placeholder="insert the post content"/>
        <input type="submit" name="create" id="create" value="insert"/>
    </form>
    <h2>Post list</h2>
    <table>
        <thead>
            <tr>
                <th>id</th>
                <th>content</th>
                <th>creation datetime</th>
                <th>edit</th>
                <th>delete</th>
            </tr>
        </thead>
        <tbody>
    <?php
        if(count($allposts)){
            foreach($allposts as $post){
                echo(join("",[
                    '<tr>',
                        '<td>',
                            $post->id,
                        '</td>',
                        '<td>',
                            htmlspecialchars($post->content),
                        '</td>',
                        '<td>',
                            $post->created_at,
                        '</td>',
                        '<td>',
                            '<a href="view.php?id=',
                            $post->id,
                            '">edit</a>',
                        '</td>',
                        '<td>',
                            '<a href="index.php?action=delete&id=',
                            $post->id,
                            '">delete</a>',
                        '</td>',
                    '</tr>'
                ]));
            }
        }else{
            echo("<p>No post here.</p>");
        }
    ?>
        </tbody>
    </table>
    <hr/>
    <h4><a href="https://github.com/notisset/wipdemo">GitHub Repo</a></h4>
</body>
</html>