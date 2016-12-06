<?php
    $curentSelectedAuthorId = isset($post->post_author) && !empty($post->post_author) ? $post->post_author : get_current_user_id();
?>
<input type="hidden" name="post_author_override" value="<?php echo $curentSelectedAuthorId; ?>">

<div class="better-post-ui-author-select-filter">
    <input type="text" placeholder="<?php _e('Search'); ?>" name="better-post-ui-author-select-filter">
</div>
<ul class="better-post-ui-author-select">
    <?php
    if ($currentAuthor) :
    ?>
    <li class="selected" data-user-id="<?php echo $currentAuthor->ID; ?>">
        <?php if (get_user_meta($currentAuthor->ID, 'user_profile_picture', true)) : ?>
            <div class="profile-image" style="background-image:url('<?php echo get_field('user_profile_picture', 'user_' . $currentAuthor->ID); ?>');"></div>
        <?php else : ?>
            <div class="profile-image"></div>
        <?php endif; ?>
        <div class="profile-info">
            <span class="user-fullname"><?php echo get_user_meta($currentAuthor->ID, 'first_name', true); ?> <?php echo get_user_meta($currentAuthor->ID, 'last_name', true); ?></span>
            <span class="user-login"><?php echo $currentAuthor->data->user_login; ?></span>
        </div>
    </li>
    <?php endif; ?>
</ul>
