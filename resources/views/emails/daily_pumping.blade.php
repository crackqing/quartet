<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>每日流水排行</title>
</head>
<body>

    <table class="table">
        <head>
    
            <tr>
                <th>抽水</th>
                <th>时间</th>
                <th>代理ID</th>
            </tr>
        </head>
    
        <tbody>
            @foreach ($daily as $d)
            <tr>
                <td>{{$d->total_choushui}}</td>
                <td>{{$d->time}}</td>
                <td>{{$d->bind_id}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
        
</body>
</html>



