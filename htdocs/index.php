<?php
session_start();

$servername = "localhost";
$username = "kudlay"; 
$password = "a5EzA3ad"; 
$dbname = "kudlay";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    $_SESSION['fio'] = $_POST['fio'];
    $_SESSION['faculty'] = $_POST['faculty'];
    $_SESSION['group_number'] = $_POST['group_number'];
    $_SESSION['education_form'] = $_POST['education_form'];
    $_SESSION['education_type'] = $_POST['education_type'];
    $_SESSION['phone'] = $_POST['phone'];
    $_SESSION['email'] = $_POST['email'];
    $step = 2;
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $fio = $_SESSION['fio'];
    $faculty = $_SESSION['faculty'];
    $group_number = $_SESSION['group_number'];
    $education_form = $_SESSION['education_form'];
    $education_type = $_SESSION['education_type'];
    $phone = $_SESSION['phone'];
    $email = $_SESSION['email'];

    $base_dir = "uploads/" . $fio;
    $user_dir = $base_dir;
    $suffix = 1;

    while (is_dir($user_dir)) {
        $user_dir = $base_dir . "_" . $suffix;
        $suffix++;
    }

    if (!is_dir($user_dir)) {
        mkdir($user_dir, 0777, true);
    }

    function uploadFile($file, $dir) {
        $unique_name = uniqid() . "_" . basename($file["name"]);
        $target_file = $dir . "/" . $unique_name;
        move_uploaded_file($file["tmp_name"], $target_file);
        return $unique_name;
    }

    $passport_photo = uploadFile($_FILES['passport_photo'], $user_dir);
    $form_086u_photo = uploadFile($_FILES['form_086u_photo'], $user_dir);
    $mor_photo = uploadFile($_FILES['mor_photo'], $user_dir);
    $hiv_aids_photo = uploadFile($_FILES['hiv_aids_photo'], $user_dir);

    $sql = "INSERT INTO students (fio, faculty, group_number, education_form, education_type, phone, email, folder_name, passport_photo, form_086u_photo, mor_photo, hiv_aids_photo)
            VALUES ('$fio', '$faculty', '$group_number', '$education_form', '$education_type', '$phone', '$email', '$user_dir', '$passport_photo', '$form_086u_photo', '$mor_photo', '$hiv_aids_photo')";

    if ($conn->query($sql) === TRUE) {
        $step = 3;
        session_unset();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    $step = 1;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>КубГАУ</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/icon" href="icon.ico">
</head>
<body>
<div class="container">
    <?php if ($step == 1): ?>
        <h1 style="display: flex; justify-content: center; align-items: center;">
            <img src="icon.png" alt="Icon" style="width: 80px; height: 115px; margin-right: 10px;">
        </h1>
        <h1>Заполните информацию о себе</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="fio" placeholder="ФИО" required>
            <input type="text" name="faculty" placeholder="Факультет" required>
            <input type="text" name="group_number" placeholder="Номер группы" required>
            <input type="text" name="education_form" placeholder="Форма обучения" required>
            <input type="text" name="education_type" placeholder="Вид обучения" required>
            <input type="text" name="phone" placeholder="Телефон" required>
            <input type="email" name="email" placeholder="Почта" required>
            <button type="submit" name="save">Сохранить</button>
        </form>
    <?php elseif ($step == 2): ?>
        <h1 style="display: flex; justify-content: center; align-items: center;">
            <img src="icon.png" alt="Icon" style="width: 80px; height: 115px; margin-right: 10px;">
        </h1>
        <h1>Загрузка фотографий</h1>
        <form method="post" enctype="multipart/form-data">
            <label for="passport_photo">Фото паспорта</label>
            <input type="file" name="passport_photo" id="passport_photo" required><br><br>
            <label for="form_086u_photo">Фото формы 086у</label>
            <input type="file" name="form_086u_photo" id="form_086u_photo" required><br><br>
            <label for="mor_photo">Фото медицинского осмотра</label>
            <input type="file" name="mor_photo" id="mor_photo" required><br><br>
            <label for="hiv_aids_photo">Фото теста на ВИЧ/СПИД</label>
            <input type="file" name="hiv_aids_photo" id="hiv_aids_photo" required><br><br>
            <button type="submit" name="submit">Отправить</button>
        </form>
    <?php elseif ($step == 3): ?>
        <h1 style="display: flex; justify-content: center; align-items: center;">
            <img src="icon.png" alt="Icon" style="width: 80px; height: 115px; margin-right: 10px;">
        </h1>
        <h1>Спасибо за заявку</h1>
        <p>Ваши данные были успешно отправлены.</p>
    <?php endif; ?>
</div>
</body>
</html>
