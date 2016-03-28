<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>

<ul>
@foreach($contracts as $contract)
  <li>
    {{$contract->ocdsid}}<br>
    @if($contract->releases->count())
    {{$contract->releases->first()->planning}}
    @endif
  </li>
@endforeach
</ul>
</body>
</html>