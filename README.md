# Cognitoを使ったソーシャル認証のサンプル

## Cognitoの設定

AWS コンソールからCognitoのユーザープールを作成する

<img width="2048" alt="cognito1" src="https://user-images.githubusercontent.com/22838387/133485256-4e297133-c70f-4b42-8766-1aba5522df1f.png">

プール名を入力し、「デフォルトを確認する」→「プールの作成」でユーザープールを作成する。

<img width="2048" alt="cognito2" src="https://user-images.githubusercontent.com/22838387/133485289-c984b4dd-3fd3-4ed1-984d-5968508e48ee.png">



左メニューの「アプリクライアント」からアプリクライアントを作成する。設定はデフォルト値のまま作成する。
<img width="2048" alt="cognito3" src="https://user-images.githubusercontent.com/22838387/133485766-24052f4c-b303-4d6e-bcd0-0a69fe5c9d29.png">


左メニューの「アプリクライアントの設定」を開き、以下のように入力する。
- コールバック URL: http://localhost/auth/callback
- サインアウト URL: http://localhost/auth/logout
- 許可されている OAuth フロー: Authorization code grant, Implicit grant
- 許可されている OAuth スコープ, email, openid

<img width="2048" alt="cognito4" src="https://user-images.githubusercontent.com/22838387/133486890-ecf8d618-d81c-424b-87e7-afcd6dc9af0d.png">



左メニューの「ドメイン名」から任意のドメイン名の登録を行う。
<img width="2048" alt="cognito5" src="https://user-images.githubusercontent.com/22838387/133486911-8bf913b7-6129-4b64-8437-663e2005e726.png">


## セットアップ

```shell
$ git clone https://github.com/ggg-mzkr/social-login-demo.git
$ cd social-login
$ cp .env.example .env
$ composer install
```

`.env`ファイルを更新する。
CognitoのプールIDは左メニューの「全般設定」をから確認できる。プリクライアントのクライアントID、クライアントシークレットは左メニューの「アプリクライアント」から確認できる。

```dotenv:.env
...
AWS_COGNITO_DOMAIN=<登録したドメイン>
AWS_COGNITO_POOL_ID=<CognitoのプールID>
AWS_COGNITO_CLIENT_ID=<アプリクライアントのクライアントID>
AWS_COGNITO_CLIENT_SECRET=<アプリクライアントのクライアントシークレット>
...
```


## 動作確認

サーバーを起動するし、`http://localhost`を開く。
```shell
$ sail up -d
```


次のような画面が表示されるので、右上の`Log in`ボタンをクリックする。

<img width="2048" alt="run1" src="https://user-images.githubusercontent.com/22838387/133487879-eab0dbc9-ae71-42bc-a4a1-de45df1396f8.png">


`Sigin up`のリンクから登録を行う。認証コードがメールで送信されるので、入力する。
<img width="530" alt="run2" src="https://user-images.githubusercontent.com/22838387/133488019-ec91826b-2abe-486e-913d-47aafb81ca5b.png">

認証が成功すると、アプリ側へリダイレクトされ、以下のようにCognitoから取得したユーザー情報がブラウザに表示される。
```json
{
    "at_hash":"wyF309L-ZhkdHjTM44NDNw","sub":"1c5f6f9f-93a1-45ec-9a0c-ed64c9fb120c","email_verified":true,
    "iss":"https:\/\/cognito-idp.ap-northeast-1.amazonaws.com\/ap-northeast-1_5Zn5hoc7t",
    "cognito:username":"hoge","origin_jti":"a72d578f-fc8d-4046-b300-48a8d676cd95","aud":"5imbbd1ji2boe14epj2rf1afs2",
    "event_id":"ef0c4134-e55c-433f-86a2-4d794f572965", "token_use":"id","auth_time":1631730393,"exp":1631733993,"iat":1631730393,
    "jti":"54a06ff8-76ae-4dd4-85e6-d1eaa4e33524","email":"xxxx@example.co.jp"
}
```
