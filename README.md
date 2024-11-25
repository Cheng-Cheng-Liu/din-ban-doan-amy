# din-ban-doan-amy

# api文件v2

## API Domain

---

## API 目錄

# 一、會員

1-1 註冊

POST /api/register

1-2 登入

POST /api/login

1-3 登出

POST /api/member/logout

1-4 取得自己會員資料

GET /api/member/users

1-5 所有會員

GET /api/back/users

1-6 取得指定id會員

GET /api/back/users/{id}

1-7 新增單筆會員

POST /api/back/users

1-8 修改自己會員

PUT /api/member/users/

1-9 修改該筆會員

PUT /api/back/users/{id}

1-10 新增該會員瀏覽紀錄

POST /api/member/browsing_historys

1-11 取得該會員瀏覽紀錄

GET /api/member/browsing_historys

1-12 新增我的最愛

POST /api/member/users/favorites

1-13 取得我的最愛

GET /api/member/users/favorites

1-14 刪除我的最愛

DELETE /api/member/users/favorites/{id}

1-15 登入紀錄

GET /api/member/users/login_log

1-16 後台會員登入

POST /api/login_back

# 二、餐廳

2-1取得啟用中全部餐廳

GET /api/restaurants

2-2取得啟用中特定餐廳

GET /api/restaurant

2-3取得全部餐廳

GET /api/back/restaurants

2-4 新增單筆餐廳

POST /api/back/restaurants

2-5 修改該筆餐廳

PUT /api/back/restaurants

2-6 刪除該筆餐廳

DELETE /api/back/restaurants

2-7 取得會員的啟用中全部餐廳

GET /api/member/restaurants

# 三、菜單

3-1 取得指定餐廳id全部已審核通過菜單

GET /api/restaurants/menu

3-2 取得指定餐廳id全部菜單

GET /api/back/restaurants/menu

3-3 新增單筆菜單

POST /api/back/restaurants/menu

3-4 修改該筆菜單

PUT /api/back/restaurants/menu

3-5 刪除該筆菜單

DELETE /api/back/restaurants/menu

# 四、評論

4-1取得指定餐廳id全部評論

GET /api/restaurants/{id}/comments

4-2 取得使用者自己的全部評論

GET /api/member/comments

4-3 新增指定餐廳id單筆評論

POST /api/member/restaurants/{id}/comments

4-4 取得使用者自己的某一筆評論

GET /api/member/comments/{id}

4-5 修改使用者自己的某一筆評論

PUT /api/member/comments/{id}

4-6 刪除使用者自己的某一筆評論

DELETE /api/member/comments/{id}

4-7 取得指定id評論

GET /api/back/restaurants/comments/{id}

4-8 修改該筆評論

PUT /api/back/restaurants/comments/{id}

4-9 刪除該筆評論

DELETE /api/back/restaurants//comments/{id}

### 

# 五、訂單

### 訂單

5-1 取得會員自己的全部訂單

GET /api/member/orders

5-2 取得全部訂單

GET /api/back/orders

5-3 建立訂單

POST /api/member/orders

# 六、錢包

6-1 取得該會員id全部錢包紀錄

GET /api/member/wallet_logs

6-2 會員錢包儲值請求

POST /api/member/wallets/recharge

6-3 會員錢包儲值

