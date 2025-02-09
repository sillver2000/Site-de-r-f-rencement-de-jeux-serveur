<?php


if (!isset($_SESSION['idUser'])) {
    header('Location: /login');
}
$_SESSION['upload'] = 'no';


$configs = include(__DIR__ . '/../../public/config.php');
global $db;

$test = $db->prepare('SELECT * FROM serveurs WHERE token = ?');
$test->execute([$token]);
$serveur = $test->fetch();


function imgEmpty()
{
    if (isset($_FILES["fileToUpload"]["name"])) {

        if ($_FILES["fileToUpload"]["name"] == null) {
            return "l'image est requis";
        }
    }
}


$isemptyfileToUpload = empty($_FILES['fileToUpload']);
$isemptyName = empty($_POST['name']);
$isemptyDesc = empty($_POST['desc']);
$isemptyJeu = empty($_POST['jeu']);
$isemptyTAG = empty($_POST['tag']);

$issetName = isset($_POST['name']);
$issetDesc = isset($_POST['desc']);
$issetJeu = isset($_POST['jeu']);
$issetFileToUpload = isset($_FILES['fileToUpload']);
$issetVar = isset($_FILES['fileToUpload']) || isset($_POST['name']) && isset($_POST['desc']) && isset($_POST['jeu']);

$filterSite = $_POST['site'] ? filter_var($_POST['site'], FILTER_VALIDATE_URL) : 'http://localhost/';
$filterDiscord = $_POST['discord'] ? filter_var($_POST['discord'], FILTER_VALIDATE_URL) : 'http://localhost/';
$filterIp = $_POST['ip'] ? filter_var($_POST['ip'], FILTER_VALIDATE_IP) : null;
$filterPort = $_POST['port'] ? filter_var($_POST['port'], FILTER_VALIDATE_INT) : null;
$filterImg = $_FILES["fileToUpload"]["name"] ? filter_var($_FILES["fileToUpload"]["name"], FILTER_SANITIZE_STRING) : '';



if ($issetVar) {
if (empty($_FILES['fileToUpload']['name'])) {
    if (isset($_SESSION['upload']) && $_POST['modifier'] == 'submit') {
        if (!isset($_POST['h-captcha-response']) && empty($_POST['h-captcha-response'] && !isset($_POST['name']) && empty($_POST['name']) && !isset($_POST['desc']) && empty($_POST['desc']))) {
        $error8 = "Les champs sont obligatoires";
    } else if ($_SESSION['upload'] == 'no' && isset($_POST['h-captcha-response']) && !empty($_POST['h-captcha-response']) && isset($_POST['tag']) && !empty($_POST['tag'])) {
        $tag = implode(',', $_POST['tag']);


        $mofifier = $db->prepare("UPDATE serveurs SET name = ?, descri = ?, tag = ?, discord = ?, url = ?, ip = ?, port = ? WHERE token = ?");
        $mofifier->execute(array($_POST['name'], $_POST['desc'], $tag, $filterDiscord, $filterSite, $filterIp, $filterPort, $token));
        header('location: /account/menu/addserveur');

        echo 'ici';
    }


    }
}
    else if (isset($_FILES["fileToUpload"]["name"])) {
        $target_dir = __DIR__ . '/../../public/assets/img/serv/';
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);


        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


// Check if image file is a actual image or fake image
        if (isset($_POST["submit"]) && $_FILES["fileToUpload"]["name"] != '') {

            if (file_exists($target_file)) {
                $_SESSION['upload'] = 'no';
                $error1 = "Le fichier existe déjà";
            }
            if (!file_exists($target_file)) {
                $_SESSION['upload'] = 'yes';
            }


            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                $error2 = "Le fichier est une image. - " . $check["mime"] . ".";
            } else {
                $error3 = "Le fichier n'est pas une image.";

            }


// Check file size
            if ($_FILES["fileToUpload"]["size"] > 70000) {
                $_SESSION['upload'] = 'no';
                $error4 = "L'image est trop grande.";

            }

// Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif") {
                $_SESSION['upload'] = 'no';
                $error5 = "Mauvais fichier, seulement les fichiers JPG, JPEG, PNG & GIF sont accepté.";

            }

// Check if $uploadOk is set to 0 by an error
            if (isset($_SESSION['upload'])) {
                if ($_SESSION['upload'] == 'no') {
                    $error6 = "Le fichier n'a pas été uploadé.";
// if everything is ok, try to upload file
                }
            }
            if (isset($_SESSION['upload']) && $_POST['modifier'] == 'submit') {
                if ($_SESSION['upload'] == 'yes' && isset($_POST['h-captcha-response']) && !empty($_POST['h-captcha-response'])) {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        $tag = implode(',', $_POST['tag']);
                        $mofifier = $db->prepare("UPDATE serveurs SET name = ?, descri = ?, tag = ?, discord = ?, url = ?, img = ?, ip = ?, port = ? WHERE id = ?");
                        $mofifier->execute(array($_POST['name'], $_POST['desc'], $tag, $filterDiscord, $filterSite, $filterImg, $filterIp, $filterPort, $_SESSION['id']));
                        $error7 = "Le fichier " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " a été envoyé.";
                        unset($_SESSION['upload']);
                        header('location: /account/menu/addserveur');
                        echo 'la';
                    }
                }

            }

        }
    }
}

//if ($issetVar && isset($_POST['checkbox']) == true && isset($_POST['h-captcha-response']) && !empty($_POST['h-captcha-response'])) {
//
//
//    if ($AjoutServeur != null) {
//        $AjoutServeur->getVerify();
//        $AjoutServeur->addServ($_POST['name'], $_POST['desc'], $_POST['jeu'], $_POST['discord'], $_POST['site'], $token, $_POST['fileToUpload'], $_POST['ip'], $_POST['port']);
//        $mail = new PHPMailer();
//        $_SESSION['mailer'] = $mail;
//
//
//        $body = "<a href='http://localhost/confirm/cle/$cle/id/{$_SESSION['id']}'>Click to confirm</a>";
//
//        $mail->isSMTP();
//        $mail->SMTPDebug = 0;
//        $mail->SMTPAuth = true;
//        $mail->SMTPSecure = 'tls';
//        $mail->Host = 'smtp.mail.yahoo.com';
//        $mail->Port = 587;
//        $mail->Username = $configs->mailUser;
//        $mail->Password = $configs->mailPass;
//
//        $mail->setFrom($configs->mailUser, 'top-serveur-prive');
//
//        $mail->AddReplyTo($configs->mailUser, "top-serveur-prive");
//
//        $mail->Subject = "confirm your Account top-serveur-prive";
//
//        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
//
//        $mail->MsgHTML($body);
//
//        $address = $filterEmail;
//        $mail->AddAddress($address);
//        header('Location: /login');
//        if (!$mail->Send()) {
//            echo "Mailer Error: " . $mail->ErrorInfo;
//        }
//
//    }
//} else {
//    $rank = "Veuillez cocher la case et choisir un rang";


require __DIR__ . '/../view/modifier-serveur.php';