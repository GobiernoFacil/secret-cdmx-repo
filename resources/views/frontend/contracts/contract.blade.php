@extends('frontend.layouts.master')

@section('content')

<div class="breadcrumb">
  <div class="container">
    <a href="/v2"><strong>&lt;</strong> Lista de Licitaciones</a>
  </div>
</div>
<article>
  <div class="col-sm-3 sidebar">
    <div class="header">
      <?php if ($elcontrato->tender):?>
      <h3>LICITACIÓN</h3>
      <h2><?php echo $elcontrato->tender->title;?></h2>
      <?php else:?>
      <h3>PLANEACIÓN</h3>
      <h2><?php echo $elcontrato->planning->budget->project;?></h2>
      <?php endif;?>
    </div>
    <nav>
      <ul class="timeline">
        <?php if($elcontrato->contracts):?>
        <li><a href="#"  id="btn-contract-nav" class="nav_stage current" data-title="Contratación">
          <?php echo file_get_contents("img/nav_contratacion.svg"); ?></a>
          <ul id="nav_contract">
            <?php 
              $count_nav = 0;
              foreach($elcontrato->contracts as $contract):?>
            <?php 
                $count_nav++;
                $time_contract = strtotime($contract->dateSigned);
                $time_contract = date('d/m/Y',$time_contract);?>
            <li class="active"><a id="btn-contract-<?php echo $count_nav;?>" href="#" data-title="<?php echo $time_contract;?>" class="t_right"></a></li>
            <?php endforeach;?>
          </ul>
        </li>
        <?php endif;?>
        <?php if($elcontrato->awards):?>
        <li><a href="#" id="btn-award-nav" class="nav_stage <?php echo !$elcontrato->contracts ? 'current' : '';?>" data-title="Adjudicación">
          <?php echo file_get_contents("img/nav_adjudicacion.svg"); ?></a> 
          <ul id="nav_award">
            <?php $count_nav = 0;
              foreach($elcontrato->awards as $award):?>
            <?php 
                $count_nav++;
                $time_award = strtotime($award->date);
                $time_award = date('d/m/Y',$time_award);?>
            <li><a id="btn-award-<?php echo $count_nav;?>" href="#" data-title="<?php echo $time_award;?>" class="t_right"></a></li>
            <?php endforeach;?>
          </ul>
        </li>
        <?php endif;?>
        <?php if($elcontrato->planning):?>
        <li><a href="#" id="btn-tender" class="nav_stage <?php echo (!$elcontrato->contracts || !$elcontrato->awards) ? 'current' : '';?>" data-title="Licitación"><?php echo file_get_contents("img/nav_licitacion.svg"); ?></a></li>
        <?php endif;?>
        <?php if($elcontrato->tender):?>
        <li><a href="#" id="btn-planning"class="nav_stage" data-title="Planeación"><?php echo file_get_contents("img/nav_planeacion.svg"); ?></a></li>
        <?php endif;?>
      </ul>
    </nav>
  </div>  

  <div class="col-sm-9 info">     
        <!-- tender-->
		@include('frontend.contracts.includes.tender')        
		
		@if($elcontrato->awards)
        <!--awards-->
		@include('frontend.contracts.includes.awards')        
	    @endif
        
        @if($elcontrato->singlecontracts)
        <!-- contratos-->
		@include('frontend.contracts.includes.contracts')        
        @endif
        <!-- planning-->
		@include('frontend.contracts.includes.planning')        
      </div>
      <div class="clearfix"></div>
</article>

