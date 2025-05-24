<!DOCTYPE html>
<html lang="en">
<head>
    {{ csrf_token() }}

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt</title>
    <link rel="stylesheet" href="/path/to/your/styles.css">
    <style>

        .contact-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #4a4a4a;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            outline: none;
            resize: vertical;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        textarea:focus {
            border-color: #0073e6;
            box-shadow: 0 0 5px rgba(0, 115, 230, 0.5);
        }

        button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #0073e6;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>
<x-layout>
    <x-slot:heading>
        About Page
    </x-slot:heading>
    <h1>Hello from the Contact Page.</h1>
</x-layout>
<div class="contact-container">
    <h1>Skontaktuj się z nami</h1>
    <form action="/contact/send" method="POST">
        <div class="form-group">
            <label for="name">Imię i nazwisko:</label>
            <input type="text" id="name" name="name" placeholder="Twoje imię i nazwisko" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" placeholder="Twój e-mail" required>
        </div>

        <div class="form-group">
            <label for="message">Wiadomość:</label>
            <textarea id="message" name="message" rows="4" placeholder="Treść Twojej wiadomości" required></textarea>
        </div>

        <button type="submit">Wyślij</button>
    </form>
</div>
</body>
</html>
