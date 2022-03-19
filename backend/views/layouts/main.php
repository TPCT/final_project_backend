<?php

use helpers\Helper;
use helpers\Helpers;
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <title>TPCT POD BOT CONTROLLER</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/assets/css/main-layout/main.css" />
</head>

<body class="bg-dark">
    <div class="loading" id="loading">
        <div class="bg-image" id="loading-image"></div>

        <div class="bg-text">
            <h1 id="loading-text">برجاء الانتظار حتي يتم تحميل البيانات</h1>
        </div>
    </div>

    <div class="container-fluid bg-dark" id="mainContainer">
        <section class="sticky-top">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark d-flex align-items-center justify-content-between border-bottom">
                <div class="container-fluid pt-2 w-100" >
                    <a class="navbar-brand d-flex" href="#">
                        TPCT POD BOT CONTROLLER<span> </span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item pe-1">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === "/admin/dashboard" ? "active" : "" ?>" aria-current="page" href="/admin/dashboard">المتحكم</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle me-5" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-fill"></i>
                                    <?= $_SESSION['user_info']['username'] ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item text-center" href="/logout">تسجيل خروج</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </section>

        <div class="bg-dark container-fluid d-flex flex-column justify-content-center align-items-center h-100 overflow-auto ">
            <section class="container-fluid h-100" style="max-height: 90vh">{{content}}</section>
            <div class="container-fluid-xxl d-flex justify-content-center bg-dark text-light p-2 fixed-bottom" style="z-index: -1;"><span>all copyrights reserved for <a>Th3 Professional Cod3r<a></span></div>
        </div>
    </div>
    <script src="/assets/js/common/main.js"></script>    
</body>

</html>