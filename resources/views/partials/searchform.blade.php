<form role="search" method="get" class="search-form" action="{{ esc_url( home_url( '/' ) ) }}">
    <div class="input-group">
        <label class="sr-only">{{ _x( 'Search for:', 'label' ) }}</label>
        <input type="text" class="form-control" placeholder="{!! esc_attr_x( 'Search &hellip;', 'placeholder' ) !!}" value="{{ get_search_query() }}" name="s" aria-label="{!! esc_attr_x( 'Search &hellip;', 'placeholder' ) !!}" aria-describedby="search-btn">
        <div class="input-group-append">
            <button class="btn btn-outline-primary" type="submit" id="search-btn">
                <span class="sr-only">{{ esc_attr_x( 'Search', 'submit button' ) }}</span>
                <i class="fa fa-search" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</form>