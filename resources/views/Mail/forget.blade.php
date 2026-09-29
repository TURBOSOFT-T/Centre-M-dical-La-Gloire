<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{ \App\Helpers\TranslationHelper::TranslateText('Vérification de votre compte ') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e0e0e0;
        }
        .header {
            text-align: center;
            background-color: #fed000;
            padding: 20px;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            padding: 20px;
        }
        .content h1 {
            color: #578b07;
        }
        .code {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 24px;
            color: #ffffff;
            background-color: #578b07;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            padding: 10px;
            background-color: #f4f4f4;
            border-top: 1px solid #e0e0e0;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
           {{--  <img src="https://agrihub.online/icons/logo%20png.png" alt="AGRIHUB Logo"> --}}
        </div>
        <div class="content">
            <h1>
                {{ \App\Helpers\TranslationHelper::TranslateText('Vérification de votre compte') }}
            </h1>
            <p>Bonjour
                {{ \App\Helpers\TranslationHelper::TranslateText('Bonjour') }} {{ $user->nom  }} ,</p>
            <p>{{ \App\Helpers\TranslationHelper::TranslateText('Merci de vous être inscrit sur Ben Mahmoud Market. Pour finaliser votre inscription et vérifier votre compte, veuillez utiliser le code de vérification ci-dessous
                ') }} :</p>
            <span class="code">{{$code }}</span>
            <p>
                {{ \App\Helpers\TranslationHelper::TranslateText('Si vous n\'avez pas demandé cette vérification, veuillez ignorer cet email') }}.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} 
                {{ \App\Helpers\TranslationHelper::TranslateText('Ben Mahmoud Market. Tous droits réservés') }}.</p>
        </div>
    </div>
</body>
</html>
                     