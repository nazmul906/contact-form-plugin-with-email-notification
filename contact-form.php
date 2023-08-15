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
    ';

    $form .= '
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var submitButton = document.getElementById("submit");
                submitButton.addEventListener("click", function() {
                    var title = document.getElementById("title").value;
                    var content = document.getElementById("post_content").value;
                    var name = document.getElementById("name").value;
                    var email = document.getElementById("email").value;

                    var formData = new FormData();
                    formData.append("action", "process_contact_form");
                    formData.append("post_title", title);
                    formData.append("post_content", content);
                    formData.append("your_name", name);
                    formData.append("email", email);


                    console.log("Test Purpose:");
                    for (var i of formData.entries()) {
                        console.log(i[0] + ": " + i[1]);
                    }
                    
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "'.admin_url('admin-ajax.php').'", true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            console.log("Request is received and Post Published");
                        }
                    };
                    xhr.send(formData);
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
            $post_url = get_permalink($post_id);
            $email_subject = "Greeting Note";
            $email_message = "Thank you for the comment. View it at: $post_url";
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
            );
            $send = wp_mail($email, $email_subject, $email_message,$headers);

             if($send){
                echo "email is sent successfully";
             }
             else{
                echo "oops!something went wrong";
             }


        }
    } else {
        echo 'Error: Invalid data.';
    }

    wp_die();
}


add_action('wp_ajax_process_contact_form', 'process_contact_form');
add_action('wp_ajax_nopriv_process_contact_form', 'process_contact_form');
