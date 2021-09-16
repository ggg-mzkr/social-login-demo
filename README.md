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

`.env`ファイルを更新する。 CognitoのプールIDは左メニューの「全般設定」をから確認できる。プリクライアントのクライアントID、クライアントシークレットは左メニューの「アプリクライアント」から確認できる。

```dotenv:.env
...
AWS_COGNITO_DOMAIN=<登録したドメイン>
AWS_COGNITO_POOL_ID=<CognitoのプールID>
AWS_COGNITO_CLIENT_ID=<アプリクライアントのクライアントID>
AWS_COGNITO_CLIENT_SECRET=<アプリクライアントのクライアントシークレット>
...
```

### 動作確認

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
    "at_hash": "wyF309L-ZhkdHjTM44NDNw",
    "sub": "1c5f6f9f-93a1-45ec-9a0c-ed64c9fb120c",
    "email_verified": true,
    "iss": "https:\/\/cognito-idp.ap-northeast-1.amazonaws.com\/ap-northeast-1_5Zn5hoc7t",
    "cognito:username": "hoge",
    "origin_jti": "a72d578f-fc8d-4046-b300-48a8d676cd95",
    "aud": "5imbbd1ji2boe14epj2rf1afs2",
    "event_id": "ef0c4134-e55c-433f-86a2-4d794f572965",
    "token_use": "id",
    "auth_time": 1631730393,
    "exp": 1631733993,
    "iat": 1631730393,
    "jti": "54a06ff8-76ae-4dd4-85e6-d1eaa4e33524",
    "email": "xxxx@example.co.jp"
}
```

## LINEログインの追加

LINEのデベロッパーコンソールにログインする。
https://account.line.biz/login

プロバイダーの 「作成」ボタンを押してプロバイダーを作成する。
<img width="1917" alt="プロバイダーの作成" src="https://user-images.githubusercontent.com/22838387/133652558-1c5040f8-2cb3-4896-a03b-51155cf00481.png">

作成したプロバイダーを開き、チャネル設定のタブから「LINEログイン」のアイコンをクリックする。
<img width="1991" alt="チャネルの作成1" src="https://user-images.githubusercontent.com/22838387/133652585-578a7ff9-7114-44e1-ae11-6b207729dac5.png">

出てきたフォームに値を入力して「作成」を押す。

<img width="884" alt="チャネルの作成2" src="https://user-images.githubusercontent.com/22838387/133652530-57fedd84-8b30-4bc3-8082-c706042f7f8d.png">

チャネルの基本設定画面へリダイレクトするので、下の方ある「メールアドレス取得権限」の「申請」をクリックする。アップロードするスクリーンショットは、本`README.md`の「その他」の項目を利用する。

続いて「LINEログイン設定」タブを開き、「コールバックURL」に`https://＜登録したドメイン＞.auth.ap-northeast-1.amazoncognito.com/oauth2/idpresponse`を入力する。

<img width="1992" alt="スクリーンショット 2021-09-17 1 48 52" src="https://user-images.githubusercontent.com/22838387/133652806-045d33d7-ffaa-40a7-b40f-cc0499ff9614.png">



次に、LINEログインの設定をCognitoへ追加する。 

AWSコンソールのCognitoサービスを開き、左メニューから「IDプロバイダ」を選択する。
いくつかあるアイコンの中から「OpenID Connect」を選択する。各項目へ以下の通りに入力する。

* プロバイダ名: 任意
* クライアント ID: LINEのチャネルID
* クライアントのシークレット(オプション): LINEのチャネルシークレット
* 属性のリクエストメソッド: GET
* 承認スコープ: profile openid email
* 発行者: https://access.line.me

上記を入力したら、一度「検出の実行」をクリックする。さらに入力フォームが現れるので、次のように入力する。

* 認証エンドポイント: https://access.line.me/oauth2/v2.1/authorize
* トークンエンドポイント: https://api.line.me/oauth2/v2.1/token
* ユーザー情報エンドポイント: https://api.line.me/v2/profile
* Jwks uri: https://api.line.me/oauth2/v2.1/verify

左メニューの「属性マッピング」を開き、以下の内容でマッピングを追加する。

```
OIDC 属性: email、ユーザープール属性: Email
```

左メニューの「アプリクライアントの設定」を開き、有効な ID プロバイダの項目の「すべて選択」にチェックを入れる。

### 動作確認

サーバーを起動するし、`http://localhost`を開く。

```shell
$ sail up -d
```

次のような画面が表示されるので、右上の`Log in`ボタンをクリックする。

<img width="2048" alt="run1" src="https://user-images.githubusercontent.com/22838387/133487879-eab0dbc9-ae71-42bc-a4a1-de45df1396f8.png">


ログインモーダルに「LINE」が追加されているので、クリックする。LINEログインのページにリダイレクトされるので、ログインを行う。


<img width="995" alt="スクリーンショット 2021-09-17 1 38 32" src="https://user-images.githubusercontent.com/22838387/133652916-cdbaf527-ddc3-46ce-93b3-1ce264d761eb.png">


以下のようなレスポンスがブラウザに表示される。`identities`の値には、line経由でユーザー情報を取得したことが示されている。

```json
{
    "at_hash": "wFDuF028Dy3fnTz580Ey4w",
    "sub": "08df934a-03ea-418e-89ba-9608bbe13fb6",
    "cognito:groups": [
        "ap-northeast-1_KdxkiWkiH_line"
    ],
    "email_verified": false,
    "iss": "https:\/\/cognito-idp.ap-northeast-1.amazonaws.com\/ap-northeast-xxxxxxx",
    "cognito:username": "line_ue26ec0103e3625f7a610db69dcd55cfb",
    "nonce": "HeXKQIEuijKV4seeM1Z1C2JzoG14qhKExUVQdnzk4I0UX8mTCp0f_pdV_kAxEnbOpnOdGzeiFKn-MoX97y74cjwOr2jwIvx0ekL-NDwFMRkxG66HpIuqeDbmUO6KpxzZUcZP-kJymvmEYJOTQFTUrjobijPXnakXBX1JEZxaab0",
    "origin_jti": "e3e232e4-3b1b-445f-81bc-3f12a11e0fbf",
    "aud": "4ja229nqaaqrqh9pi61j6o1mum",
    "identities": [
        {
            "userId": "Ue26ec0103e3625f7a610dxxxxxxxx",
            "providerName": "line",
            "providerType": "OIDC",
            "issuer": null,
            "primary": "true",
            "dateCreated": "1631807811942"
        }
    ],
    "token_use": "id",
    "auth_time": 1631810483,
    "exp": 1631814083,
    "iat": 1631810483,
    "jti": "41775d8c-a61e-45c1-9c75-4fa0808616d2",
    "email": "xxxxxxxxxx@yahoo.co.jp"
}
```

## その他

本アプリはCognitoを使ったソーシャルログインのデモアプリです。 収集した個人情報は動作確認を行う以外の目的で利用することはありません。