POST /api/wallet/**recharge/**result

# 七、報表系統

7-1 統計每小時各家餐廳的訂單總額度

GET /api/back/report/restaurants

7-2 統計每小時會員登入報表

GET /api/back/report/members

# 附件

1.ERROR CODE

---

# API 需求說明

# 會員

1-1 註冊

POST /api/register

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| email | string | V | EMAIL為帳號 |
| name | string | V | 姓名 |
| password | string | V | 密碼，請使用至少6個英文或數字 |
| nickname | string | V | 評論顯示名稱 |
| phone | string | V | 電話號碼 |

請求範例

```json
{

			"email":"123@gmail.com",
			
			"name":"王曉明",
			
			"password":"1q2f3g5gh",
			
			"nickname":"王曉明",
			
			"phone":"0911111111"

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 錯誤代碼1003，驗證email後完成註冊 |

成功響應範例

```json
{    
		 "error": "1003" 
}
```

失敗響應範例

```json
{
"error": "1001"
}

```

---

1-2 登入

POST /api/login

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| email | string | V | EMAIL為帳號 |
| password | string | V | 密碼，請使用至少6個英文或數字 |

請求範例

```json
{

			"email":"123@gmail.com",
			
			"password":"1q2f3g5gh"

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| message | string | o | 登入成功訊息 |
| token | string | o | 成功時得到token |

成功響應範例

```json
{
    "message": "登入成功",
    "token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c",
}

```

失敗響應範例

```json
{
    "error": "1001"
}

```

---

1-3 登出

POST /api/member/logout

Bearer Token

Parameters : none

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
    "error": "0",
}

```

失敗響應範例

```json
{
    "error": "1001"
}

```

---

1-4 取得自己會員資料

GET /api/member/users

Bearer Token

Parameters:none

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| id | int | v | 會員id |
| name | string | v | 會員真實姓名 |
| nickname | string | v | 暱稱 |
| phone | string | v | 電話 |
| email | string | v | email |
| status | int | x | 啟用:1，停權:2 |

成功響應範例

```json
{
		  "id":1,
			"name":"王曉明",
			"nickname":"阿明",
			"phone":"0911111111",
			"email":"vul3au@gmail.com",
			"status":1
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

1-5 所有會員

GET /api/back/users

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |
| name | string | x | 會員真實姓名 |
| email | string | x | email |
| phone | string | x | 電話 |
| nickname | string | x | 暱稱 |

請求範例

```json
{

			"limit":20,
			
			"offset":10,
			
			"nickname":"阿明",

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 所有會員筆數 |
| list[].id | int | v | 會員id |
| list[].name | string | v | 會員真實姓名 |
| list[].nickname | string | v | 暱稱 |
| list[].phone | string | v | 電話 |
| list[].email | string | v | email |
| list[].status | int | v | 啟用:1，停權:2，刪除:3 |

成功響應範例

```json
{
    "total": 20,
    "list": [
						    {
								    "id":1,
								    "name":"王曉明",
								    "nickname":"阿明",
								    "phone":"0911111111",
								    "email":"vul3au@gmail.com",
								    "status":1
						    },
						    {
								    "id":2,
								    "name":"蔡曉明",
								    "nickname":"阿明",
								    "phone":"0911111112",
								    "email":"kkdsl3au@gmail.com",
								    "status":1
						    }
				
				     ]
}

```

失敗響應範例

```json
{
    "error": "1001"
}

```

---

1-6 取得指定id會員

GET /api/back/users

Bearer Token

Parameters:none

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| id | int | v | 會員id |
| name | string | v | 會員真實姓名 |
| nickname | string | v | 暱稱 |
| phone | string | v | 電話 |
| email | string | v | email |

成功響應範例

```json
{
	  "id":1,
		"name":"王曉明",
		"nickname":"阿明",
		"phone":"0911111111",
		"email":"vul3au@gmail.com",
		"status":1,
		"roles":["member","back"],
}

```

失敗響應範例

```json
{
    "error": "1001"
}

```

---

1-7 新增單筆會員

POST /api/back/users

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| email | string | V | EMAIL為帳號 |
| name | string | V | 姓名 |
| password | string | V | 密碼，請使用至少6個英文或數字 |
| nickname | string | V | 評論顯示名稱 |
| phone | string | V | 電話號碼 |
| roles | array | V | 權限 |
| status | int | V | 啟用:1，停權:2 |

請求範例

```json
{

			"email":"123@gmail.com",
			
			"name":"王曉明",
			
			"password":"1q2f3g5gh",
			
			"nickname":"王曉明",
			
			"phone":"0911111111",
			
			"roles":["member","admin"]

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

1-8 修改自己會員

PUT /api/member/users

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| password | string | x | 密碼，請使用至少6個英文或數字 |
| nickname | string | x | 評論顯示名稱 |
| phone | string | x | 電話號碼 |
| status | int | x | 啟用:1，停權:2 |

請求範例

```json
{

			"password":"1q2f3g5gh",
			
			"nickname":"王曉明",
			
			"phone":"0911111111",
			
			"status":2
			
}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

1-9 修改該筆會員

PUT /api/back/users/{id}

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| email | string | V | EMAIL為帳號 |
| name | string | V | 姓名 |
| password | string | V | 密碼，請使用至少6個英文或數字 |
| nickname | string | V | 評論顯示名稱 |
| phone | string | V | 電話號碼 |
| roles | array | V | 權限 |
| status | int | V | 啟用:1，停權:2 |

請求範例

```json
{

			"email":"123@gmail.com",
			
			"name":"王曉明",
			
			"password":"1q2f3g5gh",
			
			"nickname":"王曉明",
			
			"phone":"0911111111",
			
			"roles":["member","admin"]
			
}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

1-10 新增該會員瀏覽紀錄

POST /api/member/browsing_historys

Bearer Token

Parameters

| 參數名稱 | 型別\bf 型別型別 | 必帶\bf 必帶必帶 | 說明\bf 說明說明 |
| --- | --- | --- | --- |
| id | int | v | 餐廳id |

請求範例

```json
{

			"id":255

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

1-11 取得該會員瀏覽紀錄

GET /api/member/browsing_historys

Bearer Token

Parameters

| 參數名稱 | 型別 | 必帶 | 說明 |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{

"limit":20,
"offset":10

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 餐廳總筆數 |
| list[] | array | v | 餐廳id |

成功響應範例

```json
{
     "total": 1,
     "list":[255,256]
}

```

失敗響應範例

```json
{
    "error": "1001"
}

```

---

1-12 新增我的最愛

POST /api/member/users/favorites

Bearer Token

Parameters

| 參數名稱 | 型別 | 必帶 | 說明 |
| --- | --- | --- | --- |
| id | int | v | 餐廳id |

請求範例

```json
{

			"id":20,

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

1-13 取得我的最愛

GET /api/member/users/favorites

Bearer Token

Parameters

| 參數名稱 | 型別 | 必帶 | 說明 |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{

			"limit":20,
			"offset":10

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 總筆數 |
| list[].id | int | v | 餐廳id |
| list[].name | string | v | 餐廳名稱 |
| list[].tag | string | v | 餐廳標籤 |
| list[].phone | string | v | 餐廳電話 |
| list[].opening_time | string | v | 開始營業時間 |
| list[].closing_time | string | v | 餐廳關門時間 |
| list[].rest_day | string | v | 從1~7代表公休是禮拜幾，例如”135”代表禮拜一、三、五公休 |
| list[].avg_score | int | v | 平均分數 |
| list[].total_comments_count | int | v | 餐廳被評論總數 |

成功響應範例

```json
{
			  "total": 24,
			  "list": [
									  {
											    "id":255,
										      "name": "Steak Home 餐廳",
										      "tag": "西式餐廳",
										      "phone": "04-22558899",
										      "opening_time": "11:00",
										      "closing_time": "19:00",
										      "rest_day": "135",
										      "avg_score": 4.3,
										      "total_comments_count": 204,
										      "status":1,
										      "priority":1
									    },
									    {
											    "id":256,
										      "name": "Oishii 餐廳",
										      "tag": "其他",
										      "phone": "04-12345678",
										      "opening_time": "10:00",
										      "closing_time": "20:00",
										      "rest_day": "2",
										      "avg_score": 4.5,
										      "total_comments_count": 150,
										      "status":1,
										      "priority":1
									    }
							  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

1-14 刪除我的最愛

DELETE /api/member/users/favorites/{id}

Bearer Token

Parameters:none

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
	  "error": "0"
}

```

失敗響應範例

```json
{
    "error": "1001"
}

```

---

1-15 登入紀錄

GET /api/member/users/login_log

Bearer Token

Parameters

| 參數名稱 | 型別 | 必帶 | 說明 |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{
	
			"limit":20,
			"offset":10

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 總筆數 |
| list[].id | int | v | 登入紀錄id |
| list[].login_time | string | v | 登入時間 |

成功響應範例

```json
{
			  "total": 24,
			  "list": [
										  {
											    "id":255,
										      "login_time": "2024-04-29 05:40:38",
										   },
										   {
											    "id":256,
										      "login_time": "2024-04-29 05:40:38",
										     
										   }
							  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

1-16 後台會員登入

POST /api/login_back

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| email | string | V | EMAIL為帳號 |
| password | string | V | 密碼，請使用至少6個英文或數字 |

請求範例

```json
{

			"email":"123@gmail.com",
			
			"password":"1q2f3g5gh"

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| message | string | o | 登入成功訊息 |
| token | string | o | 成功時得到token |

成功響應範例

```json
{
    "message": "登入成功",
    "token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c",
}

```

失敗響應範例

```json
{
    "error": "1001"
}

```

---

# 

2-1取得啟用中全部餐廳

GET /api/restaurants

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{

			"limit":20,
			
			"offset":10
			
}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 餐廳總筆數 |
| list[].id | int | v | 餐廳id |
| list[].name | string | v | 餐廳名稱 |
| list[].tag | string | v | 餐廳標籤 |
| list[].phone | string | v | 餐廳電話 |
| list[].opening_time | string | v | 開始營業時間 |
| list[].closing_time | string | v | 餐廳關門時間 |
| list[].rest_day | string | v | 從1~7代表公休是禮拜幾，例如”135”代表禮拜一、三、五公休 |
| list[].avg_score | int | v | 平均分數 |
| list[].total_comments_count | int | v | 餐廳被評論總數 |

成功響應範例

```json
{
		  "total": 24,
		  "list": [
								  {
										    "id":255,
									      "name": "Steak Home 餐廳",
									      "tag": "西式餐廳",
									      "phone": "04-22558899",
									      "opening_time": "11:00",
									      "closing_time": "19:00",
									      "rest_day": "135",
									      "avg_score": 4.3,
									      "total_comments_count": 204,
								   },
								   {
										    "id":256,
									      "name": "Oishii 餐廳",
									      "tag": "其他",
									      "phone": "04-12345678",
									      "opening_time": "10:00",
									      "closing_time": "20:00",
									      "rest_day": "2",
									      "avg_score": 4.5,
									      "total_comments_count": 150,
								   }
						  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

2-2取得啟用中特定餐廳

GET /api/restaurants

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| restaurant_id | int | v | 該餐廳的id |

請求範例

```json
{

			"restaurant_id":2
			
}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
|  |  |  |  |
| id | int | v | 餐廳id |
| name | string | v | 餐廳名稱 |
| tag | string | v | 餐廳標籤 |
| phone | string | v | 餐廳電話 |
| opening_time | string | v | 開始營業時間 |
| closing_time | string | v | 餐廳關門時間 |
| rest_day | string | v | 從1~7代表公休是禮拜幾，例如”135”代表禮拜一、三、五公休 |
| avg_score | int | v | 平均分數 |
| total_comments_count | int | v | 餐廳被評論總數 |

成功響應範例

```json

		 
		 {
										  "id":255,
									    "name": "Steak Home 餐廳",
									    "tag": "西式餐廳",
									    "phone": "04-22558899",
									    "opening_time": "11:00",
									    "closing_time": "19:00",
									    "rest_day": "135",
									    "avg_score": 4.3,
									    "total_comments_count": 204,
		 }
								   

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

2-2取得全部餐廳

GET /api/back/restaurants

Bearer Token 

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{

		"limit":20,
		
		"offset":10,

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 餐廳總筆數 |
| list[].id | int | v | 餐廳id |
| list[].name | string | v | 餐廳名稱 |
| list[].tag | string | v | 餐廳標籤 |
| list[].phone | string | v | 餐廳電話 |
| list[].opening_time | string | v | 開始營業時間 |
| list[].closing_time | string | v | 餐廳關門時間 |
| list[].rest_day | string | v | 從1~7代表公休是禮拜幾，例如”135”代表禮拜一、三、五公休 |
| list[].avg_score | int | v | 平均分數 |
| list[].total_comments_count | int | v | 餐廳被評論總數 |
| list[].status | int | v | 餐廳狀態 |
| list[].priority | int | v | 1為最優先 |

成功響應範例

```json
{
		  "total": 24,
		  "list": [
							  {
								    "id":255,
							      "name": "Steak Home 餐廳",
							      "tag": "西式餐廳",
							      "phone": "04-22558899",
							      "opening_time": "11:00",
							      "closing_time": "19:00",
							      "rest_day": "135",
							      "avg_score": 4.3,
							      "total_comments_count": 204,
							      "status":1,
							      "priority":1
							   },
							   {
								    "id":256,
							      "name": "Oishii 餐廳",
							      "tag": "其他",
							      "phone": "04-12345678",
							      "opening_time": "10:00",
							      "closing_time": "20:00",
							      "rest_day": "2",
							      "avg_score": 4.5,
							      "total_comments_count": 150,
							      "status":1,
							      "priority":1
							   }
						  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

2-3 新增單筆餐廳

POST /api/back/restaurants

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| name | string | V | 餐廳名稱 |
| tag | string |  | 一些形容 |
| phone | string |  | 電話號碼 |
| opening_time | string |  | 開門時間 |
| closing_time | string |  | 關門時間 |
| rest_day | string |  | 從1~7代表公休是禮拜幾，例如”135”代表禮拜一、三、五公休 |
| status | int | V | 啟用:1，停權:2 |
| priority | int |  | 優先順序由大到小，預設1 |

請求範例

```json
{

			"name":"家庭日式餐廷",
			
			"tag":"日式餐廳 台式風味",
			
			"phone":"04-111111111",
			
			"opening_time": "11:00",
			
			"closing_time": "19:00",
			      
			"rest_day": "135",
			
			"status":1,
			
			"priority":5

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

# 

2-4 修改該筆餐廳

PUT /api/back/restaurants

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| name | string | V | 餐廳名稱 |
| tag | string |  | 一些形容 |
| phone | string |  | 電話號碼 |
| opening_time | string |  | 開門時間 |
| closing_time | string |  | 關門時間 |
| rest_day | string |  | 從1~7代表公休是禮拜幾，例如”135”代表禮拜一、三、五公休 |
| status | int | V | 啟用:1，停權:2 |
| priority | int |  | 優先順序由大到小，預設1 |
| restaurant_id | int | V | 餐廳id |

請求範例

```json
{

			"name":"家庭日式餐廷",
			
			"tag":"日式餐廳 台式風味",
			
			"phone":"04-111111111",
			
			"opening_time": "11:00",
			
			"closing_time": "19:00",
			      
			"rest_day": "135",
			
			"status":1,
			
			"priority":1,
			
			"restaurant_id":255
}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

2-5 刪除該筆餐廳

DELETE /api/back/restaurants

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| restaurant_id | int | V | 餐廳id |

請求範例

```json
{
			"restaurant_id":255
}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
	  "error": "0"
}

```

失敗響應範例

```json
{
    "error": "1001"
}

```

---

2-6 取得會員的啟用中全部餐廳

GET /api/member/restaurants

bearer token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{

			"limit":20,
			
			"offset":10,
			
}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 餐廳總筆數 |
| list[].id | int | v | 餐廳id |
| list[].name | string | v | 餐廳名稱 |
| list[].tag | string | v | 餐廳標籤 |
| list[].phone | string | v | 餐廳電話 |
| list[].opening_time | string | v | 開始營業時間 |
| list[].closing_time | string | v | 餐廳關門時間 |
| list[].rest_day | string | v | 從1~7代表公休是禮拜幾，例如”135”代表禮拜一、三、五公休 |
| list[].avg_score | int | v | 平均分數 |
| list[].total_comments_count | int | v | 餐廳被評論總數 |
| list[].favorite | boolean | v | 是不是我的最愛裡的餐廳 |

成功響應範例

```json
{
		  "total": 24,
		  "list": [
								  {
										    "id":255,
									      "name": "Steak Home 餐廳",
									      "tag": "西式餐廳",
									      "phone": "04-22558899",
									      "opening_time": "11:00",
									      "closing_time": "19:00",
									      "rest_day": "135",
									      "avg_score": 4.3,
									      "total_comments_count": 204,
									      "favorite":true,
								   },
								   {
										    "id":256,
									      "name": "Oishii 餐廳",
									      "tag": "其他",
									      "phone": "04-12345678",
									      "opening_time": "10:00",
									      "closing_time": "20:00",
									      "rest_day": "2",
									      "avg_score": 4.5,
									      "total_comments_count": 150,
									      "favorite":false,
								   }
						  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

3-1 取得指定餐廳id全部已審核通過菜單

GET /api/restaurants/menu

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |
| restaurant_id | int | V | 餐廳id |

請求範例

```json
{

		"limit":20,
		
		"offset":10,
		
		"restaurant_id":3

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 餐點總筆數 |
| list[].id | int | v | 餐點id |
| list[].name | string | v | 餐點名稱 |
| list[].price | int | v | 餐點價格 |
| list[].another_id | string | v | 餐廳提供的餐點id |

成功響應範例

```json
{
  "total": 36,
  "list": [
  {
	    "id":4,
      "name": "菲力牛排",
      "price":599,
      "another_id": "1",
    },
    {
	    "id":5,
      "name": "沙朗牛排",
      "price":399,
      "another_id": "2",
    }
  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

3-2 取得指定餐廳id全部菜單

GET /api/back/restaurants/{id}/menu

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{

		"limit":20,
		
		"offset":10,

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 餐點總筆數 |
| list[].id | int | v | 餐點id |
| list[].name | string | v | 餐點名稱 |
| list[].price | int | v | 餐點價格 |
| list[].another_id | string | v | 餐廳提供的餐點id |

成功響應範例

```json
{
	  "total": 36,
	  "list": [
						  {
						    "id":4,
					      "name": "菲力牛排",
					      "price":599,
					      "another_id": "1",
					    },
					    {
						    "id":5,
					      "name": "沙朗牛排",
					      "price":399,
					      "another_id": "2",
					    }
					  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

3-3 新增單筆菜單

POST /api/back/restaurants/menu

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| restaurant_id  | int | V | 餐廳id |
| name | string | V | 餐點名稱 |
| price | int | V | 餐點價格 |
| another_id | string | V | 餐廳提供的餐點id |
| status | int | V | 1:啟用 2:不啟用 |

請求範例

```json
{

			"restaurant_id":123,
			
			"name":"雞排",
			
			"price":299,
			
			"another_id": "3",
			
			"status":1

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

3-4 修改該筆菜單

PUT /api/back/restaurants//menu

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| restaurant_id  | int | V | 餐廳id |
| name | string | V | 餐點名稱 |
| price | int | V | 餐點價格 |
| another_id | string | V | 餐廳提供的餐點id |
| status | int | V | 1:啟用 2:不啟用 |

請求範例

```json
{

			"restaurant_id ":123,
			
			"name":"雞排",
			
			"price":249,
			
			"another_id": 3,
			
			"status":2
			
}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
	  "error": "0"
}

```

失敗響應範例

```json
{
    "error": "1001"
}

```

---

3-5 刪除該筆菜單

DELETE /api/back/restaurants/menu

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| restaurant_id  | int | V | 餐廳id |
| another_id | string | V | 餐廳提供的餐點id |

請求範例

```json
{

			"restaurant_id":15,
			"another_id":3
			
}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
	  "error": "0"
}

```

失敗響應範例

```json
{
    "error": "1001"
}

```

---

4-1 取得指定餐廳id全部評論

GET /api/restaurants/{id}/comments

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{

			"limit":20,
			
			"offset":10,

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 餐點總筆數 |
| list[].id | int | v | 評論id |
| list[].nickname | string | v | 評論者暱稱 |
| list[].score | int | v | 評分 |
| list[].description | string | v | 評論內容 |

成功響應範例

```json
{
  "total": 36,
  "list": [
					  {
					    "id":4,
				      "nickname": "只是一個過客",
				      "score":5,
				      "description":"可圈可點"
				    },
				    {
					    "id":4,
				      "nickname": "小愛",
				      "score":5,
				      "description":"服務好"
				    }
				  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

4-2 取得使用者自己的全部評論

GET /api/member/comments

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{

			"limit":20,
			
			"offset":10,

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 餐點總筆數 |
| list[].id | int | v | 評論id |
| list[].score | int | v | 評分 |
| list[].description | string | v | 評論內容 |

成功響應範例

```json
{
	  "total": 5,
	  "list": [
						  {
						    "id":4,
					      "score":4,
					      "description":"好吃"
					    },
					    {
						    "id":4,
					      "score":5,
					      "description":"服務好"
					    }
					  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

4-3 新增指定餐廳id單筆評論

POST /api/member/restaurants/{id}/comments

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| description | string | x | 評論內容 |
| score | int | x | 評分 |

請求範例

```json
{

			"description":"有一隻小強的腳",
			
			"score":1,

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

4-4 取得使用者自己的某一筆評論

GET /api/member/comments/{id}

Bearer Token

Parameters:none

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| id | int | v | 評論id |
| score | int | v | 評分 |
| description | string | v | 評論內容 |

成功響應範例

```json
{
	    "id":4,
      "score":4,
      "description":"好吃"
    
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

4-5 修改使用者自己的某一筆評論

PUT /api/member/comments/{id}

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| description | string | x | 評論內容 |
| score | int | x | 評分 |

請求範例

```json
{

			"description":"其實很好吃",
			
			"score":3,

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

4-6 刪除使用者自己的某一筆評論

DELETE /api/member/comments/{id}

Bearer Token

Parameters:none

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

4-7 取得指定id評論

GET /api/back/restaurants/comments/{id}

Bearer Token

Parameters:none

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| id | int | v | 評論id |
| score | int | v | 評分 |
| description | string | v | 評論內容 |

成功響應範例

```json
{
	    "id":4,
      "score":4,
      "description":"好吃"
    
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

4-8 修改該筆評論

PUT /api/back/restaurants/comments/{id}

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| description | string | x | 評論內容 |
| score | int | x | 評分 |

請求範例

```json
{

		"description":"其實很好吃",
		
		"score":3,

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

4-9 刪除該筆評論

DELETE /api/back/restaurants/comments/{id}

Bearer Token

Parameters:none

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
		  "error": "0"
}

```

失敗響應範例

```json
{
	    "error": "1001"
}

```

---

5-1 取得會員自己的全部訂單

GET /api/member/orders

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{

			"limit":20,
			
			"offset":10,

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 餐點總筆數 |
| list[].id | int | v | 訂單id |
| list[].restaurant_id  | int | v | 餐廳id |
| list[].restaurant_name |  |  | 餐廳名稱 |
| list[].another_id | string | v | 餐廳給的訂單id |
| list[].phone | string | v | 訂購人電話 |
| list[].amount | int | v | 總額 |
| list[].status  | string | v | 狀態 |
| list[].remark | string | v | 備註 |
| list[].pick_up_time | string | v | 取餐時間 |
| list[].created_time | string | v | 訂單創立時間 |
| list[].detail[].meal_name | string | v | 餐點名稱 |
| list[].detail[].another_id | string | v | 餐廳提供餐點id |
| list[].detail[].price | int | v | 單點單價 |
| list[].detail[].quantity | int | v | 餐點數量 |
| list[].detail[].amount | int | v | 餐點小計 |

成功響應範例

```json
{
		  "total": 125,
		  "list": [
							  {
							      "id":2,
								    "restaurant_id":255, 
										"restaurant_name":"Steak Home 餐廳",
										"another_id":"38eb6dae-7b9e-4bed-9711-909f3b32c4e3",
										"phone":"0911111111",
										"amount":299,
										"status":"已完成", 
										"remark":"無",
										"pick_up_time":"2024-04-29 12:40:38",
										"created_time":"2024-04-29 10:40:38",
										"detail":[
																{
																		"meal_name":"雞排",
																		"another_id":"4",
																		"price":299,
																		"quantity":1,
																		"amount":299
																}
															]
						    },
						    {
							      "id":3,
								    "restaurant_id":255, 
										"restaurant_name":"Steak Home 餐廳",
										"another_id":"38eb6dae-7b9e-4bed-9711-752f3b32c4e3",
										"phone":"0911111121",
										"amount":598,
										"status":"已完成", 
										"remark":"無",
										"pick_up_time":"2024-04-30 12:29:38",
										"created_time":"2024-04-30 10:22:38",
										"created_time":"2024-04-29 10:40:38",
										"detail":[  
																{
																		"meal_name":"雞排",
																		"another_id":4,
																		"price":299,
																		"quantity":1,
																		"amount":299
																},
																{   
																		"meal_name":"魚排",
																		"another_id":5,
																		"price":299,
																		"quantity":1,
																		"amount":299
																}   
														]  
						    }
						  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

5-2 取得全部訂單

GET /api/back/orders

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{

			"limit":20,
			
			"offset":10,

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 餐點總筆數 |
| list[].id | int | v | 訂單id |
| list[].user_id | int | v | 會員id |
| list[].user_name | string | v | 會員姓名 |
| list[].phone | string | v | 訂購人電話 |
| list[].restaurant_id  | int | v | 餐廳id |
| list[].restaurant_name | string | v | 餐廳名稱 |
| list[].another_id | string | v | 餐廳給的訂單id |
| list[].amount | int | v | 總額 |
| list[].status  | string | v | 狀態 |
| list[].remark | string | v | 備註 |
| list[].pick_up_time | string | v | 取餐時間 |
| list[].created_time | string | v | 訂單創立時間 |
| list[].detail[].meal_name | string | v | 餐點名稱 |
| list[].detail[].another_id | string | v | 餐廳提供餐點id |
| list[].detail[].price | int | v | 單點單價 |
| list[].detail[].quantity | int | v | 餐點數量 |
| list[].detail[].amount | int | v | 餐點小計    |

成功響應範例

```json
{
		  "total": 125,
		  "list": [
								  {
								      "id":5
								      "user_id":3,
								      "user_name":"王曉明",
								      "phone":"0911111111",
									    "restaurant_id":255, 
											"restaurant_name":"Steak Home 餐廳",
											"another_id":"38eb6dae-7b9e-4bed-9711-909f3b32c4e3",
											"amount":299,
											"status":"已完成", 
											"remark":"",
											"pick_up_time":"2024-04-29 12:40:38",
											"created_time":"2024-04-29 10:40:38",
											"detail":[
																{
																			"meal_name":"雞排",
																			"another_id":4,
																			"price":299,
																			"quantity":1,
																			"amount":299
																	}
																]
											
								    },
								    {
								      "id":6
								      "user_id":3,
								      "user_name":"王曉明",
								      "phone":"0911111121",
									    "restaurant_id":255, 
											"restaurant_name":"Steak Home 餐廳",
											"another_id":"38eb6dae-7b9e-4bed-9711-752f3b32c4e3",
											"amount":299,
											"status":"已完成", 
											"remark":"",
											"pick_up_time":"2024-04-30 12:29:38",
											"created_time":"2024-04-30 10:22:38",
											"detail":[
																{
																			"meal_name":"雞排",
																			"another_id":4,
																			"price":299,
																			"quantity":1,
																			"amount":299
																},
																{
																			"meal_name":"魚排",
																			"another_id":5,
																			"price":299,
																			"quantity":1,
																			"amount":299
																}
															]
								    }
							  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

5-3 建立訂單

POST /api/member/orders

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| user_name | string | v | 會員姓名 |
| phone | string | v | 訂購人電話 |
| restaurant_id  | int | v | 餐廳id |
| another_id | string | v | 餐廳給的訂單id |
| amount | int | v | 總額 |
| status  | string | v | 狀態 |
| remark | string | v | 備註 |
| pick_up_time | string | v | 取餐時間 |
| created_time | string | v | 訂單創立時間 |
| detail[].meal_name | string | v | 餐點名稱 |
| detail[].meal_id | string | v | 餐點id |
| detail[].another_id | string | v | 餐廳提供餐點id |
| detail[].price | int | v | 單點單價 |
| detail[].quantity | int | v | 餐點數量 |
| detail[].amount | int | v | 餐點小計    |
| detail[].meal_remark | string | v | 餐點備註 |

請求範例

```json
{
      "user_name":"王曉明",
      "restaurant_id":1, 
			"restaurant_name":"Steak Home 餐廳",
			"phone":"0911111121",
			"amount":1298,
			"status":1, 
			"remark":"無",
			"pick_up_time":"2024-04-30 12:29:38",
			"created_time":"2024-04-30 10:22:38",
			"detail":[
										{
													"meal_name":"Steak 1",
													"meal_id":1,
													"another_id":1,
													"price":299,
													"quantity":1,
													"amount":299,
													"meal_remark":"5分熟"
										},
										{
													"meal_name":"豪華牛排",
													"meal_id":2,
													"another_id":2,
													"price":999,
													"quantity":1,
													"amount":999,
													"meal_remark":"5分熟"
										}
								]

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| error | string | O | 成功 |

成功響應範例

```json
{
	  "error": "0"
}

```

失敗響應範例

```json
{
    "error": "1001"
}

```

---

6-1取得該會員id全部錢包紀錄

GET /api/member/wallet_logs

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |

請求範例

```json
{

			"limit":20,
				
			"offset":10,
				
}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 總筆數 |
| total_now | int | v | 目前餘額 |
| list[].created_time | int | v | 交易時間 |
| list[].order_id | int | v | 對應訂單id，儲值無訂單id為null |
| list[].amount | int | v | 金額，正整數為儲值，負整數為訂單扣款 |
| list[].status | string | v | 餐廳名稱 |
| list[].remark | string | v | 備註 |

成功響應範例

```json
{
		  "total": 125,
		  "total_now":1047,
		  "list": [
							  {
							      "created_time":"2024-04-30 12:29:38",
							      "order_id":25564,
							      "amount":-255,
							      "status":"已完成",
							      "remark":null, 
							  },
							  {
							      "created_time":"2024-04-29 12:29:38",
							      "order_id":null,
							      "amount":1000,
							      "status":"已完成",
							      "remark":null, 
							  }
						  ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

6-2 會員錢包儲值請求

POST /api/member/wallets/recharge

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| amount | int | v | 儲值金額 |

請求範例

```json
{

			"amount":200,

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| transaction_url | string | v | 回傳的交易網址 |

成功響應範例

```json
{
    "transaction_url": "http://neil.xincity.xyz:9999/"
}
```

失敗響應範例

```json
{
    "error": "3003",
}

```

---

6-3 會員錢包儲值

POST /api/wallet/recharge/result

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| merchant_id | Int | O | 串接商編號。 |
| merchant_trade_no | String | O | 串接商的訂單編號為唯一值，不可重複。 |
| rtn_code | Int | O | 交易結果，回傳值為 1 時，為付款成功，其餘為交易異常。 |
| rtn_msg | String | O | 交易訊息，最長200字元。 |
| amount | Int | O | 交易金額為正整數，限新台幣。 |
| payment_date | String | O | 交易成功時間，格式為yyyy/MM/dd HH:mm:ss |
| trade_date | String | O | 訂單完成時間，格式為yyyy/MM/dd HH:mm:ss |
| check_mac_value | String | O | 檢查碼 |

請求範例

```json
{
    "merchant_id": 1,
    "merchant_trade_no": "mypay2023031215312",
    "rtn_code": 1,
		"rtn_msg": "交易成功",
    "amount": 10000,
		"payment_date": "2023/10/20 11:59:59",
		"trade_date": "2023/10/20 11:59:59",
    "check_mac_value": "6CC73080A3CF1EA1A844F1EEF96A873FA4D1DD485BDA6517696A4D8EF0EAC94E"
}
```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| received | boolean | O | 成功接收 |

成功響應範例

```json
{
		"received": true
}
```

---

7-1 統計每小時各家餐廳的訂單總額度

GET /api/back/report/restaurants

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |
| date | string | x | 預設昨日 |

請求範例

```json
{
			
			"limit":20,
			
			"offset":10,
			
			"date":"20240704"

}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| total | int | v | 總餐廳筆數 |
| list[].id | int | v | 餐廳id |
| list[].statistics[ ].time | string | v | 從0~23，例如0為00:00:00~00:59:59的訂單總金額，23為23:00:00~23:59:59的訂單總金額 |
| list[].statistics[ ].amount | int | v | 訂單總金額 |

成功響應範例

```json
{
    "total": 128,
    "list": [
        {
            "id": 255,
            "statistics": [
                {"time": "0", "amount": 0},
                {"time": "1", "amount": 0},
                {"time": "2", "amount": 0},
                {"time": "3", "amount": 0},
                {"time": "4", "amount": 0},
                {"time": "5", "amount": 0},
                {"time": "6", "amount": 0},
                {"time": "7", "amount": 0},
                {"time": "8", "amount": 2000},
                {"time": "9", "amount": 1254},
                {"time": "10", "amount": 21354},
                {"time": "11", "amount": 34531},
                {"time": "12", "amount": 1566},
                {"time": "13", "amount": 0},
                {"time": "14", "amount": 0},
                {"time": "15", "amount": 265},
                {"time": "16", "amount": 2345},
                {"time": "17", "amount": 15943},
                {"time": "18", "amount": 1532},
                {"time": "19", "amount": 1564},
                {"time": "20", "amount": 2568},
                {"time": "21", "amount": 0},
                {"time": "22", "amount": 0},
                {"time": "23", "amount": 0}
            ]
        }
    ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

7-2 統計每小時會員登入報表

GET /api/back/report/members

Bearer Token

Parameters

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| limit | int | x | 筆數 |
| offset | int | x | 第幾頁 |
| date | string | x | 預設昨日 |

請求範例

```json
{

			"limit":20,
			
			"offset":10,
			
			"date":"20240507"
			
}

```

Response

| $\bf 參數名稱$ | $\bf 型別$ | $\bf 必帶$ | $\bf 說明$ |
| --- | --- | --- | --- |
| statistics[].time | time | v | 從0~23，例如0為00:00:00~00:59:59的會員登入總人數，23為23:00:00~23:59:59的會員登入總人數 |
| statistics[].amount | int | v | 登入總人數 |

成功響應範例

```json
{
    "statistics": [
        {"time": "0", "amount": 0},
        {"time": "1", "amount": 0},
        {"time": "2", "amount": 0},
        {"time": "3", "amount": 0},
        {"time": "4", "amount": 0},
        {"time": "5", "amount": 0},
        {"time": "6", "amount": 0},
        {"time": "7", "amount": 0},
        {"time": "8", "amount": 0},
        {"time": "9", "amount": 2000},
        {"time": "10", "amount": 1254},
        {"time": "11", "amount": 21354},
        {"time": "12", "amount": 34531},
        {"time": "13", "amount": 1566},
        {"time": "14", "amount": 0},
        {"time": "15", "amount": 0},
        {"time": "16", "amount": 265},
        {"time": "17", "amount": 2345},
        {"time": "18", "amount": 15943},
        {"time": "19", "amount": 1532},
        {"time": "20", "amount": 1564},
        {"time": "21", "amount": 2568},
        {"time": "22", "amount": 0},
        {"time": "23", "amount": 0}
    ]
}

```

失敗響應範例

```json
{
    "error": "1001",
}

```

---

# 附件

## 1.ERR

分類:

0:成功

1xxx:系統共用。

2xxx:用戶相關。

3xxx:交易相關。

| 錯誤代碼﻿​ | 說明說明﻿​ |
| --- | --- |
| 0 | 成功 |

| 錯誤代碼﻿​ | 說明說明﻿​ |
| --- | --- |
| 1001 | 參數錯誤 |
| 1002 | 執行資料庫操作失敗 |
| 1003 | 請驗證email |

| 錯誤代碼﻿​ | 說明說明﻿​ |
| --- | --- |
| 2001 | 已註冊請直接登入 |
| 2002 | 帳號或密碼錯誤 |
| 2003 | 請先驗證email |
| 2004 | 會員狀態問題 |
| 2005 | 權限不足 |
| 2006 | 重複的email |
| 2007 | IP不在白名單裡 |

| 錯誤代碼﻿​ | 說明說明﻿​ |
| --- | --- |
| 3001 | 餘額不足 |
| 3002 | 店家未接單成功 |
| 3003 | 儲值請求失敗 |
| 3004 | 交易總額不正確 |
| 3005 | 請勿重複送出請求 |