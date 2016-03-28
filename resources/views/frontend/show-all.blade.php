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
    <?php $r = $contract->releases->last(); ?>
    title: {{$r->planning->project}}<br>
    budget: {{number_format($r->planning->amount)}}<br>
    tender title: {{$r->tender->title}}<br>
    tender description: {{$r->tender->description}}<br>
    @endif
  </li>
@endforeach
</ul>
</body>
</html>