<!-- BEGIN: MAIN -->
<h2>{PHP.L.market}</h2>
<div class="wrapper">
	<ul class="std">
		<li><a href="{PHP|cot_url('admin','m=config&n=edit&o=module&p=market')}">{PHP.L.Configuration}</a></li>
		<li><a href="{ADMIN_HOME_URL}">{PHP.L.adm_valqueue}: {ADMIN_HOME_MARKETQUEUED}</a></li>
		<li><a href="{PHP|cot_url('market','m=add')}">{PHP.L.Add}</a></li>
		<li><a href="{PHP.db_market|cot_url('admin','m=extrafields&n=$this')}">{PHP.L.home_extrafields_market}</a></li>
	</ul>
</div>
<!-- END: MAIN -->