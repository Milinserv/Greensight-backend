<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

try {
    if (isset($_POST['name'])
        && isset($_POST['surname'])
        && isset($_POST['email'])
        && isset($_POST['password'])
        && isset($_POST['passwordConfirm'])) {

        $errors = validation($_POST['email'], $_POST['password'], $_POST['passwordConfirm']);
        $users = json_decode(file_get_contents('../dataObject/users.json'));
        $logFile = '../log/log.txt';
        $usersFile = '../dataObject/users.json';

        if (!$errors) {
            if (isEmail($users, $_POST['email'])) {
                $log = date('Y-m-d H:i:s') .
                    ' Пользователь с таким Email: ' . $_POST['email'] . ' существует';

                logging($logFile, $log); //логируем попытку входа

                echo json_encode(array('error' => 'Пользователь с таким Email существует'), JSON_UNESCAPED_UNICODE);
                return;
            } else {
                $log = date('Y-m-d H:i:s') .
                    ' Пользователь ' . $_POST['name'] . ' ' . $_POST['surname'] . ' зарегистрирован c Email: ' . $_POST['email'];

                logging($logFile, $log); //логируем успешную регистрацию

                //добавление зарегистрированного пользователя в файл users.json
                $file = json_decode(file_get_contents($usersFile, JSON_UNESCAPED_UNICODE));
                $dataUser = ["id" => nextId($users), "name" => $_POST['name'], "surname" => $_POST['surname'], "email" => $_POST['email']];
                $file[] = $dataUser;
                $json_string = json_encode($file, JSON_UNESCAPED_UNICODE);
                file_put_contents($usersFile, $json_string);

                echo json_encode(array('state' => 'Пользователь зарегистрирован'), JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(array('error' => $errors), JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(array('error' => 'Все поля обязательны для заполнения'), JSON_UNESCAPED_UNICODE);
    }
} catch (exception $e) {
    echo "Непредвиденная ошибка";
}

function validation($email, $password, $passwordConfirm): array
{
    $error = array();
    if (!str_contains($email, "@")) {
        $error[] = 'Поле Email не содержит @';
    }
    if ($password !== $passwordConfirm) {
        $error[] = 'Пароли не совпадают';
    }
    return $error;
}

function isEmail($users, $email): bool
{
    $isEmail = false;
    foreach ($users as $user) {
        if ($user->email == $email) {
            $isEmail = true;
        }
    }
    return $isEmail;
}

function nextId($users): int
{
    $id = 0;
    foreach ($users as $user) {
        $id = $user->id + 1;
    }
    return $id;
}

function logging($file, $log): void
{
    if (file_exists($file)) {
        file_put_contents($file, $log . PHP_EOL, FILE_APPEND);
    } else {
        fopen($file, 'w');
        file_put_contents($file, $log . PHP_EOL, FILE_APPEND);
    }
}