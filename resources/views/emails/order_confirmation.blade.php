<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Confirmation</title>
</head>
<body>
    <h2>Thank you for you Order, {{ $order->user->name }}</h2>
    <p>Your order ID: {{ $order->id }}</p>
    <p>Total Price: ${{ $order->total_price }}</p>
    <p>We will process your order soon.</p>
</body>
</html>