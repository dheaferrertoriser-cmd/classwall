<?php
session_start();
require 'db.php';

$_SESSION['visits'] = ($_SESSION['visits'] ?? 0) + 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $author = trim($_POST['author']);
    $body = trim($_POST['body']);

    if (!empty($author) && !empty($body)) {

        $stmt = $pdo->prepare(
            "INSERT INTO messages (author, body)
            VALUES (:author, :body)"
        );

        $stmt->execute([
            ':author' => $author,
            ':body' => $body
        ]);
    }

    if (isset($_POST['remember'])) {
        setcookie(
            'author_name',
            $author,
            time() + (86400 * 30),
            '/'
        );
    }

    header("Location: index.php");
    exit();
}

$savedName = $_COOKIE['author_name'] ?? '';
$search = $_GET['search'] ?? '';

if (!empty($search)) {

    $stmt = $pdo->prepare(
        "SELECT * FROM messages
        WHERE body ILIKE :search
        ORDER BY created_at DESC"
    );

    $stmt->execute([
        ':search' => "%$search%"
    ]);

} else {

    $stmt = $pdo->query(
        "SELECT * FROM messages
        ORDER BY created_at DESC"
    );
}

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>

<title>Class Wall</title>

<style>

body{
    margin:0;
    padding:40px;
    background:#0a1931;
    font-family:Arial, sans-serif;
}

.container{
    max-width:850px;
    margin:auto;
    text-align:center;
}

h1{
    color:white;
    margin-bottom:10px;
}

.counter{
    color:white;
    margin-bottom:25px;
}

.box{
    background:white;
    padding:25px;
    border-radius:15px;
    margin-bottom:25px;
    box-shadow:0px 4px 12px rgba(0,0,0,0.25);
}

input[type=text]{
    width:100%;
    padding:12px;
    border:1px solid #ccc;
    border-radius:10px;
    box-sizing:border-box;
}

textarea{
    width:100%;
    height:120px;
    padding:12px;
    border:1px solid #ccc;
    border-radius:10px;
    box-sizing:border-box;
    resize:none;
}

button{
    background:#163172;
    color:white;
    border:none;
    padding:12px 25px;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
}

button:hover{
    background:#1f4ba5;
}

.message{
    background:white;
    border-radius:12px;
    padding:15px;
    text-align:left;
    margin-bottom:15px;
    border-left:6px solid #163172;
}

.author{
    font-weight:bold;
    color:#163172;
}

.date{
    color:gray;
    font-size:12px;
}

</style>

</head>

<body>

<div class="container">

    <h1>💬 CLASS WALL</h1>

    <p class="counter">
        You've visited <?php echo $_SESSION['visits']; ?> times
    </p>

    <div class="box">

        <h2>🔍 Search Messages</h2>

        <form method="GET">

            <input
                type="text"
                name="search"
                placeholder="Search messages..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <br><br>

            <button type="submit">
                Search
            </button>

        </form>

    </div>

    <div class="box">

        <h2>✍️ Post a Message</h2>

        <form method="POST">

            <input
                type="text"
                name="author"
                placeholder="Enter your name"
                value="<?php echo htmlspecialchars($savedName); ?>"
                required
            >

            <br><br>

            <textarea
                name="body"
                placeholder="Write your message here..."
                required
            ></textarea>

            <br><br>

            <label>

                <input type="checkbox" name="remember">

                Remember my name

            </label>

            <br><br>

            <button type="submit">
                Post Message
            </button>

        </form>

    </div>

    <div class="box">

        <h2>📢 Message Wall</h2>

        <?php foreach ($messages as $message): ?>

            <div class="message">

                <div class="author">
                    <?php echo htmlspecialchars($message['author']); ?>
                </div>

                <p>
                    <?php echo htmlspecialchars($message['body']); ?>
                </p>

                <div class="date">
                    <?php echo htmlspecialchars($message['created_at']); ?>
                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

</body>
</html>