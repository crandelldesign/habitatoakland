<?php
/*
The comments page for Bones
*/

// don't load it if you can't comment
if ( post_password_required() ) {
  return;
}

?>

<?php // You can start editing here. ?>

  <?php if ( have_comments() ) : ?>

    <h3 id="comments-title" class="h2"><?php comments_number( __( '<span>No</span> Comments', 'bonestheme' ), __( '<span>One</span> Comment', 'bonestheme' ), __( '<span>%</span> Comments', 'bonestheme' ) );?></h3>

    <section class="commentlist">
      <?php
        wp_list_comments( array(
          'style'             => 'div',
          'short_ping'        => true,
          'avatar_size'       => 40,
          'callback'          => 'bones_comments',
          'type'              => 'all',
          'reply_text'        => __('Reply', 'bonestheme'),
          'page'              => '',
          'per_page'          => '',
          'reverse_top_level' => null,
          'reverse_children'  => ''
        ) );
      ?>
    </section>

    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
    	<nav class="navigation comment-navigation" role="navigation">
      	<div class="comment-nav-prev"><?php previous_comments_link( __( '&larr; Previous Comments', 'bonestheme' ) ); ?></div>
      	<div class="comment-nav-next"><?php next_comments_link( __( 'More Comments &rarr;', 'bonestheme' ) ); ?></div>
    	</nav>
    <?php endif; ?>

    <?php if ( ! comments_open() ) : ?>
    	<p class="no-comments"><?php _e( 'Comments are closed.' , 'bonestheme' ); ?></p>
    <?php endif; ?>

  <?php endif; ?>

  <?php

    $commenter = wp_get_current_commenter();
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );

    $fields =  array(

    'author' =>
      '<div class="form-group comment-form-author"><label for="author">' . __( 'Name', 'domainreference' ) . '</label> ' .
      ( $req ? '<span class="required">*</span>' : '' ) .
      '<input id="author" class="form-control" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
      '" ' . $aria_req . ' /></div>',

    'email' =>
      '<div class="form-group comment-form-email"><label for="email">' . __( 'Email', 'domainreference' ) . '</label> ' .
      ( $req ? '<span class="required">*</span>' : '' ) .
      '<input id="email" name="email" class="form-control" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
      '" ' . $aria_req . ' /></div>',

    'url' =>
      '<div class="form-group comment-form-url"><label for="url">' . __( 'Website', 'domainreference' ) . '</label>' .
      '<input id="url" name="url" class="form-control" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) .
      '"   /></div>',
    );
    $comments_args = array(

        // change "Leave a Reply" to "Comment"
        //'title_reply'=>'Discuss this post ?',
        'fields' => apply_filters( 'comment_form_default_fields', $fields ),
        'comment_field' =>  '<div class="form-group comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) .
            '</label><textarea id="comment" name="comment" class="form-control"  rows="8" aria-required="true">' .
            '</textarea></div>',
        'comment_notes_after' => '<div class="form-group"><div class="g-recaptcha" data-sitekey="'. getenv('RECAPTCHA_SITEKEY') .'"></div></div>',
        'submit_button' => '<input name="%1$s" type="submit" id="%2$s" class="%3$s btn btn-darkblue" value="%4$s" /><span class="clearfix"></span>'
    );

  ?>

  <?php comment_form($comments_args); ?>

