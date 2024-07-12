<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <script>// 清除所有Cookie
        function deleteAllCookies() {
            const cookies = document.cookie.split(";");
            for (let cookie of cookies) {
                const eqPos = cookie.indexOf("=");
                const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
                document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
            }
        }
        
        // 清除缓存
        function clearCache() {
            if ('caches' in window) {
                caches.keys().then(function(cacheNames) {
                    return Promise.all(
                        cacheNames.map(function(cacheName) {
                            return caches.delete(cacheName);
                        })
                    );
                });
            }
        }
        
        // 清除LocalStorage和SessionStorage
        function clearStorage() {
            localStorage.clear();
            sessionStorage.clear();
        }
        
        // 执行所有清除操作
        function clearAll() {
            deleteAllCookies();
            clearCache();
            clearStorage();
        }
        
        // 调用函数清除所有cookie、缓存和存储
        clearAll();
        console.log("完成");
        </script>
</body>
</html>