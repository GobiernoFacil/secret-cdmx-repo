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
    tender amount: {{$r->tender->amount}}<br>
    buyer name: {{$r->buyer ? $r->buyer->name : "nope"}}<br>
    items: {{$r->tender->items->count()}}<br>
    @if($r->tender->items->count())
    <ul>
      @foreach($r->tender->items as $item)
      <li>{{$item->description}}</li>
      @endforeach
    </ul>
    @endif
    <br>
    @endif
  </li>
@endforeach
</ul>
</body>
</html>