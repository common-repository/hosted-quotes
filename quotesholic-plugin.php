<?php
/*
Plugin Name: Hosted Quotes
Plugin URI: https://www.quotesholic.com/
Description: Quote of the day wordpress plugin from quotesholic.com, This includes a customized widget which allows you to embed anywhere on your wordpress site and load quote of the day from more than 10000 quotes collection.
Version: 1.0
Author: Quote Holic Team
Author URI: https://quotesholic.com/
License: GPL2
*/
// The widget class
class qqoftheday_quotesholic_widget extends WP_Widget
{

    // Main constructor
    public function __construct()
    {
        parent::__construct(
            'my_custom_widget',
            __('Quote Of The Day', 'text_domain'),
            array(
                'customize_selective_refresh' => true,
            )
        );
    }

    public function form($instance)
    {
        // Set widget defaults
        $defaults = array(
            'title'    => '',
            'text'     => '',
            'checkbox' => false,
            'showCredits' => false
        );

        // Parse current settings with defaults
        extract(wp_parse_args((array) $instance, $defaults)); ?>

        <?php // Widget Title 
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Widget Title', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <?php // Checkbox 
        ?>
        <p>
            <input id="<?php echo esc_attr($this->get_field_id('checkbox')); ?>" name="<?php echo esc_attr($this->get_field_name('checkbox')); ?>" type="checkbox" value="1" <?php checked('1', $checkbox); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('checkbox')); ?>"><?php _e('Show Quote Images', 'text_domain'); ?></label>
        </p>

        <?php // Checkbox 
        ?>
        <p>
            <input id="<?php echo esc_attr($this->get_field_id('showCredits')); ?>" name="<?php echo esc_attr($this->get_field_name('showCredits')); ?>" type="checkbox" value="1" <?php checked('1', $showCredits); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('showCredits')); ?>"><?php _e('Show Credits to Publisher', 'text_domain'); ?></label>
        </p>

<?php }


    // Update widget settings
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title']    = isset($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : '';
        $instance['text']     = isset($new_instance['text']) ? wp_strip_all_tags($new_instance['text']) : '';
        $instance['checkbox']     = isset($new_instance['checkbox']) ? wp_strip_all_tags($new_instance['checkbox']) : '';
        $instance['showCredits']     = isset($new_instance['showCredits']) ? wp_strip_all_tags($new_instance['showCredits']) : '';
        return $instance;
    }

    public function widget($args, $instance)
    {
        extract($args);


        // Check the widget options
        $title    = isset($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        $text     = 'plugin from <a href="https://quotesholic.com/" target="_blank">quotesholic.com</a>';
        $checkbox = !empty($instance['checkbox']) ? $instance['checkbox'] : false;
        $showCredits = !empty($instance['showCredits']) ? $instance['showCredits'] : false;

        // WordPress core before_widget hook (always include )
        echo $before_widget;
        // Display the widget
        echo '<div class="widget-text wp_widget_plugin_box">';

        // Display widget title if defined
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        
        //read data from quotesholic API which free and open 
        $url = 'https://quotesholic.com/api/quoteoftheday';
        $responseFromAPI = wp_remote_get( $url );
        $response     = wp_remote_retrieve_body( $responseFromAPI );
        $quoteoftheday = json_decode($response);

        if ($quoteoftheday) {

            if ($checkbox) {
                echo '<img style="padding:10px;" src="' . $quoteoftheday->imageUrl . '"></img>';
            }
            echo '<p>' . $quoteoftheday->message . '</p>';
            echo '<p style="font-size: small;">- ' . $quoteoftheday->author->authorName . '</p>';
        } else {
            echo '<p>“Live as if you were to die tomorrow. Learn as if you were to live forever.</p>';
            echo '<p style="font-size: small;">- Mahatma Gandhi</p>';
        }

        // Display text field
        if ($text && $showCredits) {
            echo '<p style="font-size: x-small;">' . $text . '</p>';
        }

        echo '</div>';

        // WordPress core after_widget hook (always include )
        echo $after_widget;
    }
}

// Register the widget
function qqoftheday_register_this_widget()
{
    register_widget('qqoftheday_quotesholic_widget');
}
add_action('widgets_init', 'qqoftheday_register_this_widget');
