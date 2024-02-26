<!DOCTYPE html>
<html>

<head>
    <title>Please complete your registration</title>
</head>

<body>
    <p>Dear Sir/Madam,</p>
    <br>
    <p><b>We have received your temporary registration with the following email address:</b></p>
    <p>* Email address: {{$email}}</p>
    <p>* Time of registration: {{$timeNow}}/p>
    <br />
    <p>Please click on the following URL within 24 hours to complete the membership registration.</p>
    <p>Registered link: <a
            href="{{config('app.url_client') . '/confirm-register?email='.$email.'&token='.$token }}">{{config('app.url_client')
            . '/confirm-register?email='.$email.'&token='.$token }}</a></p>
    <br />
    <p><b>Note</b></p>
    <p>* Depending on the email service you are using, the link may become broken and result in the URL being cut off.</p>
    <p>* If clicking on the link does not work, please copy the entire URL without any spaces and paste it to your web browser.</p>
    <p>* This URL will expire within 24 hours. In that case, please try again from the beginning.</p>
    <p>* If you do not recognize the content of this email, please ignore this email.</p>
    <br />
    <p><b>Contact information</b></p>
    <p>Company name</p>
    <p>Address: </p>
    <p>Email: </p>
    <p>Hotline: </p>
    <p>Website: <a href=""></a></p>
    <br />
    <p><i>If you have any questions about  or your registration, please contact us from the contact information details above. Thank you for being part of our community.</i></p>
</body>

</html>