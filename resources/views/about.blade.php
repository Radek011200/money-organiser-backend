<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
</head>
<x-layout>
    <x-slot:heading>
        About Page
    </x-slot:heading>
    <h1>Hello from the About Page.</h1>
</x-layout>
<body>

<main>
    <p>Welcome to our website! We are dedicated to providing quality content and services to our users.</p>
    <p>Feel free to reach out if you have any questions or feedback.</p>
</main>
<footer>
    &copy; <?php echo date("Y"); ?> Your Company Name. All Rights Reserved.
</footer>
</body>
</html>
