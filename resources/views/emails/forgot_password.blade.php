<!DOCTYPE html>
<html>

<head>
    <title>パスワード再設定</title>
</head>

<body>
<p>お客様、</p>
<br>
<p>次のIDに関連付けられているアカウントのパスワード再設定リクエストを受けました：{{$email}}。</p>
<p>パスワードのリセットにお心当たりがない場合は、このメールを無視してください。</p>
<br>
<p>下記URLクリックし、パスワード再設定画面にアクセスしてください。<a
            href="{{config('app.url_client') . '/reset-password?email='.$email.'&token='.$token }}">{{config('app.url_client')
        . '/reset-password?email='.$email.'&token='.$token
        }}</a> </p>
<br>
<p>このメールに返信はできません。</p>
<p>お問い合せ等ございましたら、まずは下記ウェブサイトにてご確認ください。</p>
<p>更にご質問がございましたら、下記連絡先にご連絡ください。</p>
<br>
<p>連絡先</p>
<p>社名</p>
<p>住所： </p>
<p>Eメール： </p>
<p>ホットライン： </p>
</body>
</html>
