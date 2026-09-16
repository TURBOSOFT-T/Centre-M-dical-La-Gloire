<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{ \App\Helpers\TranslationHelper::TranslateText('Bienvenue ') }}
    </title>
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
        .footer {
            text-align: center;
            padding: 10px;
            background-color: #f4f4f4;
            border-top: 1px solid #e0e0e0;
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #578b07;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
       {{--  <div class="header">
            <img src="https://agrihub.online/icons/logo%20png.png" alt="AGRIHUB Logo">
        </div> --}}
        <div class="content">
            <h1>
                {{ \App\Helpers\TranslationHelper::TranslateText('Bienvenue chez Ben Madmoud Market !') }}
            </h1>
            <p> {{ \App\Helpers\TranslationHelper::TranslateText('Bonjour') }}{{ $user->nom }} ,</p>
            <p> {{ \App\Helpers\TranslationHelper::TranslateText('Votre email est') }} : {{ $user->email }} ,</p>
            <p> {{ \App\Helpers\TranslationHelper::TranslateText('Votre mot de passe est') }} : {{ $user->phone }} ,</p>

            <p>
                {{ \App\Helpers\TranslationHelper::TranslateText('Nous sommes ravis de vous compter parmi nos nouveaux membres. Chez Ben Mahmoud Market, nous nous engageons à vous fournir le meilleur  pour répondre à tous vos besoins.
               ') }} 
            </p>
            <p>
                {{ \App\Helpers\TranslationHelper::TranslateText('Votre inscription a été réalisée avec succès. Vous pouvez dès à présent explorer notre large gamme de produits et profiter de nos offres exclusives réservées aux membres.
                ') }}
            </p>
            <a href="#" class="button">
                {{ \App\Helpers\TranslationHelper::TranslateText('Découvrir nos produits') }}
            </a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} 
                {{ \App\Helpers\TranslationHelper::TranslateText(' Tous droits réservés') }}.</p>
        </div>
    </div>
</body>
</html>
