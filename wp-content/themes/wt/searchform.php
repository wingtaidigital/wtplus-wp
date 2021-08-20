<form action="<?php echo esc_url( home_url( '/' ) ); ?>" class="wt-search-form wt-gutter-half" autocomplete="off">
	<div class="row collapse">
		<div class="column">
			<input type="search" placeholder="search" title="search" value="<?php echo get_search_query(); ?>" name="s" id="s" required>
		</div>
		<div class="column shrink wt-relative">
			<button type="submit" title="search">
				<i class="icon icon-search" aria-hidden="true"></i>
			</button>
			<!--						<button class="close-button" data-toggle="header-search" aria-label="Close" type="button">-->
			<!--							<span aria-hidden="true">×</span>-->
			<!--						</button>-->
		</div>
	</div>
</form>
