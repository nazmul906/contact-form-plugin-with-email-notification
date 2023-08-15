<?php
/*
Plugin Name: Contact Form
Description: A plugin to add a contact form
Author Name: Nazmul
Version: 1.00
*/

if (!function_exists('add_shortcode')) {
    echo "You are in the wrong tunnel";
    exit;
}

function add_contact_form()
{
    $form = '
        <form id="contactForm" method="post">
            <label for="title">Post Title:</label>
            <input type="text" name="title" id="title" required> <br>

            <label for="content">Post Content:</label>
            <input type="text" name="content" id="post_content" required> <br>

            <label for="name">Your Name:</label>
            <input type="text" name="name" id="name" required> <br>

            <label for="email">Your Email:</label>
            <input type="email" name="email" id="email" required><br>

            <button type="button" id="submit">Submit</button>

        </form>

        <script>
            jQuery(document).ready(function($) {
                $("#submit").click(function() {
                    var title = $("#title").val();
                    var content = $("#post_content").val(); 
                    var name = $("#name").val();
                    var email = $("#email").val();

                    var formData = new FormData();
                    formData.append("action", "process_contact_form");
                    formData.append("post_title", title);
                    formData.append("post_content", content);
                    formData.append("your_name", name);
                    formData.append("email", email);

                    $.ajax({
                        type: "POST",
                        url: my_ajax_object.ajaxurl, // Use the localized variable
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log("Request is received");
                        },
                        error: function() {
                            console.log("There is an error");
                        }
                    });
                });
            });
        </script>
    ';

    return $form;
}

add_shortcode('contact_form', 'add_contact_form');

function process_contact_form()
{
    if (isset($_POST['post_title'], $_POST['post_content'], $_POST['your_name'], $_POST['email'])) {
        $title = sanitize_text_field($_POST['post_title']);
        $content = sanitize_textarea_field($_POST['post_content']);
        $name = sanitize_text_field($_POST['your_name']);
        $email = sanitize_email($_POST['email']);

        $post_data = array(
            'post_title' => $title,
            'post_content' => $content,
            'post_author' => 1, 
            'post_status' => 'publish',
            'post_type' => 'post'
        );

        $post_id = wp_insert_post($post_data);
    
        if ($post_id) {
            $email_url = get_permalink($post_id);
            $email_subject = "Greeting Note";
            $email_message = "Thank you for the comment. View it at: $email_url";

            wp_mail($email, $email_subject, $email_message);
        }
    } else {
        echo 'Error: Invalid data.';
    }

    wp_die();
}

add_action('wp_ajax_process_contact_form', 'process_contact_form');
add_action('wp_ajax_nopriv_process_contact_form', 'process_contact_form');
