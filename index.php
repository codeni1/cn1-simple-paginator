<?php
include 'Paginator.php';
include 'conn.php';
$conn = new mysqli('localhost', $username,  $password,$database);

$limit = 10;
$query = "SELECT * FROM data";
$queryResult = mysqli_query($conn, $query);
$totalRows = mysqli_num_rows($queryResult);

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$paginator = new Paginator($conn, $query);
$data = $paginator->getData($limit, $page);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Paginator</title>
    <style>
        body {
            font-family: Arial;
            font-size: 18px;
        }
        ul.pagination {
            margin-left: -40px;
        }

        ul.pagination li {
            padding:5px;
        }
        ul.pagination li a {
            text-decoration: none;
        }
    </style>
</head>
<body>
<h2>Simple Paginator by <a href="https://codeni1.com/" target="_blank">Codeni1</a></h2>
<?php
echo $paginator->createNumberLinks();
?>
<table border="1">
    <tr>
        <td>id</td>
        <td>data</td>
    </tr>
    <?php
        foreach ($data as $item) {
            ?>
                <tr>
                    <td><b><?php echo $item['id'] ?></b></td>
                    <td><?php echo $item['value'] ?></td>
                </tr>
            <?php
        }
    ?>
</table>

<?php
echo $paginator->createNumberLinks();
?>
</body>
</html>
