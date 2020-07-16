<!DOCTYPE html>
<html>
<head>
    {{ include('common/head.php') }}
    <link rel="stylesheet" href="{{version('/css/error.css')}}">
</head>
<body>
    {{ include('common/top.php') }}
    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="error-message-wrapper">
                    <h4 class="title">{{title}}</h4>
                    <h5 class="sub-title">{{message}}</h5>
                    <p class="desc">{{desc | raw}}</p>
                    {% if (redirectUrl) %}
                    <a href="{{redirectUrl}}" class="btn ">{{btnText}}</a>
                    {% endif %}
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
</body>
</html>