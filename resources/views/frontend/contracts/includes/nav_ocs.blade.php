<nav>
	<ul class="timeline">
		@if($elcontrato->singlecontracts->count())
		<li><a href="#"  id="btn-contract-nav" class="nav_stage current" data-id="contracts" data-title="Contratación">
			<?php echo file_get_contents("img/nav_contratacion.svg"); ?></a>
			<ul id="nav_contract">
			  <?php    $count_nav = 0;?>
			  @foreach($elcontrato->singlecontracts as $contract)
			  <?php $count_nav++;
				  $date_signed = strtotime($contract->date_signed);
				  $date_signed = date('d/m/Y',$date_signed);
			  ?>
				<li class="active">
					<a  href="#" data-id="contract-{{$contract->id}}" data-title="{{ $contract->date_signed ? $date_signed : ''}}" class="t_right"></a>
				</li>
			  @endforeach
			</ul>
		</li>
	  	@endif

		@if($elcontrato->awards->count())
	  	<li><a href="#" id="btn-award-nav" class="nav_stage {{ !$elcontrato->singlecontracts ? 'current' : '' }}" data-id="awards" data-title="Adjudicación">
	  		<?php echo file_get_contents("img/nav_adjudicacion.svg"); ?></a> 
	  		<ul id="nav_award" {!! $elcontrato->singlecontracts->count() ? "class='hide'" : '' !!}>
	  		  <?php $count_nav = 0;?>
	  		  @foreach($elcontrato->awards as $award)
	  		  <?php 
	  		      $count_nav++;
	  		      $time_award = strtotime($award->date);
	  		      $time_award = date('d/m/Y',$time_award);?>
	  		  <li><a id="btn-award-<?php echo $count_nav;?>" href="#" data-title="{{ $award->date ? $time_award : ''}}" class="t_right"></a></li>
	  		  @endforeach
	  		</ul>
	  	</li>
	  	@endif
	  	
	  	@if($elcontrato->tender)
	  	<li><a href="#" data-id="tender" class="nav_stage {{ (!$elcontrato->singlecontracts || !$elcontrato->awards) ? 'current' : ''}}" data-title="Licitación"><?php echo file_get_contents("img/nav_licitacion.svg"); ?></a></li>
	  	@endif
	  	@if($elcontrato->planning)
	  	<li><a href="#" data-id="planning"class="nav_stage" data-title="Planeación"><?php echo file_get_contents("img/nav_planeacion.svg"); ?></a></li>
	  	@endif
	</ul>
</nav>