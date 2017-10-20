<?php
/*
* This is the basic search form that will get shown when you use get_search_form() anywhere in your theme.
* Updated with new HTMl5 goodness.
*
*/
?>

<form role="search" method="get" id="searchform" class="searchform" action="<?php echo home_url( '/' ); ?>">
    <span class="sr-only"><?php echo _x( 'Search for:', 'label' ) ?></span>
    <div class="searchbar input-group">
        <input class="form-control" type="search" id="s"
            placeholder="<?php echo esc_attr_x( 'Search …', 'placeholder' ) ?>"
            value="<?php echo get_search_query() ?>" name="s"
            title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" />
        <div class="input-group-btn">
            <button class="btn btn-default search" type="button"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
