
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>myTwitter</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>


<div class="well" style="width: 400px; margin: 0 auto; margin-top: 20px;">
    <form class="form-horizontal" method="post" action="login.php">

        <div class="form-group ">
            <div class="col-sm-offset-5 col-sm-7">
                <strong>Logowanie</strong>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="email">E-mail:</label>
            <div class="col-sm-8">
                <input name="email" id="email" type="text" maxlength="255" value=""/>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="password">Hasło:</label>
            <div class="col-sm-7">
                <input name="password" id="password" type="password" maxlength="255" value=""/>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-5 col-sm-7">
                <button class="btn btn-info btn-xs" type="submit" name="login">Loguj</button>
            </div>
        </div>

    </form>
</div>

</body>
</html>