<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <button id="payButton">Pay Now</button>

    <script>
        $(document).ready(function() {
            $("#payButton").click(function() {
                // 发送 POST 请求到 localhost/api/get，包含请求体
                $.post("http://localhost/api/get", {"amount": 1000}, function(data) {
                    // 成功后获取返回的 URL
                    let redirectUrl=JSON.parse(data.error);

                    // 使用返回的 URL 进行重定向
                    window.location.href = redirectUrl;
                });
            });
        });
    </script>
</body>
</html>
