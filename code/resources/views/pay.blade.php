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
                // 要記得換token
                $.ajax({
                    url: "http://localhost:8082/api/member/wallets/recharge",
                    type: "POST",
                    contentType: "application/json",
                    headers: {
                        "Authorization": "Bearer " + "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwODIvYXBpL2xvZ2luIiwiaWF0IjoxNzE3NTc3MDYxLCJleHAiOjE3MTc1ODQyNjEsIm5iZiI6MTcxNzU3NzA2MSwianRpIjoiOXpXRzRqVnFVbnNHbVE5RCIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.AwKX-sZXjxTT28I2oXL0iwp3Ux1IVmgd6FCf_BtcWxA"
                    },
                    data: JSON.stringify({ "amount": 1000 }),
                    success: function(data) {
                        // 成功后获取返回的 URL
                        let response = JSON.parse(data.error);
                        let redirectUrl = response.transaction_url;

                        // 使用返回的 URL 进行重定向
                        window.location.href = redirectUrl;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("请求失败: " + textStatus, errorThrown);
                    }
                });
            });
        });
    </script>
</body>
</html>
