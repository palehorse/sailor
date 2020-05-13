<!DOCTYPE html>
<html>
<head>
    {{ include('common/head.php') }}
    <link rel="stylesheet" href="{{version('/css/error.css')}}">
    <script src="{{version('/js/signin.js')}}"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="message-wrapper">
                    <h3 class="title">{{title}}</h3>
                    <h4 class="sub-title">{{message}}</h4>
                    <p class="desc">{{desc | raw}}</p>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
</body>
</html>