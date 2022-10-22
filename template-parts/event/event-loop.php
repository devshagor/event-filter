<div class="col-md-4">
    <div class="event-item">
        <?php if(has_post_thumbnail()): ?>
            <div class="thumb">
                <a href="<?php echo esc_url(the_permalink()); ?>">
                    <?php 
                        the_post_thumbnail();
                    ?>
               </a>
            </div>
        <?php endif; ?>
        <div class="content">
            <h2 class="event-title">
                <a href="<?php echo esc_url(the_permalink()); ?>">
                    <?php echo esc_html(the_title()); ?>
                </a>
            </h2>
            <p>
                <?php 
                    echo wp_trim_words( get_the_excerpt(), 10, '...' );
                ?>
            </p>
        </div>
    </div>
</div>