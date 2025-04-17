<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$id = (int)$_GET['id'];
$request = array();
$request["type"] = "get_forum_post";
$request["id"] = $id;
$response = rabbitConnect($request);

$title = $response['title'];
$user = $response['user'];
$created = $response['created'];
$description = $response['description'];
?>

<div class="container py-5">
  
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo $title; ?></h4>
                </div>
                <div class="card-body">
                    <p><strong>Posted by:</strong> <?php echo $user; ?></p>
                    <p><small class="text-muted">Posted on: <?php echo $created; ?></small></p>
                    <p><?php echo $description; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    Add Your Comment
                </div>
                <div class="card-body">
                    <form action="forum_post.php?id=<?php echo $id; ?>" method="POST">
                        <div class="mb-3">
                            <label for="comment" class="form-label">Comment</label>
                            <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    if (isset($_POST['comment'])) {
        $request = array();
        $request['type'] = 'create_forum_comment';
        $request['comment'] = $_POST['comment'];
        $request['forum_id'] = $id;
        $request['session_id'] = session_id();

        $response = rabbitConnect($request);
        header('Location: forum_post.php?id=' . $id);
        exit;
    }
    
    $request = array();
    $request['type'] = 'get_forum_comments';
    $request['forum_id'] = $id; 
    $comments = rabbitConnect($request);
    ?>

   
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h5 class="mb-3">Comments</h5>
            <?php if (!empty($comments)): ?>
                <?php foreach($comments as $comment): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p><strong>Commenter:</strong> <?php echo $comment['user']; ?></p>
                            <p><small class="text-muted"><?php echo $comment['created']; ?></small></p>
                            <p><?php echo $comment['comment']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>
        </div>
    </div>
</div>