<script type="text/javascript" src="/js/bower_components/jquery/dist/jquery.min.js"></script>
<script>
$( document ).ready(function() {
  
  var tender      = $("#tender"),
    planning  = $("#planning"),
    contract1   = $("#contract-1"),
    contract2   = $("#contract-2"),
    award1    = $("#award-1"),
    award2    = $("#award-2"),
    timeline  = $(".timeline li"),
    //subnav
    nav_contract = $("#nav_contract"),
    nav_award    = $("#nav_award"),
    ///btn
    btn_contract_nav = $("#btn-contract-nav"),
    btn_contract1    = $("#btn-contract-1"),
    btn_contract2    = $("#btn-contract-2"),
    btn_award_nav  = $("#btn-award-nav"),
    btn_award1     = $("#btn-award-1"),
    btn_award2     = $("#btn-award-2"),
    btn_planning   = $("#btn-planning"),
    btn_tender       = $("#btn-tender");
  
  ///hide
  <?php if($elcontrato->singlecontracts):?>
    tender.hide();
    planning.hide();
    award1.hide();
    award2.hide();
    contract2.hide();
    nav_award.hide();
  <?php endif;?>
  
  <?php if(!$elcontrato->singlecontracts):?>
    <?php if($elcontrato->awards):?>
      tender.hide();
      planning.hide();
    <?php else:?>
      <?php if($elcontrato->tender):?>
      planning.hide();
      <?php endif;?>
    <?php endif;?>
  <?php endif;?>
  
  
  
  function changeClass(element) {
    timeline.removeClass("active");
    timeline.children("a").removeClass("current");
    $(element).parent("li").addClass("active");
    $(element).addClass("current");
  };
  
  //// btns
  btn_contract_nav.on("click", function (){
    event.preventDefault();
    timeline.removeClass("active");
    timeline.children("a").removeClass("current");
    $(this).addClass("current");    
    nav_contract.children("li:first-child").addClass("active");
    //hide
    tender.hide();
    planning.hide();
    award1.hide();
    award2.hide();
    nav_award.hide();
    contract2.hide();
    //show
    nav_contract.show();
    contract1.show();
  });
  btn_contract1.on("click", function (){
    event.preventDefault();
    changeClass(this);
    $(this).addClass("active");
    btn_contract_nav.addClass("current")
    //hide
    tender.hide();
    planning.hide();
    award1.hide();
    award2.hide();
    contract2.hide();
    nav_award.hide();
    //show
    nav_contract.show();
    contract1.show();
  });
  btn_contract2.on("click", function (){
    event.preventDefault();
    changeClass(this);
    $(this).addClass("active");
    btn_contract_nav.addClass("current")
    //hide
    tender.hide();
    planning.hide();
    award1.hide();
    award2.hide();
    contract1.hide();
    nav_award.hide();
    //show
    nav_contract.show();
    contract2.show();
  });
  
  ///btn awards
  btn_award_nav.on("click", function (){
    event.preventDefault();
    timeline.removeClass("active");
    timeline.children("a").removeClass("current");
    $(this).addClass("current");    
    nav_award.children("li:first-child").addClass("active");
    //hide
    tender.hide();
    planning.hide();
    award2.hide();
    contract1.hide();
    contract2.hide();
    nav_contract.hide();
    //show
    nav_award.show();
    award1.show();
  });
  btn_award1.on("click", function (){
    event.preventDefault();
    changeClass(this);
    $(this).addClass("active");
    btn_award_nav.addClass("current")
    //hide
    tender.hide();
    planning.hide();
    award2.hide();
    contract1.hide();
    contract2.hide();
    nav_contract.hide();
    //show
    nav_award.show();
    award1.show();
  });
  btn_award2.on("click", function (){
    event.preventDefault();
    changeClass(this);
    $(this).addClass("active");
    btn_award_nav.addClass("current")
    //hide
    tender.hide();
    planning.hide();
    award1.hide();
    contract1.hide();
    contract2.hide();
    nav_contract.hide();
    //show
    nav_award.show();
    award2.show();
  });
  
  ///btn tender
  btn_tender.on("click", function (){
    event.preventDefault();
    changeClass(this);
    //hide
    contract1.hide();
    planning.hide();
    contract2.hide();
    nav_contract.hide();
    award1.hide();
    award2.hide();
    nav_award.hide();
    //show
    tender.show();
  });
  
  ///btn planning
  btn_planning.on("click", function (){
    event.preventDefault();
    changeClass(this);
    //hide
    tender.hide();
    contract1.hide();
    contract2.hide();
    nav_contract.hide();
    award1.hide();
    award2.hide();
    nav_award.hide();
    //show
    planning.show();
  });
    
});
</script>
@endsection